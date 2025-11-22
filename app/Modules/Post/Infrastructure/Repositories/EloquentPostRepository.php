<?php

use App\Modules\Post\Infrastructure\Persistence\Eloquent\Models\Post;
use App\Modules\Post\Domain\Entities\Post as PostEntity;
use App\Modules\Post\Domain\Repositories\PostRepositoryInterface;

class EloquentPostRepository implements PostRepositoryInterface {

    public function save(PostEntity $post): PostEntity {

        
        $post = Post::create(
            [
                'title' => $post->title,
                'subtitle' => $post->subtitle,
                'body' => $post->body,
                'excerpt' => $post->excerpt,
                'is_featured' => $post->isFeatured,
                'author_id' => $post->authorId,
                'meta' => $post->meta,
                'status' => $post->status,
                'slug' => $post->slug,
            ]
        );

        return new PostEntity(
            $post->title,
            $post->subtitle,
            $post->body,
            $post->excerpt,
            $post->is_featured,
            $post->author_id,
            $post->published_at,
            $post->meta,
            $post->id,
            $post->slug,
            $post->status,
        );
        
    }

    // public function findById(string $id): ?Post {
    //     $post = PostPost::find($id);

    //     if (!$post) {
    //         return null;
    //     }

    //     return new Post(
    //         $post->title,
    //         $post->subtitle,
    //         $post->body,
    //         $post->excerpt,
    //         $post->is_featured,
    //         $post->author_id,
    //         $post->published_at,
    //         $post->meta,
    //         $post->id,
    //         $post->slug,
    //         $post->status,
    //     );
    // }


    // public function exitsByTitleAndAuthorId(string $title, string $authorId, ?string $ignoreId = null): bool {
    //     $query = PostPost::where('title', $title)
    //                      ->where('author_id', $authorId);

    //     if ($ignoreId) {
    //         $query->where('id', '!=', $ignoreId);
    //     }

    //     return $query->exists();
    // }



}