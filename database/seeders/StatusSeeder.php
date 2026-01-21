<?php

namespace Database\Seeders;

use App\Models\Status;
use App\Modules\Authentication\Domain\ValueObjects\Id;
use App\Modules\Authentication\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Utilisateur', 'Licence', 'Structure'];
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
        foreach ($categories as $category) {

            foreach ($statuses as $element) {
                $status = Status::where(['category' => $category, 'code' => $element['code']])->first();
                if (!$status) {
                    Status::create([
                        'id' => Id::generate()->value(),
                        'category' => $category,
                        'code' => $element['code'],
                        'label' => $element['label']
                    ]);
                }
            }
        }
    }
}
