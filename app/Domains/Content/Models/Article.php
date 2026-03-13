<?php
// app/Domains/Content/Models/Article.php

namespace App\Domains\Content\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Domains\Content\Enums\ContentStatus;
use App\Domains\Content\Enums\ContentType;
use App\Domains\Media\Traits\HasMediaCollections;
use App\Models\User;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Article extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia, HasMediaCollections {
        HasMediaCollections::registerMediaCollections insteadof InteractsWithMedia;
        HasMediaCollections::registerMediaConversions insteadof InteractsWithMedia;
    }

    protected $table = 'articles';

    protected $fillable = [
        'type',
        'title',
        'slug',
        'excerpt',
        'content',
        'youtube_url',
        'edition',

        'status',
        'published_at',
        'author_id',

        // Venda
        'is_sellable',
        'price',
        'whatsapp_number',

        // SEO
        'seo_title',
        'seo_description',
        'seo_keywords',

        // Metadata
        'reading_time',
        'views_count',
    ];

    protected $casts = [
        'type' => ContentType::class,
        'status' => ContentStatus::class,

        'published_at' => 'datetime',

        'is_sellable' => 'boolean',
        'price' => 'decimal:2',

        'reading_time' => 'integer',
        'views_count' => 'integer',
    ];

    protected $attributes = [
        'type' => 'article',
        'status' => 'draft',

        'is_sellable' => false,
        'views_count' => 0,
    ];

    protected $with = ['media'];

    protected $appends = [
        'category',
        'type_label',
        'status_label',
        'is_published',
        'whatsapp_link',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'article_category')
            ->withTimestamps();
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'article_tag')
            ->withTimestamps();
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePublished($query)
    {
        return $query->where('status', ContentStatus::PUBLISHED)
            ->where('published_at', '<=', now());
    }

    public function scopeSellable($query)
    {
        return $query->where('is_sellable', true);
    }

    public function scopeOfType($query, ContentType|string $type)
    {
        return $query->where('type', $type instanceof ContentType ? $type->value : $type);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', ContentStatus::DRAFT);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', ContentStatus::SCHEDULED);
    }

    public function scopeArchived($query)
    {
        return $query->where('status', ContentStatus::ARCHIVED);
    }

    public function scopeVisible($query)
    {
        return $query->where('status', ContentStatus::PUBLISHED)
            ->where('published_at', '<=', now());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', ContentStatus::SCHEDULED)
            ->where('published_at', '>', now());
    }

    public function scopeForAuthor($query, $authorId)
    {
        return $query->where('author_id', $authorId);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
                ->orWhere('slug', 'like', "%{$term}%")
                ->orWhere('excerpt', 'like', "%{$term}%")
                ->orWhere('content', 'like', "%{$term}%");
        });
    }

    public function scopeFilterByCategory($query, $categoryId)
    {
        if ($categoryId === 'all') {
            return $query;
        }

        return $query->whereHas('categories', function ($q) use ($categoryId) {
            $q->where('categories.id', $categoryId);
        });
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getUrlAttribute(): string
    {
        return route('articles.show', [$this->type, $this->slug]);
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type->label();
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    public function getIsPublishedAttribute(): bool
    {
        return $this->status === ContentStatus::PUBLISHED &&
            $this->published_at?->lte(now());
    }

    public function getCategoryAttribute(): ?string
    {
        return $this->categories()->first()?->name;
    }

    public function getExcerptOrFallbackAttribute(): string
    {
        if ($this->excerpt) {
            return $this->excerpt;
        }

        return substr(strip_tags($this->content), 0, 200) . '...';
    }

    /*
    |--------------------------------------------------------------------------
    | Venda
    |--------------------------------------------------------------------------
    */

    public function getIsPaidAttribute(): bool
    {
        return $this->is_sellable && $this->price > 0;
    }

    public function getFormattedPriceAttribute(): ?string
    {
        if (!$this->price) {
            return null;
        }

        return number_format($this->price, 2, ',', '.') . ' MZN';
    }

    public function getWhatsappLinkAttribute(): ?string
    {
        if (!$this->whatsapp_number) {
            return null;
        }

        // Garantir que o número tenha apenas dígitos
        $number = preg_replace('/\D+/', '', $this->whatsapp_number);

        if (!$number) {
            return null;
        }

        $edition = $this->edition ? " ({$this->edition})" : '';

        $message = rawurlencode(
            "Olá, tenho interesse no jornal: {$this->title}{$edition}. Gostaria de saber mais detalhes sobre a compra."
        );

        return "https://wa.me/{$number}?text={$message}";
    }

    /*
    |--------------------------------------------------------------------------
    | YouTube Helpers
    |--------------------------------------------------------------------------
    */

    public function getYouTubeIdAttribute(): ?string
    {
        if (!$this->youtube_url) {
            return null;
        }

        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $this->youtube_url, $matches);

        return $matches[1] ?? null;
    }

    public function getYouTubeThumbnailAttribute(): ?string
    {
        return $this->youtube_id
            ? "https://img.youtube.com/vi/{$this->youtube_id}/maxresdefault.jpg"
            : null;
    }

    /*
    |--------------------------------------------------------------------------
    | Reading Time
    |--------------------------------------------------------------------------
    */

    public function getReadingTimeInMinutesAttribute(): string
    {
        if ($this->reading_time) {
            return $this->reading_time . ' min';
        }

        if ($this->type === ContentType::ARTICLE && $this->content) {
            $wordCount = str_word_count(strip_tags($this->content));
            $minutes = max(1, ceil($wordCount / 200));
            return $minutes . ' min';
        }

        return '1 min';
    }

    /*
    |--------------------------------------------------------------------------
    | Mutators
    |--------------------------------------------------------------------------
    */

    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = $value ?: str($this->title)->slug();
    }

    /*
    |--------------------------------------------------------------------------
    | Permissions
    |--------------------------------------------------------------------------
    */

    public function canBeEditedBy(User $user): bool
    {
        if ($user->hasRole('admin')) return true;
        if ($user->hasRole('editor')) return true;

        if ($user->hasRole('author')) {
            return $this->author_id === $user->id;
        }

        return false;
    }

    public function canBeDeletedBy(User $user): bool
    {
        if ($user->hasRole('admin')) return true;

        if ($user->hasRole('author')) {
            return $this->author_id === $user->id && $this->status === ContentStatus::DRAFT;
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | Boot
    |--------------------------------------------------------------------------
    */

    protected static function booted()
    {
        static::deleting(function (Article $article) {

            logger()->info('Artigo sendo deletado', [
                'article_id' => $article->id,
                'title' => $article->title,
            ]);
        });
    }
}
