<?php

namespace Zhineng\Gatekeeper\Tests;

use Zhineng\Gatekeeper\Models\Permission;
use Zhineng\Gatekeeper\Models\Role;

class RoleTest extends FeatureTest
{
    public function test_assigns_permission_to_role()
    {
        $readPosts = Permission::create(['name' => 'read:posts']);

        $editor = Role::create(['name' => 'editor']);
        $this->assertFalse($editor->allows($readPosts));

        $editor->assignPermission($readPosts);
        $this->assertTrue($editor->fresh()->allows($readPosts));
    }

    public function test_assigns_permission_to_role_by_scope()
    {
        $readPosts = Permission::create(['name' => 'read:posts']);

        $editor = Role::create(['name' => 'editor']);
        $this->assertFalse($editor->allows($readPosts));

        $editor->assignPermission($readPosts->name);
        $this->assertTrue($editor->fresh()->allows($readPosts));
    }

    public function test_assigns_multiple_permissions_to_role()
    {
        $readPosts = Permission::create(['name' => 'read:posts']);
        $writePosts = Permission::create(['name' => 'write:posts']);

        $editor = Role::create(['name' => 'editor']);
        $editor->assignPermission([$readPosts, $writePosts->name]);
        $this->assertTrue($editor->fresh()->allows($readPosts));
        $this->assertTrue($editor->fresh()->allows($writePosts));
    }

    public function test_removes_permission_from_role()
    {
        $readPosts = Permission::create(['name' => 'read:posts']);
        $editor = Role::create(['name' => 'editor']);

        $editor->assignPermission($readPosts);
        $editor->removePermission($readPosts);

        $this->assertFalse($editor->allows($readPosts));
    }

    public function test_removes_permission_from_role_by_scope()
    {
        $readPosts = Permission::create(['name' => 'read:posts']);
        $editor = Role::create(['name' => 'editor']);

        $editor->assignPermission($readPosts);
        $editor->removePermission($readPosts->name);

        $this->assertFalse($editor->allows($readPosts));
    }

    public function test_removes_multiple_permissions_from_role()
    {
        $readPosts = Permission::create(['name' => 'read:posts']);
        $writePosts = Permission::create(['name' => 'write:posts']);

        $editor = Role::create(['name' => 'editor']);
        $editor->assignPermission([$readPosts, $writePosts]);
        $editor->removePermission([$readPosts, $writePosts->name]);

        $this->assertFalse($editor->allowsAny([$readPosts, $writePosts]));
    }

    public function test_checks_if_has_any_permission()
    {
        $readPosts = Permission::create(['name' => 'read:posts']);
        $writePosts = Permission::create(['name' => 'write:posts']);

        $editor = Role::create(['name' => 'editor']);
        $this->assertFalse($editor->allowsAny([$readPosts, $writePosts]));

        $editor->assignPermission($writePosts);
        $this->assertTrue($editor->fresh()->allowsAny([$readPosts, $writePosts]));
    }
}
