<?php

namespace App\Modules\Navigation\Domain\Repositories;

use Illuminate\Support\Collection;

interface MenuItemRepositoryInterface
{
    /** @return Collection<MenuItem> */
    public function getAllWithOverrides(string $structureId): Collection;
}
