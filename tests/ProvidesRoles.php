<?php

namespace Zhineng\Gatekeeper\Tests;

use Zhineng\Gatekeeper\Models\Role;

trait ProvidesRoles
{
    public function provides_roles()
    {
        return [
            'role model' => [
                fn() => Role::create(['name' => 'editor']),
            ],
            'role name' => [
                fn() => Role::create(['name' => 'editor'])->name,
            ],
            'multiple role models' => [
                fn() => [
                    Role::create(['name' => 'admin']),
                    Role::create(['name' => 'editor']),
                ],
            ],
            'multiple role names' => [
                fn() => [
                    Role::create(['name' => 'admin'])->name,
                    Role::create(['name' => 'editor'])->name,
                ],
            ],
            'multiple roles' => [
                fn() => [
                    Role::create(['name' => 'admin']),
                    Role::create(['name' => 'editor'])->name,
                ],
            ],
        ];
    }
}