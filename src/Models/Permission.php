<?php

namespace Zhineng\Gatekeeper\Models;

use Illuminate\Database\Eloquent\Model;
use Zhineng\Gatekeeper\Facades\Gatekeeper;

class Permission extends Model
{
    protected $guarded = [];

    public function roles()
    {
        return $this->morphedByMany(Gatekeeper::roleModel(), 'assignable', 'assigned_permissions');
    }
}
