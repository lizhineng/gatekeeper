<?php

namespace Zhineng\Gatekeeper;

use Illuminate\Database\Eloquent\Builder;
use Zhineng\Gatekeeper\Exceptions\CouldNotFindRole;
use Zhineng\Gatekeeper\Facades\Gatekeeper;
use Zhineng\Gatekeeper\Models\Permission;
use Zhineng\Gatekeeper\Models\Role;

trait HasRoles
{
    public function roles()
    {
        return $this->morphToMany(Role::class, 'assignable', 'assigned_roles');
    }

    public function permissions()
    {
        return $this->morphToMany(Permission::class, 'assignable', 'assigned_permissions');
    }

    public function scopeRole(Builder $query, Role $role): Builder
    {
        return $query->whereHas('roles', fn ($query) => $query->whereKey($role->getKey()));
    }

    public function scopePermission(Builder $query, Permission|string $permission): Builder
    {
        if (is_string($permission)) {
            $permission = Permission::gatekeeper()->permission($permission);
        }

        return $query->whereHas('roles', fn ($query) => $query->whereKey($permission->roles->pluck('id')));
    }

    /**
     * Assign roles to the entity.
     *
     * @param  Role|iterable|string  $roles
     * @return $this
     * @throws CouldNotFindRole
     */
    public function assignRole(Role|iterable|string $roles): self
    {
        $roles = is_iterable($roles) ? $roles: [$roles];

        $ids = [];

        foreach ($roles as $role) {
            if (is_string($role)) {
                $role = Gatekeeper::roleModel()::where('name', $role)->first() ?: throw CouldNotFindRole::byName($role);
            }

            $ids[] = $role->getKey();
        }

        $this->roles()->attach($ids);

        return $this;
    }

    /**
     * Remove roles from the entity.
     *
     * @param  Role|iterable|string  $roles
     * @return $this
     * @throws CouldNotFindRole
     */
    public function removeRole(Role|iterable|string $roles): self
    {
        $roles = is_iterable($roles) ? $roles : [$roles];

        $ids = [];

        foreach ($roles as $role) {
            if (is_string($role)) {
                $role = Gatekeeper::roleModel()::where('name', $role)->first() ?: throw CouldNotFindRole::byName($role);
            }

            $ids[] = $role->getKey();
        }

        $this->roles()->detach($ids);

        return $this;
    }

    /**
     * Determine if the user has the given role.
     *
     * @param  Role|iterable|string  $role
     * @return bool
     */
    public function hasRole(Role|iterable|string $role): bool
    {
        if (is_iterable($role)) {
            return $this->hasAllRoles($role);
        }

        if (is_string($role)) {
            $role = Gatekeeper::roleModel()::where('name', $role)->first();
        }

        return $role ? $this->roles->contains($role) : false;
    }

    /**
     * Determine if the user has all the given roles.
     *
     * @param  iterable  $roles
     * @return bool
     */
    public function hasAllRoles(iterable $roles): bool
    {
        foreach ($roles as $role) {
            if (! $this->hasRole($role)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if the entity has any of the given roles.
     *
     * @param  iterable  $roles
     * @return bool
     */
    public function hasAnyRoles(iterable $roles): bool
    {
        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Assign permission to the entity.
     *
     * @param  Permission  $permission
     * @return $this
     */
    public function assignPermission(Permission $permission): self
    {
        $this->permissions()->attach($permission->getKey());

        return $this;
    }

    /**
     * Determine if entity has the given permission.
     *
     * @param  Permission|string  $permission
     * @return bool
     * @throws Exceptions\CouldNotFindPermission
     */
    public function allows(Permission|string $permission): bool
    {
        if (is_string($permission)) {
            $permission = Permission::gatekeeper()->permission($permission);
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

    public function allowsAny(array $permissions): bool
    {
        return collect($permissions)->some(fn ($permission) => $this->allows($permission));
    }
}