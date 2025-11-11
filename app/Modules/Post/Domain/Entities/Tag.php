<?php

namespace App\Domain\Post\Entities;

class Tag
{
    // Tag entity implementation

    public function __construct(
        public ?string $id,
        public string $name,
        public ?\DateTimeImmutable $createdAt = null,
        public ?\DateTimeImmutable $updatedAt = null,
    ) {}
}