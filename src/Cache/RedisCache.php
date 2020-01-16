<?php

namespace Sametsahindogan\JWTRedis\Cache;

use Illuminate\Support\Facades\Cache;
use Sametsahindogan\JWTRedis\Contracts\RedisCacheContract;

class RedisCache implements RedisCacheContract
{
    /** @var mixed $data */
    protected $data;

    /** @var int $time */
    private $time;

    /** @var string $key */
    protected $key;

    /**
     * @param $time
     */
    public function key(string $key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @param $data
     * @return $this|RedisCacheContract
     */
    public function data($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCache()
    {
        return Cache::get($this->key);
    }

    /**
     * @return bool
     */
    public function removeCache()
    {
        return Cache::forget($this->key);
    }

    /**
     * @param $oldKey
     * @param $newKey
     * @return mixed
     */
    public function refreshCache()
    {
        if (!$this->getCache()) return false;

        $this->key($this->key)->removeCache();

        return $this->key($this->key)->data($this->data)->cache();
    }

    /**
     * @return mixed
     */
    public function cache()
    {
        $this->setTime();

        return Cache::remember($this->key, $this->time, function () {
            return $this->data;
        });
    }

    /**
     * Calculate * 60 for converting hour.
     * @param $time
     */
    private function setTime()
    {
        $this->time = ( config('jwtredis.redis_ttl_jwt') ? config('jwt.ttl') : config('jwtredis.redis_ttl') ) * 60;
        return $this;
    }
}
