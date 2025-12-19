<?php
namespace App\Modules\Authentication\Application\V1\UseCases;

use App\Core\Contracts\Cache\CacheServiceInterface;
use App\Modules\Authentication\Application\Services\HashingService;
use App\Modules\Authentication\Application\V1\Commands\RegisterUserCommand;
use App\Modules\Authentication\Application\V1\Data\UserData;
use App\Modules\Authentication\Application\V1\Handlers\RegisterUserHandler;
use App\Modules\Authentication\Domain\Entities\UserEntity;
use App\Modules\Authentication\Domain\Enums\UserStatus;
use App\Modules\Authentication\Domain\Repositories\UserRepositoryInterface;
use App\Modules\Authentication\Domain\ValueObjects\Email;
use App\Modules\Authentication\Domain\ValueObjects\Id;
use App\Modules\Authentication\Domain\ValueObjects\PhoneNumber;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Log;


class RegisterUserUseCase
{
     public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly CacheServiceInterface $cacheService,
        private readonly HashingService $hashingService,
    ) {}

    public function execute(RegisterUserCommand $command): UserEntity
    {

        $emailVO = new Email($command->email);
        $passwordHashed = $this->hashingService->hash($command->password);
        $phoneVO = new PhoneNumber($command->phone);

        $userEntity = UserEntity::register(
            id: Id::generate(),
            fullname: $command->fullname,
            phone: $phoneVO,
            phoneVerifiedAt: null,
            email: $emailVO,
            status: UserStatus::ACTIVE,
            hashedPassword: $passwordHashed
        );     



        return $this->cacheService->remember(
            key: "user:".$userEntity->getId().":session",
            ttl: 3600,
            callback: fn() => $this->userRepository->save($userEntity)
        );



    }
}
