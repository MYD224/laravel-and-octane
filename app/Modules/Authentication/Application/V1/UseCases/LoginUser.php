<?php
namespace App\Modules\Authentication\Domain\Entities;

class LoginUser
{
    public function __construct(private AuthService $authService) {}

    public function execute(string $email, string $password): string
    {
        return $this->authService->attemptLogin($email, $password);
    }
}
