<?php

namespace Database\Seeders;

use App\Models\UserStatus;
use App\Modules\Authentication\Domain\ValueObjects\Id;
use Illuminate\Database\Seeder;

class UserStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            [
                'code' => 'active',
                'label' => 'Actif'
            ],
            [
                'code' => 'inactive',
                'label' => 'Inactif'
            ],
            [
                'code' => 'suspended',
                'label' => 'Suspendu'
            ],
        ];

        foreach ($statuses as $element) {
            $status = UserStatus::where('code', $element['code'])->first();
            if (!$status) {
                UserStatus::create([
                    'id' => Id::generate()->value(),
                    'code' => $element['code'],
                    'label' => $element['label']
                ]);
            }
        }
    }
}
