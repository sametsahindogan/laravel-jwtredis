<?php

namespace Sametsahindogan\JWTRedis\Contracts;

interface RedisCacheContract
{
    /**
     * @param string $key
     * @return $this
     */
    public function key(string $key);

    /**
     * @param $data
     * @return mixed
     */
    public function data($data);

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
