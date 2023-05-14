<?php

namespace Zhineng\Gatekeeper;

use Zhineng\Gatekeeper\Concerns\HasPermissions;
use Zhineng\Gatekeeper\Concerns\HasRoles;

trait HasCapabilities
{
    use HasRoles, HasPermissions;
}