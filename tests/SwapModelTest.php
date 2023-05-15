<?php

namespace Zhineng\Gatekeeper\Tests;

use Zhineng\Gatekeeper\Facades\Gatekeeper;
use Zhineng\Gatekeeper\Models\Permission;
use Zhineng\Gatekeeper\Models\Role;
use Zhineng\Gatekeeper\Tests\Fixtures\MyPermission;
use Zhineng\Gatekeeper\Tests\Fixtures\MyRole;

class SwapModelTest extends FeatureTest
{
    public function setUp(): void
    {
        parent::setUp();

        Gatekeeper::permissionUsing(MyPermission::class);
        Gatekeeper::roleUsing(MyRole::class);
    }

    public function test_swaps_permission_model()
    {
        $this->assertSame(MyPermission::class, Gatekeeper::permissionModel());
    }

    public function test_swaps_role_model()
    {
        $this->assertSame(MyRole::class, Gatekeeper::roleModel());
    }

    public function test_respects_swapped_model_when_retrieving_roles_from_entity()
    {
        $editor = Role::create(['name' => 'editor']);
        $user = $this->makeUser()->assignRole($editor);
        $this->assertInstanceOf(MyRole::class, $user->roles()->first());
    }

    public function test_respects_swapped_model_when_retrieving_permissions_from_entity()
    {
        $readPosts = Permission::create(['name' => 'read:posts']);
        $user = $this->makeUser()->assignPermission($readPosts);
        $this->assertInstanceOf(MyPermission::class, $user->permissions()->first());
    }
}