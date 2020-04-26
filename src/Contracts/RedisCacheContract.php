<?php

namespace Sametsahindogan\JWTRedis\Contracts;

/**
 * Interface RedisCacheContract
 * @package Sametsahindogan\JWTRedis\Contracts
 */
interface RedisCacheContract
{
    /**
     * @param string $key
     * @return RedisCacheContract
     */
    public function key(string $key): RedisCacheContract;

    /**
     * @param $data
     * @return RedisCacheContract
     */
    public function data($data): RedisCacheContract;

    /**
     * @return mixed
     */
    public function removeCache();

    /**
     * @return mixed
     */
    public function getCache();

    /**
     * @return mixed
     */
    public function refreshCache();

    /**
     * @return mixed
     */
    public function cache();
}
