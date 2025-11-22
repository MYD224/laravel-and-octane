<?php

namespace App\Modules\Authentication\Application\V1\Commands;

class RegisterUserCommand {
    
    public function __construct(
        public string $fullname,
        public string $email,
        public string $phone,
        public string $password,       
    ){}
}