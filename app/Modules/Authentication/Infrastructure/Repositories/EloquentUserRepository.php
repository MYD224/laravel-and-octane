<?php

namespace App\Modules\Authentication\Infrastructure\Repositories;

use App\Modules\Authentication\Domain\Entities\UserEntity;
use App\Modules\Authentication\Domain\Enums\UserStatus;
use App\Modules\Authentication\Domain\Repositories\UserRepositoryInterface;
use App\Modules\Authentication\Domain\ValueObjects\Email;
use App\Modules\Authentication\Domain\ValueObjects\Id;
use App\Modules\Authentication\Domain\ValueObjects\PhoneNumber;
use App\Modules\Authentication\Infrastructure\Persistence\Eloquent\Models\User as ModelsUser;
use phpDocumentor\Reflection\Types\This;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function save(UserEntity $user): void
    {


        ModelsUser::updateOrCreate(
            ['id' => $user->getId()],
            [
                'email' => $user->getEmail(),
                'phone' => $user->getPhoneNumber(),
                'fullname' => $user->getFullname(),
                'status' => $user->getStatus(),
                'otp_code' => $user->getOtpCode(),
                'otp_expires_at' => $user->getOtpExpiresAt(),
                'password' => $user->getHashedPassword(),
            ]
        );
    }

    public function generatPassportToken(string $id): string {
        $user = ModelsUser::find($id);
        return $user->createToken('authToken')->accessToken;

    }

    public function deleteTokens(string $phone) {
        $model = ModelsUser::where('phone', $phone)->first();

        $model->tokens()->delete();

    }

    public function findById(string $id): ?UserEntity
    {
        $model = ModelsUser::find($id);
        if (!$model) return null;

        return $this->userInstance($model);
    }

    public function findByPhone(string $phone): ?UserEntity
    {
        $model = ModelsUser::where('phone', $phone)->first();
        if (!$model) return null;

        return $this->userInstance($model);
    }

    private function userInstance(ModelsUser $model): UserEntity {

        return UserEntity::register(
            id: new Id($model->id),
            fullname: $model->fullname,
            phone: new PhoneNumber($model->phone),
            phoneVerifiedAt: $model->phone_verified_at,
            email: new Email($model->email),
            status: $model->status,
            hashedPassword: $model->password,
        );
    }
}
