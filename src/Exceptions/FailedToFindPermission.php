<?php

namespace Zhineng\Gatekeeper\Exceptions;

use Exception;

class FailedToFindPermission extends Exception
{
    public static function byName(string $name): static
    {
        return new static("Failed to retrieve the permission by given name [$name].");
    }
}