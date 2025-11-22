<?php

namespace App\Modules\Post\Domain\Services;

use App\Domain\Post\Entities\Post;
use App\Modules\Post\Domain\Repositories\PostRepositoryInterface;
use Carbon\CarbonImmutable;

class PostService
{

    public function __construct(
        private PostRepositoryInterface $posts,
        private CategoryRepository $categories,
        private TagRepository $tags,
    ) {}


    public function createPost(

        string $title,
        string $subtitle,
        string $body,
        bool $isFeatured,
        string $authorId,
        ?string $excerpt,
        ?CarbonImmutable $publishedAt,
        ?array $meta,
        array $categoryIds,
        array $tagIds
    ): PostData {

        if ($this->posts->existsByTitleAndAuthorId($title, $authorId)) {

            throw new \Exception("Post with title '{$title}' already exists.");
        }
        
        $post = new Post(
            $title,
            $subtitle,
            $body,
            $excerpt,
            $isFeatured,
            $authorId,
            $publishedAt,
            $meta,
        );

        foreach ($categoryIds as $categoryId) {
            $category = $this->categories->findById($categoryId);
            $post->addCategory($category);
        }

        foreach ($tagIds as $tagId) {
            $tag = $this->tags->findById($tagId);
            $post->addTag($tag);
        }

        $this->posts->save($post);


        return PostData::from($post);
    }
}
