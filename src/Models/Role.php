<?php

namespace Zhineng\Gatekeeper\Models;

use Illuminate\Database\Eloquent\Model;
use Zhineng\Gatekeeper\Manager;

class Role extends Model
{
    protected $guarded = [];

    public static function create(array $attributes): static
    {
        return tap((new static($attributes)))->save();
    }

    public function permissions()
    {
        return $this->morphToMany(Permission::class, 'assignable', 'assigned_permissions');
    }

    public function assignPermission(Permission|array|string $permissions): self
    {
        if (is_array($permissions)) {
            $permissions = collect($permissions)
                ->map(fn ($permission) => $this->manager()->permission($permission)->getKey());
        } elseif (is_string($permissions)) {
            $permissions = $this->manager()->permission($permissions);
        }

        $this->permissions()->attach($permissions);

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

    protected function manager(): Manager
    {
        return Manager::getInstance();
    }
}