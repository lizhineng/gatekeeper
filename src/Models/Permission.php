<?php

namespace Zhineng\Gatekeeper\Models;

use Illuminate\Database\Eloquent\Model;
use Zhineng\Gatekeeper\Concerns\AwareOfGatekeeper;

class Permission extends Model
{
    use AwareOfGatekeeper;

    protected $guarded = [];

    public function roles()
    {
        return $this->morphedByMany(Role::class, 'assignable', 'assigned_permissions');
    }
}
