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
        // private string $fullname,
        private string $firstnames,
        private string $lastname,
        private string $gender,
        private ?PhoneNumber $phone,
        private string $hashedPassword,
        private UserStatus | string $status = UserStatus::ACTIVE->value,
        private ?bool $isSendOtp,
        private ?CarbonImmutable $phoneVerifiedAt = null,
        private ?CarbonImmutable $emailVerifiedAt = null,
        private ?string $authProvider = null,
        private ?string $authProviderId = null,
        private CarbonImmutable $createdAt = new CarbonImmutable(),
        private CarbonImmutable $updatedAt = new CarbonImmutable(),

    ) {}


    public static function register(
        Id $id,
        // string $fullname,
        string $firstnames,
        string $lastname,
        string $gender,
        CarbonImmutable | null $phoneVerifiedAt,
        Email $email,
        UserStatus $status,
        string $hashedPassword,
        ?bool $isSendOtp,
        ?PhoneNumber $phone = null,
        CarbonImmutable | null $emailVerifiedAt,
        ?string $authProvider = null,
        ?string $authProviderId = null,
    ): self {
        return new self($id, $email, $firstnames, $lastname, $gender, $phone, $hashedPassword, $status, $isSendOtp, $phoneVerifiedAt, $emailVerifiedAt, $authProvider, $authProviderId);
    }

    public function activate(): void
    {
        $this->status = UserStatus::ACTIVE;
    }



    public function update(
        ?Email $email = null,
        // ?string $fullname = null,
        ?string $firstnames = null,
        ?string $lastname = null,
        ?string $gender = null,
        ?PhoneNumber $phone = null,
        ?UserStatus $status = null,
        ?string $hashedPassword = null,
        ?CarbonImmutable $phoneVerifiedAt = null,
        ?CarbonImmutable $emailVerifiedAt = null,
        ?string $authProvider = null,
        ?string $authProviderId = null,
        ?bool $isSendOtp = null

    ): void {

        // if ($fullname !== null) {
        //     $this->fullname = $fullname;
        // }

        if ($firstnames !== null) {
            $this->firstnames = $firstnames;
        }

        if ($lastname !== null) {
            $this->lastname = $lastname;
        }

        if ($gender != null) {
            $this->gender = $gender;
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

        if ($isSendOtp !== null) {
            $this->isSendOtp = $isSendOtp;
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
        return "";
        // return $this->fullname;
    }

    public function getHashedPassword(): string
    {
        return $this->hashedPassword;
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

    /**
     * Get the value of firstnames
     */
    public function getFirstnames()
    {
        return $this->firstnames;
    }


    /**
     * Get the value of lastname
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Get the value of gender
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Get the value of isSendOtp
     */
    public function getIsSendOtp()
    {
        return $this->isSendOtp;
    }
}
