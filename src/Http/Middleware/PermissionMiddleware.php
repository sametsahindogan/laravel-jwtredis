<?php

namespace Sametsahindogan\JWTRedis\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class PermissionMiddleware extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param $request
     * @param Closure $next
     * @param $permission
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function handle($request, Closure $next, $permission)
    {
        try {
            $this->setIfClaimIsNotExist($request);
        } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
            return $this->getErrorResponse($e);
        }

        $this->setAuthedUser($request);

        $permissions = is_array($permission) ? $permission : explode('|', $permission);

        foreach ($permissions as $permission) {
            if ($request->authedUser->can($permission)) {
                return $next($request);
            }
        }

        return $this->getErrorResponse('PermissionException');
    }
}
