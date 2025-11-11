<?php
declare(strict_types=1);

namespace App\Domain\Post\Enums;

enum PostStatus: string
{
    case DRAFT     = 'draft';
    case PUBLISHED = 'published';
    case SCHEDULED = 'scheduled';
    case ARCHIVED  = 'archived';
    case DELETED   = 'deleted';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::PUBLISHED => 'Published',
            self::SCHEDULED => 'Scheduled',
            self::ARCHIVED => 'Archived',
            self::DELETED => 'Deleted',
        };
    }

    public static function values(): array
    {
        return array_map(fn(PostStatus $s) => $s->value, self::cases());
    }

    public static function labels(): array
    {
        return array_combine(self::values(), array_map(fn(PostStatus $s) => $s->label(), self::cases()));
    }

    public function isPublished(): bool
    {
        return $this === self::PUBLISHED;
    }
}