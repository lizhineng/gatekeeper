<?php

namespace Zhineng\Gatekeeper\Tests;

use Illuminate\Database\Eloquent\Collection;
use Zhineng\Gatekeeper\Models\Role;
use Zhineng\Gatekeeper\Tests\Fixtures\User;

class RoleScopeTest extends FeatureTest
{
    public function test_scopes_users_by_role()
    {
        $editor = Role::make(['name' => 'editor']);
        $user1 = $this->makeUser()->assignRole($editor);
        $user2 = $this->makeUser();

        $this->assertInstanceOf(Collection::class, $users = User::role($editor)->get());
        $this->assertTrue($users->contains($user1));
        $this->assertFalse($users->contains($user2));
    }
}