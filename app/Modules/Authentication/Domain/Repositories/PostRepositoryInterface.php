<?php


namespace App\Domain\Booking\Repositories;

use App\Domain\Post\Entities\Post;

interface PostRepositoryInterface
{
    public function save(Post $post): Post;
    public function findById(string $id): ?Post;
    public function existsByTitleAndAuthorId(string $title, string $authorId, ?string $ignoreId = null): bool;

    
}
