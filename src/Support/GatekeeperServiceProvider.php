<?php

namespace Zhineng\Gatekeeper\Support;

use Illuminate\Support\ServiceProvider;

class GatekeeperServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->offerPublishing();
    }

    protected function offerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../migrations/create_gatekeeper_tables.php' =>
                    database_path('migrations/'.date('Y_m_d_His').'_create_gatekeeper_tables.php'),
            ]);
        }
    }
}