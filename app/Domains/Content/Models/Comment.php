<?php

namespace App\Domains\Content\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Comment extends Model
{
    use SoftDeletes;

    protected $table = 'comments';

    protected $fillable = [
        'commentable_type',
        'commentable_id',
        'name',
        'email',
        'content',
        'ip_address',
        'user_agent',
        'referer',
        'metadata',
        'status',
        'parent_id',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Status constants
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_SPAM = 'spam';
    public const STATUS_TRASH = 'trash';

    // Relationships
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    // public function replies(): HasMany
    // {
    //     return $this->hasMany(Comment::class, 'parent_id')->latest();
    // }

    // Na model Comment
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function scopeWithReplies($query, $approvedOnly = true)
    {
        return $query->with(['replies' => function ($q) use ($approvedOnly) {
            if ($approvedOnly) {
                $q->approved();
            }
            $q->with('replies');
        }]);
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeSpam($query)
    {
        return $query->where('status', self::STATUS_SPAM);
    }

    public function scopeForArticle($query, int $articleId)
    {
        return $query->where('commentable_type', Article::class)
            ->where('commentable_id', $articleId)
            ->whereNull('parent_id');
    }

    public function scopeForArticleCount($query, int $articleId)
    {
        return $query->where('commentable_type', Article::class)
            ->where('commentable_id', $articleId);
    }

    // Accessors
    public function getCreatedDateAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    public function getAuthorNameAttribute(): string
    {
        return $this->name;
    }

    // Helpers
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function markAsApproved(): self
    {
        $this->update(['status' => self::STATUS_APPROVED]);
        return $this;
    }

    public function markAsSpam(): self
    {
        $this->update(['status' => self::STATUS_SPAM]);
        return $this;
    }
}
