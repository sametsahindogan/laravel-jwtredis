<?php

namespace Sametsahindogan\JWTRedis\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Manager;
use Tymon\JWTAuth\Token;
use Sametsahindogan\JWTRedis\Services\ErrorService\ErrorBuilder;
use Sametsahindogan\JWTRedis\Services\Result\ErrorResult;
use Sametsahindogan\JWTRedis\Services\Result\SuccessResult;

class Refreshable
{

    /**
     * The JWT Authenticator.
     *
     * @var \Tymon\JWTAuth\JWTAuth
     */
    protected $auth;

    /**
     * @var Manager $manager
     */
    protected $manager;

    /**
     * Create a new BaseMiddleware instance.
     *
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
     * @param \Closure $next
     *
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     *
     */
    public function handle($request, Closure $next)
    {
        $this->checkForToken($request);

        try {

            $token = $this->auth->parseToken()->refresh();

            $request->claim = $this->manager->decode(new Token($token))->get('sub');

        } catch (JWTException $e) {

            return response()->json(
                new ErrorResult(
                    (new ErrorBuilder())
                        ->title('Operation Failed')
                        ->message($e->getMessage() . $e->getCode())
                        ->extra([])
                )
            );

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
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     *
     */
    protected function checkForToken(Request $request)
    {
        if (!$this->auth->parser()->setRequest($request)->hasToken()) {
            throw new UnauthorizedHttpException('jwt-auth', 'Token not provided');
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
                return response()->json(
                    new ErrorResult(
                        (new ErrorBuilder())
                            ->title('Operation Failed')
                            ->message('Your account has been blocked by the administrator.')
                            ->extra([])
                    )
                );
            }

        }

        $token = $token ?: $this->auth->refresh();

        return response()->json(new SuccessResult(['token' => $token]));
    }
}

