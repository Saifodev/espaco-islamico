<?php

namespace App\Domains\Media\Actions;

use App\Domains\Content\Models\Article;
use App\Domains\Media\Services\MediaService;
use App\Domains\Media\Enums\MediaCollectionType;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

class UploadGalleryImagesAction
{
    public function __construct(
        private readonly MediaService $mediaService
    ) {}

    /**
     * Executar upload de múltiplas imagens
     */
    public function execute(Article $article, array $files): array
    {
        $uploadedMedia = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $media = $this->mediaService->upload(
                    model: $article,
                    file: $file,
                    collection: MediaCollectionType::GALLERY,
                    customProperties: [
                        'uploaded_by' => Auth::id(),
                        'uploaded_at' => now()->toIso8601String(),
                    ],
                    preserveOriginal: true // Adicionar este parâmetro se o MediaService suportar
                );
                
                $uploadedMedia[] = $media;
            }
        }

        return $uploadedMedia;
    }
}