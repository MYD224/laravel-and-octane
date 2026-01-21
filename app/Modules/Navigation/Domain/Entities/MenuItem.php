<?php

namespace App\Modules\Navigation\Domain\Entities;

use App\Modules\Navigation\Domain\Entities\StructureMenuOverride as EntitiesStructureMenuOverride;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuItem extends Model
{
    use SoftDeletes, HasUuids;
    public $incrementing = false;

    protected $guarded = [];

    protected $casts = ['default_label' => 'array'];

    public function overrides()
    {
        return $this->hasMany(EntitiesStructureMenuOverride::class);
    }

    // Business Logic: Determine if an action is permitted for a user
    public function isActionAllowed($user, string $action): bool
    {
        return $user->can("{$this->code}.{$action}");
    }

    // Business Logic: Resolve translated label
    public function getLabelForStructure(string $structureId, string $locale): string
    {
        $override = $this->overrides->where('structure_id', $structureId)->first();
        return $override?->custom_label[$locale] ?? ($this->default_label[$locale] ?? $this->default_label['fr']);
    }
}
