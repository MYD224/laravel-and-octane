<?php

namespace App\Core\Contracts\Security;

use App\Modules\Authentication\Domain\ValueObjects\OtpCode;

interface OtpServiceInterface
{
    public function generate(string $key, int $ttl = 300): OtpCode;

    public function validate(string $key, string $code): bool;

    public function delete(string $key): void;
}
