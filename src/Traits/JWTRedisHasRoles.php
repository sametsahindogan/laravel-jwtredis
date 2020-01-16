<?php

namespace Sametsahindogan\JWTRedis\Traits;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles as OriginalHasRole;

trait JWTRedisHasRoles
{
    use OriginalHasRole {
        assignRole as protected originalAssignRole;
        givePermissionTo as protected originalGivePermissionTo;
    }

    /**
     * @OVERRIDE!
     *
     * !Important; Triggering the observer.
     *
     * @param mixed ...$roles
     * @return $this
     */
    public function assignRole(...$roles)
    {
        $this->originalAssignRole(...$roles);

        $this->triggerTheObserver();

        return $this;
    }

    /**
     * @OVERRIDE!
     *
     * !Important; Made only one changes this method for triggering observer.
     *
     * @param mixed ...$permissions
     * @return $this
     */
    public function givePermissionTo(...$permissions)
    {
        $this->originalGivePermissionTo(...$permissions);

        $this->triggerTheObserver();

        return $this;
    }

    /**
     * @OVERRIDE!
     *
     * !Important; Made only one changes this method for triggering observer.
     *
     * @return bool
     */
    public function hasPermissionTo($permission, $guardName = null): bool
    {
        return $this->hasDirectPermission($permission) || $this->hasPermissionViaRole($permission);
    }

    /**
     * @OVERRIDE!
     *
     * !Important; Made some changes this method for check authed user only permissions without db query.
     *
     * @return bool
     */
    public function hasDirectPermission($permission): bool
    {
        if (is_string($permission)) {
            return $this->permissions->contains('name', $permission);
        }

        if (is_int($permission)) {
            return $this->permissions->contains('id', $permission);
        }
    }

    /**
     * @OVERRIDE!
     *
     * !Important; Made some changes this method for check authed user only permissions of role without db query.
     *
     * @return bool
     */
    public function hasPermissionViaRole($permission): bool
    {
        $roles = $this->roles;
        $allow = false;

        if (is_string($permission)) {
            foreach ($roles as $role) {
                if ($role->permissions->contains('name', $permission)) $allow = true;
            }
        }

        if (is_int($permission)) {
            foreach ($roles as $role) {
                if ($role->permissions->contains('id', $permission)) $allow = true;
            }
        }
        return $allow;
    }

    /**
     * @return bool
     */
    public function checkUserStatus()
    {
        $column = config('jwtredis.status_column_title');
        $values = config('jwtredis.banned_statuses');

        return !in_array($this->$column, $values);
    }

    /**
     * Get the stored key in the Redis for user data.
     *
     * @return string
     */
    public function getRedisKey()
    {
        return config('jwtredis.redis_auth_prefix') . $this->getJWTIdentifier();
    }

    /**
     * @param $role
     * @return bool
     */
    public function triggerTheObserver()
    {
        /** @var Model $model */
        $model = $this;

        $class = config('jwtredis.observer');

        (new $class)->updated($model);
    }
}
