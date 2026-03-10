<?php

namespace App\Domains\Content\Services;

use App\Domains\Content\Models\Article;
use App\Domains\Content\Enums\ContentStatus;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ArticleService
{
    /**
     * Criar um novo
     */
    public function create(array $data, User $author): Article
    {
        return DB::transaction(function () use ($data, $author) {
            $data['author_id'] = $author->id;
            
            // Se for publicado imediatamente, define published_at
            if (($data['status'] ?? null) === ContentStatus::PUBLISHED->value) {
                $data['published_at'] = $data['published_at'] ?? now();
            }

            $article = Article::create($data);

            // Sincronizar relações
            if (isset($data['categories'])) {
                $article->categories()->sync($data['categories']);
            }

            if (isset($data['tags'])) {
                $article->tags()->sync($data['tags']);
            }

            $this->clearCache();

            Log::info('Artigo criado', [
                'article_id' => $article->id,
                'title' => $article->title,
                'author_id' => $author->id
            ]);

            return $article;
        });
    }

    /**
     * Atualizar um artigo existente
     */
    public function update(Article $article, array $data): Article
    {
        return DB::transaction(function () use ($article, $data) {
            // Se status mudou para published e não tem published_at, define agora
            if (($data['status'] ?? null) === ContentStatus::PUBLISHED->value && 
                !$article->published_at && 
                !isset($data['published_at'])) {
                $data['published_at'] = now();
            }

            $article->update($data);

            // Sincronizar relações
            if (isset($data['categories'])) {
                $article->categories()->sync($data['categories']);
            }

            if (isset($data['tags'])) {
                $article->tags()->sync($data['tags']);
            }

            $this->clearCache();

            Log::info('Artigo atualizado', [
                'article_id' => $article->id,
                'title' => $article->title
            ]);

            return $article;
        });
    }

    /**
     * Publicar um artigo manualmente
     */
    public function publish(Article $article, ?User $user = null): Article
    {
        if ($article->status === ContentStatus::PUBLISHED) {
            return $article;
        }

        $article->update([
            'status' => ContentStatus::PUBLISHED,
            'published_at' => $article->published_at ?? now(),
        ]);

        $this->clearCache();

        Log::info('Artigo publicado', [
            'article_id' => $article->id,
            'title' => $article->title,
            'user_id' => $user?->id
        ]);

        return $article;
    }

    /**
     * Arquivar um artigo
     */
    public function archive(Article $article, ?User $user = null): Article
    {
        if ($article->status === ContentStatus::ARCHIVED) {
            return $article;
        }

        $article->update([
            'status' => ContentStatus::ARCHIVED,
        ]);

        $this->clearCache();

        Log::info('Artigo arquivado', [
            'article_id' => $article->id,
            'title' => $article->title,
            'user_id' => $user?->id
        ]);

        return $article;
    }

    /**
     * Restaurar artigo arquivado
     */
    public function restore(Article $article, ?User $user = null): Article
    {
        if ($article->status !== ContentStatus::ARCHIVED) {
            return $article;
        }

        // Restaura para rascunho
        $article->update([
            'status' => ContentStatus::DRAFT,
        ]);

        $this->clearCache();

        Log::info('Artigo restaurado', [
            'article_id' => $article->id,
            'title' => $article->title,
            'user_id' => $user?->id
        ]);

        return $article;
    }

    /**
     * Agendar artigo para publicação futura
     */
    public function schedule(Article $article, string $publishAt, ?User $user = null): Article
    {
        $article->update([
            'status' => ContentStatus::SCHEDULED,
            'published_at' => $publishAt,
        ]);

        $this->clearCache();

        Log::info('Artigo agendado', [
            'article_id' => $article->id,
            'title' => $article->title,
            'published_at' => $publishAt,
            'user_id' => $user?->id
        ]);

        return $article;
    }

    /**
     * Incrementar contador de visualizações
     */
    public function incrementViews(Article $article): void
    {
        $article->increment('views_count');
    }

    /**
     * Limpar cache relacionado a artigos
     */
    private function clearCache(): void
    {
        // Cache::tags(['articles'])->flush();
        Cache::flush();
    }

    /**
     * Processar artigos agendados (chamado pelo scheduler)
     */
    public function processScheduledArticles(int $limit = 100): int
    {
        $count = 0;
        
        Article::scheduled()
            ->where('published_at', '<=', now())
            ->limit($limit)
            ->chunk(100, function ($articles) use (&$count) {
                foreach ($articles as $article) {
                    $this->publish($article);
                    $count++;
                }
            });

        if ($count > 0) {
            Log::info('Artigos agendados publicados', ['count' => $count]);
        }

        return $count;
    }
}