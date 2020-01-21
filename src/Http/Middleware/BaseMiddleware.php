<?php

namespace Sametsahindogan\JWTRedis\Http\Middleware;

use Illuminate\Support\Facades\Auth;
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
    }
}
