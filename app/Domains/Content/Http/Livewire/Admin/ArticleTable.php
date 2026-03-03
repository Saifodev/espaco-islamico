<?php

namespace App\Domains\Content\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Domains\Content\Models\Article;
use App\Domains\Content\Enums\ContentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ArticleTable extends Component
{
    use WithPagination;

    // Campos de filtro
    public string $search = '';
    public ?string $status = null;
    public ?int $authorId = null;
    
    // Campos para armazenar os valores quando o filtro for aplicado
    public string $appliedSearch = '';
    public ?string $appliedStatus = null;
    public ?int $appliedAuthorId = null;
    
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    
    protected $queryString = [
        'appliedSearch' => ['as' => 'search', 'except' => ''],
        'appliedStatus' => ['as' => 'status', 'except' => ''],
        'appliedAuthorId' => ['as' => 'author', 'except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function mount()
    {
        // Inicializa os valores aplicados com os valores atuais
        $this->appliedSearch = $this->search;
        $this->appliedStatus = $this->status;
        $this->appliedAuthorId = $this->authorId;
    }

    public function applyFilters()
    {
        // Aplica os filtros apenas quando o botão for clicado
        $this->appliedSearch = $this->search;
        $this->appliedStatus = $this->status;
        $this->appliedAuthorId = $this->authorId;
        
        $this->resetPage();
    }

    public function clearFilters()
    {
        // Limpa todos os filtros
        $this->search = '';
        $this->status = null;
        $this->authorId = null;
        
        $this->appliedSearch = '';
        $this->appliedStatus = null;
        $this->appliedAuthorId = null;
        
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
    }

    public function render()
    {
        $query = Article::query()
            ->with(['author', 'categories'])
            ->when($this->appliedSearch, function (Builder $query) {
                $query->search($this->appliedSearch);
            })
            ->when($this->appliedStatus, function (Builder $query) {
                $query->where('status', $this->appliedStatus);
            })
            ->when($this->appliedAuthorId, function (Builder $query) {
                $query->where('author_id', $this->appliedAuthorId);
            })
            ->orderBy($this->sortField, $this->sortDirection);

        // Autores só veem seus próprios artigos
        if (Auth::user()->hasRole('author')) {
            $query->where('author_id', Auth::id());
        }

        $articles = $query->paginate(15);
        $statuses = collect(ContentStatus::cases())
            ->map(fn($status) => [
                'value' => $status->value,
                'label' => $status->label(),
                'color' => $status->color()
            ]);

        $users = null;
        if (Auth::user()->can('view any articles')) {
            $users = \App\Models\User::orderBy('name')->get();
        }

        return view('livewire.admin.article-table', [
            'articles' => $articles,
            'statuses' => $statuses,
            'users' => $users,
            'hasActiveFilters' => $this->appliedSearch !== '' || 
                                  $this->appliedStatus !== null || 
                                  $this->appliedAuthorId !== null,
        ]);
    }

    // Métodos de ação (archive, publish, delete)
    public function archive($id)
    {
        $article = Article::findOrFail($id);
        $this->authorize('archive', $article);
        
        // Implementar lógica de arquivamento
        // $this->articleService->archive($article, Auth::user());
        
        session()->flash('success', 'Artigo arquivado com sucesso!');
    }

    public function publish($id)
    {
        $article = Article::findOrFail($id);
        $this->authorize('publish', $article);
        
        // Implementar lógica de publicação
        // $this->articleService->publish($article, Auth::user());
        
        session()->flash('success', 'Artigo publicado com sucesso!');
    }

    public function delete($id)
    {
        $article = Article::findOrFail($id);
        $this->authorize('delete', $article);
        
        $article->delete();
        
        session()->flash('success', 'Artigo movido para a lixeira!');
    }
}