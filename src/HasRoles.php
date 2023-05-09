<?php

namespace Zhineng\Gatekeeper;

use Illuminate\Database\Eloquent\Builder;
use Zhineng\Gatekeeper\Concerns\HasPermissions;
use Zhineng\Gatekeeper\Exceptions\CouldNotFindRole;
use Zhineng\Gatekeeper\Facades\Gatekeeper;
use Zhineng\Gatekeeper\Models\Role;

trait HasRoles
{
    use HasPermissions;

    public function roles()
    {
        return $this->morphToMany(Role::class, 'assignable', 'assigned_roles');
    }

    public function scopeRole(Builder $query, Role $role): Builder
    {
        return $query->whereHas('roles', fn ($query) => $query->whereKey($role->getKey()));
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
}