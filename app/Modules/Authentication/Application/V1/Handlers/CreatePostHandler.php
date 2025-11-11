<?php

namespace App\Application\Post\Handlers;

use App\Application\Post\Commands\CreatePostCommand;
use App\Application\Post\Data\PostData;
use App\Domain\Post\Services\PostService;

class CreatePostHandler
{
    public function __construct(
        private PostService $postService,
    ){}


    public function handle(CreatePostCommand $command): PostData
    {

        
        return $this->postService->createPost(
            title: $command->title,
            subtitle: $command->subtitle,
            body: $command->body,
            isFeature: $command->isFeatured,
            excerpt: $command->excerpt,
            authorId: $command->authorId,
            publishedAt: $command->publishedAt,
            meta: $command->meta,
            categoryIds: $command->categoryIds,
            tagIds: $command->tagIds,

        );


       
    }
   
}