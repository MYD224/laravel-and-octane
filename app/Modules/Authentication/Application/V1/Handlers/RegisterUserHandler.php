<?php

namespace App\Modules\Authentication\Application\V1\Handlers;

use App\Modules\Authentication\Application\V1\Commands\RegisterUserCommand;
use App\Modules\Authentication\Application\V1\Data\UserData;
use App\Modules\Authentication\Application\Services\UserService;

class RegisterUserHandler
{
    
    public function __construct(
        private UserService $userService,
    ){}


    public function handle(RegisterUserCommand $command): UserData
    {

        
        $userData = $this->userService->createUser(
            fullname: $command->fullname,
            email: $command->email,
            phone: $command->phone,
            password: $command->password,
        );

        return $userData;
       
    }
   
}