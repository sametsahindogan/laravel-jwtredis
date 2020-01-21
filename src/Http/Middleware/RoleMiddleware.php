<?php

namespace Sametsahindogan\JWTRedis\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Sametsahindogan\ResponseObjectCreator\ErrorResult;
use Sametsahindogan\ResponseObjectCreator\ErrorService\ErrorBuilder;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class RoleMiddleware extends BaseMiddleware
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

        if (!$request->authedUser->hasAnyRole($roles)) {
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
