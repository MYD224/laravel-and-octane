<?php
namespace App\Application\Api\V1\Post\Data;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Date;

class CreatePostData extends Data
{
    public function __construct(
        public string $title,
        public string $subtitle,
        public string $body,
        public bool $is_featured = false,
        public ?string $excerpt = null,
        public string $author_id,
        #[Date]
        public ?CarbonImmutable $published_at = null,
        public ?array $meta = null,

        /** @var string[] */
        public array $tag_ids = [],
        
        /** @var string[] */
        public array $category_ids = [],
    ) {}
}