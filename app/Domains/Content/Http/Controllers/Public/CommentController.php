<?php

namespace App\Domains\Content\Http\Controllers\Public;

use App\Domains\Content\Services\CommentService;
use App\Domains\Content\Http\Requests\StoreCommentRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct(
        private readonly CommentService $commentService
    ) {}

    /**
     * Get comments for an article
     */
    public function index(Request $request, $articleId): JsonResponse
    {
        $article = is_numeric($articleId)
            ? \App\Domains\Content\Models\Article::findOrFail($articleId)
            : \App\Domains\Content\Models\Article::where('slug', $articleId)->firstOrFail();

        $comments = $this->commentService->getForArticle($article->id, false);
        // contador de comentários incluido os filhos
        $count_comments = \App\Domains\Content\Models\Comment::forArticleCount($article->id)->count();

        return response()->json([
            'success' => true,
            'data' => $comments,
            'count_comments' => $count_comments,
        ]);
    }

    /**
     * Store a new comment
     */
    public function store(StoreCommentRequest $request): JsonResponse
    {
        try {
            $key = 'comment-ip-' . $request->ip();

            if (cache()->has($key)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aguarde antes de enviar outro comentário.'
                ], 429);
            }

            cache()->put($key, true, 30); // 30 segundos

            $comment = $this->commentService->store($request);

            $message = $comment->isApproved()
                ? 'Comentário publicado com sucesso!'
                : 'Comentário enviado para aprovação. Obrigado!';

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'id' => $comment->id,
                    'status' => $comment->status,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar comentário. Tente novamente.',
            ], 500);
        }
    }
}
