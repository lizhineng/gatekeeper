<?php

namespace Zhineng\Gatekeeper\Models;

use Illuminate\Database\Eloquent\Model;
use Zhineng\Gatekeeper\Concerns\HasPermissions;

class Role extends Model
{
    use HasPermissions;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'roles';

    protected $guarded = [];
}