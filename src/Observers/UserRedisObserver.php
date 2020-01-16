<?php

namespace Sametsahindogan\JWTRedis\Observers;

use Sametsahindogan\JWTRedis\Facades\RedisCache;
use Illuminate\Database\Eloquent\Model;

class UserRedisObserver
{
    /**
     * Handle the Model "updated" event.
     * @return void
     */
    public function updated(Model $model)
    {
        RedisCache::key($model->getRedisKey())
            ->data($model->load(config('jwtredis.cache_relations')))
            ->refreshCache();
    }

    /**
     * Handle the Model "deleted" event.
     * @return void
     */
    public function deleted(Model $model)
    {
        RedisCache::key($model->getRedisKey())
            ->removeCache();
    }
}
