<?php

namespace App\Domain\Post\ValueObjects;

final class Excerpt
{
    public function __construct(private string $value)
    {
        if (strlen($value) > 250) {
            throw new \InvalidArgumentException('Excerpt cannot exceed 250 characters.');
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public static function fromBody(string $body): self
    {
        return new self(substr(strip_tags($body), 0, 250));
    }
}
