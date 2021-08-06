<?php

namespace Zhineng\Gatekeeper\Tests;

use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;
use Illuminate\Database\Eloquent\Collection;

class CacheTest extends FeatureTest
{
    protected Repository $cache;

    public function setUp(): void
    {
        parent::setUp();

        $this->cache = new Repository(new ArrayStore);
        $this->gatekeeper->cacheUsing($this->cache);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->gatekeeper->withoutCache();
    }

    public function test_retrieves_permissions_with_cache()
    {
        $this->assertInstanceOf(Collection::class, $this->gatekeeper->permissions());
    }
}
