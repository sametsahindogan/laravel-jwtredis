<?php

namespace Sametsahindogan\JWTRedis\Observers;

use Illuminate\Database\Eloquent\Model;
use Sametsahindogan\JWTRedis\Facades\RedisCache;
use Sametsahindogan\JWTRedis\Jobs\ProcessObserver;

/**
 * Class UserRedisObserver
 * @package Sametsahindogan\JWTRedis\Observers
 */
class UserRedisObserver
{
    /**
     * Handle the Model "updated" event.
     * @return void
     */
    public function updated(Model $model)
    {
        if (config('jwtredis.observer_events_queue')) {
            dispatch((new ProcessObserver($model, __FUNCTION__)));
        } else {
            // Refresh user..
            $model = config('jwtredis.user_model')::find($model->id);
            return RedisCache::key($model->getRedisKey())
                ->data($model->load(config('jwtredis.cache_relations')))
                ->refreshCache();
        }
    }

    /**
     * Handle the Model "deleted" event.
     * @return void
     */
    public function deleted(Model $model)
    {
        if (config('jwtredis.observer_events_queue')) {
            dispatch((new ProcessObserver($model, __FUNCTION__)));
        } else {
            return RedisCache::key($model->getRedisKey())->removeCache();
        }
    }
}
