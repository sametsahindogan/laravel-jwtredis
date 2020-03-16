<?php

namespace Sametsahindogan\JWTRedis\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Sametsahindogan\JWTRedis\Facades\RedisCache;

/**
 * Class ProcessObserver
 * @package Sametsahindogan\JWTRedis\Jobs
 */
class ProcessObserver implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var Model $model */
    private $model;

    /** @var string $process */
    private $process;

    /**
     * ProcessObserver constructor.
     * @param Model $model
     * @param string $process
     */
    public function __construct(Model $model, string $process)
    {
        $this->model = $model;
        $this->process = $process;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $method = $this->process;

        $this->$method();
    }

    /**
     * @return mixed
     */
    protected function deleted()
    {
        return RedisCache::key($this->model->getRedisKey())->removeCache();
    }

    /**
     * @return mixed
     */
    protected function updated()
    {
        // Refresh user..
        $this->model = config('jwtredis.user_model')::find($this->model->id);

        return RedisCache::key($this->model->getRedisKey())
            ->data($this->model->load(config('jwtredis.cache_relations')))
            ->refreshCache();
    }
}
