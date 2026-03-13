<?php

namespace App\Domains\Content\Http\Controllers\Admin;

use App\Domains\Content\Models\Article;
use App\Domains\Content\Models\Comment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    /**
     * Aprovar comentário
     */
    public function approve(Article $article, Comment $comment): RedirectResponse
    {
        $this->authorize('moderate comments', $comment);

        $comment->markAsApproved();

        return redirect()
            ->back()
            ->with('success', 'Comentário aprovado com sucesso!');
    }

    /**
     * Marcar como spam
     */
    public function markAsSpam(Article $article, Comment $comment): RedirectResponse
    {
        $this->authorize('moderate comments', $comment);

        $comment->markAsSpam();

        return redirect()
            ->back()
            ->with('success', 'Comentário marcado como spam!');
    }

    /**
     * Mover para lixeira
     */
    public function destroy(Article $article, Comment $comment): RedirectResponse
    {
        $this->authorize('moderate comments', $comment);

        $comment->delete();

        return redirect()
            ->back()
            ->with('success', 'Comentário movido para a lixeira!');
    }

    /**
     * Ações em massa
     */
    public function bulkAction(Request $request, Article $article): RedirectResponse
    {
        $request->validate([
            'comments' => 'required|array',
            'comments.*' => 'exists:comments,id',
            'action' => 'required|in:approve,spam,delete'
        ]);

        $comments = Comment::whereIn('id', $request->comments)->get();
        $successCount = 0;

        foreach ($comments as $comment) {
            try {
                switch ($request->action) {
                    case 'approve':
                        if (Auth::user()->can('moderate', $comment)) {
                            $comment->markAsApproved();
                            $successCount++;
                        }
                        break;
                    case 'spam':
                        if (Auth::user()->can('moderate', $comment)) {
                            $comment->markAsSpam();
                            $successCount++;
                        }
                        break;
                    case 'delete':
                        if (Auth::user()->can('delete', $comment)) {
                            $comment->delete();
                            $successCount++;
                        }
                        break;
                }
            } catch (\Exception $e) {
                Log::error('Erro em ação em massa de comentários', [
                    'comment_id' => $comment->id,
                    'action' => $request->action,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return redirect()
            ->back()
            ->with('success', "{$successCount} comentário(s) processado(s) com sucesso!");
    }
}
