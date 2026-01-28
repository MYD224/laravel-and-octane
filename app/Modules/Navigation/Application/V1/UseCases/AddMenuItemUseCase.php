<?php

namespace App\Modules\Navigation\Application\V1\UseCases;

use App\Core\Contracts\Cache\CacheServiceInterface;
use App\Modules\Navigation\Domain\Repositories\MenuItemRepositoryInterface;
use Exception;

class AddMenuItemUseCase
{
    public function __construct(
        private readonly MenuItemRepositoryInterface $menuItemRepository,
        private readonly CacheServiceInterface $cache
    ) {}


    public function execute($menu)
    {
        try {
            $menuItem = $this->menuItemRepository->addMenu($menu);

            //add the element to the list of menus in cache
            return $menuItem;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
