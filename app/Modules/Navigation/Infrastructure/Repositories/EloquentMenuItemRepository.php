<?php

namespace App\Modules\Navigation\Infrastructure\Repositories;

use App\Modules\Navigation\Domain\Entities\MenuItem;
use App\Modules\Navigation\Domain\Repositories\MenuItemRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentMenuItemRepository implements MenuItemRepositoryInterface
{
    public function getAllWithOverrides(string $structureId): Collection
    {
        return MenuItem::with(['overrides' => function ($q) use ($structureId) {
            $q->where('structure_id', $structureId);
        }])->orderBy('sort_order')->get();
    }
}
