<?php

namespace Zhineng\Gatekeeper;

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Database\Eloquent\Collection;
use Zhineng\Gatekeeper\Exceptions\FailedToFindPermission;
use Zhineng\Gatekeeper\Models\Permission;

class Manager
{
    protected static ?Manager $instance = null;

    protected ?Repository $cache = null;

    public static function getInstance(): Manager
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    public static function setInstance(Manager $manager): void
    {
        static::$instance = $manager;
    }

    public function permission(Permission|string $permission): Permission
    {
        if (is_string($permission)) {
            return $this->permissions()->firstWhere('name', $permission)?: throw FailedToFindPermission::byName($permission);
        }

        return $permission;
    }

    public function permissions(): Collection
    {
        if ($this->hasCache()) {
            return $this->cache()->remember($this->cacheKey(), $seconds = 60 * 60 * 24, function () {
                return Permission::with('roles')->get();
            });
        }

        return Permission::with('roles')->get();
    }

    public function cache(): ?Repository
    {
        return $this->cache;
    }

    public function hasCache(): bool
    {
        return ! is_null($this->cache);
    }

    public function cacheUsing(Repository $cache): self
    {
        $this->cache = $cache;

        return $this;
    }

    public function flushCache(): void
    {
        $this->cache()->forget($this->cacheKey());
    }

    public function cacheKey(): string
    {
        return 'gatekeeper.permissions';
    }
}