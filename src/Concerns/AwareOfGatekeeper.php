<?php

namespace Zhineng\Gatekeeper\Concerns;

use LogicException;
use Zhineng\Gatekeeper\Manager as Gatekeeper;

trait AwareOfGatekeeper
{
    private static ?Gatekeeper $gatekeeper = null;

    public static function setGatekeeper(Gatekeeper $instance)
    {
        static::$gatekeeper = $instance;
    }

    protected function gatekeeper(): Gatekeeper
    {
        if (is_null(static::$gatekeeper)) {
            throw new LogicException('Missing Gatekeeper instance.');
        }

        return static::$gatekeeper;
    }
}
