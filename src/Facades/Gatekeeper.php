<?php

namespace Zhineng\Gatekeeper\Facades;

use Illuminate\Support\Facades\Facade;

class Gatekeeper extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'gatekeeper';
    }
}