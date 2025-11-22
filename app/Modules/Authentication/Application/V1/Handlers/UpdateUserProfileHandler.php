<?php

namespace App\Modules\Authentication\Application\V1\Handlers;

use App\Modules\Authentication\Application\V1\Commands\UpdateUserProfileCommand;
use App\Modules\Authentication\Infrastructure\Persistence\Eloquent\Models\User;
use App\Modules\Authentication\Application\V1\Data\UserData;
use App\Modules\Authentication\Application\Services\UserService;

class UpdateUserProfileHandler
{
    
    public function __construct(
        private UserService $userService,
    ){}


    public function handle(UpdateUserProfileCommand $command): UserData
    {

        
        $userData = $this->userService->update(
            id: $command->id,
            otpCode: $command->otpCode,
            otpExpiresAt: $command->otpExpiresAt,
            phoneVerifiedAt: $command->phoneVerifiedAt
        );

        return $userData;
       
    }
   
}