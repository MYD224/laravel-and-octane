<?php

namespace App\Modules\Navigation\Domain\Repositories;

use App\Modules\Navigation\Domain\Entities\MenuItemEntity;
use App\Modules\Navigation\Infrastructure\Persistence\Eloquent\Models\MenuItem;
use Illuminate\Support\Collection;

interface MenuItemRepositoryInterface
{
    /** @return Collection<MenuItem> */
    public function getAllWithOverrides(string | null $structureId): Collection;
    public function addMenu(MenuItemEntity $menu): MenuItem;
    public function addMenuAccessMode(string $menuId, array $accessModes);
    public function findById(string $id): ?MenuItem;
}
