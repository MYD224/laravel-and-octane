<?php

namespace App\Modules\Authentication\Domain\ValueObjects;

final class OtpCode
{
    public function __construct(
        private readonly string $code
    ) {
        if (!preg_match('/^[0-9]{4,8}$/', $code)) {
            throw new \InvalidArgumentException("Invalid OTP format: $code");
        }
    }

    public function value(): string
    {
        return $this->code;
    }

    public function __toString(): string
    {
        return $this->code;
    }
}
