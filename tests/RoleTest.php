<?php

namespace Zhineng\Gatekeeper\Tests;

use Zhineng\Gatekeeper\Models\Permission;
use Zhineng\Gatekeeper\Models\Role;

class RoleTest extends FeatureTest
{
    public function tests_assigns_permission_to_role()
    {
        $readPosts = Permission::make(['name' => 'read:posts']);

        $editor = Role::make(['name' => 'editor']);
        $this->assertFalse($editor->allows($readPosts));

        $editor->assignPermission($readPosts);
        $this->assertTrue($editor->fresh()->allows($readPosts));
    }

    public function test_removes_permission_from_role()
    {
        $readPosts = Permission::make(['name' => 'read:posts']);
        $editor = Role::make(['name' => 'editor']);

        $editor->assignPermission($readPosts);
        $editor->removePermission($readPosts);

        $this->assertFalse($editor->allows($readPosts));
    }
}
