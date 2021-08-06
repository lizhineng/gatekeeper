<?php

namespace Zhineng\Gatekeeper\Exceptions;

use Exception;

class CouldNotFindPermission extends Exception
{
    public static function byName(string $name): static
    {
        return new static("Could not retrieve the permission by given name [$name].");
    }
}