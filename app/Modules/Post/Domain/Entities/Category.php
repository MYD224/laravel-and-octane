<?php

namespace App\Domain\Post\Entities;


class Category
{
    // Category entity implementation

    public function __construct(
        public ?string $id,
        public string $name,
        public ?string $description = null,
        public ?\DateTimeImmutable $createdAt = null,
        public ?\DateTimeImmutable $updatedAt = null,
    ) {}
}