<?php

namespace Zhineng\Gatekeeper\Models;

use Illuminate\Database\Eloquent\Model;
use Zhineng\Gatekeeper\Concerns\AwareOfGatekeeper;

class Permission extends Model
{
    use AwareOfGatekeeper;

    protected $guarded = [];

    public static function normalize(Permission|iterable|string $permissions)
    {
        if (is_iterable($permissions)) {
            return collect($permissions)
                ->map(fn ($permission) => static::$gatekeeper->permission($permission)->getKey());
        } elseif (is_string($permissions)) {
            return static::$gatekeeper->permission($permissions);
        }

        return $permissions;
    }

    public function roles()
    {
        return $this->morphedByMany(Role::class, 'assignable', 'assigned_permissions');
    }
}
