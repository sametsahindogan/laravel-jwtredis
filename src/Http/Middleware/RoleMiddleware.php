<?php

namespace Sametsahindogan\JWTRedis\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class RoleMiddleware extends BaseMiddleware
{
    /**
     * @param $request
     * @param Closure $next
     * @param $role
     *
     * @throws AuthorizationException
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        try {
            $this->setIfClaimIsNotExist($request);
        } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
            return $this->getErrorResponse($e);
        }

        $this->setAuthedUser($request);

        $roles = is_array($role) ? $role : explode('|', $role);

        if (config('jwtredis.check_banned_user')) {
            if (!$request->authedUser->checkUserStatus()) {
                return $this->getErrorResponse('AccountBlockedException');
            }
        }

        if (!$request->authedUser->hasAnyRole($roles)) {
            return $this->getErrorResponse('RoleException');
        }

        return $next($request);
    }
}
