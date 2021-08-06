<?php

namespace Zhineng\Gatekeeper\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $guarded = [];

    public function permissions()
    {
        return $this->morphToMany(Permission::class, 'assignable', 'assigned_permissions');
    }

    public function assignPermission(Permission|iterable|string $permissions): self
    {
        $permissions = Permission::normalize($permissions);

        $this->permissions()->attach($permissions);

        return $this;
    }

    public function removePermission(Permission|iterable|string $permissions): self
    {
        $permissions = Permission::normalize($permissions);

        $this->permissions()->detach($permissions);

        return $this;
    }

    public function allows(Permission $permission): bool
    {
        return $this->permissions->contains($permission);
    }

    public function allowsAny(array $permissions): bool
    {
        return collect($permissions)->some(fn ($permission) => $this->allows($permission));
    }
}