<?php

namespace App\Domains\Media\Actions;

use App\Domains\Content\Models\Article;
use App\Domains\Media\Services\MediaService;
use App\Domains\Media\Enums\MediaCollectionType;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

class UploadFeaturedImageAction
{
    public function __construct(
        private readonly MediaService $mediaService
    ) {}

    /**
     * Executar upload da imagem de destaque
     */
    public function execute(Article $article, UploadedFile $file): void
    {
        // Se artigo já publicado, log de alerta
        if ($article->isPublished) {
            logger()->warning('Substituindo imagem de artigo publicado', [
                'article_id' => $article->id,
                'title' => $article->title,
            ]);
        }

        $this->mediaService->upload(
            model: $article,
            file: $file,
            collection: MediaCollectionType::FEATURED_IMAGE,
            customProperties: [
                'uploaded_by' => Auth::id(),
                'uploaded_at' => now()->toIso8601String(),
                'original_name' => $file->getClientOriginalName(),
            ]
        );
    }
}