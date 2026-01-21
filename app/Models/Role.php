<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Role extends SpatieRole
{
    protected $fillable = ['name', 'guard_name', 'structure_id', 'group_id'];

    protected static function booted()
    {

        // used to automatically add created_by_id and updated_by_id
        $userId = auth()->id() ?? config('app.system_user_id');
        static::creating(function ($model) use ($userId) {
            $model->created_by_id = $userId;
        });

        static::updating(function ($model) use ($userId) {
            $model->updated_by_id = $userId;
        });
    }
    public function scopeForStructure(Builder $query, ?string $structureId)
    {
        if ($structureId) {
            $query->where(function ($q) use ($structureId) {
                $q->where('structure_id', $structureId)
                    ->orWhereNull('structure_id');
            });
        }
    }

    public function syncPermissions(...$permissions)
    {
        $result = parent::syncPermissions(...$permissions);

        $userId = Auth::id() ?? config('app.system_user_id');

        DB::table('role_has_permissions')
            ->where('role_id', $this->getKey())
            ->update([
                'created_by_id' => $userId,
                'updated_by_id' => $userId,
            ]);

        return $result;
    }

    public function givePermissionTo(...$permissions)
    {
        $result = parent::givePermissionTo(...$permissions);

        $userId = Auth::id() ?? config('app.system_user_id');

        DB::table('role_has_permissions')
            ->where('role_id', $this->getKey())
            ->update([
                'created_by_id' => $userId,
                'updated_by_id' => $userId,
            ]);

        return $result;
    }
}
