<?php

namespace Sametsahindogan\JWTRedis\Facades;

use Illuminate\Support\Facades\Facade;
use Sametsahindogan\JWTRedis\Contracts\RedisCacheContract;

class RedisCache extends Facade
{
    public static function getFacadeAccessor()
    {
        return RedisCacheContract::class;
    }
}
