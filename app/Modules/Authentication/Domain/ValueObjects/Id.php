<?php

namespace App\Modules\Authentication\Domain\ValueObjects;
use Ramsey\Uuid\Uuid;

final class Id
{
    public function __construct(private string $value)
    {
        if (!Uuid::isValid($value)) {
            throw new \InvalidArgumentException("Invalid UUID");
        }
    }

    public static function generate(): self
    {
        return new self(Uuid::uuid4()->toString());
    }

    public function value(): string
    {
        return $this->value;
    }
}