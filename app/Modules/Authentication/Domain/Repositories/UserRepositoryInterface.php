<?php

namespace App\Modules\Authentication\Domain\Repositories;

use App\Modules\Authentication\Domain\Entities\UserEntity;

interface UserRepositoryInterface
{
    public function save(UserEntity $user): void;
    public function findById(string $id): ?UserEntity;
    public function deleteTokens(string $phone);

}