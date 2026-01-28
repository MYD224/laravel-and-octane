<?php

namespace App\Modules\Navigation\Infrastructure\Repositories;

use App\Models\Permission;
use App\Modules\Authentication\Domain\ValueObjects\Id;
use App\Modules\Navigation\Domain\Entities\MenuItemEntity;
use App\Modules\Navigation\Domain\Repositories\MenuItemRepositoryInterface;
use App\Modules\Navigation\Infrastructure\Persistence\Eloquent\Models\MenuItem;
use Illuminate\Support\Collection;

class EloquentMenuItemRepository implements MenuItemRepositoryInterface
{
    public function getAllWithOverrides(string | null $structureId): Collection
    {
        return MenuItem::with(['overrides' => function ($q) use ($structureId) {
            $q->where('structure_id', $structureId);
        }])->orderBy('sort_order')->get();
    }
    public function addMenu(MenuItemEntity $menu): MenuItem
    {
        $menuItem = new MenuItem();
        $menuItem->id = Id::generate()->value();
        $menuItem->code = $menu->getCode();
        $menuItem->type = $menu->getType();
        $menuItem->default_label = $menu->getDefaultLabel();
        $menuItem->route_path = $menu->getRoutePath();
        $menuItem->icon = $menu->getIcon();
        $menuItem->module_id = $menu->getModuleId() ?? null;
        $menuItem->parent_id = $menu->getParentId() ?? null;
        $menuItem->sort_order = $menu->getSortOrder();
        // $menuItem->children = $menu->getChildren();
        $menuItem->created_by_id = $menu->getCreatedById();
        $menuItem->updated_by_id = $menu->getUpdatedById();
        $menuItem->save();


        return $menuItem;
    }

    public function findById(string $id): ?MenuItem
    {
        $menu = MenuItem::find($id);
        return $menu ?? null;
    }

    public function addMenuAccessMode(string $menuId, array $accessModes)
    {
        $menu = MenuItem::find($menuId);
        if ($menu) {
            foreach ($accessModes as $action) {
                $permissionName = $menu->code . '.' . $action;
                Permission::firstOrCreate([
                    'name' => $permissionName,
                    'guard_name' => 'api'
                ]);
            }
        }
    }
}
