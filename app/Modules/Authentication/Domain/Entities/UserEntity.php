<?php

namespace App\Modules\Authentication\Domain\Entities;

use App\Modules\Authentication\Domain\Enums\UserStatus;
use App\Modules\Authentication\Domain\ValueObjects\Email;
use App\Modules\Authentication\Domain\ValueObjects\Id;
use App\Modules\Authentication\Domain\ValueObjects\PhoneNumber;
use Carbon\CarbonImmutable;

class UserEntity
{

    public function __construct(
        private Id $id,
        private Email $email,
        private string $fullname,
        private PhoneNumber $phone,
        private string $hashedPassword,
        private UserStatus | string $status = UserStatus::ACTIVE->value,
        private ?CarbonImmutable $phoneVerifiedAt = null,
        private ?int $otpCode = null,
        private ?CarbonImmutable $otpExpiresAt = null,
        private CarbonImmutable $createdAt = new CarbonImmutable(),
        private CarbonImmutable $updatedAt = new CarbonImmutable(),
    ) {}


    public static function register(Id $id, string $fullname,  PhoneNumber $phone, CarbonImmutable | null $phoneVerifiedAt ,Email $email, string|UserStatus $status, string $hashedPassword): self
    {
        return new self($id, $email, $fullname, $phone,$hashedPassword, $status ,$phoneVerifiedAt);
    }

    public function activate(): void {
        $this->status = UserStatus::ACTIVE;
    }

   

    public function update(
        ?Email $email = null,
        ?string $fullname = null,
        ?PhoneNumber $phone = null,
        ?UserStatus $status = null,
        ?string $hashedPassword = null,
        ?int $otpCode = null,
        ?CarbonImmutable $otpExpiresAt = null,
        ?CarbonImmutable $phoneVerifiedAt = null,
    ): void {

        if ($fullname !== null) {
            $this->fullname = $fullname;
        }

        if ($email !== null) {
            if (!$this->email->equals($email)) {
                $this->email = $email;
            }
        }

        if ($phone !== null) {
            $this->phone = $phone;
        }

        if ($status !== null) {
            $this->status = $status;
        }

        if ($hashedPassword !== null) {
            $this->hashedPassword = $hashedPassword;
        }

        if ($otpCode !== null) {
            $this->otpCode = $otpCode;
        }

        if ($otpExpiresAt !== null) {
            $this->otpExpiresAt = $otpExpiresAt;
        }

        if ($phoneVerifiedAt !== null) {
            $this->phoneVerifiedAt = $phoneVerifiedAt;
        }

        $this->touchUpdatedAt();
    }


    public function getPhoneVerifiedAt() {
        return $this->phoneVerifiedAt;
    }
    
    public function eguals(Email $email): bool
    {
        return $this->getEmail() == $email->value();
    }

    private function touchUpdatedAt(): void
    {
        $this->updatedAt = new CarbonImmutable();
    }


    public function getId(): string
    {
        return $this->id->value();
    }

    public function getEmail(): string
    {
        return $this->email->value();
    }

    public function getPhoneNumber(): string
    {
        return $this->phone->value();
    }

    public function getFullname(): string
    {
        return $this->fullname;
    }

    public function getHashedPassword(): string
    {
        return $this->hashedPassword;
    }

    public function getOtpCode()
    {
        return $this->otpCode;
    }

    public function getOtpExpiresAt()
    {
        return $this->otpExpiresAt;
    }

    public function getStatus(): UserStatus
    {
        return $this->status;
    }

     public function getCreatedAt(): CarbonImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): CarbonImmutable
    {
        return $this->updatedAt;
    }
}
