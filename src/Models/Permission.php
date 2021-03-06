<?php

namespace Fusion\Models;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\Permission\Models\Permission as BasePermission;

class Permission extends BasePermission
{
    /**
     * Wildcard guard.
     *
     * @var string
     */
    protected $guard_name = '*';

    /**
     * [Override].
     *
     * A Permission belongs to Users.
     * User Model is set in permissions config file.
     */
    public function users(): MorphToMany
    {
        return $this->morphedByMany(
            config('permission.models.user'),
            'model',
            config('permission.table_names.model_has_permissions'),
            'permission_id',
            config('permission.column_names.model_morph_key')
        );
    }
}
