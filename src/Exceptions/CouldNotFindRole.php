<?php

namespace Zhineng\Gatekeeper\Exceptions;

use Exception;

class CouldNotFindRole extends Exception
{
    public static function byName(string $name): self
    {
        return new static("Could not retrieve the role by given name [{$name}].");
    }
}