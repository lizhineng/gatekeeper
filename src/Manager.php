<?php

namespace Zhineng\Gatekeeper;

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Database\Eloquent\Collection;
use Zhineng\Gatekeeper\Exceptions\CouldNotFindPermission;
use Zhineng\Gatekeeper\Models\Permission;
use Zhineng\Gatekeeper\Models\Role;

class Manager
{
    protected ?Repository $cache = null;

    protected static string $permissionModel = Permission::class;

    protected static string $roleModel = Role::class;

    public function bootEloquent()
    {
        static::$permissionModel::setGatekeeper($this);
    }

    public function permission(Permission|string $permission): Permission
    {
        if (is_string($permission)) {
            return $this->permissions()->firstWhere('name', $permission)?: throw CouldNotFindPermission::byName($permission);
        }

        return $permission;
    }

    public function permissions(): Collection
    {
        if ($this->hasCache()) {
            return $this->cache()->remember($this->cacheKey(), $seconds = 60 * 60 * 24, function () {
                return static::$permissionModel::with('roles')->get();
            });
        }

        return static::$permissionModel::with('roles')->get();
    }

    public function cache(): ?Repository
    {
        return $this->cache;
    }

    public function hasCache(): bool
    {
        return ! is_null($this->cache());
    }

    public function cacheUsing(Repository $cache): self
    {
        $this->cache = $cache;

        return $this;
    }

    public function withoutCache(): self
    {
        $this->cache = null;

        return $this;
    }

    public function cacheKey(): string
    {
        return 'gatekeeper.permissions';
    }
}