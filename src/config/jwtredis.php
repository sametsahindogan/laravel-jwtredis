<?php

return [

    /**
     * This observer class, listening event on your user model.
     */
    'observer' => \Sametsahindogan\JWTRedis\Observers\UserRedisObserver::class,

    /**
     *
     * If observer async option is true, model's events are processed in the queue.
     * ! Don't forget to run Laravel Queue Worker.
     */
    'observer_events_queue' => false,

    /**
     *
     * This is your user model.
     */
    'user_model' =>  \App\User::class,

    /**
     *
     * If it's option is true, user stored in Redis up to jwt_ttl value time.
     */
    'redis_ttl_jwt' => true,

    /**
     *
     * User stored in Redis redis_ttl value time.
     */
    'redis_ttl' => 60,

    /**
     *
     * If it's user id is 1, this user stored in Redis auth_1.
     */
    'redis_auth_prefix' => 'auth_',

     /**
      *
      * If check banned user option is true, every necessary middleware check if user banned.
      */
    'check_banned_user' => false,

    /**
     *
     * Status column name.
     */
    'status_column_title' => 'status',

    /**
     *
     * Return 'user is banned' response for this user statuses.
     */
    'banned_statuses' => ['banned', 'deactivate'],

    /**
     * You can add this array to your own relations, anything you want to store in Redis.
     */
    'cache_relations' => [
        'roles.permissions',
        'permissions'
    ],

    /**
     * You can customize the error messages and error codes.
     */
    'error_codes' => [

        'default' => [
            'title' => 'Operation Failed',
            'message' => 'An error occurred.',
            'code' => 0,
        ],

        'AccountBlockedException' => [
            'title' => 'Operation Failed',
            'message' => 'Your account has been blocked by the administrator.',
            'code' => 1,
        ],

        'TokenNotProvidedException' => [
            'title' => 'Operation Failed',
            'message' => 'Token not provided.',
            'code' => 2,
        ],

        'JWTException' => [
            'title' => 'Operation Failed',
            'message' => 'A token is required',
            'code' => 3,
        ],

        'TokenBlacklistedException' => [
            'title' => 'Operation Failed',
            'message' => 'The token has been blacklisted.',
            'code' => 4,
        ],

        'TokenExpiredException' => [
            'title' => 'Operation Failed',
            'message' => 'Token has expired.',
            'code' => 5,
        ],

        'TokenInvalidException' => [
            'title' => 'Operation Failed',
            'message' => 'Could not decode or verify token.',
            'code' => 6,
        ],

        'PermissionException' => [
            'title' => 'Operation Failed',
            'message' => 'User does not have the right permissions.',
            'code' => 7,
        ],

        'RoleException' => [
            'title' => 'Operation Failed',
            'message' => 'User does not have the right roles.',
            'code' => 8,
        ],

        'RoleOrPermissionException' => [
            'title' => 'Operation Failed',
            'message' => 'User does not have the right roles or permissions.',
            'code' => 9,
        ]
    ]
];
