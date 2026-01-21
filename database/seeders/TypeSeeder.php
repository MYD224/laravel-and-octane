<?php

namespace Database\Seeders;

use App\Models\Type;
use App\Modules\Authentication\Domain\ValueObjects\Id;
use App\Modules\Authentication\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Database\Seeder;

class TypeSeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Structure'];
        $types = [
            [
                'code' => 'owner',
                'label' => 'Proprietaire'
            ],
            [
                'code' => 'mairie',
                'label' => 'Mairie'
            ],
            [
                'code' => 'syndicat',
                'label' => 'Syndicat'
            ]
        ];
        $user = User::orderBy('created_at')->first();
        foreach ($categories as $category) {

            foreach ($types as $element) {
                $type = Type::where(['category' => $category, 'code' => $element['code']])->first();
                if (!$type) {
                    Type::create([
                        'id' => Id::generate()->value(),
                        'category' => $category,
                        'code' => $element['code'],
                        'label' => $element['label'],
                        'created_by_id' => $user->id,
                        'updated_by_id' => $user->id
                    ]);
                }
            }
        }
    }
}
