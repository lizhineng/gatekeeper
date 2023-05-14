<?php

namespace Zhineng\Gatekeeper\Tests;

use Zhineng\Gatekeeper\Models\Permission;

trait ProvidesPermissions
{
    public function provides_permissions()
    {
        return [
            'permission model' => [
                fn() => Permission::create(['name' => 'read:posts']),
            ],
            'permission name' => [
                fn() => Permission::create(['name' => 'read:posts'])->name,
            ],
            'multiple permission models' => [
                fn() => [
                    Permission::create(['name' => 'read:posts']),
                    Permission::create(['name' => 'write:posts']),
                ],
            ],
            'multiple permission names' => [
                fn() => [
                    Permission::create(['name' => 'read:posts'])->name,
                    Permission::create(['name' => 'write:posts'])->name,
                ],
            ],
            'multiple permissions' => [
                fn() => [
                    Permission::create(['name' => 'read:posts']),
                    Permission::create(['name' => 'write:posts'])->name,
                ],
            ],
        ];
    }
}