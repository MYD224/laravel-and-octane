<?php
namespace App\Modules\Authentication\Application\V1\UseCases;

use App\Core\Contracts\Cache\CacheServiceInterface;
use App\Core\Contracts\Security\OtpServiceInterface;
use App\Modules\Authentication\Application\V1\Commands\GenerateOtpCommand;
use App\Modules\Authentication\Application\V1\Commands\VerifyOtpCommand;
use App\Modules\Authentication\Application\V1\Handlers\GenerateOtpHandler;
use App\Modules\Authentication\Domain\Repositories\UserRepositoryInterface;
use App\Modules\User\Domain\Exceptions\UserNotFoundException;

class GenerateOtpUseCase
{
     public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly CacheServiceInterface $cache,
        private readonly OtpServiceInterface $otpService,
    ) {}

    public function execute(GenerateOtpCommand $command): array
    {

        // 1. Load the user (Domain rule: OTP is tied to user)
        $user = $this->cache->remember(
            "user:{$command->userId}:session", 
            3600, 
            fn() => $this->userRepository->findById($command->userId)
        );
        
        if (!$user) {
            throw new UserNotFoundException("User ID {$command->userId} not found.");
        }

        // 2. Generate the OTP using the core OtpService
        $otp = $this->otpService->generate("user:{$user->getId()}:otp", $command->ttl);

        // 3. Dispatch domain event (for sending SMS/Email)
        // $this->events->dispatch(new UserOtpGenerated(
        //     userId: $user->id,
        //     otp: (string) $otp
        // ));

        // 4. Return result to controller or API
        return [
            'status' => 'success',
            'ttl'    => $command->ttl,
            'otp'    => $otp->value(), // return only if debugging/testing
            // In production, remove this and ONLY send via SMS/email
        ];
    }
}
