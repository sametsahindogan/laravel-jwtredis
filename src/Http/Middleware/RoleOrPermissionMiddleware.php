<?php

namespace Sametsahindogan\JWTRedis\Http\Middleware;

use Closure;
use Sametsahindogan\JWTRedis\Services\ErrorService\ErrorBuilder;
use Sametsahindogan\JWTRedis\Services\Result\ErrorResult;

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
        $this->setIfClaimIsNotExist($request);

        $this->setAuthedUser($request);

        $rolesOrPermissions = is_array($roleOrPermission) ? $roleOrPermission : explode('|', $roleOrPermission);

        if (!$request->authedUser->hasAnyRole($rolesOrPermissions) && !$request->authedUser->hasAnyPermission($rolesOrPermissions)) {
            return response()->json(
                new ErrorResult(
                    (new ErrorBuilder())
                        ->title('Operation Failed')
                        ->message('User does not have the right roles.')
                        ->extra([])
                )
            );
        }

        return $next($request);
    }
}
