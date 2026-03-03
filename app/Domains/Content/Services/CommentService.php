<?php

namespace App\Domains\Content\Services;

use App\Domains\Content\Models\Article;
use App\Domains\Content\Models\Comment;
use App\Domains\Content\Http\Requests\StoreCommentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CommentService
{
    /**
     * Get comments for an article
     */
    public function getForArticle(int $articleId, bool $approvedOnly = true): array
    {
        $query = Comment::forArticle($articleId);

        if ($approvedOnly) {
            $query->approved();
        }

        $comments = $query->latest()
            ->withReplies($approvedOnly)
            ->get();

        return $comments->map(fn($c) => $this->formatComment($c))->toArray();
    }

    private function formatComment(Comment $comment): array
    {
        return [
            'id' => $comment->id,
            'author_name' => $comment->name,
            'content' => $comment->content,
            'created_date' => $comment->created_date,
            'replies' => $comment->replies->map(
                fn($reply) => $this->formatComment($reply)
            )->toArray(),
        ];
    }

    /**
     * Store a new comment
     */
    public function store(StoreCommentRequest $request): Comment
    {
        $article = Article::findOrFail($request->article_id);

        // Collect metadata
        $metadata = [
            'timestamp' => now()->toIso8601String(),
            'previous_comments_count' => Comment::forArticle($article->id)->count(),
        ];

        // Create comment
        $comment = new Comment();
        $comment->commentable()->associate($article);
        $comment->name = $request->name;
        $comment->email = $request->email;
        $comment->content = $this->sanitizeContent($request->content);
        $comment->ip_address = $request->ip();
        $comment->user_agent = $request->userAgent();
        $comment->referer = $request->header('referer');
        $comment->metadata = $metadata;
        $comment->parent_id = $request->parent_id;

        // Auto-approve if email is from admin or trusted domain
        $comment->status = $this->determineInitialStatus($request);

        $comment->save();

        // Log for moderation
        $this->logNewComment($comment);

        return $comment;
    }

    /**
     * Sanitize comment content
     */
    private function sanitizeContent(string $content): string
    {
        // Remove HTML tags
        $content = strip_tags($content);

        // Trim and normalize spaces
        $content = preg_replace('/\s+/', ' ', trim($content));

        return $content;
    }

    /**
     * Determine initial status based on rules
     */
    private function determineInitialStatus(StoreCommentRequest $request): string
    {
        if ($this->isLikelySpam($request)) {
            return Comment::STATUS_SPAM;
        }

        // Auto-approve if email already has approved comments
        if ($request->email) {
            $hasApprovedBefore = Comment::where('email', $request->email)
                ->where('status', Comment::STATUS_APPROVED)
                ->exists();

            if ($hasApprovedBefore) {
                return Comment::STATUS_APPROVED;
            }
        }

        return Comment::STATUS_PENDING;
    }

    /**
     * Basic spam detection
     */
    private function isLikelySpam(StoreCommentRequest $request): bool
    {
        $content = strtolower($request->content);

        // Check for multiple links
        $linkCount = preg_match_all('/https?:\/\//', $content);
        if ($linkCount > 2) {
            return true;
        }

        // Check for repeated characters
        if (preg_match('/(.)\\1{10,}/', $content)) {
            return true;
        }

        // Too many comments from same IP in short time
        $recentCount = Comment::where('ip_address', $request->ip())
            ->where('created_at', '>=', now()->subMinutes(5))
            ->count();

        if ($recentCount > 5) {
            return true;
        }

        // Check for common spam keywords
        $spamKeywords = ['viagra', 'casino', 'cialis', 'porn', 'sex'];
        foreach ($spamKeywords as $keyword) {
            if (str_contains($content, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Log new comment for monitoring
     */
    private function logNewComment(Comment $comment): void
    {
        Log::info('New comment submitted', [
            'comment_id' => $comment->id,
            'article_id' => $comment->commentable_id,
            'ip' => $comment->ip_address,
            'status' => $comment->status,
            'email' => $comment->email,
        ]);
    }

    /**
     * Moderate comment
     */
    public function moderate(int $commentId, string $status): bool
    {
        $comment = Comment::findOrFail($commentId);

        $allowedStatuses = [
            Comment::STATUS_APPROVED,
            Comment::STATUS_SPAM,
            Comment::STATUS_TRASH,
        ];

        if (!in_array($status, $allowedStatuses)) {
            return false;
        }

        $comment->status = $status;
        return $comment->save();
    }

    /**
     * Get pending comments count
     */
    public function getPendingCount(): int
    {
        return Comment::pending()->count();
    }
}
