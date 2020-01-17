<?php

namespace Sametsahindogan\JWTRedis\Http\Middleware;

use Closure;
use Sametsahindogan\JWTRedis\Services\ErrorService\ErrorBuilder;
use Sametsahindogan\JWTRedis\Services\Result\ErrorResult;

class PermissionMiddleware extends BaseMiddleware
{

    /**
     *
     * Handle an incoming request.
     *
     * @param $request
     * @param Closure $next
     * @param $permission
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function handle($request, Closure $next, $permission)
    {
        $this->setIfClaimIsNotExist($request);

        $this->setAuthedUser($request);

        $permissions = is_array($permission) ? $permission : explode('|', $permission);

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
