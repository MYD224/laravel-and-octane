<?php

namespace App\Application\Api\V1\Post\Data;

use App\Application\User\Data\AuthorData;
use App\Domain\Post\Entities\Post;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Date;

class PostData extends Data
{
    public function __construct(
        public string $title,
        public string $subtitle,
        public string $body,
        public bool $is_featured = false,
        public ?string $excerpt = null,
        public AuthorData $author,
        #[Date]
        public ?CarbonImmutable $published_at = null,
        public ?array $meta = null,
    ) {}



}
