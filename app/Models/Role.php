<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Builder;

class Role extends SpatieRole
{
    protected $fillable = ['name', 'guard_name', 'structure_id', 'group_id'];

    protected static function booted()
    {
        static::addGlobalScope('structure', function (Builder $builder) {
            if (auth()->check()) {
                // Return global roles (structure_id is null) or roles for this specific structure
                $builder->where(function ($query) {
                    $query->where('structure_id', auth()->user()->structure_id)
                        ->orWhereNull('structure_id');
                });
            }
        });
    }
}
