<?php

namespace Zhineng\Gatekeeper\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Zhineng\Gatekeeper\HasCapabilities;

class User extends Model
{
    use HasCapabilities;
}