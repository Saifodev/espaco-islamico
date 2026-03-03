<?php

namespace App\Domains\Media\Services;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Domains\Media\DataTransferObjects\MediaUploadData;
use App\Domains\Media\Enums\MediaCollectionType;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class MediaService
{
    /**
     * Upload de arquivo único
     */
    public function upload(
        HasMedia $model,
        UploadedFile $file,
        MediaCollectionType $collection,
        ?string $customName = null,
        array $customProperties = []
    ): Media {
        $this->validateUpload($file, $collection);

        return DB::transaction(function () use ($model, $file, $collection, $customName, $customProperties) {
            // Se for coleção singleFile, remove arquivo anterior
            if (!$collection->allowsMultiple()) {
                $model->clearMediaCollection($collection->value);
            }

            // Prepara o upload
            $mediaAdder = $model
                ->addMedia($file)
                ->usingName($customName ?? $this->generateFileName($file))
                ->withCustomProperties($customProperties);

            // Adiciona à coleção
            $media = $mediaAdder->toMediaCollection($collection->value);

            Log::info('Mídia upload realizada', [
                'model' => get_class($model),
                'model_id' => $model->id,
                'media_id' => $media->id,
                'collection' => $collection->value,
                'file_name' => $media->file_name,
                'size' => $media->size,
            ]);

            return $media;
        });
    }

    /**
     * Upload de múltiplos arquivos
     */
    public function uploadMultiple(
        HasMedia $model,
        array $files,
        MediaCollectionType $collection,
        array $customProperties = []
    ): array {
        if (!$collection->allowsMultiple()) {
            throw new Exception("Coleção {$collection->value} não permite múltiplos arquivos");
        }

        $uploadedMedia = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $uploadedMedia[] = $this->upload($model, $file, $collection, null, $customProperties);
            }
        }

        return $uploadedMedia;
    }

    /**
     * Substituir arquivo em coleção singleFile
     */
    public function replace(
        HasMedia $model,
        UploadedFile $newFile,
        MediaCollectionType $collection,
        ?int $oldMediaId = null
    ): Media {
        if ($collection->allowsMultiple()) {
            throw new Exception("Use uploadMultiple para coleções que permitem múltiplos arquivos");
        }

        return DB::transaction(function () use ($model, $newFile, $collection, $oldMediaId) {
            // Remove mídia específica ou toda coleção
            if ($oldMediaId) {
                $oldMedia = Media::find($oldMediaId);
                if ($oldMedia && $oldMedia->model->is($model)) {
                    $oldMedia->delete();
                }
            } else {
                $model->clearMediaCollection($collection->value);
            }

            // Faz novo upload
            return $this->upload($model, $newFile, $collection);
        });
    }

    /**
     * Remover arquivo específico
     */
    public function remove(HasMedia $model, int $mediaId, bool $forceDelete = false): bool
    {
        return DB::transaction(function () use ($model, $mediaId, $forceDelete) {
            $media = Media::find($mediaId);

            if (!$media || !$media->model->is($model)) {
                throw new Exception("Mídia não encontrada ou não pertence ao modelo");
            }

            // Verifica se é imagem de destaque obrigatória
            if ($media->collection_name === MediaCollectionType::FEATURED_IMAGE->value) {
                // Não permite remover se for artigo publicado
                if (method_exists($model, 'isPublished') && $model->isPublished) {
                    throw new Exception("Não é possível remover imagem de destaque de artigo publicado");
                }
            }

            $media->delete();

            Log::info('Mídia removida', [
                'model' => get_class($model),
                'model_id' => $model->id,
                'media_id' => $mediaId,
            ]);

            return true;
        });
    }

    /**
     * Ordenar mídia em coleção (para galerias)
     */
    public function reorder(HasMedia $model, MediaCollectionType $collection, array $orderedIds): bool
    {
        if (!$collection->allowsMultiple()) {
            throw new Exception("Coleção não suporta ordenação");
        }

        DB::transaction(function () use ($model, $collection, $orderedIds) {
            foreach ($orderedIds as $position => $mediaId) {
                Media::where('model_id', $model->id)
                    ->where('model_type', get_class($model))
                    ->where('collection_name', $collection->value)
                    ->where('id', $mediaId)
                    ->update(['order_column' => $position + 1]);
            }
        });

        return true;
    }

    /**
     * Validar upload
     */
    protected function validateUpload(UploadedFile $file, MediaCollectionType $collection): void
    {
        $rules = $collection->validationRules();
        
        $validator = validator(
            ['file' => $file],
            ['file' => $rules],
            ['file' => $collection->validationMessages()]
        );

        if ($validator->fails()) {
            throw new Exception($validator->errors()->first('file'));
        }
    }

    /**
     * Gerar nome de arquivo único
     */
    protected function generateFileName(UploadedFile $file): string
    {
        return uniqid() . '_' . str($file->getClientOriginalName())->slug();
    }

    /**
     * Limpar mídia órfã
     */
    public function cleanupOrphanedMedia(): int
    {
        $orphanedMedia = Media::query()
            ->whereDoesntHaveMorph('model', '*')
            ->get();

        $count = $orphanedMedia->count();

        foreach ($orphanedMedia as $media) {
            Storage::disk($media->disk)->deleteDirectory($media->id);
            $media->delete();
        }

        Log::info('Limpeza de mídia órfã', ['removed_count' => $count]);

        return $count;
    }

    /**
     * Obter estatísticas de mídia
     */
    public function getStatistics(): array
    {
        $totalMedia = Media::count();
        $totalSize = Media::sum('size');
        $byCollection = Media::select('collection_name', DB::raw('count(*) as count'))
            ->groupBy('collection_name')
            ->pluck('count', 'collection_name')
            ->toArray();

        return [
            'total_media' => $totalMedia,
            'total_size_mb' => round($totalSize / 1024 / 1024, 2),
            'by_collection' => $byCollection,
            'recent_uploads' => Media::with('model')
                ->latest()
                ->take(10)
                ->get()
                ->map(fn($media) => [
                    'id' => $media->id,
                    'name' => $media->name,
                    'collection' => $media->collection_name,
                    'size_mb' => round($media->size / 1024 / 1024, 2),
                    'model_type' => class_basename($media->model_type),
                    'created_at' => $media->created_at->diffForHumans(),
                ]),
        ];
    }
}