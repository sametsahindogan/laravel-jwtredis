<?php

namespace Sametsahindogan\JWTRedis\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Sametsahindogan\JWTRedis\Services\ErrorService\ErrorBuilder;
use Sametsahindogan\JWTRedis\Services\Result\ErrorResult;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Token;

abstract class BaseMiddleware
{

    /**
     *  If you don't use Authentication Middleware before that Middleware,
     *  application need to set a Claim (by Token) in Request object for
     *  using Laravel's Auth facade.
     *
     * @param $request
     */
    protected function setIfClaimIsNotExist($request)
    {
        if ($request->claim === null) {
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

            /** Application need this assignment for using Laravel's Auth facade. */
            $request->claim = $token->get('sub');
        }

        return true;
    }

    /**
     * This first request always comes from Redis,
     * then will always be stored in this Request object.
     *
     * @param $request
     * @return bool
     */
    protected function setAuthedUser($request)
    {
        $request->authedUser = Auth::user();
        return true;
    }

}
