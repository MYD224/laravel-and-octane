<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Status;
use App\Models\Structure;
use App\Models\Type;
use App\Models\UserStructure;
use App\Modules\Authentication\Domain\ValueObjects\Id;
use App\Modules\Authentication\Infrastructure\Persistence\Eloquent\Models\User;
use App\Modules\Navigation\Domain\Entities\MenuItem;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class MenuItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $menuItems = [
            [
                'code' => 'tech-admin',
                'type' => 'menu',
                'route_path' => '/admin-technique',
                'label' => ['en' => 'Technical Admin', 'fr' => 'Admin Technique']
            ],
            [
                'code' => 'users',
                'type' => 'menu',
                'route_path' => '/users',
                'label' => ['en' => 'Users', 'fr' => 'Utilisateurs'],
                'parent' => 'tech-admin'
            ],
            [
                'code' => 'groups',
                'type' => 'menu',
                'route_path' => '/groups',
                'label' => ['en' => 'Groups', 'fr' => 'Groupes'],
                'parent' => 'tech-admin'
            ],
        ];
        $user = User::where('email', 'admin@collect.local')->first();
        foreach ($menuItems as $item) {
            $existing = MenuItem::where('code', $item['code'])->first();
            if ($existing) {
                continue; // Skip if already exists
            }
            $parent = null;
            if (isset($item['parent']) && $item['parent']) {
                $parent = MenuItem::where('code', $item['parent'])->first();
            }
            MenuItem::create([
                'id' => Id::generate()->value(),
                'code' => $item['code'],
                'type' => $item['type'],
                'route_path' => isset($parent) ? $parent->route_path . $item['route_path'] : $item['route_path'],
                'default_label' => $item['label'],
                'parent_id' => isset($parent) ? $parent->id : null,
                'created_by_id' => $user->id,
                'updated_by_id' => $user->id
            ]);


            // Generate Spatie Permissions automatically
            foreach (['view', 'add', 'edit', 'delete', 'export', 'confidential'] as $action) {
                \Spatie\Permission\Models\Permission::firstOrCreate([
                    'name' => "{$item['code']}.{$action}",
                    'guard_name' => 'api',
                    'created_by_id' => $user->id,
                    'updated_by_id' => $user->id
                ]);
            }
        }

        $name = 'Global Itech';
        $structure = Structure::where('name', $name)->where('is_owner', true)->first();
        if (!$structure) {
            $type = Type::where('category', 'structure')->where('code', 'owner')->first();
            $status = Status::where('category', 'structure')->where('code', 'active')->first();
            $structure = Structure::create([
                'id' => Id::generate()->value(),
                'name' => $name,
                'is_owner' => true,
                'type_id' => $type->id,
                'status_id' => $status->id,
                'created_by_id' => $user->id,
                'updated_by_id' => $user->id,
            ]);
        }

        $userStructure = UserStructure::where('user_id', $user->id)->where('structure_id', $structure->id)->first();
        if (!$userStructure) {
            UserStructure::create([
                'id' => Id::generate()->value(),
                'user_id' => $user->id,
                'structure_id' => $structure->id,
                'created_by_id' => $user->id,
                'updated_by_id' => $user->id,
            ]);
        }

        $role = Role::firstOrCreate([
            'name' => 'Super Admin',
            'structure_id' => $structure->id,
            'guard_name' => 'api',
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id
        ]);
        $permissions = Permission::all();
        $role->syncPermissions($permissions);
        $user->assignRole($role);
    }
}
