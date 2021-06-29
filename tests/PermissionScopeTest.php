<?php

namespace Zhineng\Gatekeeper\Tests;

use Illuminate\Database\Eloquent\Collection;
use Zhineng\Gatekeeper\Models\Permission;
use Zhineng\Gatekeeper\Models\Role;
use Zhineng\Gatekeeper\Tests\Fixtures\User;

class PermissionScopeTest extends FeatureTest
{
    public function test_scopes_users_by_permission()
    {
        $editor = Role::create(['name' => 'editor']);
        $readPosts = Permission::create(['name' => 'read:posts']);
        $editor->assignPermission($readPosts);

        $user1 = $this->makeUser()->assignRole($editor);
        $user2 = $this->makeUser();

        $this->assertInstanceOf(Collection::class, $users = User::permission($readPosts)->get());
        $this->assertTrue($users->contains($user1));
        $this->assertFalse($users->contains($user2));
    }

    public function test_scopes_users_by_permission_name()
    {
        $readPosts = Permission::create(['name' => 'read:posts']);

        $this->assertInstanceOf(Collection::class, User::permission($readPosts->name)->get());
    }
}