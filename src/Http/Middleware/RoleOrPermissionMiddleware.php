<?php

namespace Sametsahindogan\JWTRedis\Http\Middleware;

use Closure;
use Sametsahindogan\ResponseObjectCreator\ErrorResult;
use Sametsahindogan\ResponseObjectCreator\ErrorService\ErrorBuilder;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class RoleOrPermissionMiddleware extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param $request
     * @param Closure $next
     * @param $roleOrPermission
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function handle($request, Closure $next, $roleOrPermission)
    {
        try {

            $this->setIfClaimIsNotExist($request);

        } catch (TokenExpiredException|TokenInvalidException|JWTException $e) {

            return response()->json(
                new ErrorResult(
                    (new ErrorBuilder())
                        ->title('Operation Failed')
                        ->message($e->getMessage())
                        ->extra([])
                )
            );
        }

        $this->setAuthedUser($request);

        $rolesOrPermissions = is_array($roleOrPermission) ? $roleOrPermission : explode('|', $roleOrPermission);

        if (!$request->authedUser->hasAnyRole($rolesOrPermissions) && !$request->authedUser->hasAnyPermission($rolesOrPermissions)) {
            return response()->json(
                new ErrorResult(
                    (new ErrorBuilder())
                        ->title('Operation Failed')
                        ->message('User does not have the right roles or permissions.')
                        ->extra([])
                )
            );
        }

        return $next($request);
    }
}
