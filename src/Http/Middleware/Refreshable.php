<?php

namespace Sametsahindogan\JWTRedis\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
     * @var JWTAuth
     */
    protected $auth;

    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @param JWTAuth $auth
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
     * @param $request
     * @param Closure $next
     * @return JsonResponse|Response
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
     * @param Request $request
     * @return JsonResponse
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
     * @return Response|JsonResponse
     */
    protected function setAuthenticationResponse($token = null)
    {
        $token = $token ?: $this->auth->refresh();

        return response()->json(new SuccessResult(['token' => $token]));
    }
}
