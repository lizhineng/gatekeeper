<?php

namespace Zhineng\Gatekeeper\Tests;

use Zhineng\Gatekeeper\Tests\Fixtures\MyPermission;
use Zhineng\Gatekeeper\Tests\Fixtures\MyRole;

class SwapModelTest extends FeatureTest
{
    public function setUp(): void
    {
        parent::setUp();

        $this->gatekeeper->permissionUsing(MyPermission::class);
        $this->gatekeeper->roleUsing(MyRole::class);
    }

    public function test_swaps_permission_model()
    {
        $this->assertSame(MyPermission::class, $this->gatekeeper->permissionModel());
    }

    public function test_swaps_role_model()
    {
        $this->assertSame(MyRole::class, $this->gatekeeper->roleModel());
    }
}