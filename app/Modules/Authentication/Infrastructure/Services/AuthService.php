<?php

namespace App\Modules\Authentication\Infrastructure\Services;

class AuthService
{
    public function attemptLogin(string $phone, string $password): string
    {
        if (!auth()->attempt(['phone' => $phone, 'password' => $password])) {
            throw new \Exception('Invalid credentials');
        }

        // Return Passport token
        // return auth()->user()->createToken('API Token')->accessToken;
        return true;
    }
}
