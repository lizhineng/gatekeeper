<?php

namespace Zhineng\Gatekeeper\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $guarded = [];

    public static function create(array $attributes): static
    {
        return tap((new static($attributes)))->save();
    }

    public function roles()
    {
        return $this->morphedByMany(Role::class, 'assignable', 'assigned_permissions');
    }
}