<?php

namespace Zhineng\Gatekeeper\Models;

use Illuminate\Database\Eloquent\Model;
use Zhineng\Gatekeeper\Concerns\AwareOfGatekeeper;
use Zhineng\Gatekeeper\Facades\Gatekeeper;

class Permission extends Model
{
    use AwareOfGatekeeper;

    protected $guarded = [];

    public function roles()
    {
        return $this->morphedByMany(Gatekeeper::roleModel(), 'assignable', 'assigned_permissions');
    }
}
