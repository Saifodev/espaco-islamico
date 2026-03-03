<?php

namespace App\Domains\Media\Actions;

use App\Domains\Content\Models\Article;
use App\Domains\Media\Services\MediaService;
use Exception;

class RemoveMediaAction
{
    public function __construct(
        private readonly MediaService $mediaService
    ) {}

    /**
     * Executar remoção de mídia
     */
    public function execute(Article $article, int $mediaId): void
    {
        try {
            $this->mediaService->remove($article, $mediaId);
        } catch (Exception $e) {
            throw new Exception("Erro ao remover mídia: " . $e->getMessage());
        }
    }
}