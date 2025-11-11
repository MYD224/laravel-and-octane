<?php

namespace App\Domain\Post\Entities;


use App\Domain\Post\Enums\PostStatus;

class Post
{
    // Post entity implementation
    
    public function __construct(
        public ?string $id,
        public string $title,
        public ?string $subtitle,
        public string $body,
        public ?string $excerpt,
        public string $slug,
        public string $authorId,
        public ?string $status = PostStatus::DRAFT->value,
        public bool $isFeatured = false,
        public int $viewsCount = 0,
        public ?array $meta = null,
        public ?\DateTimeImmutable $publishedAt = null,
        public ?\DateTimeImmutable $createdAt = null,
        public ?\DateTimeImmutable $updatedAt = null,
        public array $tags = [],
        public array $categories = [],
    ) {}

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

   public function addCategory(Category $category): void
    {
        if (!in_array($category, $this->categories)) {
            $this->categories[] = $category;
        }
    }

    public function addTag(Tag $tag): void
    {
        if (!in_array($tag, $this->tags)) {
            $this->tags[] = $tag;
        }
    }
    

}