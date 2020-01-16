<?php

namespace Sametsahindogan\JWTRedis;

use Illuminate\Support\ServiceProvider;
use Sametsahindogan\JWTRedis\Cache\RedisCache;
use Sametsahindogan\JWTRedis\Contracts\RedisCacheContract;
use Sametsahindogan\JWTRedis\Guards\JWTRedisGuard;
use Illuminate\Support\Facades\Auth;
use Sametsahindogan\JWTRedis\Providers\JWTRedisUserProvider;

class JWTRedisServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->bindRedisCache();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishConfig();
        $this->overrideJWTGuard();
        $this->overrideUserProvider();
        $this->bindObservers();
    }

    protected function publishConfig()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/jwtredis.php', 'jwtredis');

        $this->publishes([__DIR__ . '/config/jwtredis.php' => config_path('jwtredis.php')], 'config');
    }

    protected function overrideJWTGuard()
    {
        // Override JWT Guard for without DB query..
        Auth::extend('jwt_redis_guard', function ($app, $name, array $config) {

            // Return an instance of Illuminate\Contracts\Auth\Guard...
            return new JWTRedisGuard($app['tymon.jwt'], Auth::createUserProvider($config['provider']), $app['request']);
        });
    }

    protected function overrideUserProvider()
    {
        /**
         * Override Eloquent Provider for fetching user with role&permission query.
         */
        Auth::provider('jwt_redis_user_provider', function ($app, array $config) {

            // Return an instance of Illuminate\Contracts\Auth\UserProviderContract...
            return new JWTRedisUserProvider($app['hash'], $config['model']);
        });
    }

    protected function bindRedisCache()
    {
        $this->app->bind(RedisCacheContract::class, function ($app) {
            return new RedisCache();
        });
    }

    protected function bindObservers()
    {
        config('jwtredis.user_model')::observe(config('jwtredis.observer'));
    }
}
