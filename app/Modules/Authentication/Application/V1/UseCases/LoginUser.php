<?php
namespace App\Modules\Authentication\Application\V1\UseCases;

use App\Modules\Authentication\Infrastructure\Services\AuthService;

class LoginUser
{
    public function __construct(private AuthService $authService) {}

    public function execute(string $email, string $password): string
    {
        return $this->authService->attemptLogin($email, $password);
    }
}
