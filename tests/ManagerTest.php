<?php

namespace Zhineng\Gatekeeper\Tests;

use Zhineng\Gatekeeper\Tests\Fixtures\MyPermission;
use Zhineng\Gatekeeper\Tests\Fixtures\MyRole;

class ManagerTest extends FeatureTest
{
    public function test_swaps_permission_model()
    {
        $this->gatekeeper->permissionUsing(MyPermission::class);
        $this->assertSame(MyPermission::class, $this->gatekeeper->permissionModel());
    }

    public function test_swaps_role_model()
    {
        $this->gatekeeper->roleUsing(MyRole::class);
        $this->assertSame(MyRole::class, $this->gatekeeper->roleModel());
    }
}