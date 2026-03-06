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
        'seo_title',
        'seo_description',
        'seo_keywords',
        'reading_time',
        'views_count',
    ];

    protected $casts = [
        'type' => ContentType::class,
        'status' => ContentStatus::class,
        'published_at' => 'datetime',
    ];

    protected $attributes = [
        'type' => 'article',
        'status' => 'draft',
        'views_count' => 0,
    ];

    protected $with = ['media'];
    
    protected $appends = ['category', 'type_label', 'status_label'];

    // Relationships
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

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', ContentStatus::PUBLISHED)
            ->where('published_at', '<=', now());
    }

    public function scopeOfType($query, ContentType|string $type)
    {
        return $query->where('type', $type instanceof ContentType ? $type->value : $type);
    }

    // Accessors
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

    public function getCategoryAttribute(): ?string
    {
        return $this->categories()->first()?->name;
    }

    // YouTube helpers
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

    // Validação para publicação
    public function canBePublished(): bool
    {
        $requirements = $this->type->mediaRequirements();
        
        // Verificar imagem de destaque se required
        if ($requirements['featured_image'] === 'required' && !$this->hasFeaturedImage()) {
            return false;
        }
        
        // Verificar PDF para jornal
        if ($this->type === ContentType::NEWSPAPER && 
            $requirements['pdf'] === 'required' && 
            !$this->hasMedia('pdf')) {
            return false;
        }
        
        // Verificar YouTube URL para vídeo
        if ($this->type === ContentType::VIDEO && !$this->youtube_url) {
            return false;
        }
        
        return true;
    }

    public function getPublishErrors(): array
    {
        $errors = [];
        $requirements = $this->type->mediaRequirements();
        
        if ($requirements['featured_image'] === 'required' && !$this->hasFeaturedImage()) {
            $errors[] = 'Imagem de destaque é obrigatória';
        }
        
        if ($this->type === ContentType::NEWSPAPER) {
            if ($requirements['pdf'] === 'required' && !$this->hasMedia('pdf')) {
                $errors[] = 'Arquivo PDF da edição é obrigatório';
            }
            if (!$this->edition) {
                $errors[] = 'Número da edição é obrigatório';
            }
        }
        
        if ($this->type === ContentType::VIDEO && !$this->youtube_url) {
            $errors[] = 'URL do YouTube é obrigatória';
        }
        
        if ($this->type === ContentType::ARTICLE && !$this->content) {
            $errors[] = 'Conteúdo do artigo é obrigatório';
        }
        
        return $errors;
    }

    /* aaaaaaaaa */
    public function approvedComments()
    {
        return $this->morphMany(Comment::class, 'commentable')
            ->where('status', Comment::STATUS_APPROVED);
    }

    // Scopes...
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

    // Accessors...
    public function getIsPublishedAttribute(): bool
    {
        return $this->status === ContentStatus::PUBLISHED &&
            $this->published_at?->lte(now());
    }

    public function getIsScheduledAttribute(): bool
    {
        return $this->status === ContentStatus::SCHEDULED;
    }

    public function getIsDraftAttribute(): bool
    {
        return $this->status === ContentStatus::DRAFT;
    }

    public function getIsArchivedAttribute(): bool
    {
        return $this->status === ContentStatus::ARCHIVED;
    }

    public function getExcerptOrFallbackAttribute(): string
    {
        if ($this->excerpt) {
            return $this->excerpt;
        }

        return substr(strip_tags($this->content), 0, 200) . '...';
    }

    // Accessors para mídia
    public function getFeaturedImageThumbAttribute(): ?string
    {
        return $this->getFeaturedImageUrl('thumb');
    }

    public function getFeaturedImagePreviewAttribute(): ?string
    {
        return $this->getFeaturedImageUrl('preview');
    }

    public function getFeaturedImageLargeAttribute(): ?string
    {
        return $this->getFeaturedImageUrl('large');
    }

    public function getGalleryThumbsAttribute(): array
    {
        return $this->getGalleryUrls('thumb');
    }

    public function getGalleryPreviewsAttribute(): array
    {
        return $this->getGalleryUrls('preview');
    }

    // Mutators
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = $value ?: str($this->title)->slug();
    }

    // Helpers existentes...
    public function canBeEditedBy(User $user): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('editor')) {
            return true;
        }

        if ($user->hasRole('author')) {
            return $this->author_id === $user->id;
        }

        return false;
    }

    public function canBeDeletedBy(User $user): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('editor')) {
            return false;
        }

        if ($user->hasRole('author')) {
            return $this->author_id === $user->id && $this->isDraft;
        }

        return false;
    }

    // Boot do modelo
    protected static function booted()
    {
        static::deleting(function (Article $article) {
            // As mídias serão deletadas automaticamente pela MediaLibrary
            // devido à configuração 'delete_media_on_model_deletion' => true
            logger()->info('Artigo sendo deletado, mídias serão removidas', [
                'article_id' => $article->id,
                'title' => $article->title,
            ]);
        });
    }
}