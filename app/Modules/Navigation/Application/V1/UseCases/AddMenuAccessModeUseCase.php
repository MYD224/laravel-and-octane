<?php

namespace App\Modules\Navigation\Application\V1\UseCases;

use App\Core\Contracts\Cache\CacheServiceInterface;
use App\Modules\Navigation\Application\V1\Commands\AddMenuAccessModeCommand;
use App\Modules\Navigation\Domain\Exceptions\MenuItemNotFoundException;
use App\Modules\Navigation\Domain\Repositories\MenuItemRepositoryInterface;
use Exception;

class AddMenuAccessModeUseCase
{
    public function __construct(
        private readonly MenuItemRepositoryInterface $menuItemRepository,
        private readonly CacheServiceInterface $cache
    ) {}


    public function execute(AddMenuAccessModeCommand $command)
    {
        $menu = $this->menuItemRepository->findById($command->menuId);

        if (!$menu) {
            throw new MenuItemNotFoundException("Menu item not found", 404);
        }
        $this->menuItemRepository->addMenuAccessMode($command->menuId, $command->accessModes);
    }
}
