<?php

namespace App\Domain\Post\ValueObjects;

final class Title
{
    public function __construct(private string $value)
    {
        if (strlen($value) < 3) {
            throw new \InvalidArgumentException('Title must be at least 3 characters.');
        }

        if (strlen($value) > 150) {
            throw new \InvalidArgumentException('Title cannot exceed 150 characters.');
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(Title $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
