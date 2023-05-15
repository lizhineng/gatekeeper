<?php

namespace Zhineng\Gatekeeper\Tests;

use Illuminate\Database\Eloquent\Collection;
use Zhineng\Gatekeeper\Models\Permission;
use Zhineng\Gatekeeper\Models\Role;
use Zhineng\Gatekeeper\Tests\Fixtures\User;

class PermissionFilterTest extends FeatureTest
{
    use ProvidesPermissions;

    /**
     * @dataProvider provides_permissions
     */
    public function test_filters_entities_by_permission_role($getPermissions)
    {
        $permissions = $getPermissions();

        $editor = Role::create(['name' => 'editor']);
        $editor->assignPermission($permissions);

        $user1 = $this->makeUser()->assignRole($editor);
        $user2 = $this->makeUser();

        $result = User::permission($permissions)->get();
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(1, $result);
        $this->assertTrue($result->contains($user1));
        $this->assertFalse($result->contains($user2));
    }

    public function test_filters_entities_by_permission_direct_assignment()
    {
        $readPosts = Permission::create(['name' => 'read:posts']);

        $user1 = $this->makeUser()->assignPermission($readPosts);
        $user2 = $this->makeUser();

        $result = User::permission($readPosts)->get();
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(1, $result);
        $this->assertTrue($result->contains($user1));
        $this->assertFalse($result->contains($user2));
    }

    public function test_filters_roles_by_permission()
    {
        $editor = Role::create(['name' => 'editor']);
        $admin = Role::create(['name' => 'admin']);
        $readPosts = Permission::create(['name' => 'read:posts']);
        $editDashboard = Permission::create(['name' => 'edit:dashboard']);
        $editor->assignPermission($readPosts);
        $admin->assignPermission($editDashboard);

        $result = Role::permission($editDashboard)->get();
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(1, $result);
        $this->assertTrue($result->contains($admin));
        $this->assertFalse($result->contains($editor));
    }
}