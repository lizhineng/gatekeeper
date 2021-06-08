<?php

namespace Zhineng\Gatekeeper\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name'];

    public static function make(array $attributes): static
    {
        return tap((new static($attributes)))->save();
    }

    public function permissions()
    {
        return $this->morphToMany(Permission::class, 'assignable', 'assigned_permissions');
    }

    public function assignPermission(Permission $permission): self
    {
        $this->permissions()->attach($permission);

        return $this;
    }

    public function removePermission(Permission $permission): self
    {
        $this->permissions()->detach($permission);

        return $this;
    }

    public function allows(Permission $permission): bool
    {
        return $this->permissions->contains($permission);
    }
}