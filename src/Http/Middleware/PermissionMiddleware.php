<?php

namespace Sametsahindogan\JWTRedis\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Sametsahindogan\JWTRedis\Services\ErrorService\ErrorBuilder;
use Sametsahindogan\JWTRedis\Services\Result\ErrorResult;

class PermissionMiddleware
{
    public function handle($request, Closure $next, $permission)
    {
        $request->authedUser = Auth::user();

        $permissions = is_array($permission)
            ? $permission
            : explode('|', $permission);

        foreach ($permissions as $permission) {
            if ($request->authedUser->can($permission)) {
                return $next($request);
            }
        }

        return response()->json(
            new ErrorResult(
                (new ErrorBuilder())
                    ->title('Operation Failed')
                    ->message('User does not have the right roles.')
                    ->extra([])
            )
        );
    }
}
