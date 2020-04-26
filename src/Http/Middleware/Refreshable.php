<?php

namespace Sametsahindogan\JWTRedis\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Sametsahindogan\ResponseObjectCreator\SuccessResult;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Manager;
use Tymon\JWTAuth\Token;

class Refreshable extends BaseMiddleware
{
    /**
     * The JWT Authenticator.
     *
     * @var \Tymon\JWTAuth\JWTAuth
     */
    protected $auth;

    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @param \Tymon\JWTAuth\JWTAuth $auth
     *
     * @return void
     */
    public function __construct(JWTAuth $auth, Manager $manager)
    {
        $this->auth = $auth;
        $this->manager = $manager;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->checkForToken($request);

        try {
            $token = $this->auth->parseToken()->refresh();

            /** Application need this assignment for using Laravel's Auth facade. */
            $request->claim = $this->manager->decode(new Token($token))->get('sub');
        } catch (TokenInvalidException | JWTException $e) {
            return $this->getErrorResponse($e);
        }

        // Send the refreshed token back to the client.
        return $this->setAuthenticationResponse($token);
    }

    /**
     * Check the request for the presence of a token.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    protected function checkForToken(Request $request)
    {
        if (!$this->auth->parser()->setRequest($request)->hasToken()) {
            return $this->getErrorResponse('TokenNotProvided');
        }
    }

    /**
     * Set the token response.
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    protected function setAuthenticationResponse($token = null)
    {
        if (config('jwtredis.check_banned_user')) {
            if (!Auth::user()->checkUserStatus()) {
                return $this->getErrorResponse('AccountBlockedException');
            }
        }

        $token = $token ?: $this->auth->refresh();

        return response()->json(new SuccessResult(['token' => $token]));
    }
}
