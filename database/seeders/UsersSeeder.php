<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Structure;
use App\Models\UserStatus;
use App\Models\UserStructure;
use App\Modules\Authentication\Application\Services\HashingService;
use App\Modules\Authentication\Domain\ValueObjects\Id;
use Illuminate\Database\Seeder;
use  App\Modules\Authentication\Infrastructure\Persistence\Eloquent\Models\User;
use Spatie\Permission\Models\Permission;

class UsersSeeder extends Seeder
{
    public function run(HashingService $hashingService): void
    {
        $hashedPassword = $hashingService->hash('pass@1234');
        $phone = '620687185';
        $email = 'dummy@user.com';
        $user = User::where('email', $email)->orWhere('phone', $phone)->first();
        if (!$user) {
            $status = UserStatus::where('code', 'active')->first();
            $user = User::create([
                'id' => Id::generate()->value(),
                'fullname' => 'Admin User',
                'phone' => $phone,
                'email' => $email,
                'phone_verified_at' => now(),
                'email_verified_at' => null,
                'status' => $status?->id,
                'password' => $hashedPassword
            ]);
        }

        $name = 'Global Itech';
        $structure = Structure::where('name', $name)->where('is_owner', true)->first();
        if (!$structure) {
            $structure = Structure::create([
                'id' => Id::generate()->value(),
                'name' => $name,
                'is_owner' => true,
                'created_by_id' => $user->id,
                'last_updated_by_id' => $user->id,
            ]);
        }

        $userStructure = UserStructure::where('user_id', $user->id)->where('structure_id', $structure->id)->first();
        if (!$userStructure) {
            UserStructure::create([
                'id' => Id::generate()->value(),
                'user_id' => $user->id,
                'structure_id' => $structure->id,
                'created_by_id' => $user->id,
                'last_updated_by_id' => $user->id,
            ]);
        }

        $role = Role::firstOrCreate([
            'name' => 'Super Admin',
            'structure_id' => $structure->id,
            'guard_name' => 'api'
        ]);
        $permissions = Permission::all();
        $role->syncPermissions($permissions);
        $user->assignRole($role);
    }
}
