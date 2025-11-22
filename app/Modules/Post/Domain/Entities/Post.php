<?php
namespace App\Modules\Post\Domain\Entities;

use App\Domain\Post\Enums\PostStatus;
use App\Domain\Post\ValueObjects\Title;

class Post
{
    
    public function __construct(
        public readonly string $id,
        public readonly Title $title,
        public readonly ?string $subtitle,
        public readonly string $body,
        public readonly ?string $excerpt,
        public readonly string $slug,
        public readonly string $authorId,
        public readonly ?string $status = PostStatus::DRAFT->value,
        public readonly bool $isFeatured = false,
        public readonly int $viewsCount = 0,
        public readonly ?array $meta = null,
        public readonly ?\CarbonImmutable $publishedAt = null,
        public readonly ?\CarbonImmutable $createdAt = null,
        public readonly ?\CarbonImmutable $updatedAt = null,
        public readonly array $tags = [],
        public readonly array $categories = [],
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