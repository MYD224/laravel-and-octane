<?php

namespace App\Modules\Authentication\Application\V1\UseCases;

use App\Core\Contracts\Cache\CacheServiceInterface;
use App\Core\Contracts\Security\OtpServiceInterface;
use App\Modules\Authentication\Application\V1\Commands\RegisterUserCommand;
use App\Modules\Authentication\Application\V1\Commands\VerifyOtpCommand;
use App\Modules\Authentication\Application\V1\Handlers\VerifyOtpHandler;
use App\Modules\Authentication\Domain\Exceptions\InvalidOtpException;
use App\Modules\Authentication\Domain\Exceptions\OtpExpiredException;
use App\Modules\Authentication\Domain\Repositories\UserRepositoryInterface;
use App\Modules\User\Domain\Exceptions\UserNotFoundException;

class VerifyOtpUseCase
{
    public function __construct(
        // private VerifyOtpHandler $handler
        private readonly UserRepositoryInterface $userRepository,
        private readonly CacheServiceInterface $cache,
        private readonly OtpServiceInterface $otpService,
    ) {}

    public function execute(VerifyOtpCommand $command): array
    {

        // $userEntity = $this->userRepository->findById($command->userId);
        $userEntity = $this->cache->remember(
            key: "user:" . $command->userId . ":session",
            ttl: 3600,
            callback: fn() => $this->userRepository->findById($command->userId)
        );
        if (!$userEntity) {
            throw new UserNotFoundException("User ID {$command->userId} not found.");
        }

        // 2. Validate the OTP
        $isValid = false;
        try {
            $isValid = $this->otpService->validate(
                key: "user:{$userEntity->getId()}:otp",
                code: $command->otp
            );
        } catch (OtpExpiredException $e) {
            throw $e; // safe to bubble up
        }

        if (!$isValid) {
            throw new InvalidOtpException();
        }

        // 4. Fire event
        // $this->events->dispatch(new UserOtpVerified(
        //     userId: $user->id
        // ));

        // 5. Return result
        return [
            'status'  => 'verified',
            'message' => 'OTP successfully validated.',
            'user_id' => $userEntity->getId(),
        ];

        // return $this->handler->handle($command);
    }
}
