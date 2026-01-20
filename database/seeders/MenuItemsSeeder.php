<?php

namespace Database\Seeders;

use App\Modules\Authentication\Domain\ValueObjects\Id;
use App\Modules\Navigation\Domain\Entities\MenuItem;
use Illuminate\Database\Seeder;

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
            ]);


            // Generate Spatie Permissions automatically
            foreach (['view', 'add', 'edit', 'delete', 'export', 'confidential'] as $action) {
                \Spatie\Permission\Models\Permission::firstOrCreate([
                    'name' => "{$item['code']}.{$action}",
                    'guard_name' => 'api'
                ]);
            }
        }
    }
}
