<?php

namespace App\Modules\Navigation\Application\V1\UseCases;

use App\Core\Contracts\Cache\CacheServiceInterface;
use App\Modules\Authentication\Domain\Repositories\UserRepositoryInterface;
use App\Modules\Authentication\Infrastructure\Persistence\Eloquent\Models\User;
use App\Modules\Navigation\Domain\Repositories\MenuItemRepositoryInterface;
use Illuminate\Support\Facades\App;

class GetNavigationTreeUseCase
{
    public function __construct(
        private MenuItemRepositoryInterface $repository,
        private readonly UserRepositoryInterface $userRepository,
        private readonly CacheServiceInterface $cache,
    ) {}

    public function execute($structureId = null, $userId): array
    {
        $navigation = $this->cache->remember(
            key: "nav_tree_{$userId}",
            ttl: 86400,
            callback: function () use ($structureId, $userId) {
                $locale = App::getLocale();
                $items = $this->repository->getAllWithOverrides($structureId);
                $user = $this->userRepository->findUserById($userId, ['structures:id,name']);
                $structureId = $user?->structures()->first()?->id; // a adapter plus tard en cas de structures multilples

                return $this->buildTree($items, null, $structureId, $user, $locale);
            }
        );
        return $navigation;
    }

    private function buildTree($items, $parentId, $structureId, $user, $locale): array
    {
        return $items->where('parent_id', $parentId)
            ->filter(fn($item) => $user->hasPermissionTo("{$item->code}.view", "api")) // Security Gate
            ->filter(function ($item) {
                // Check if the tenant explicitly hid this item
                $override = $item->overrides->first();
                return $override ? $override->is_visible : true;
            })
            ->map(function ($item) use ($items, $structureId, $user, $locale) {
                return [
                    'id' => $item->id,
                    'code' => $item->code,
                    'label' => $item->getLabelForStructure($structureId, $locale),
                    'path' => $item->route_path,
                    'type' => $item->type,
                    'icon' => $item->icon,
                    'can' => [
                        'add'    => $user->hasPermissionTo("{$item->code}.add", "api"),
                        'edit'   => $user->hasPermissionTo("{$item->code}.edit", "api"),
                        'delete' => $user->hasPermissionTo("{$item->code}.delete", "api"),
                        'see-confidential' => $user->hasPermissionTo("{$item->code}.confidential", "api"),
                        'export' => $user->hasPermissionTo("{$item->code}.export", "api"),
                    ],
                    // Recursion for sub-menus and tabs
                    'children' => $this->buildTree($items, $item->id, $structureId, $user, $locale)
                ];
            })
            ->values()
            ->toArray();
    }
}
