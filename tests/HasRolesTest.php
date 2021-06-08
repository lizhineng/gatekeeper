<?php

namespace Zhineng\Gatekeeper\Tests;

use Zhineng\Gatekeeper\Models\Permission;
use Zhineng\Gatekeeper\Models\Role;

class HasRolesTest extends FeatureTest
{
    public function test_assigns_role_to_user()
    {
        $editor = Role::make(['name' => 'editor']);

        $user = $this->makeUser();
        $this->assertFalse($user->hasRole($editor));

        $user->assignRole($editor);
        $this->assertTrue($user->fresh()->hasRole($editor));
    }

    public function test_removes_user_from_role()
    {
        $editor = Role::make(['name' => 'editor']);

        $user = $this->makeUser()
            ->assignRole($editor)
            ->removeRole($editor);

        $this->assertFalse($user->hasRole($editor));
    }

    public function test_checks_user_permission()
    {
        $readPosts = Permission::make(['name' => 'read:posts']);
        $editor = Role::make(['name' => 'editor'])->assignPermission($readPosts);
        $user = $this->makeUser()->assignRole($editor);
        $this->assertTrue($user->allows($readPosts));
    }
}