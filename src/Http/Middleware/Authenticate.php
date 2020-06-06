<?php

namespace Sametsahindogan\JWTRedis\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class Authenticate extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param $request
     * @param Closure $next
     *
     * @return JsonResponse
     */
    public function handle($request, Closure $next)
    {
        try {
            $this->setIfClaimIsNotExist($request);
        } catch (TokenExpiredException | TokenInvalidException | JWTException | TokenBlacklistedException $e) {
            return $this->getErrorResponse($e);
        }

        if (config('jwtredis.check_banned_user')) {
            $this->setAuthedUser($request);

            if (!$request->authedUser->checkUserStatus()) {
                return $this->getErrorResponse('AccountBlockedException');
            }
        }

        return $next($request);
    }
}
