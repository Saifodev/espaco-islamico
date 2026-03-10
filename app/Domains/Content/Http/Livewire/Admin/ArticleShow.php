<?php

namespace App\Domains\Content\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Domains\Content\Models\Article;
use App\Domains\Content\Models\Comment;
use App\Domains\Content\Services\CommentService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ArticleShow extends Component
{
    use WithPagination, AuthorizesRequests;

    public Article $article;
    
    // Filtros de comentários
    public string $commentStatus = 'all';
    public string $commentSearch = '';
    
    // Gerenciamento de comentários
    public ?int $editingCommentId = null;
    public string $editingContent = '';
    public ?int $replyingToId = null;
    public string $replyContent = '';
    
    // Confirmações
    public bool $confirmingBulkAction = false;
    public array $selectedComments = [];
    public ?string $bulkActionType = null;
    public bool $selectAll = false;
    
    // Estatísticas
    public array $stats = [];
    
    protected $queryString = [
        'commentStatus' => ['except' => 'all'],
        'commentSearch' => ['except' => ''],
    ];

    protected function getListeners()
    {
        return [
            'comment-approved' => '$refresh',
            'comment-moderated' => '$refresh',
        ];
    }

    public function mount(Article $article)
    {
        $this->article = $article->load(['author', 'categories', 'tags']);
        $this->authorize('view', $article);
        $this->loadStats();
    }

    public function loadStats()
    {
        $this->stats = [
            'total_comments' => $this->article->comments()->count(),
            'approved_comments' => $this->article->comments()->approved()->count(),
            'pending_comments' => $this->article->comments()->pending()->count(),
            'spam_comments' => $this->article->comments()->spam()->count(),
            'views' => $this->article->views_count,
            'reading_time' => $this->article->reading_time_in_minutes,
        ];
    }

    public function getCommentsProperty()
    {
        $query = $this->article->comments()
            ->with('replies')
            ->whereNull('parent_id')
            ->latest();

        // Filtro por status
        if ($this->commentStatus !== 'all') {
            $query->where('status', $this->commentStatus);
        }

        // Busca
        if ($this->commentSearch) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->commentSearch . '%')
                    ->orWhere('email', 'like', '%' . $this->commentSearch . '%')
                    ->orWhere('content', 'like', '%' . $this->commentSearch . '%');
            });
        }

        return $query->paginate(20);
    }

    public function approveComment($commentId)
    {
        $comment = Comment::findOrFail($commentId);
        $this->authorize('moderate', $comment);
        
        $comment->markAsApproved();
        $this->loadStats();
        $this->dispatch('notify', [
            'message' => 'Comentário aprovado com sucesso!',
            'type' => 'success'
        ]);
    }

    public function markAsSpam($commentId)
    {
        Log::info('Attempting to mark comment as spam', ['comment_id' => $commentId]);
        $comment = Comment::findOrFail($commentId);
        $this->authorize('moderate', $comment);
        
        $comment->markAsSpam();
        $this->loadStats();
        $this->dispatch('notify', [
            'message' => 'Comentário marcado como spam!',
            'type' => 'warning'
        ]);
    }

    public function moveToTrash($commentId)
    {
        Log::info('Attempting to move comment to trash', ['comment_id' => $commentId]);
        $comment = Comment::findOrFail($commentId);
        $this->authorize('delete', $comment);
        
        $comment->delete();
        $this->selectedComments = array_diff($this->selectedComments, [$commentId]);
        $this->loadStats();
        $this->dispatch('notify', [
            'message' => 'Comentário movido para a lixeira!',
            'type' => 'info'
        ]);
    }

    public function startEdit($commentId)
    {
        $comment = Comment::findOrFail($commentId);
        $this->authorize('update', $comment);
        
        $this->editingCommentId = $commentId;
        $this->editingContent = $comment->content;
    }

    public function cancelEdit()
    {
        $this->editingCommentId = null;
        $this->editingContent = '';
    }

    public function updateComment()
    {
        $this->validate([
            'editingContent' => 'required|min:3|max:1000',
        ]);

        $comment = Comment::findOrFail($this->editingCommentId);
        $this->authorize('update', $comment);
        
        $comment->update([
            'content' => $this->editingContent,
        ]);

        $this->cancelEdit();
        
        $this->dispatch('notify', [
            'message' => 'Comentário atualizado com sucesso!',
            'type' => 'success'
        ]);
    }

    public function startReply($commentId)
    {
        $this->replyingToId = $commentId;
        $this->replyContent = '';
    }

    public function cancelReply()
    {
        $this->replyingToId = null;
        $this->replyContent = '';
    }

    public function submitReply()
    {
        $this->validate([
            'replyContent' => 'required|min:3|max:1000',
        ]);

        $parent = Comment::findOrFail($this->replyingToId);
        $this->authorize('create', [Comment::class, $parent->commentable]);
        
        $comment = new Comment();
        $comment->commentable()->associate($this->article);
        $comment->name = Auth::user()->name;
        $comment->email = Auth::user()->email;
        $comment->content = $this->replyContent;
        $comment->ip_address = request()->ip();
        $comment->user_agent = request()->userAgent();
        $comment->parent_id = $this->replyingToId;
        $comment->status = Comment::STATUS_APPROVED; // Admins sempre aprovados
        $comment->save();

        $this->cancelReply();
        $this->loadStats();
        
        $this->dispatch('notify', [
            'message' => 'Resposta publicada com sucesso!',
            'type' => 'success'
        ]);
    }

    public function confirmBulkAction(string $action)
    {
        if (empty($this->selectedComments)) {
            $this->dispatch('notify', [
                'message' => 'Selecione pelo menos um comentário.',
                'type' => 'warning'
            ]);
            return;
        }

        $this->bulkActionType = $action;
        $this->confirmingBulkAction = true;
    }

    public function executeBulkAction()
    {
        $comments = Comment::whereIn('id', $this->selectedComments)->get();
        
        foreach ($comments as $comment) {
            switch ($this->bulkActionType) {
                case 'approve':
                    $this->authorize('moderate', $comment);
                    $comment->markAsApproved();
                    break;
                case 'spam':
                    $this->authorize('moderate', $comment);
                    $comment->markAsSpam();
                    break;
                case 'delete':
                    $this->authorize('delete', $comment);
                    $comment->delete();
                    break;
            }
        }

        $this->confirmingBulkAction = false;
        $this->bulkActionType = null;
        $this->selectedComments = [];
        $this->selectAll = false;
        $this->loadStats();
        
        $this->dispatch('notify', [
            'message' => count($comments) . ' comentário(s) processado(s) com sucesso!',
            'type' => 'success'
        ]);
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedComments = $this->comments->pluck('id')->toArray();
        } else {
            $this->selectedComments = [];
        }
    }

    public function getHasActiveFiltersProperty(): bool
    {
        return $this->commentStatus !== 'all' || !empty($this->commentSearch);
    }

    public function clearFilters()
    {
        $this->commentStatus = 'all';
        $this->commentSearch = '';
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.admin.article-show', [
            'comments' => $this->comments,
        ])->layout('layouts.admin');
    }
}