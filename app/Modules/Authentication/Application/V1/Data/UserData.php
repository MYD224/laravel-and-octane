<?php

namespace App\Modules\Authentication\Application\V1\Data;

use App\Modules\Authentication\Domain\Entities\UserEntity;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;

class UserData extends Data
{



    public function __construct(
        public string $id,
        // public string $fullname,
        public string $firstnames,
        public string $lastname,
        public string $gender,
        public string $phone,
        public string $email,
        public string $status,
        public bool $isSendOtp,
        public CarbonImmutable | null $phoneVerifiedAt,
        public CarbonImmutable | null $emailVerifiedAt,

    ) {}


    public static function fromEntity(UserEntity $userEntity): self
    {
        return new self(
            id: $userEntity->getId(),
            // fullname: $userEntity->getFullname(),
            firstnames: $userEntity->getFirstnames(),
            lastname: $userEntity->getLastname(),
            gender: $userEntity->getGender(),
            phone: $userEntity->getPhoneNumber(),
            email: $userEntity->getEmail(),
            status: $userEntity->getStatus(),
            isSendOtp: $userEntity->getIsSendOtp(),
            phoneVerifiedAt: $userEntity->getPhoneVerifiedAt(),
            emailVerifiedAt: $userEntity->getEmailVerifiedAt()
        );
    }
}
