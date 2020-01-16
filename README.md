# laravel-jwtredis

[![GitHub license](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://raw.githubusercontent.com/kevoj/redis-jwt/master/LICENSE)

> This package allow management of storing authed users on Redis with (roles&permissions, status and 
anything relation you want). 

<a><img src="https://chris.lu/upload/images/redis.png" width="80"></a>
<a><img src="https://cdn.auth0.com/blog/jwtalgos/logo.png" width="80"></a>

> Also this package have observer for listening and updating  to your user model on Redis.
This observer is triggered `when you assign roles & permissions to user, or update and delete to your user` model.

## Requirements

This package work with together [tymondesigns/jwt-auth](https://github.com/tymondesigns/jwt-auth) and [spatie/laravel-permission](https://github.com/spatie/laravel-permission) package under the hood.

![#](https://placehold.it/15/f03c15/000000?text=+) `Make sure to install and configure these dependencies. You must publish, migrate etc. all packages.` ![#](https://placehold.it/15/f03c15/000000?text=+)
 
- [nrk/predis](https://github.com/nrk/predis) **>= 1.1** (**Recommended 1.1**)
- [tymondesigns/jwt-auth](https://github.com/tymondesigns/jwt-auth) **>= 1.0** (**Recommended 1.0.x**)
- [spatie/laravel-permission](https://github.com/spatie/laravel-permission) **>= 3.3** (**Recommended 3.3**)

## Installation

```bash
composer require sametsahindogan/laravel-jwtredis
```

Once this has finished, you will need to add&change these values in `.env` file:
```dotenv
CACHE_DRIVER=redis
REDIS_CLIENT=predis
```

Next, you will need to change the `guards` and `providers` arrays in your `config/auth.php` config as follows:
```php
'guards' => [
        'api' => [
            'driver' => 'jwt_redis_guard',
            'provider' => 'users'
        ],
    ],

'providers' => [
        'users' => [
            'driver' => 'jwt_redis_user_provider',
            'model' =>  App\User::class, /* Your User Model */
        ],
    ],
```
   
Next, you will need to add the service provider to the `providers` array in your `config/app.php` config as follows:
```php
Sametsahindogan\JWTRedis\JWTRedisServiceProvider::class,
```

Next, also in the `config/app.php` config file, under the `aliases` array, you may want to add the `RedisCache` facade.
```php
'RedisCache' => \Sametsahindogan\JWTRedis\Facades\RedisCache::class,
```

Finally, you will want to publish the config using the following command:
```bash
php artisan vendor:publish --provider='Sametsahindogan\JWTRedis\JWTRedisServiceProvider'
```

When everything is complete, don't forget to add this Trait to your user model.
```php
use JWTRedisHasRoles;
```

## Usage

You do not have any instructions for use. You can use the Laravel Auth 
class as you like. `Auth::user()`, `$user->can('permission')` and 
all [spatie/laravel-permission](https://github.com/spatie/laravel-permission) package methods etc. 
every authorization check you made, will always be checked from Redis, not database.


You need to add your `$routeMiddleware` array in `app/Http/Kernel.php`
```php
'auth'               => \Sametsahindogan\JWTRedis\Http\Middleware\Authenticate::class,
'refreshable'        => \Sametsahindogan\JWTRedis\Http\Middleware\Refreshable::class,
'role'               => \Sametsahindogan\JWTRedis\Http\Middleware\RoleMiddleware::class,
'permission'         => \Sametsahindogan\JWTRedis\Http\Middleware\PermissionMiddleware::class,
'role_or_permission' => \Sametsahindogan\JWTRedis\Http\Middleware\RoleOrPermissionMiddleware::class,
```

For authorize user by token; <br>
( Use this middleware if the user's identity is not important. This middleware only checks if Token is valid. Doesn't send query the database.)
```php
Route::get("/example", "ExampleController@example")->middleware('auth');
```

To check user authority, you need to this one of these middlewares;<br>
( Use this middleware if the user's identity is important. This middlewares fetch user from Redis and mark as authorized. 
And you will reach all default auth methods anywhere you want. Just call Laravel Auth facades.)
```php
Route::get("/example", "ExampleController@example")->middleware('role:admin|user');
Route::get("/example", "ExampleController@example")->middleware('permissions:get-user|set-user');
Route::get("/example", "ExampleController@example")->middleware('role_or_permission:admin|get-user');
```

For refresh token, you can add `refreshable` middleware to route;<br>
( Also this middleware can refreshes user from Redis. )
```php
Route::get("/example", "ExampleController@example")->middleware('refreshable');
```

## Options

You can customize some options in that package `config/jwt redis.php` file.


You can set your user model.
```php
'user_model' =>  \App\Models\User::class,
```

If you want to use your custom observer class, you can also change this or inherit this class.
```php
'observer' => \Sametsahindogan\JWTRedis\Observers\UserRedisObserver::class,
```

If it's option is true, user status checked by necessary middlewares.
```php
'check_banned_user' => false,
```
- If you want to use banned user checking; you can set your own table column title and statuses value.
```php
    'status_column_title' => 'status',

    'banned_status_title' => ['banned', 'deactivate']
```
You can add this array your own relations anything you want store in Redis.
```php
'cache_relations' => [
        'roles.permissions',
        'permissions'
    ],
```

## License
MIT © [Samet Sahindogan](https://github.com/sametsahindogan/jwt-redis/blob/master/LICENSE)
