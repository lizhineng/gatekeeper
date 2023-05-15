<?php

namespace Zhineng\Gatekeeper\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Zhineng\Gatekeeper\Exceptions\CouldNotFindPermission;
use Zhineng\Gatekeeper\Facades\Gatekeeper;
use Zhineng\Gatekeeper\Models\Permission;

trait HasPermissions
{
    /**
     * Directly-assigned permissions of the entity.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function permissions()
    {
        return $this->morphToMany(Gatekeeper::permissionModel(), 'assignable', 'assigned_permissions');
    }

    /**
     * Scope a query to only include entities with the given permission.
     *
     * @param  Builder  $query
     * @param  Permission|iterable|string  $permissions
     * @return void
     * @throws CouldNotFindPermission
     */
    public function scopePermission(Builder $query, Permission|iterable|string $permissions): void
    {
        $permissions = is_iterable($permissions) ? $permissions : [$permissions];

        $ids = [];

        foreach ($permissions as $permission) {
            if (is_string($permission)) {
                $permission = Gatekeeper::permissionModel()::where('name', $permission)->first() ?: throw CouldNotFindPermission::byName($permission);
            }

            $ids[] = $permission->getKey();
        }

        $query->whereHas('permissions', fn ($query) => $query->whereKey($ids));
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
     * Remove permission from the entity.
     *
     * @param  Permission|iterable|string  $permissions
     * @return $this
     * @throws CouldNotFindPermission
     */
    public function removePermission(Permission|iterable|string $permissions): self
    {
        $permissions = is_iterable($permissions) ? $permissions : [$permissions];

        $ids = [];

        foreach ($permissions as $permission) {
            if (is_string($permission)) {
                $permission = Gatekeeper::permissionModel()::where('name', $permission)->first() ?: throw CouldNotFindPermission::byName($permission);
            }

            $ids[] = $permission->getKey();
        }

        $this->permissions()->detach($ids);

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

        return $this->permissions->contains($permission);
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