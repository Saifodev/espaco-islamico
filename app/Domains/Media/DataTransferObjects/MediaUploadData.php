<?php

namespace App\Domains\Media\DataTransferObjects;

use Illuminate\Http\UploadedFile;
use App\Domains\Media\Enums\MediaCollectionType;

class MediaUploadData
{
    public function __construct(
        public readonly UploadedFile $file,
        public readonly MediaCollectionType $collection,
        public readonly ?string $customName = null,
        public readonly array $customProperties = [],
        public readonly array $manipulations = [],
    ) {}

    /**
     * Criar a partir de array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            file: $data['file'],
            collection: $data['collection'],
            customName: $data['custom_name'] ?? null,
            customProperties: $data['custom_properties'] ?? [],
            manipulations: $data['manipulations'] ?? [],
        );
    }

    /**
     * Obter nome do arquivo sanitizado
     */
    public function getSanitizedName(): string
    {
        if ($this->customName) {
            return $this->sanitizeFileName($this->customName);
        }

        return $this->sanitizeFileName($this->file->getClientOriginalName());
    }

    /**
     * Sanitizar nome do arquivo
     */
    protected function sanitizeFileName(string $name): string
    {
        // Remove extensão
        $pathInfo = pathinfo($name);
        $filename = $pathInfo['filename'];
        $extension = $pathInfo['extension'] ?? '';

        // Sanitiza o nome
        $filename = str($filename)
            ->slug()
            ->limit(100, '')
            ->toString();

        return $filename . ($extension ? '.' . $extension : '');
    }
}