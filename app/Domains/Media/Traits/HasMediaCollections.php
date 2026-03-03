<?php

namespace App\Domains\Media\Traits;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Domains\Media\Enums\MediaCollectionType;

trait HasMediaCollections
{
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(MediaCollectionType::FEATURED_IMAGE->value)
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->useFallbackUrl('/images/fallback-article.jpg')
            ->useFallbackPath(public_path('/images/fallback-article.jpg'));

        $this->addMediaCollection(MediaCollectionType::GALLERY->value)
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);

        $this->addMediaCollection(MediaCollectionType::DOCUMENTS->value)
            ->acceptsMimeTypes(['application/pdf']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(300)
            ->format('webp')
            ->quality(80)
            ->nonQueued();

        $this->addMediaConversion('preview')
            ->width(1200)
            ->height(630)
            ->format('webp')
            ->quality(85)
            ->nonQueued();

        $this->addMediaConversion('large')
            ->width(1920)
            ->height(1080)
            ->format('webp')
            ->quality(90)
            ->queued();
    }

    public function getFeaturedImageUrl(?string $conversion = 'preview'): ?string
    {
        $media = $this->getFirstMedia(MediaCollectionType::FEATURED_IMAGE->value);

        return $media?->getUrl($conversion);
    }

    public function getGalleryUrls(?string $conversion = 'thumb'): array
    {
        return $this->getMedia(MediaCollectionType::GALLERY->value)
            ->map(fn (Media $media) => $media->getUrl($conversion))
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