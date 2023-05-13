<?php

namespace Zhineng\Gatekeeper\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Zhineng\Gatekeeper\Exceptions\CouldNotFindPermission;
use Zhineng\Gatekeeper\Facades\Gatekeeper;
use Zhineng\Gatekeeper\Models\Permission;

trait HasPermissions
{
    public function permissions()
    {
        return $this->morphToMany(Permission::class, 'assignable', 'assigned_permissions');
    }

    public function scopePermission(Builder $query, Permission|string $permission): Builder
    {
        if (is_string($permission)) {
            $permission = Permission::gatekeeper()->permission($permission);
        }

        return $query->whereHas('roles', fn ($query) => $query->whereKey($permission->roles->pluck('id')));
    }

    /**
     * Assign permission to the entity.
     *
     * @param  Permission|iterable|string  $permissions
     * @return $this
     */
    public function assignPermission(Permission|iterable|string $permissions): self
    {
        $permissions = is_iterable($permissions) ? $permissions : [$permissions];

        $ids = [];

        foreach ($permissions as $permission) {
            if (is_string($permission)) {
                $permission = Gatekeeper::permissionModel()::where('name', $permission)->first() ?: throw CouldNotFindPermission::byName($permission);
            }

            $ids[] = $permission->getKey();
        }

        $this->permissions()->attach($ids);

        return $this;
    }

    /**
     * Determine if the entity has the given permission.
     *
     * @param  Permission|iterable|string  $permission
     * @return bool
     * @throws CouldNotFindPermission
     */
    public function allows(Permission|iterable|string $permission): bool
    {
        if (is_iterable($permission)) {
            return $this->allowsAll($permission);
        }

        if (is_string($permission)) {
            $permission = Gatekeeper::permissionModel()::where('name', $permission)->first() ?: false;
        }

        if ($permission === false) {
            return false;
        }

        return $this->allowsThroughDirectPermission($permission)
            || $this->allowsThroughRole($permission);
    }

    /**
     * Determine if the entity allows the given permission through direct permission.
     *
     * @param  Permission  $permission
     * @return bool
     */
    public function allowsThroughDirectPermission(Permission $permission): bool
    {
        return $this->permissions->contains($permission);
    }

    /**
     * Determine if the entity allows the given permission through role.
     *
     * @param  Permission  $permission
     * @return bool
     */
    public function allowsThroughRole(Permission $permission): bool
    {
        return $permission->roles->intersect($this->roles)->isNotEmpty();
    }

    /**
     * Determine if the entity has all the given permissions.
     *
     * @param  iterable  $permissions
     * @return bool
     * @throws CouldNotFindPermission
     */
    public function allowsAll(iterable $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (! $this->allows($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if entity has any of the given permissions.
     *
     * @param  iterable  $permissions
     * @return bool
     */
    public function allowsAny(iterable $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->allows($permission)) {
                return true;
            }
        }

        return false;
    }
}