<?php

namespace Zhineng\Gatekeeper\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Zhineng\Gatekeeper\HasRoles;

class User extends Model
{
    use HasRoles;
}