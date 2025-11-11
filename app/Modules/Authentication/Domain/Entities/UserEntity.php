<?php
namespace App\Modules\Authentication\Domain\Entities;
class UserEntity
{
    public function __construct(
        public string $id,
        public string $phone,
        public string $passwordHash,
        public bool $isActive
    ) {}

    public function canLogin(): bool
    {
        return $this->isActive;
    }
}