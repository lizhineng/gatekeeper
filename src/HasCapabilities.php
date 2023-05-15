<?php

namespace Zhineng\Gatekeeper;

use Illuminate\Database\Eloquent\Builder;
use Zhineng\Gatekeeper\Concerns\HasPermissions;
use Zhineng\Gatekeeper\Concerns\HasRoles;
use Zhineng\Gatekeeper\Exceptions\CouldNotFindPermission;
use Zhineng\Gatekeeper\Facades\Gatekeeper;
use Zhineng\Gatekeeper\Models\Permission;

trait HasCapabilities
{
    use HasRoles, HasPermissions;

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

        $permissionIds = [];
        $roleIds = [];

        foreach ($permissions as $permission) {
            if (is_string($permission)) {
                $permission = Gatekeeper::permissionModel()::where('name', $permission)->first() ?: throw CouldNotFindPermission::byName($permission);
            }

            $permissionIds[] = $permission->getKey();
            $roleIds[] = $permission->roles->pluck('id');
        }

        $query->where(function (Builder $query) use ($permissionIds, $roleIds) {
            $query->whereHas('permissions', fn ($query) => $query->whereKey($permissionIds))
                ->orWhereHas('roles', fn ($query) => $query->whereKey($roleIds));
        });
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

        return $this->allowsViaDirectAssignment($permission)
            || $this->allowsViaRole($permission);
    }

    /**
     * Determine if the entity has the given permission via direct assignment.
     *
     * @param  Permission  $permission
     * @return bool
     */
    public function allowsViaDirectAssignment(Permission $permission): bool
    {
        return $this->permissions->contains($permission);
    }

    /**
     * Determine if the entity has the given permission via role.
     *
     * @param  Permission  $permission
     * @return bool
     */
    public function allowsViaRole(Permission $permission): bool
    {
        return $permission->roles->intersect($this->roles)->isNotEmpty();
    }
}