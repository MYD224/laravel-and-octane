<?php

namespace App\Modules\Authentication\Infrastructure\Repositories;

use App\Models\Status;
use App\Modules\Authentication\Domain\Entities\UserEntity;
use App\Modules\Authentication\Domain\Enums\UserStatus;
use App\Modules\Authentication\Domain\Repositories\UserRepositoryInterface;
use App\Modules\Authentication\Domain\ValueObjects\Email;
use App\Modules\Authentication\Domain\ValueObjects\Id;
use App\Modules\Authentication\Domain\ValueObjects\PhoneNumber;
use App\Modules\Authentication\Infrastructure\Persistence\Eloquent\Models\TokenConnexion;
use App\Modules\Authentication\Infrastructure\Persistence\Eloquent\Models\User as ModelsUser;
use App\Modules\Authentication\Infrastructure\Persistence\Eloquent\Models\UserConnexion;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Log;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function save(UserEntity $user): UserEntity
    {
        $status = Status::where('category', 'Utilisateur')->where('code', $user->getStatus())->first();
        // if(isset($user->getStatus())){
        // }
        $user = ModelsUser::updateOrCreate(
            ['id' => $user->getId()],
            [
                'email' => $user->getEmail(),
                'phone' => $user->getPhoneNumber(),
                // 'fullname' => $user->getFullname(),
                'firstnames' => $user->getFirstnames(),
                'lastname' => $user->getLastname(),
                'gender' => $user->getGender(),
                'status_id' => $status->id,
                'is_send_otp' => $user->getIsSendOtp(),
                'phone_verified_at' => $user->getPhoneVerifiedAt(),
                'email_verified_at' => $user->getEmailVerifiedAt(),
                'password' => $user->getHashedPassword(),
                'auth_provider' => $user->getAuthProvider(),
                'provider_id' => $user->getAuthProviderId(),
            ]
        );
        $user = $user->with('status:id,label,code')->first();

        return $this->userInstance($user);
    }

    public function generatPassportToken(string $id): string
    {
        $user = ModelsUser::find($id);
        return $user->createToken('authToken')->accessToken;
    }

    public function deleteTokens(string $phone)
    {
        $model = ModelsUser::where('phone', $phone)->first();
        if ($model)
            $model->tokens()->delete();
    }

    public function findById(string $id): ?UserEntity
    {
        $model = ModelsUser::find($id);
        if (!$model) return null;

        return $this->userInstance($model);
    }

    public function findUserById(string $id, array $relations = []): ?ModelsUser
    {
        $model = ModelsUser::with($relations)->find($id);
        if (!$model) return null;

        return $model;
    }

    public function findByPhone(string $phone): ?UserEntity
    {
        $model = ModelsUser::with('status:id,label,code')->where('phone', $phone)->first();
        if (!$model) return null;
        $model->status;
        return $this->userInstance($model);
    }

    public function findByEmail(string $email): ?UserEntity
    {
        $model =  ModelsUser::where('email', $email)->first();
        if (!$model) return null;

        return $this->userInstance($model);
    }

    public function findByAuthProviderAndProviderId(string $authProvider, string $providerId): ?UserEntity
    {
        $model = ModelsUser::where('auth_provider', $authProvider)
            ->where('provider_id', $providerId)
            ->first();
        if (!$model) return null;

        return $this->userInstance($model);
    }

    public function updateUserAfterSocialRegistration(string $id, string $authProvider, string $providerId, string $email, string $fullname, ?string $password, ?string $emailValidatedAt = null): UserEntity
    {
        $user = ModelsUser::updateOrCreate(
            ['id' => $id],
            [
                'email' => $email,
                'fullname' => $fullname,
                'email_verified_at' => $emailValidatedAt,
                'password' => $password,
                'auth_provider' => $authProvider,
                'provider_id' => $providerId,
            ]
        );
        return $this->userInstance($user);
    }

    public function saveConnexion(string $userId, ?string $otp_code): void
    {
        $tokenId = null;
        if (isset($otp_code)) {
            $tokenConnexion = TokenConnexion::create([
                'user_id' => $userId,
                'code' => $otp_code,
                'created_by_id' => $userId,
                'updated_by_id' => $userId
            ]);

            $tokenId = $tokenConnexion->id;
        }

        UserConnexion::create([
            'user_id' => $userId,
            'token_id' => $tokenId,
            'created_by_id' => $userId,
            'updated_by_id' => $userId
        ]);
    }

    private function userInstance(ModelsUser $model): UserEntity
    {
        $status = $model->status?->code;
        $phone = $model->phone ? new PhoneNumber($model->phone) : null;
        // $status = $model->status ?? UserStatus::ACTIVE->value;
        $phoneVerifiedAt = $model->phone_verified_at ? CarbonImmutable::parse($model->phone_verified_at) : null;
        return UserEntity::register(
            id: new Id($model->id),
            // fullname: $model->fullname,
            firstnames: $model->firstnames,
            lastname: $model->lastname,
            gender: $model->gender,
            phone: $phone,
            isSendOtp: $model->is_send_otp,
            phoneVerifiedAt: $phoneVerifiedAt,
            email: new Email($model->email),
            status: $status,
            hashedPassword: $model->password,
            authProviderId: $model->provider_id,
            authProvider: $model->auth_provider,
        );
    }
}
