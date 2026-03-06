<?php

namespace App\Domains\Content\Http\Controllers\Public;

use App\Domains\Content\Models\Article;
use App\Domains\Content\Models\Category;
use App\Domains\Content\Services\ArticleService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function __construct(
        private readonly ArticleService $articleService
    ) {}

    /**
     * Lista de conteúdo por tipo
     */
    public function index(Request $request, ?string $type = null): View
    {
        $category = $request->get('category', 'all');
        
        // Tipos válidos
        $validTypes = ['article', 'video', 'newspaper'];
        $type = $type && in_array($type, $validTypes) ? $type : null;
        
        // Títulos e descrições por tipo
        $typeData = [
            'article' => [
                'title' => 'Artigos',
                'description' => 'Investigação, análises e reflexões sobre o Islão, a comunidade e a sociedade.',
                'icon' => 'book-open',
                'empty_message' => 'Nenhum artigo encontrado',
                'empty_submessage' => 'Tente outra categoria ou pesquisa.'
            ],
            'video' => [
                'title' => 'Vídeos',
                'description' => 'Palestras, khutbahs, séries e documentários sobre o Islão.',
                'icon' => 'play',
                'empty_message' => 'Nenhum vídeo encontrado',
                'empty_submessage' => 'Vídeos em breve nesta categoria.'
            ],
            'newspaper' => [
                'title' => 'Jornais',
                'description' => 'Leia todas as edições do Espaço Islâmico directamente no site, sem necessidade de download.',
                'icon' => 'file-text',
                'empty_message' => 'Nenhuma edição disponível',
                'empty_submessage' => 'As edições serão publicadas em breve.',
                'badge' => 'Semanário Digital'
            ]
        ];

        $currentType = $type ?? 'article';
        $pageTitle = $typeData[$currentType]['title'];
        
        // Query base
        $query = Article::visible()
            ->with(['author', 'categories', 'tags'])
            // ->when($type, function ($q) use ($type) {
            //     return $q->where('type', $type);
            // })
            ->byType($type ?? 'article')
            ->filterByCategory($category);

        // Ordenação diferente para jornais
        if ($type === 'newspaper') {
            $query->orderBy('published_at', 'desc');
        } else {
            $query->latest('published_at');
        }

        $items = $query->paginate(12);

        // Categorias específicas por tipo
        $categories = $this->getCategoriesByType($type);

        return view('public.content.index', [
            'items' => $items,
            'type' => $currentType,
            'typeData' => $typeData[$currentType],
            'pageTitle' => $pageTitle,
            'categories' => $categories,
            'selectedCategory' => $request->get('category', 'all'),
            'searchQuery' => $request->get('search', '')
        ]);
    }

    /**
     * Exibir um item específico
     */
    public function show(string $type, string $slug): View
    {
        $article = Article::visible()
            ->with(['author', 'categories', 'tags'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Incrementar visualização
        $this->articleService->incrementViews($article);

        // Itens relacionados
        $relatedItems = Article::visible()
            ->where('id', '!=', $article->id)
            ->where('type', $article->type)
            ->whereHas('categories', function ($query) use ($article) {
                $query->whereIn('categories.id', $article->categories->pluck('id'));
            })
            ->with(['author', 'categories'])
            ->limit(3)
            ->get();

        return view('public.content.show', [
            'item' => $article,
            'relatedItems' => $relatedItems,
            'type' => $article->type
        ]);
    }

    /**
     * Obter categorias por tipo de conteúdo
     */
    private function getCategoriesByType(?string $type): array
    {
        $categories = Category::active()
            ->forContentType($type ?? 'article')
            ->ordered()
            ->get()
            ->keyBy('slug')
            ->toArray();

        $categories = array_map(function ($cat) {
            return [
                'id' => $cat['id'],
                'name' => $cat['name'],
                'slug' => $cat['slug'],
            ];
        }, $categories);

        $CategoriesIncludingAll = array_merge([
            [
                'id' => 'all',
                'name' => 'Todas',
                'slug' => 'all',
            ]
        ], $categories);

        return $CategoriesIncludingAll;
    }
}