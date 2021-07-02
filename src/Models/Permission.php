<?php

namespace Zhineng\Gatekeeper\Models;

use Illuminate\Database\Eloquent\Model;
use Zhineng\Gatekeeper\Manager;

class Permission extends Model
{
    protected $guarded = [];

    protected static function booted()
    {
        static::saved(function ($model) {
            Manager::getInstance()->flushCache();
        });
    }

    public function roles()
    {
        return $this->morphedByMany(Role::class, 'assignable', 'assigned_permissions');
    }
}