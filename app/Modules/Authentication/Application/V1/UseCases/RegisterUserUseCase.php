<?php
namespace App\Modules\Authentication\Application\V1\UseCases;

use App\Modules\Authentication\Application\V1\Commands\RegisterUserCommand;
use App\Modules\Authentication\Application\V1\Data\UserData;
use App\Modules\Authentication\Application\V1\Handlers\RegisterUserHandler;

class RegisterUserUseCase
{
     public function __construct(
        private RegisterUserHandler $handler
    ) {}

    public function execute(RegisterUserCommand $command): UserData
    {
        return $this->handler->handle($command);
    }
}
