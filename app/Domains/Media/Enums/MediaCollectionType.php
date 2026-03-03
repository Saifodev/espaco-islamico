<?php

namespace App\Domains\Media\Enums;

enum MediaCollectionType: string
{
    case FEATURED_IMAGE = 'featured_image';
    case GALLERY = 'gallery';
    case DOCUMENTS = 'documents';

    /**
     * Obter regras de validação para a coleção
     */
    public function validationRules(): array
    {
        return match ($this) {
            self::FEATURED_IMAGE => [
                'mimes:jpeg,png,jpg,webp',
                'max:5120', // 5MB
                'dimensions:min_width=800,min_height=400,max_width=3840,max_height=2160',
            ],
            self::GALLERY => [
                'mimes:jpeg,png,jpg,webp',
                'max:5120', // 5MB
                'dimensions:min_width=400,min_height=300',
            ],
            self::DOCUMENTS => [
                'mimes:pdf',
                'max:10240', // 10MB
            ],
        };
    }

    /**
     * Mensagens de erro personalizadas
     */
    public function validationMessages(): array
    {
        return match ($this) {
            self::FEATURED_IMAGE => [
                'mimes' => 'A imagem de destaque deve ser JPG, PNG ou WebP',
                'max' => 'A imagem de destaque não pode ter mais que 5MB',
                'dimensions' => 'A imagem de destaque deve ter no mínimo 800x400 pixels',
            ],
            self::GALLERY => [
                'mimes' => 'As imagens da galeria devem ser JPG, PNG ou WebP',
                'max' => 'Cada imagem da galeria não pode ter mais que 5MB',
                'dimensions' => 'As imagens da galeria devem ter no mínimo 400x300 pixels',
            ],
            self::DOCUMENTS => [
                'mimes' => 'Apenas arquivos PDF são permitidos',
                'max' => 'O arquivo PDF não pode ter mais que 10MB',
            ],
        };
    }

    /**
     * Verificar se é coleção de imagem
     */
    public function isImage(): bool
    {
        return in_array($this, [self::FEATURED_IMAGE, self::GALLERY]);
    }

    /**
     * Verificar se é coleção de documento
     */
    public function isDocument(): bool
    {
        return $this === self::DOCUMENTS;
    }

    /**
     * Verificar se permite múltiplos arquivos
     */
    public function allowsMultiple(): bool
    {
        return in_array($this, [self::GALLERY, self::DOCUMENTS]);
    }

    /**
     * Obter nome amigável
     */
    public function label(): string
    {
        return match ($this) {
            self::FEATURED_IMAGE => 'Imagem de Destaque',
            self::GALLERY => 'Galeria de Imagens',
            self::DOCUMENTS => 'Documentos',
        };
    }
}