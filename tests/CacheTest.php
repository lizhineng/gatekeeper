<?php

namespace Zhineng\Gatekeeper\Tests;

use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;
use Illuminate\Support\Facades\DB;
use Zhineng\Gatekeeper\Manager;
use Zhineng\Gatekeeper\Models\Permission;

class CacheTest extends FeatureTest
{
    protected static ?Manager $gatekeeper = null;

    protected int $queryCount = 0;

    public function setUp(): void
    {
        parent::setUp();

        $this->enableQueryCount();
    }

    public function test_retrieves_permissions_has_cache()
    {
        $this->cacheReady();

        $this->resetQueryCount();

        $this->gatekeeper()->permissions();

        $this->assertQueryCount(0);
    }

    public function test_invalidates_cache_when_permission_created()
    {
        $this->cacheReady();

        Permission::create(['name' => 'foo']);

        $this->resetQueryCount();

        $this->gatekeeper()->permissions();

        $this->assertQueryCount(2);
    }

    /**
     * Initialize the cache by retrieving the permissions.
     */
    protected function cacheReady(): self
    {
        $this->gatekeeper()->permissions();

        return $this;
    }

    protected function gatekeeper(): Manager
    {
        if (static::$gatekeeper) {
            return static::$gatekeeper;
        }

        $gatekeeper = new Manager;
        $gatekeeper->cacheUsing(new Repository(new ArrayStore));

        Manager::setInstance($gatekeeper);

        return static::$gatekeeper = $gatekeeper;
    }

    protected function enableQueryCount()
    {
        DB::connection()->listen(function () {
            $this->queryCount++;
        });
    }

    protected function resetQueryCount()
    {
        $this->queryCount = 0;
    }

    protected function assertQueryCount(int $count)
    {
        $this->assertEquals($count, $this->queryCount);
    }
}