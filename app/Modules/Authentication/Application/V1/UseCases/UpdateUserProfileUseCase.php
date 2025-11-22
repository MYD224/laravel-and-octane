<?php

namespace App\Modules\Authentication\Application\V1\UseCases;

use App\Modules\Authentication\Application\V1\Commands\UpdateUserProfileCommand;
use App\Modules\Authentication\Application\V1\Handlers\UpdateUserProfileHandler;

class UpdateUserProfileUseCase
{
    public function __construct(
        private UpdateUserProfileHandler $handler
    ) {}

    public function execute(UpdateUserProfileCommand $command)
    {
        return $this->handler->handle($command);
    }


    // public function execute(string $userId)
    // {
    //     return $this->cache->remember("users.$userId", 300, function () use ($userId) {
    //         $user = $this->repo->findById($userId);

    //         if (!$user) {
    //             throw new UserNotFoundException();
    //         }

    //         return $user;
    //     });
    // }


    // Redis version

    // public function execute(string $id)
    // {
    //     return $this->cache->rememberTag(
    //         tag: "users",
    //         key: "user:$id",
    //         ttl: 600,
    //         callback: function () use ($id) {
    //             $user = $this->repo->findById($id);
    //             if (!$user) throw new UserNotFoundException();
    //             return $user;
    //         }
    //     );
    // }
}
