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
        private ?PhoneNumber $phone,
        private string $hashedPassword,
        private string $status,
        private ?CarbonImmutable $phoneVerifiedAt = null,
        private ?CarbonImmutable $emailVerifiedAt = null,
        private ?string $authProvider = null,
        private ?string $authProviderId = null,
        private ?CarbonImmutable $otpExpiresAt = null,
        private CarbonImmutable $createdAt = new CarbonImmutable(),
        private CarbonImmutable $updatedAt = new CarbonImmutable(),
    ) {}


    public static function register(
        Id $id,
        string $fullname,
        CarbonImmutable | null $phoneVerifiedAt,
        Email $email,
        string $status,
        string $hashedPassword,
        ?PhoneNumber $phone = null,
        ?CarbonImmutable $emailVerifiedAt = null,
        ?string $authProvider = null,
        ?string $authProviderId = null
    ): self {
        return new self($id, $email, $fullname, $phone, $hashedPassword, $status, $phoneVerifiedAt, $emailVerifiedAt, $authProvider, $authProviderId);
    }

    public function activate(): void
    {
        // $this->status = UserStatus::ACTIVE;
    }



    public function update(
        ?Email $email = null,
        ?string $fullname = null,
        ?PhoneNumber $phone = null,
        ?string $status = null,
        ?string $hashedPassword = null,
        ?CarbonImmutable $otpExpiresAt = null,
        ?CarbonImmutable $phoneVerifiedAt = null,
        ?CarbonImmutable $emailVerifiedAt = null,
        ?string $authProvider = null,
        ?string $authProviderId = null,

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

        if ($otpExpiresAt !== null) {
            $this->otpExpiresAt = $otpExpiresAt;
        }

        if ($phoneVerifiedAt !== null) {
            $this->phoneVerifiedAt = $phoneVerifiedAt;
        }
        if ($emailVerifiedAt !== null) {
            $this->emailVerifiedAt = $emailVerifiedAt;
        }
        if ($authProvider !== null) {
            $this->authProvider = $authProvider;
        }
        if ($authProviderId !== null) {
            $this->authProviderId = $authProviderId;
        }

        $this->touchUpdatedAt();
    }






    public function getPhoneVerifiedAt()
    {
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

    public function getPhoneNumber(): ?string
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


    public function getOtpExpiresAt()
    {
        return $this->otpExpiresAt;
    }

    public function getStatus(): string
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

    /**
     * Set the value of phoneVerifiedAt
     *
     * @return  self
     */
    public function setPhoneVerifiedAt($phoneVerifiedAt)
    {
        $this->phoneVerifiedAt = $phoneVerifiedAt;

        return $this;
    }

    /**
     * Get the value of phone
     */
    public function getPhone()
    {
        return $this->phone->value() ? $this->phone->value() : null;
    }

    /**
     * Get the value of emailVerifiedAt
     */
    public function getEmailVerifiedAt()
    {
        return $this->emailVerifiedAt;
    }

    /**
     * Get the value of authProvider
     */
    public function getAuthProvider(): ?string
    {
        return $this->authProvider;
    }

    /**
     * Get the value of authProviderId
     */
    public function getAuthProviderId(): ?string
    {
        return $this->authProviderId;
    }
}
