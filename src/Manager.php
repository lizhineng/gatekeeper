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

    /**
     * The underlying permission model.
     *
     * @var class-string
     */
    protected string $permissionModel = Permission::class;

    /**
     * The underlying role model.
     *
     * @var class-string
     */
    protected string $roleModel = Role::class;

    /**
     * Configure permission model.
     *
     * @param  class-string  $model
     * @return $this
     */
    public function permissionUsing(string $model): self
    {
        $this->permissionModel = $model;

        return $this;
    }

    /**
     * Configure role model.
     *
     * @param  class-string  $model
     * @return $this
     */
    public function roleUsing(string $model): self
    {
        $this->roleModel = $model;

        return $this;
    }

    /**
     * Retrieve underlying permission model.
     *
     * @return class-string
     */
    public function permissionModel(): string
    {
        return $this->permissionModel;
    }

    /**
     * Retrieve underlying role model.
     *
     * @return class-string
     */
    public function roleModel(): string
    {
        return $this->roleModel;
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
                return $this->permissionModel()::with('roles')->get();
            });
        }

        return $this->permissionModel()::with('roles')->get();
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