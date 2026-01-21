<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Support\Facades\Auth;

class ModelHasRole extends MorphPivot
{

    protected static function booted()
    {
        static::creating(function ($pivot) {
            $userId = Auth::id() ?? config('app.system_user_id');
            $pivot->created_by_id = $userId;
            $pivot->updated_by_id = $userId;
        });

        static::updating(function ($pivot) {
            $pivot->updated_by_id = Auth::id() ?? config('app.system_user_id');
        });
    }
}
