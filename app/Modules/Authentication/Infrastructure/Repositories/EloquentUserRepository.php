<?php

namespace App\Modules\Authentication\Infrastructure\Repositories;

use App\Infrastructure\Persistence\Eloquent\Models\User as ModelsUser;
use App\Modules\Authentication\Domain\Entities\User;

class EloquentUserRepository implements UserRepository
{
    public function save(User $user): void
    {
        ModelsUser::create([
            'id' => $user->id,
            'phone' => $user->phone,
            'password' => $user->passwordHash,
            'is_active' => $user->isActive,
        ]);
    }

    public function findByPhone(string $phone): ?User
    {
        $model = ModelsUser::where('phone', $phone)->first();
        if (!$model) return null;
        return new User($model->id, $model->phone, $model->password, $model->is_active);
    }
}
