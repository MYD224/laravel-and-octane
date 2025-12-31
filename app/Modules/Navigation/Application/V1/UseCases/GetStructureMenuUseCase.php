<?php

namespace App\Application\Navigation;

use App\Modules\Navigation\Domain\Repositories\MenuItemRepositoryInterface as RepositoriesMenuItemRepositoryInterface;
use Domain\Navigation\Repositories\MenuItemRepositoryInterface;

class GetStructureMenuUseCase
{
    public function __construct(
        private RepositoriesMenuItemRepositoryInterface $repository
    ) {}

    public function execute(string $structureId, $user): array
    {
        // 1. Fetch from Repository (Infrastructure)
        $all = $this->repository->getAllWithOverrides($structureId);

        // 2. Filter and Build Tree (Application Logic)
        return $this->buildTree($all, null, $user, $structureId);
    }

    private function buildTree($items, $parentId, $user, $structureId): array
    {
        $branch = [];
        $locale = app()->getLocale();

        foreach ($items as $item) {
            if ($item->parent_id == $parentId) {
                // Security Check
                if (!$item->isActionAllowed($user, 'view')) continue;

                $branch[] = [
                    'id' => $item->id,
                    'name' => $item->name,
                    'label' => $item->getLabelForStructure($structureId, $locale),
                    'type' => $item->type,
                    'path' => $item->route_path,
                    'can' => [
                        'add' => $item->isActionAllowed($user, 'add'),
                        'edit' => $item->isActionAllowed($user, 'edit'),
                        'delete' => $item->isActionAllowed($user, 'delete'),
                    ],
                    'children' => $this->buildTree($items, $item->id, $user, $structureId)
                ];
            }
        }
        return $branch;
    }
}
