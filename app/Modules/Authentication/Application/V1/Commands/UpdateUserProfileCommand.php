<?php

namespace App\Modules\Authentication\Application\V1\Commands;

use Carbon\CarbonImmutable;

use Spatie\LaravelData\Attributes\Validation\Date;

class UpdateUserProfileCommand
{

    public function __construct(
        public string $id,
        public ?string $fullname = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $phoneVerifiedAt = null,
        public ?string $emailVerifiedAt = null,
        public ?string $password = null,
        public ?string $status = null,
        public ?CarbonImmutable $otpExpiresAt = null
    ) {}
}
