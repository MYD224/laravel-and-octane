<?php

namespace App\Modules\Authentication\Domain\Repositories;

use App\Modules\Authentication\Application\V1\Data\UserData;
use App\Modules\Authentication\Domain\Entities\UserEntity;
use App\Modules\Authentication\Infrastructure\Persistence\Eloquent\Models\User;
use Carbon\CarbonImmutable;

interface UserRepositoryInterface
{
    public function save(UserEntity $user): UserEntity;
    public function findById(string $id): ?UserEntity;
    public function findUserById(string $id, array $relations = []): ?User;
    public function deleteTokens(string $phone);
    public function generatPassportToken(string $id): string;
    public function findByPhone(string $phone): ?UserEntity;
    public function findByEmail(string $email): ?UserEntity;
    public function findByAuthProviderAndProviderId(string $authProvider, string $providerId): ?UserEntity;
    public function updateUserAfterSocialRegistration(
        string $id,
        string $authProvider,
        string $providerId,
        string $email,
        string $fullname,
        ?string $password,
        ?string $emailValidatedAt = null
    ): UserEntity;
}
