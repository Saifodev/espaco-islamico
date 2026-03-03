<?php

namespace App\Domains\Media\Services;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class CustomPathGenerator implements PathGenerator
{
    /**
     * Gerar caminho base para a mídia
     */
    public function getPath(Media $media): string
    {
        return $this->getBasePath($media) . '/';
    }

    /**
     * Gerar caminho para conversões
     */
    public function getPathForConversions(Media $media): string
    {
        return $this->getBasePath($media) . '/conversions/';
    }

    /**
     * Gerar caminho para imagens responsivas
     */
    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getBasePath($media) . '/responsive/';
    }

    /**
     * Obter caminho base organizado por modelo e data
     */
    protected function getBasePath(Media $media): string
    {
        $modelType = str_replace('\\', '_', $media->model_type);
        $modelId = $media->model_id;
        $mediaId = $media->id;
        
        // Organização: model_type/model_id/collection_name/media_id
        return implode('/', [
            $modelType,
            $modelId,
            $media->collection_name,
            $mediaId,
        ]);
    }
}