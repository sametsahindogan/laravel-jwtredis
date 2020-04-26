<?php

namespace Sametsahindogan\JWTRedis\Contracts;

/**
 * Interface RedisCacheContract.
 */
interface RedisCacheContract
{
    /**
     * @param string $key
     *
     * @return RedisCacheContract
     */
    public function key(string $key): self;

    /**
     * @param $data
     *
     * @return RedisCacheContract
     */
    public function data($data): self;

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
