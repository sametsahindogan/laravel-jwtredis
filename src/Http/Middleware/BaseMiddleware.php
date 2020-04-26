<?php

namespace Sametsahindogan\JWTRedis\Http\Middleware;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Sametsahindogan\ResponseObjectCreator\ErrorResult;
use Sametsahindogan\ResponseObjectCreator\ErrorService\ErrorBuilder;
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
     * @return bool
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
     */
    protected function setAuthedUser($request)
    {
        $request->authedUser = Auth::user();
    }

    /**
     * @param $exception
     * @return JsonResponse
     */
    protected function getErrorResponse($exception)
    {
        $error = config('jwtredis.errors.'.class_basename($exception)) ?? config('jwtredis.errors.default');

        return response()->json(
            new ErrorResult(
                (new ErrorBuilder())
                    ->title($error['title'])
                    ->message($error['message'])
                    ->code($error['code'])
            )
        );
    }
}
