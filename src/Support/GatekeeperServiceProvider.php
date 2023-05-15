<?php

namespace Zhineng\Gatekeeper\Support;

use Illuminate\Support\ServiceProvider;
use Zhineng\Gatekeeper\Manager;

class GatekeeperServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->configure();
        $this->offerPublishing();
    }

    public function register()
    {
        $this->app->singleton('gatekeeper', function ($app) {
            $gatekeeper = new Manager;
            $gatekeeper->cacheUsing($app['cache']->store(config('gatekeeper.store')));

            return $gatekeeper;
        });
    }

    protected function configure()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/gatekeeper.php', 'gatekeeper'
        );
    }

    protected function offerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/gatekeeper.php' => config_path('gatekeeper.php'),
            ], 'gatekeeper-config');

            $this->publishes([
                __DIR__.'/../../migrations/create_gatekeeper_tables.php' =>
                    database_path('migrations/'.date('Y_m_d_His').'_create_gatekeeper_tables.php'),
            ], 'gatekeeper-migrations');
        }
    }
}
