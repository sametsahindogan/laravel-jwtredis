<?php

namespace Sametsahindogan\JWTRedis\Http\Middleware;

use Closure;
use Sametsahindogan\JWTRedis\Services\ErrorService\ErrorBuilder;
use Sametsahindogan\JWTRedis\Services\Result\ErrorResult;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Token;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {

            /** @var Token $token */
            $token = JWTAuth::getPayload(JWTAuth::getToken());

            if (!$token) {

                return response()->json(
                    new ErrorResult(
                        (new ErrorBuilder())
                            ->title('Operation Failed')
                            ->message('User not found.')
                            ->extra([])
                    )
                );
            }

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

        $request->claim = $token->get('sub');

        return $next($request);
    }

}
