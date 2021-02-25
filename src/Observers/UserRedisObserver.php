<?php

namespace Sametsahindogan\JWTRedis\Observers;

use Illuminate\Database\Eloquent\Model;
use Sametsahindogan\JWTRedis\Jobs\ProcessObserver;

/**
 * Class UserRedisObserver.
 */
class UserRedisObserver
{
    /**
     * Handle the Model "updated" event.
     *
     * @param Model $model
     *
     * @return void
     */
    public function updated(Model $model)
    {
        $this->handler($model, __FUNCTION__);
    }

    /**
     * Handle the Model "deleted" event.
     *
     * @param Model $model
     *
     * @return void
     */
    public function deleted(Model $model)
    {
        $this->handler($model, __FUNCTION__);
    }

    /**
     * @param Model  $model
     * @param string $action
     */
    protected function handler(Model $model, string $action)
    {
        $handler = new ProcessObserver($model, $action);

        config('jwtredis.observer_events_queue') ? dispatch($handler) : $handler->updated();
    }
}
