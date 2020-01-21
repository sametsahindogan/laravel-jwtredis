<?php

namespace Sametsahindogan\JWTRedis\Facades;

use Illuminate\Support\Facades\Facade;
use Sametsahindogan\JWTRedis\Contracts\RedisCacheContract;

/**
 * Class RedisCache
 * @package Sametsahindogan\JWTRedis\Facades
 */
class RedisCache extends Facade
{
    /**
     * @return string
     */
    public static function getFacadeAccessor()
    {
        return RedisCacheContract::class;
    }
}
