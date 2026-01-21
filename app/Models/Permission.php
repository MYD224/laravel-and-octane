<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    protected static function booted()
    {
        $userId = auth()->id() ?? config('app.system_user_id');
        static::creating(function ($model) use ($userId) {
            $model->created_by_id = $userId;
            $model->updated_by_id = auth()->id();
        });

        static::updating(function ($model) use ($userId) {
            $model->updated_by_id = $userId;
        });
    }
}
