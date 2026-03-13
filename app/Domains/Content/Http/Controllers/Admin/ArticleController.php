<?php

namespace App\Domains\Content\Http\Controllers\Admin;

use App\Domains\Content\Models\Article;
use App\Domains\Content\Models\Category;
use App\Domains\Content\Enums\ContentStatus;
use App\Domains\Content\Services\ArticleService;
use App\Domains\Media\Services\MediaService;
use App\Domains\Content\Http\Requests\ArticleRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function __construct(
        private readonly ArticleService $articleService,
        private readonly MediaService $mediaService
    ) {}

    /**
     * Lista de artigos
     */
    public function index(Request $request): View
    {
        $this->authorize('view articles', Article::class);

        $query = Article::with(['author', 'categories']);

        // Filtros
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('excerpt', 'like', '%' . $request->search . '%')
                    ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->whereHas('categories', fn($q) => $q->where('categories.id', $request->category_id));
        }

        if ($request->filled('author_id')) {
            $query->where('author_id', $request->author_id);
        }

        // Autores só veem seus próprios artigos
        if (Auth::user()->hasRole('author')) {
            $query->where('author_id', Auth::id());
        }

        // Ordenação
        $sortField = $request->get('sort_field', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $articles = $query->paginate(15)->withQueryString();

        // Estatísticas
        $stats = [
            'total' => Article::count(),
            'published' => Article::where('status', ContentStatus::PUBLISHED)->count(),
            'draft' => Article::where('status', ContentStatus::DRAFT)->count(),
            'archived' => Article::where('status', ContentStatus::ARCHIVED)->count(),
        ];

        $categories = Category::orderBy('name')->get();
        $users = Auth::user()->can('view any articles')
            ? \App\Models\User::orderBy('name')->get(['id', 'name'])
            : null;

        return view('admin.articles.index', compact(
            'articles',
            'stats',
            'categories',
            'users'
        ));
    }

    /**
     * Formulário de criação
     */
    public function create(): View
    {
        $this->authorize('create articles', Article::class);

        $categories = Category::orderBy('name')->get();
        $contentTypes = [
            ['value' => 'article', 'label' => 'Artigo'],
            ['value' => 'video', 'label' => 'Vídeo'],
            ['value' => 'newspaper', 'label' => 'Jornal'],
            ['value' => 'news', 'label' => 'Notícia'],
        ];
        $statusOptions = [
            ['value' => 'draft', 'label' => 'Rascunho'],
            ['value' => 'published', 'label' => 'Publicar'],
            ['value' => 'scheduled', 'label' => 'Agendar'],
        ];

        $article = null;

        return view('admin.articles.form', compact(
            'article',
            'categories',
            'contentTypes',
            'statusOptions'
        ));
    }

    /**
     * Armazenar novo artigo
     */
    public function store(ArticleRequest $request): RedirectResponse
    {
        $this->authorize('create articles', Article::class);

        try {
            DB::transaction(function () use ($request) {
                $data = $this->prepareData($request);
                $data['author_id'] = Auth::id();

                $article = Article::create($data);

                // Sincronizar categorias
                if ($request->has('categories')) {
                    $article->categories()->sync($request->categories);
                }

                // Upload de imagem
                if ($request->hasFile('featured_image')) {
                    $this->uploadFeaturedImage($article, $request->file('featured_image'));
                }

                // Upload de PDF (para newspaper)
                if ($request->hasFile('pdf_file')) {
                    // $this->uploadPdf($article, $request->file('pdf_file'));
                    Log::info('Upload de PDF para novo artigo');
                }

                // Guardar artigo na request para usar após o transaction
                $request->session()->put('created_article_id', $article->id);
            });

            $articleId = $request->session()->get('created_article_id');
            $request->session()->forget('created_article_id');

            return redirect()
                ->route('admin.articles.show', $articleId)
                ->with('success', 'Artigo criado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao criar artigo', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erro ao criar artigo: ' . $e->getMessage());
        }
    }

    /**
     * Visualizar artigo
     */
    public function show(Article $article): View
    {
        $this->authorize('view articles', $article);

        $article->load(['author', 'categories', 'tags', 'comments' => function ($q) {
            $q->whereNull('parent_id')->with('replies')->latest();
        }]);

        // Estatísticas de comentários
        $stats = [
            'total_comments' => $article->comments()->count(),
            'approved_comments' => $article->comments()->approved()->count(),
            'pending_comments' => $article->comments()->pending()->count(),
            'spam_comments' => $article->comments()->spam()->count(),
            'views' => $article->views_count ?? 0,
        ];

        return view('admin.articles.show', compact('article', 'stats'));
    }

    /**
     * Formulário de edição
     */
    public function edit(Article $article): View
    {
        $this->authorize('edit any articles', $article);

        $categories = Category::orderBy('name')->get();
        $contentTypes = [
            ['value' => 'article', 'label' => 'Artigo'],
            ['value' => 'video', 'label' => 'Vídeo'],
            ['value' => 'newspaper', 'label' => 'Jornal'],
            ['value' => 'news', 'label' => 'Notícia'],
        ];
        $statusOptions = [
            ['value' => 'draft', 'label' => 'Rascunho'],
            ['value' => 'published', 'label' => 'Publicado'],
            ['value' => 'scheduled', 'label' => 'Agendado'],
            ['value' => 'archived', 'label' => 'Arquivado'],
        ];

        // Carregar mídia existente
        $featuredImage = $article->getFirstMedia('featured_image');
        $pdfFile = $article->getFirstMedia('documents');

        return view('admin.articles.form', compact(
            'article',
            'categories',
            'contentTypes',
            'statusOptions',
            'featuredImage',
            'pdfFile'
        ));
    }

    /**
     * Atualizar artigo
     */
    public function update(ArticleRequest $request, Article $article): RedirectResponse
    {
        $this->authorize('edit any articles', $article);

        try {
            DB::transaction(function () use ($request, $article) {
                $data = $this->prepareData($request);

                $article->update($data);

                // Sincronizar categorias
                $article->categories()->sync($request->categories ?? []);

                // Upload de nova imagem
                if ($request->hasFile('featured_image')) {
                    // Remover imagem antiga
                    if ($article->hasMedia('featured_image')) {
                        $article->clearMediaCollection('featured_image');
                    }
                    $this->uploadFeaturedImage($article, $request->file('featured_image'));
                }

                // Remover imagem (se solicitado)
                if ($request->has('remove_featured_image') && $request->remove_featured_image) {
                    $article->clearMediaCollection('featured_image');
                }

                // Upload de novo PDF
                if ($request->hasFile('pdf_file')) {
                    if ($article->hasMedia('documents')) {
                        $article->clearMediaCollection('documents');
                    }
                    $this->uploadPdf($article, $request->file('pdf_file'));
                }

                // Remover PDF (se solicitado)
                if ($request->has('remove_pdf') && $request->remove_pdf) {
                    $article->clearMediaCollection('documents');
                }
            });

            return redirect()
                ->route('admin.articles.show', $article)
                ->with('success', 'Artigo atualizado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar artigo', [
                'article_id' => $article->id,
                'error' => $e->getMessage()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erro ao atualizar artigo: ' . $e->getMessage());
        }
    }

    /**
     * Publicar artigo
     */
    public function publish(Article $article): RedirectResponse
    {
        $this->authorize('publish articles', $article);

        if (!$article->canBePublished()) {
            return redirect()
                ->back()
                ->with('error', 'Não é possível publicar este artigo. Verifique se todos os campos obrigatórios estão preenchidos.');
        }

        $article->update([
            'status' => ContentStatus::PUBLISHED,
            'published_at' => now()
        ]);

        return redirect()
            ->back()
            ->with('success', 'Artigo publicado com sucesso!');
    }

    /**
     * Arquivar artigo
     */
    public function archive(Article $article): RedirectResponse
    {
        $this->authorize('archive articles', $article);

        $article->update(['status' => ContentStatus::ARCHIVED]);

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'Artigo arquivado com sucesso!');
    }

    /**
     * Restaurar artigo
     */
    public function restore(Article $article): RedirectResponse
    {
        $this->authorize('restore articles', $article);

        $article->update(['status' => ContentStatus::DRAFT]);

        return redirect()
            ->route('admin.articles.edit', $article)
            ->with('success', 'Artigo restaurado com sucesso!');
    }

    /**
     * Deletar artigo (soft delete)
     */
    public function destroy(Article $article): RedirectResponse
    {
        $this->authorize('delete any articles', $article);

        $article->comments()->delete();
        $article->delete();

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'Artigo movido para a lixeira!');
    }

    /**
     * Preparar dados do request
     */
    private function prepareData(Request $request): array
    {
        $data = [
            'type' => $request->type,
            'title' => $request->title,
            'slug' => $request->slug ?: Str::slug($request->title),
            'excerpt' => $request->excerpt,
            'content' => in_array($request->type, ['article', 'news']) ? $request->content : null,
            'youtube_url' => $request->type === 'video' ? $request->youtube_url : null,
            'edition' => $request->type === 'newspaper' ? $request->edition : null,
            'is_sellable' => $request->boolean('is_sellable'),
            'price' => $request->is_sellable ? $request->price : null,
            'whatsapp_number' => $request->is_sellable ? $request->whatsapp_number : null,
            'status' => $request->status,
            'published_at' => $request->status === 'scheduled' ? $request->published_at : now(),
        ];

        // SEO fields (se existirem)
        if ($request->has('seo_title')) {
            $data['seo_title'] = $request->seo_title;
        }
        if ($request->has('seo_description')) {
            $data['seo_description'] = $request->seo_description;
        }
        if ($request->has('seo_keywords')) {
            $data['seo_keywords'] = $request->seo_keywords;
        }

        return $data;
    }

    /**
     * Upload de imagem de destaque
     */
    private function uploadFeaturedImage(Article $article, $file): void
    {
        $article->addMedia($file)
            ->withCustomProperties([
                'uploaded_by' => Auth::id(),
                'uploaded_at' => now()->toDateTimeString(),
            ])
            ->toMediaCollection('featured_image');
    }

    /**
     * Upload de PDF
     */
    private function uploadPdf(Article $article, $file): void
    {
        $article->addMedia($file)
            ->withCustomProperties([
                'uploaded_by' => Auth::id(),
                'uploaded_at' => now()->toDateTimeString(),
                'edition' => request()->edition,
            ])
            ->toMediaCollection('documents');
    }
}
