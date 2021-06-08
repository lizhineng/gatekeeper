<?php

namespace Zhineng\Gatekeeper;

use Illuminate\Database\Eloquent\Builder;
use Zhineng\Gatekeeper\Models\Permission;
use Zhineng\Gatekeeper\Models\Role;

trait HasRoles
{
    public function roles()
    {
        return $this->morphToMany(Role::class, 'assignable', 'assigned_roles');
    }

    public function scopeRole(Builder $query, Role $role): Builder
    {
        return $query->whereHas('roles', fn ($query) => $query->whereKey($role->getKey()));
    }

    public function scopePermission(Builder $query, Permission $permission): Builder
    {
        return $query->whereHas('roles', fn ($query) => $query->whereKey($permission->roles->pluck('id')));
    }

    public function assignRole(Role $role): self
    {
        $this->roles()->attach($role);

        return $this;
    }

    public function removeRole(Role $role): self
    {
        $this->roles()->detach($role);

        return $this;
    }

    public function hasRole(Role $role): bool
    {
        return $this->roles->contains($role);
    }

    public function allows(Permission $permission): bool
    {
        return $this->roles->intersect($permission->roles)->isNotEmpty();
    }
}