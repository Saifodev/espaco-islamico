<?php

namespace App\Domains\Media\Traits;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Domains\Media\Enums\MediaCollectionType;

trait HasMediaCollections
{
    public function registerMediaCollections(): void
    {
        $fallbackImagePath = asset('placeholder.png');

        $this->addMediaCollection(MediaCollectionType::FEATURED_IMAGE->value)
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->useFallbackUrl($fallbackImagePath)
            ->useFallbackPath($fallbackImagePath);

        $this->addMediaCollection(MediaCollectionType::GALLERY->value)
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);

        $this->addMediaCollection(MediaCollectionType::DOCUMENTS->value)
            ->acceptsMimeTypes(['application/pdf']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        // Opção 1: Remover todas as conversões
        // Simplesmente não registrar nenhuma conversão
        // Isso fará com que apenas os arquivos originais sejam usados
        
        // Opção 2: Se quiser manter a possibilidade de conversões apenas com GD
        // Você pode tentar configurar o driver para GD, mas ainda assim algumas 
        // funcionalidades podem tentar usar proc_open
        if (config('media-library.image_driver') === 'gd') {
            $this->addMediaConversion('thumb')
                ->nonOptimized() // Remove otimizações externas
                ->nonQueued();
                
            $this->addMediaConversion('preview')
                ->nonOptimized()
                ->nonQueued();
                
            $this->addMediaConversion('large')
                ->nonOptimized()
                ->queued();
        }
    }

    public function getFeaturedImageUrl(?string $conversion = 'preview'): ?string
    {
        $media = $this->getFirstMedia(MediaCollectionType::FEATURED_IMAGE->value);
        
        if (!$media) {
            return null;
        }
        
        // Se as conversões foram removidas, sempre retorna a URL original
        return $media->getUrl();
    }

    public function getGalleryUrls(?string $conversion = 'thumb'): array
    {
        return $this->getMedia(MediaCollectionType::GALLERY->value)
            ->map(fn (Media $media) => $media->getUrl()) // Sempre retorna URL original
            ->toArray();
    }

    public function getDocuments(): array
    {
        return $this->getMedia(MediaCollectionType::DOCUMENTS->value)
            ->map(fn (Media $media) => [
                'id' => $media->id,
                'name' => $media->name,
                'file_name' => $media->file_name,
                'size' => $media->size,
                'url' => $media->getUrl(),
                'created_at' => $media->created_at,
            ])
            ->toArray();
    }

    public function hasFeaturedImage(): bool
    {
        return $this->hasMedia(MediaCollectionType::FEATURED_IMAGE->value);
    }

    public function scopeWithMedia($query)
    {
        return $query->with('media');
    }
}