<?php

namespace Sametsahindogan\JWTRedis\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Sametsahindogan\JWTRedis\Services\ErrorService\ErrorBuilder;
use Sametsahindogan\JWTRedis\Services\Result\ErrorResult;
use Spatie\Permission\Exceptions\UnauthorizedException;

class RoleOrPermissionMiddleware
{
    public function handle($request, Closure $next, $roleOrPermission)
    {
        $request->authedUser = Auth::user();

        $rolesOrPermissions = is_array($roleOrPermission)
            ? $roleOrPermission
            : explode('|', $roleOrPermission);

        if (! $request->authedUser->hasAnyRole($rolesOrPermissions) && ! $request->authedUser->hasAnyPermission($rolesOrPermissions)) {
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
