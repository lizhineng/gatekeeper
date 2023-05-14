<?php

namespace Zhineng\Gatekeeper\Tests;

use Illuminate\Database\Eloquent\Collection;
use Zhineng\Gatekeeper\Models\Role;
use Zhineng\Gatekeeper\Tests\Fixtures\User;

class RoleFilterTest extends FeatureTest
{
    use ProvidesRoles;

    /**
     * @dataProvider provides_roles
     */
    public function test_filters_entity_by_role($getRoles)
    {
        $roles = $getRoles();

        $user1 = $this->makeUser()->assignRole($roles);
        $user2 = $this->makeUser();

        $users = User::role($roles)->get();
        $this->assertInstanceOf(Collection::class, $users);
        $this->assertCount(1, $users);
        $this->assertTrue($users->contains($user1));
        $this->assertFalse($users->contains($user2));
    }
}