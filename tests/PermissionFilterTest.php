<?php

namespace Zhineng\Gatekeeper\Tests;

use Illuminate\Database\Eloquent\Collection;
use Zhineng\Gatekeeper\Models\Role;
use Zhineng\Gatekeeper\Tests\Fixtures\User;

class PermissionFilterTest extends FeatureTest
{
    use ProvidesPermissions;

    /**
     * @dataProvider provides_permissions
     */
    public function test_filters_entities_by_permission($getPermissions)
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
}