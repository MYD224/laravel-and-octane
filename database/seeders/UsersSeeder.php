<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Status;
use App\Models\Structure;
use App\Models\Type;
use App\Models\UserStatus;
use App\Models\UserStructure;
use App\Modules\Authentication\Application\Services\HashingService;
use App\Modules\Authentication\Domain\ValueObjects\Id;
use Illuminate\Database\Seeder;
use  App\Modules\Authentication\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class UsersSeeder extends Seeder
{
    public function run(HashingService $hashingService): void
    {
        $users = [
            [
                'id' => '00000000-0000-0000-0000-000000000000',
                'email' => 'system@collect.local',
                'firstnames' => 'System',
                'lastname' => 'User',
                'phone' => '000000000',
                'password' => Str::random(32)
            ],
            [
                'id' => Id::generate()->value(),
                'email' => 'admin@collect.local',
                'firstnames' => 'Admin Technique',
                'lastname' => 'Global',
                'phone' => '620687185',
                'password' => 'pass@1234'
            ]
        ];

        foreach ($users as $user) {

            $hashedPassword = $hashingService->hash($user['password']);
            $phone = $user['phone'];
            $email = $user['email'];
            $user_ = User::where('email', $email)->orWhere('phone', $phone)->first();
            if (!$user_) {
                $status = Status::where('category', 'Utilisateur')->where('code', 'active')->first();
                $user = User::create([
                    'id' => $user['id'],
                    // 'fullname' => 'Admin User',
                    'firstnames' => $user['firstnames'],
                    'lastname' => $user['lastname'],
                    'phone' => $phone,
                    'email' => $email,
                    'phone_verified_at' => now(),
                    'email_verified_at' => null,
                    'status_id' => $status?->id,
                    'password' => $hashedPassword
                ]);
            }
        }
    }
}
