<?php


namespace App\Modules\Post\Domain\Repositories;

use App\Modules\Post\Domain\Entities\Post;

interface PostRepositoryInterface
{
    public function save(Post $post): Post;
    // public function findById(string $id): ?Post;
    // public function existsByTitleAndAuthorId(string $title, string $authorId, ?string $ignoreId = null): bool;

    
}
