<?php

namespace Zhineng\Gatekeeper\Tests;

use Zhineng\Gatekeeper\Models\Permission;
use Zhineng\Gatekeeper\Models\Role;

class RoleTest extends FeatureTest
{
    use ProvidesPermissions;

    /**
     * @dataProvider provides_permissions
     */
    public function test_assigns_permission_to_role($getPermissions)
    {
        $permissions = $getPermissions();

        $editor = Role::create(['name' => 'editor']);
        $this->assertFalse($editor->allows($permissions));

        $editor->assignPermission($permissions);
        $this->assertTrue($editor->fresh()->allows($permissions));
    }

    /**
     * @dataProvider provides_permissions
     */
    public function test_removes_permission_from_role($getPermissions)
    {
        $permissions = $getPermissions();

        $editor = Role::create(['name' => 'editor'])->assignPermission($permissions);
        $this->assertTrue($editor->allows($permissions));

        $editor->removePermission($permissions);
        $this->assertFalse($editor->fresh()->allows($permissions));
    }

    public function test_allows_all_permissions_checking()
    {
        $readPosts = Permission::create(['name' => 'read:posts']);
        $writePosts = Permission::create(['name' => 'write:posts']);

        $editor = Role::create(['name' => 'editor']);
        $this->assertFalse($editor->allowsAll([$readPosts, $writePosts]));

        $editor->assignPermission($readPosts);
        $this->assertFalse($editor->fresh()->allowsAll([$readPosts, $writePosts]));

        $editor->assignPermission($writePosts);
        $this->assertTrue($editor->fresh()->allowsAll([$readPosts, $writePosts]));
    }

    public function test_allows_any_permissions_checking()
    {
        $readPosts = Permission::create(['name' => 'read:posts']);
        $writePosts = Permission::create(['name' => 'write:posts']);

        $editor = Role::create(['name' => 'editor']);
        $this->assertFalse($editor->allowsAny([$readPosts, $writePosts]));

        $editor->assignPermission($writePosts);
        $this->assertTrue($editor->fresh()->allowsAny([$readPosts, $writePosts]));
    }
}
