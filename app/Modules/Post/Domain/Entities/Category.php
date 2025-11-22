<?php

namespace App\Domain\Post\Entities;


class Category
{
    // Category entity implementation

    public function __construct(
        public ?string $id,
        public string $name,
        public ?string $description = null,
        public ?\CarbonImmutable $createdAt = null,
        public ?\CarbonImmutable $updatedAt = null,
    ) {}
}