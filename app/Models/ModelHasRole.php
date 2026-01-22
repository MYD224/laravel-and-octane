<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Support\Facades\Auth;

class ModelHasRole extends MorphPivot
{

    protected $guarded = [];
    protected $table = 'model_has_roles';

    protected static function booted()
    {
        static::creating(function ($pivot) {
            $pivot->created_by_id = Auth::id() ?? config('app.system_user_id');
            $pivot->updated_by_id = Auth::id() ?? config('app.system_user_id');
        });

        static::updating(function ($pivot) {
            $pivot->updated_by_id = Auth::id() ?? config('app.system_user_id');
        });
    }
}
