<?php

namespace App\Application\Post\Commands;

class CreatePostCommand {
    public function __construct(
        public string $title,
        public string $subtitle,
        public string $body,
        public bool $isFeatured = false,
        public ?string $excerpt = null,
        public string $authorId,
        public ?\Carbon\CarbonImmutable $publishedAt = null,
        public ?array $meta = null,

        public array $tagIds = [],

        public array $categoryIds = [],
    )
    {}
}