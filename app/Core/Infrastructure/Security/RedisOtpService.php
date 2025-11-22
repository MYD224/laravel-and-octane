<?php

namespace App\Core\Infrastructure\Security;

use App\Core\Contracts\Security\OtpServiceInterface;
use App\Modules\Authentication\Domain\ValueObjects\OtpCode;
use Illuminate\Support\Facades\Redis;
use App\Modules\Authentication\Domain\Exceptions\OtpExpiredException;

class RedisOtpService implements OtpServiceInterface
{
    private string $prefix;

    public function __construct(string $prefix = "otp:")
    {
        $this->prefix = $prefix;
    }

    private function key(string $key): string
    {
        return $this->prefix . $key;
    }

    public function generate(string $key, int $ttl = 300): OtpCode
    {
        $otp = new OtpCode(random_int(100000, 999999));

        Redis::setex(
            $this->key($key),
            $ttl,
            $otp->value()
        );

        return $otp;
    }

    public function validate(string $key, string $code): bool
    {
        $stored = Redis::get($this->key($key));

        if ($stored === null) {
            throw new OtpExpiredException();
        }

        if ($stored !== $code) {
            return false;
        }

        // One-time OTP → consume it
        Redis::del($this->key($key));

        return true;
    }

    public function delete(string $key): void
    {
        Redis::del($this->key($key));
    }
}
