<?php
namespace App\Modules\Authentication\Application\V1\Handlers;



use App\Modules\Authentication\Application\V1\Commands\GenerateOtpCommand;
use App\Core\Contracts\Security\OtpServiceInterface;
use App\Modules\Authentication\Domain\Repositories\UserRepositoryInterface;
use App\Modules\Authentication\Domain\Exceptions\UserNotFoundException;
use App\Modules\Authentication\Domain\Events\UserOtpGenerated;
use App\Core\Contracts\EventDispatcherInterface;
use App\Modules\User\Domain\Exceptions\UserNotFoundException as ExceptionsUserNotFoundException;

final class GenerateOtpHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly OtpServiceInterface $otpService,
        // private readonly EventDispatcherInterface $events // optional
    ) {}

    public function handle(GenerateOtpCommand $command): array
    {
        // 1. Load the user (Domain rule: OTP is tied to user)
        $user = $this->userRepository->findById($command->userId);

        if (!$user) {
            throw new ExceptionsUserNotFoundException("User ID {$command->userId} not found.");
        }

        // 2. Generate the OTP using the core OtpService
        $otp = $this->otpService->generate("user:{$user->getId()}", $command->ttl);

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
