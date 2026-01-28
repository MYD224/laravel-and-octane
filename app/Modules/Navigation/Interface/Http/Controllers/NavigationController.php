<?php

namespace App\Modules\Navigation\Interface\Http\Controllers;

use App\Core\Contracts\Cache\CacheServiceInterface;
use App\Core\Interface\Controllers\BaseController;
use App\Modules\Authentication\Domain\ValueObjects\Id;
use App\Modules\Navigation\Application\V1\Commands\AddMenuAccessModeCommand;
use App\Modules\Navigation\Application\V1\UseCases\AddMenuAccessModeUseCase;
use App\Modules\Navigation\Application\V1\UseCases\AddMenuItemUseCase;
use App\Modules\Navigation\Application\V1\UseCases\GetNavigationTreeUseCase;
use App\Modules\Navigation\Domain\Entities\MenuItemEntity;
use App\Modules\Navigation\Domain\Enums\MenuType;
use App\Modules\Navigation\Domain\Exceptions\MenuItemNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;


class NavigationController extends BaseController
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

    public function addMenu(Request $request, AddMenuItemUseCase $addMenuItemUseCase)
    {
        $validator = Validator::make($request->all(), [
            'code'          => 'required|string|max:255',
            'default_label' => 'required|array', // Should be array like {"en": "Label", "fr": "Étiquette"}
            'type'          => 'required|string|in:menu,tab',
            'order'         => 'required|integer|max:100',
            'path'          => 'required|string|max:255',
            'icon'          => 'required|string|max:255',
            'parent_id'     => 'nullable|string',
            'module_id'     => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = request()->user();

        // Convert string IDs to Id value objects
        $parentId = $request->parent_id ? new Id($request->parent_id) : null;
        $moduleId = $request->module_id ? new Id($request->module_id) : null;
        $userId = new Id($user->id);

        // Create MenuType enum from string
        $typeVO = MenuType::from($request->type);

        $menu = MenuItemEntity::create(
            id: Id::generate(),
            moduleId: $moduleId,
            parentId: $parentId,
            code: $request->code,
            type: $typeVO,
            routePath: $request->path,
            icon: $request->icon,
            defaultLabel: $request->default_label, // Should be array
            sortOrder: $request->order,
            createdById: $userId,
            updatedById: $userId,
            // children: []
        );

        $menu = $addMenuItemUseCase->execute($menu);

        return response()->json([
            'data' => $menu,
            'message' => 'Menu item created successfully'
        ], 201);
    }

    public function addMenuAccessModes(Request $request, AddMenuAccessModeUseCase $useCase)
    {
        $validator = Validator::make($request->all(), [
            'menu_id'          => 'required|string|max:255',
            'access_modes' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        try {
            $resutl = $useCase->execute(
                new AddMenuAccessModeCommand(
                    menuId: $request->menu_id,
                    accessModes: $request->access_modes
                )
            );
        } catch (MenuItemNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while adding access modes',
                'error' => $e->getMessage()
            ], 500);
        }


        return response()->json([
            'message' => 'Access added sucessfully'
        ], 201);
    }
}
