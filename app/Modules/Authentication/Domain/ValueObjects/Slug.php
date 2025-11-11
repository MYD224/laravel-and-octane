<?php

namespace App\Domain\Post\ValueObjects;

use Illuminate\Support\Str;

final class Slug
{
    public function __construct(private string $value)
    {
        if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $value)) {
            throw new \InvalidArgumentException("Invalid slug format: {$value}");
        }
    }

    public static function fromTitle(string $title): self
    {
        return new self(Str::slug($title));
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(Slug $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
