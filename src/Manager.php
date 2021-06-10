<?php

namespace Zhineng\Gatekeeper;

use Illuminate\Database\Eloquent\Collection;
use Zhineng\Gatekeeper\Exceptions\FailedToFindPermission;
use Zhineng\Gatekeeper\Models\Permission;

class Manager
{
    protected static ?Manager $instance = null;

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
        return Permission::with('roles')->get();
    }
}