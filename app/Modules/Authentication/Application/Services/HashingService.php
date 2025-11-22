<?php
namespace App\Modules\Authentication\Application\Services;

class HashingService
{
    public function hash(string $password): string
    {
        return password_hash($password, PASSWORD_ARGON2ID);
    }
}
