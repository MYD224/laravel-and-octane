<?php

namespace App\Modules\Authentication\Application\V1\Commands;

final class GenerateOtpCommand
{
    public function __construct(
        public readonly string $userId,
        public readonly int $ttl = 300 // 5 minutes default
    ) {}
}
