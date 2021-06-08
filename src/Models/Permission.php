<?php

namespace Zhineng\Gatekeeper\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['name'];

    public static function make(array $attributes): static
    {
        return tap((new static($attributes)))->save();
    }

    public function roles()
    {
        return $this->morphedByMany(Role::class, 'assignable', 'assigned_permissions');
    }
}