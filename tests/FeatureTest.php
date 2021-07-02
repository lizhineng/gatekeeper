<?php

namespace Zhineng\Gatekeeper\Tests;

require __DIR__.'/../migrations/create_gatekeeper_tables.php';

use CreateGatekeeperTables;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\TestCase;
use Zhineng\Gatekeeper\Tests\Fixtures\User;

abstract class FeatureTest extends TestCase
{
    protected static Dispatcher|null $dispatcher = null;

    public function setUp(): void
    {
        $this->registerContainer();
        $this->registerDatabase();
        $this->migrate();
        $this->migrateForTesting();
    }

    protected function makeUser(): User
    {
        return User::create();
    }

    protected function registerContainer(): void
    {
        $container = Container::setInstance(new Container);

        Facade::setFacadeApplication($container);
    }

    protected function registerDatabase(): void
    {
        $container = Container::getInstance();

        $db = new Manager($container);

        $container->bind('db', fn () => $db);

        $db->addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);

        $db->bootEloquent();
        $db->setAsGlobal();

        $db->connection()->setEventDispatcher($this->dispatcher());

        Model::setEventDispatcher($this->dispatcher());
    }

    protected function migrate(): void
    {
        (new CreateGatekeeperTables)->up();
    }

    protected function migrateForTesting(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }

    protected function dispatcher(): Dispatcher
    {
        if (static::$dispatcher) {
            return static::$dispatcher;
        }

        return static::$dispatcher = new Dispatcher(Container::getInstance());
    }
}