<?php

namespace App\Domains\Content\Http\Controllers\Admin;

use App\Domains\Content\Models\Article;
use App\Domains\Content\Models\Category;
use App\Domains\Content\Models\Tag;
use App\Domains\Content\Services\ArticleService;
use App\Domains\Content\Http\Requests\ArticleRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    public function __construct(
        private readonly ArticleService $articleService
    ) {}

    /**
     * Lista de artigos (admin)
     */
    public function index(): View
    {
        $this->authorize('viewAny', Article::class);

        return view('admin.articles.index');
    }

    /**
     * Formulário de criação
     */
    public function create(): View
    {
        $this->authorize('create', Article::class);

        $categories = Category::ordered()->get();
        $tags = Tag::orderBy('name')->get();

        return view('admin.articles.form', compact('categories', 'tags'));
    }

    /**
     * Armazenar novo
     */
    public function store(ArticleRequest $request): RedirectResponse
    {
        $this->authorize('create', Article::class);

        $article = $this->articleService->create(
            $request->validated(),
            $request->user()
        );

        return redirect()
            ->route('admin.articles.edit', $article)
            ->with('success', 'Artigo criado com sucesso!');
    }

    /**
     * Formulário de edição
     */
    public function edit(Article $article): View
    {
        $this->authorize('update', $article);

        $categories = Category::ordered()->get();
        $tags = Tag::orderBy('name')->get();

        return view('admin.articles.form', compact('article', 'categories', 'tags'));
    }

    /**
     * Atualizar artigo
     */
    public function update(ArticleRequest $request, Article $article): RedirectResponse
    {
        $this->authorize('update', $article);

        $article = $this->articleService->update($article, $request->validated());

        return redirect()
            ->route('admin.articles.edit', $article)
            ->with('success', 'Artigo atualizado com sucesso!');
    }

    /**
     * Publicar artigo
     */
    public function publish(Article $article): RedirectResponse
    {
        $this->authorize('publish', $article);

        $this->articleService->publish($article, Auth::user());

        return redirect()
            ->back()
            ->with('success', 'Artigo publicado com sucesso!');
    }

    /**
     * Arquivar artigo
     */
    public function archive(Article $article): RedirectResponse
    {
        $this->authorize('archive', $article);

        $this->articleService->archive($article, Auth::user());

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'Artigo arquivado com sucesso!');
    }

    /**
     * Deletar artigo (soft delete)
     */
    public function destroy(Article $article): RedirectResponse
    {
        $this->authorize('delete', $article);

        $article->delete();

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'Artigo movido para a lixeira!');
    }
}