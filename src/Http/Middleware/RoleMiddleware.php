<?php

namespace Sametsahindogan\JWTRedis\Http\Middleware;

use Sametsahindogan\JWTRedis\Services\ErrorService\ErrorBuilder;
use Sametsahindogan\JWTRedis\Services\Result\ErrorResult;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * @param $request
     * @param Closure $next
     * @param $role
     * @return mixed
     * @throws AuthorizationException
     */
    public function handle($request, Closure $next, $role)
    {
        $request->authedUser = Auth::user();

        $roles = is_array($role) ? $role : explode('|', $role);

        if(config('jwtredis.check_banned_user')){
            if (!$request->authedUser->checkUserStatus()) {
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

        if (!$request->authedUser->hasAnyRole($roles) || !$request->authedUser->can('get-users')) {
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
