<?php

namespace Sametsahindogan\JWTRedis\Cache;

use Illuminate\Support\Facades\Redis;
use Sametsahindogan\JWTRedis\Contracts\RedisCacheContract;

class RedisCache implements RedisCacheContract
{
    /** @var mixed */
    protected $data;

    /** @var int */
    private $time;

    /** @var string */
    protected $key;

    /**
     * @param string $key
     *
     * @return RedisCacheContract
     */
    public function key(string $key): RedisCacheContract
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @param $data
     *
     * @return RedisCacheContract
     */
    public function data($data): RedisCacheContract
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCache()
    {
        $data = Redis::get($this->key);

        if (!is_null($data)) {
            return $this->unserialize($data);
        }

        return $data;
    }

    /**
     * @return bool
     */
    public function removeCache()
    {
        return Redis::del($this->key);
    }

    /**
     * @return bool|mixed
     */
    public function refreshCache()
    {
        if (!$this->getCache()) {
            return false;
        }

        $this->key($this->key)->removeCache();

        return $this->key($this->key)->data($this->data)->cache();
    }

    /**
     * @return mixed
     */
    public function cache()
    {
        $this->setTime();

        Redis::setex($this->key, $this->time, $this->serialize($this->data));

        return $this->data;
    }

    /**
     * @return $this
     */
    private function setTime(): RedisCacheContract
    {
        $this->time = (config('jwtredis.redis_ttl_jwt') ? config('jwt.ttl') : config('jwtredis.redis_ttl')) * 60;

        return $this;
    }

    /**
     * @param $value
     *
     * @return int|string
     */
    protected function serialize($value)
    {
        if (config('jwtredis.igbinary_serialization')) {
            return igbinary_serialize($value);
        }

        return serialize($value);
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    protected function unserialize($value)
    {
        if (config('jwtredis.igbinary_serialization')) {
            return igbinary_unserialize($value);
        }

        return unserialize($value);
    }
}
