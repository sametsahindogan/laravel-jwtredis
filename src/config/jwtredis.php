<?php

return [

    /**
     * This observer class, listening event on your models.
     */
    'observer' => \Sametsahindogan\JWTRedis\Observers\UserRedisObserver::class,

    /**
     *
     * This user model, your auth model.
     */
    'user_model' =>  \App\Models\User::class,

    /**
     *
     * If it's option is true, user stored in Redis up to jwt_ttl value time.
     */
    'redis_ttl_jwt' => true,

    /**
     *
     * User stored in Redis redis_ttl value second.
     */
    'redis_ttl' => 60,

    /**
     *
     * If it's user id is 1, this user stored in Redis auth_1.
     */
    'redis_auth_prefix' => 'auth_',

     /**
      *
      * If it's option is true, every Role or Permission middleware checked user banned.
      */
    'check_banned_user' => true,

    /**
     *
     * Status column name.
     */
    'status_column_title' => 'status',

    /**
     *
     * Return banned user response for this user status.
     */
    'banned_statuses' => ['banned', 'deactivate'],

    /**
     * You can add this array your own relations anything you want.
     */
    'cache_relations' => [
        'roles.permissions',
        'permissions'
    ],

];
