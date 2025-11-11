<?php
namespace App\Infrastructure\Persistence\Eloquent\Models\Post;

use App\Domain\Post\Enums\PostStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;




/**
 * App\Infrastructure\Persistence\Eloquent\Models\Post\Post
 *
 * A fully featured Blog Post Eloquent model with:
 * - slug generation & uniqueness
 * - common relationships (author, categories, tags, comments)
 * - scopes (published, popular, latest)
 * - casts, accessors (is_published, reading_time)
 * - SoftDeletes & HasFactory
 *
 * Create a new file at:
 * /media/ghostg/DATA/experimental-projects/betelcom-backend/app/Infrastructure/Persistence/Eloquent/Models/Post/Post.php
 */
class Post extends Model
{
    use HasFactory, SoftDeletes;

    // Table name (optional if following conventions)
    protected $table = 'posts';

    // Mass assignable attributes
    protected $fillable = [
        'title',
        'subtitle',
        'body',
        'excerpt',
        'slug',
        'user_id',
        'status',
        'published_at',
        'is_featured',
        'views_count',
        'meta', // json for seo, etc
    ];

    // Casts
    protected $casts = [
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
        'views_count' => 'integer',
        'meta' => 'array',
        "status" => PostStatus::class
    ];

    // Appended accessors
    protected $appends = [
        'is_published',
        'reading_time',
    ];

    // Default attributes
    protected $attributes = [
        'status' => PostStatus::DRAFT->value,
        'views_count' => 0,
        'is_featured' => false,
    ];

    // Booted model callbacks
    protected static function booted(): void
    {
        // Generate unique slug when creating if not provided or empty
        static::creating(function (Post $post) {
            if (empty($post->slug) && ! empty($post->title)) {
                $post->slug = static::generateUniqueSlug($post->title);
            }

            // Ensure published_at is set when status is published
            if ($post->status === PostStatus::PUBLISHED && empty($post->published_at)) {
                $post->published_at = now();
            }
        });

        // Ensure slug uniqueness when updating title
        static::updating(function (Post $post) {
            if ($post->isDirty('title') && ! $post->isDirty('slug')) {
                $post->slug = static::generateUniqueSlug($post->title, $post->id);
            }
        });
    }

    /**
     * Generate a URL-friendly unique slug for this model.
     *
     * @param  string  $title
     * @param  int|null $ignoreId
     * @return string
     */
    protected static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: Str::random(8);
        $slug = $base;
        $i = 1;

        while (
            static::where('slug', $slug)
                ->when($ignoreId, fn (Builder $q) => $q->where('id', '<>', $ignoreId))
                ->withTrashed()
                ->exists()
        ) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    /**
     * Route model binding key (use slug)
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Author / User relationship.
     * Uses the configured user model so it works with custom user models.
     */
    public function author()
    {
        $userModel = config('auth.providers.users.model') ?? \App\Models\User::class;
        return $this->belongsTo($userModel, 'author_id');
    }

    /**
     * Categories relationship (many-to-many)
     */
    /*public function categories()
    {
        return $this->belongsToMany(
            \App\Infrastructure\Persistence\Eloquent\Models\Category\Category::class,
            'category_post',
            'post_id',
            'category_id'
        )->withTimestamps();
    }*/

    /**
     * Tags relationship (many-to-many)
     */
    /*
    public function tags()
    {
        return $this->belongsToMany(
            \App\Infrastructure\Persistence\Eloquent\Models\Tag\Tag::class,
            'post_tag',
            'post_id',
            'tag_id'
        )->withTimestamps();
    }*/

    /**
     * Comments relationship (one-to-many)
     */
   /* public function comments()
    {
        return $this->hasMany(
            \App\Infrastructure\Persistence\Eloquent\Models\Comment\Comment::class,
            'post_id'
        );
    }*/

    /**
     * Scope: published posts (status + published_at in the past)
     */
    /*public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', PostStatus::PUBLISHED)
                     ->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
    }*/

    /**
     * Scope: popular posts by views
     */
   /* public function scopePopular(Builder $query, int $limit = 10): Builder
    {
        return $query->published()->orderByDesc('views_count')->limit($limit);
    }*/

    /**
     * Scope: latest posts
     */
    /*public function scopeLatest(Builder $query, int $limit = 10): Builder
    {
        return $query->published()->orderByDesc('published_at')->limit($limit);
    }*/

    /**
     * Accessor: is_published boolean
     */
   /* public function getIsPublishedAttribute(): bool
    {
        return $this->status === self::STATUS_PUBLISHED
            && $this->published_at instanceof Carbon
            && $this->published_at->isPast();
    }*/

    /**
     * Accessor: approximate reading time in minutes
     */
    /*public function getReadingTimeAttribute(): int
    {
        $text = strip_tags((string) ($this->body ?? ''));
        $words = str_word_count($text);
        $wpm = 200; // average words per minute
        return (int) max(1, floor($words / $wpm));
    }*/

    /**
     * Increment view count in an atomic way.
     */
    public function incrementViews(int $by = 1): int
    {
        $this->increment('views_count', $by);
        $this->refresh();
        return $this->views_count;
    }

    /**
     * Helper to publish the post now.
     */
    public function publishNow(): bool
    {
        $this->status = PostStatus::PUBLISHED;
        $this->published_at = $this->published_at ?? now();
        return $this->save();
    }

    /**
     * Helper to set post as draft.
     */
    /*public function markAsDraft(): bool
    {
        $this->status = PostStatus::DRAFT;
        return $this->save();
    }*/
}