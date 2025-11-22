<?php
namespace App\Modules\Authentication\Application\V1\Data;

use App\Modules\Authentication\Domain\Entities\UserEntity;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;

class UserData extends Data
{

  

    public function __construct(
        public string $id,
        public string $fullname,
        public string $phone,
        public string $email,
        public string $status,
        public CarbonImmutable | null $phoneVerifiedAt,

    ) {}


    public static function fromEntity(UserEntity $userEntity): self
    {
        return new self(
            id: $userEntity->getId(),
            fullname: $userEntity->getFullname(),
            phone: $userEntity->getPhoneNumber(),
            email: $userEntity->getEmail(),
            status: $userEntity->getStatus()->value(),
            phoneVerifiedAt: $userEntity->getPhoneVerifiedAt()
        );
    }


}
