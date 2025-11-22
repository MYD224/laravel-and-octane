<?php

namespace App\Modules\Authentication\Domain\ValueObjects;

final class PhoneNumber
{
    private string $phoneNumber; // always stored as national 9 digits

    /**
     * Accepts formats:
     * - 622689659
     * - 622 68 96 59
     * - +224622689659
     * - 00224622689659
     * - 224622689659
     */
    private const GUINEA_REGEX = '/^(?:\+?224|00224)?\s*([6-7]\d{8})$/';

    public function __construct(string $number)
    {
        $standardized = $this->standardize($number);

        if (! $this->isValid($standardized)) {
            throw new \InvalidArgumentException("Invalid phone number: {$number}");
        }

        // Always store national 9-digit format
        $this->phoneNumber = $standardized;
    }

    /**
     * Normalize input:
     * - remove spaces
     * - remove all non-digit except "+" if at beginning
     * - unify to digits only after stripping country code
     */
    private function standardize(string $number): string
    {
        // Remove spaces and symbols except leading +
        $cleaned = preg_replace('/[^\d+]/', '', $number);

        // Replace +224 / 00224 / 224 prefix → empty
        $cleaned = preg_replace('/^(?:\+224|00224|224)/', '', $cleaned);

        return $cleaned;
    }

    private function isValid(string $number): bool
    {
        // Validate only the last 9 digits (national)
        return preg_match('/^[6-7]\d{8}$/', $number) === 1;
    }

    /**
     * Returns national 9-digit format.
     * Example: 622689659
     */
    public function getNationalFormat(): string
    {
        return $this->phoneNumber;
    }

    /**
     * International format: +224622689659
     */
    public function getInternationalFormat(): string
    {
        return '+224' . $this->phoneNumber;
    }

    /**
     * Default cast when used as string.
     */
    public function __toString(): string
    {
        return $this->getInternationalFormat();
    }

    public function value(): string
    {
        return $this->phoneNumber;
    }
}
