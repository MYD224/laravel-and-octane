<?php

namespace App\Modules\Navigation\Interface\Http\Controllers;

use App\Core\Contracts\Cache\CacheServiceInterface;
use App\Modules\Navigation\Application\V1\UseCases\GetNavigationTreeUseCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;


class NavigationController
{
    public function __construct(
        private GetNavigationTreeUseCase $useCase,

        private readonly CacheServiceInterface $cache,
    ) {}

    public function __invoke(Request $request)
    {
        $user = $request->user();
        $structureId = $user->structure_id;
        $roleId = $user->roles->first()?->id;

        // Cache unique to Structure and Role
        $cacheKey = "nav_{$structureId}_{$roleId}_{$request->getLocale()}";

        $menu = Cache::remember($cacheKey, now()->addDay(), function () use ($user, $structureId) {
            return $this->useCase->execute($structureId, $user);
        });

        return response()->json($menu);
    }

    public function getUserMenu(Request $request)
    {
        $user = $request->user();
        $menu = $this->useCase->execute(null, $user->id);
        return response()->json($menu);
    }
}
