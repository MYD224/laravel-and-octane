<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\UserStatusSeeder as SeedersUserStatusSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use UserStatusSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
            SeedersUserStatusSeeder::class,
            MenuItemsSeeder::class,
            UsersSeeder::class,
        ]);
    }
}
