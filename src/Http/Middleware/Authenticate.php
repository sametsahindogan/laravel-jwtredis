<?php

namespace Sametsahindogan\JWTRedis\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class Authenticate extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {

            $this->setIfClaimIsNotExist($request);

        } catch (TokenExpiredException|TokenInvalidException|JWTException|TokenBlacklistedException $e) {

            return $this->getErrorResponse($e);

        }

        return $next($request);
    }

}
