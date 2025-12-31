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
                'name' => 'users',
                'type' => 'menu',
                'route_path' => '/users',
                'label' => ['en' => 'Users', 'fr' => 'Utilisateurs']
            ],
            // Add more here...
        ];

        foreach ($menuItems as $item) {
            MenuItem::create([
                'id' => Id::generate()->value(),
                'name' => $item['name'],
                'type' => $item['type'],
                'route_path' => $item['route_path'],
                'default_label' => $item['label'],
            ]);

            // Generate Spatie Permissions automatically
            foreach (['view', 'add', 'edit', 'delete', 'export'] as $action) {
                \Spatie\Permission\Models\Permission::firstOrCreate([
                    'name' => "{$item['name']}.{$action}",
                    'guard_name' => 'api'
                ]);
            }
        }
    }
}
