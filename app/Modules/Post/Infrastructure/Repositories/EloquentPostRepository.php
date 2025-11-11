<?php

use App\Domain\Booking\Repositories\PostRepositoryInterface;
use App\Domain\Post\Entities\Post;
use App\Infrastructure\Persistence\Eloquent\Models\Post\Post as PostPost;

class EloquentPostRepository implements PostRepositoryInterface {

    public function save(Post $post): Post {

        
        $post = PostPost::create(
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

        return new Post(
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

    public function findById(string $id): ?Post {
        $post = PostPost::find($id);

        if (!$post) {
            return null;
        }

        return new Post(
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


    public function exitsByTitleAndAuthorId(string $title, string $authorId, ?string $ignoreId = null): bool {
        $query = PostPost::where('title', $title)
                         ->where('author_id', $authorId);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->exists();
    }



}