<?php
// app/Domains/Content/Http/Livewire/Admin/ArticleTable.php

namespace App\Domains\Content\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Domains\Content\Models\Article;
use App\Domains\Content\Enums\ContentStatus;
use App\Domains\Content\Services\ArticleService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ArticleTable extends Component
{
    use WithPagination;

    // Filtros
    public string $search = '';
    public ?string $status = null;
    public ?int $categoryId = null;
    public ?int $authorId = null;
    public string $dateRange = '';

    // Ordenação
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    // UI State
    public bool $showFilters = false;
    public bool $confirmingAction = false;
    public ?int $selectedArticleId = null;
    public string $actionType = '';
    public array $selectedArticles = [];
    public bool $selectAll = false;

    // Bulk actions
    public bool $showBulkActions = false;
    public string $bulkAction = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'categoryId' => ['except' => ''],
        'authorId' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    protected $listeners = [
        'refreshTable' => '$refresh',
        'articleUpdated' => '$refresh',
    ];

    public function mount()
    {
        // Se for autor, só vê os próprios artigos
        if (Auth::user()->hasRole('author')) {
            $this->authorId = Auth::id();
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatus()
    {
        $this->resetPage();
    }

    public function updatedCategoryId()
    {
        $this->resetPage();
    }

    public function updatedAuthorId()
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->status = null;
        $this->categoryId = null;
        $this->authorId = Auth::user()->hasRole('author') ? Auth::id() : null;
        $this->dateRange = '';
        $this->resetPage();
    }

    // Ações individuais
    public function confirmAction(int $id, string $action)
    {
        $this->selectedArticleId = $id;
        $this->actionType = $action;
        $this->confirmingAction = true;
    }

    public function executeAction()
    {
        $article = Article::find($this->selectedArticleId);

        if (!$article) {
            $this->dispatch('notify', [
                'message' => 'Artigo não encontrado.',
                'type' => 'error'
            ]);
            $this->cancelAction();
            return;
        }

        try {
            switch ($this->actionType) {
                case 'publish':
                    $this->authorize('publish', $article);
                    if (!$article->canBePublished()) {
                        $errors = $article->getPublishErrors();
                        $this->dispatch('notify', [
                            'message' => 'Não é possível publicar: ' . implode(', ', $errors),
                            'type' => 'error'
                        ]);
                        $this->cancelAction();
                        return;
                    }
                    $article->update([
                        'status' => ContentStatus::PUBLISHED,
                        'published_at' => now()
                    ]);
                    $message = 'Artigo publicado com sucesso!';
                    break;

                case 'archive':
                    $this->authorize('archive', $article);
                    $article->update(['status' => ContentStatus::ARCHIVED]);
                    $message = 'Artigo arquivado com sucesso!';
                    break;

                case 'unarchive':
                    $this->authorize('archive', $article);
                    $article->update(['status' => ContentStatus::DRAFT]);
                    $message = 'Artigo restaurado com sucesso!';
                    break;

                case 'delete':
                    $this->authorize('delete', $article);
                    $article->delete();
                    $message = 'Artigo movido para a lixeira!';
                    break;

                default:
                    $this->dispatch('notify', [
                        'message' => 'Ação inválida.',
                        'type' => 'error'
                    ]);
                    $this->cancelAction();
                    return;
            }

            $this->dispatch('notify', [
                'message' => $message,
                'type' => 'success'
            ]);

            $this->dispatch('refreshTable');
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Erro: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }

        $this->cancelAction();
    }

    public function cancelAction()
    {
        $this->confirmingAction = false;
        $this->selectedArticleId = null;
        $this->actionType = '';
    }

    // Bulk actions
    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedArticles = $this->getQuery()->pluck('id')->toArray();
        } else {
            $this->selectedArticles = [];
        }
    }

    public function confirmBulkAction(string $action)
    {
        if (empty($this->selectedArticles)) {
            $this->dispatch('notify', [
                'message' => 'Selecione pelo menos um artigo.',
                'type' => 'warning'
            ]);
            return;
        }

        $this->bulkAction = $action;
        $this->confirmingAction = true;
    }

    public function executeBulkAction()
    {
        if (empty($this->selectedArticles)) {
            $this->cancelAction();
            return;
        }

        $articles = Article::whereIn('id', $this->selectedArticles)->get();
        $successCount = 0;
        $errorMessages = [];

        foreach ($articles as $article) {
            try {
                switch ($this->bulkAction) {
                    case 'publish':
                        if (Auth::user()->can('publish', $article) && $article->canBePublished()) {
                            $article->update([
                                'status' => ContentStatus::PUBLISHED,
                                'published_at' => now()
                            ]);
                            $successCount++;
                        }
                        break;

                    case 'archive':
                        if (Auth::user()->can('archive', $article)) {
                            $article->update(['status' => ContentStatus::ARCHIVED]);
                            $successCount++;
                        }
                        break;

                    case 'delete':
                        if (Auth::user()->can('delete', $article)) {
                            $article->delete();
                            $successCount++;
                        }
                        break;
                }
            } catch (\Exception $e) {
                $errorMessages[] = "Erro no artigo {$article->id}: " . $e->getMessage();
            }
        }

        $message = "{$successCount} artigo(s) processado(s) com sucesso.";
        if (!empty($errorMessages)) {
            $message .= " Erros: " . implode(', ', $errorMessages);
        }

        $this->dispatch('notify', [
            'message' => $message,
            'type' => $successCount > 0 ? 'success' : 'error'
        ]);

        $this->selectedArticles = [];
        $this->selectAll = false;
        $this->cancelAction();
        $this->dispatch('refreshTable');
    }

    protected function getQuery(): Builder
    {
        $query = Article::query()
            ->with(['author', 'categories'])
            ->when($this->search, function (Builder $query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('excerpt', 'like', '%' . $this->search . '%')
                        ->orWhere('content', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->status, function (Builder $query) {
                $query->where('status', $this->status);
            })
            ->when($this->categoryId, function (Builder $query) {
                $query->whereHas('categories', function ($q) {
                    $q->where('categories.id', $this->categoryId);
                });
            })
            ->when($this->authorId, function (Builder $query) {
                $query->where('author_id', $this->authorId);
            });

        // Autores só veem seus próprios artigos (reforçar)
        if (Auth::user()->hasRole('author')) {
            $query->where('author_id', Auth::id());
        }

        return $query->orderBy($this->sortField, $this->sortDirection);
    }

    public function getStatusBadgeClass(string $status): string
    {
        return match ($status) {
            'published' => 'bg-green-100 text-green-800',
            'draft' => 'bg-gray-100 text-gray-800',
            'scheduled' => 'bg-yellow-100 text-yellow-800',
            'archived' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
    
    public function render()
    {
        $articles = $this->getQuery()->paginate(15);

        $stats = [
            'total' => Article::count(),
            'published' => Article::where('status', ContentStatus::PUBLISHED)->count(),
            'draft' => Article::where('status', ContentStatus::DRAFT)->count(),
            'archived' => Article::where('status', ContentStatus::ARCHIVED)->count(),
        ];

        $categories = \App\Domains\Content\Models\Category::orderBy('name')->get();

        $users = null;
        if (Auth::user()->can('view any articles')) {
            $users = \App\Models\User::orderBy('name')->get(['id', 'name']);
        }

        return view('livewire.admin.article-table', [
            'articles' => $articles,
            'stats' => $stats,
            'categories' => $categories,
            'users' => $users,
            'hasActiveFilters' => $this->search !== '' ||
                $this->status !== null ||
                $this->categoryId !== null ||
                $this->authorId !== null,
        ]);
    }
}
