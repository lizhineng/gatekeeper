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
     * @param  Permission|string  $permission
     * @return $this
     */
    public function assignPermission(Permission|string $permission): self
    {
        if (is_string($permission)) {
            $permission = Gatekeeper::permissionModel()::where('name', $permission)->first() ?: throw CouldNotFindPermission::byName($permission);
        }

        $this->permissions()->attach($permission->getKey());

        return $this;
    }

    /**
     * Determine if the entity has the given permission.
     *
     * @param  Permission|string  $permission
     * @return bool
     * @throws CouldNotFindPermission
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