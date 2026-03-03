<?php
// app/Domains/Content/Enums/ContentType.php

namespace App\Domains\Content\Enums;

enum ContentType: string
{
    case ARTICLE = 'article';
    case VIDEO = 'video';
    case NEWSPAPER = 'newspaper';
    
    // Labels amigáveis
    public function label(): string
    {
        return match($this) {
            self::ARTICLE => 'Artigo',
            self::VIDEO => 'Vídeo',
            self::NEWSPAPER => 'Jornal',
        };
    }
    
    // Ícones para UI
    public function icon(): string
    {
        return match($this) {
            self::ARTICLE => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
            self::VIDEO => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z',
            self::NEWSPAPER => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z',
        };
    }
    
    // Cores para UI
    public function color(): string
    {
        return match($this) {
            self::ARTICLE => 'blue',
            self::VIDEO => 'purple',
            self::NEWSPAPER => 'amber',
        };
    }
    
    // Regras de validação baseadas no tipo
    public function validationRules(bool $isPublishing = false): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:articles,slug'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'string'],
            'seo_title' => ['nullable', 'string', 'max:70'],
            'seo_description' => ['nullable', 'string', 'max:160'],
            'seo_keywords' => ['nullable', 'string', 'max:255'],
        ];
        
        // Regras específicas por tipo
        return match($this) {
            self::ARTICLE => array_merge($rules, [
                'content' => [$isPublishing ? 'required' : 'nullable', 'string'],
                'reading_time' => ['nullable', 'integer', 'min:1', 'max:999'],
            ]),
            
            self::VIDEO => array_merge($rules, [
                'youtube_url' => [$isPublishing ? 'required' : 'nullable', 'url', 'regex:/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.?be)\/.+$/'],
                'content' => ['nullable', 'string'], // Descrição opcional
                'reading_time' => ['nullable', 'integer', 'min:1', 'max:999'],
            ]),
            
            self::NEWSPAPER => array_merge($rules, [
                'edition' => [$isPublishing ? 'required' : 'nullable', 'string', 'max:255'],
                'content' => ['nullable', 'string'], // Descrição opcional
            ]),
        };
    }
    
    // Campos que devem ser mostrados/ocultos
    public function visibleFields(): array
    {
        return match($this) {
            self::ARTICLE => [
                'content' => true,
                'reading_time' => true,
                'youtube_url' => false,
                'edition' => false,
                'gallery' => true,
                'documents' => true,
                'pdf' => false,
            ],
            self::VIDEO => [
                'content' => true, // Como descrição
                'reading_time' => true,
                'youtube_url' => true,
                'edition' => false,
                'gallery' => false,
                'documents' => false,
                'pdf' => false,
            ],
            self::NEWSPAPER => [
                'content' => true, // Como descrição
                'reading_time' => false,
                'youtube_url' => false,
                'edition' => true,
                'gallery' => false,
                'documents' => false,
                'pdf' => true, // PDF principal da edição
            ],
        };
    }
    
    // Requisitos de mídia para publicação
    public function mediaRequirements(): array
    {
        return match($this) {
            self::ARTICLE => [
                'featured_image' => 'required',
                'gallery' => 'optional',
                'pdf' => 'hidden',
            ],
            self::VIDEO => [
                'featured_image' => 'optional', // Pode ser thumbnail do YouTube
                'gallery' => 'hidden',
                'pdf' => 'hidden',
            ],
            self::NEWSPAPER => [
                'featured_image' => 'required', // Capa da edição
                'pdf' => 'required',
                'gallery' => 'hidden',
            ],
        };
    }
    
    // Mensagens de placeholder para campos
    public function placeholders(): array
    {
        return match($this) {
            self::ARTICLE => [
                'title' => 'Ex: Como criar um site profissional em Laravel',
                'excerpt' => 'Um resumo cativante do seu artigo...',
                'content' => 'Escreva seu artigo aqui...',
            ],
            self::VIDEO => [
                'title' => 'Ex: Tutorial Laravel Livewire - Passo a Passo',
                'youtube_url' => 'https://youtube.com/watch?v=...',
                'content' => 'Descrição do vídeo (opcional)...',
            ],
            self::NEWSPAPER => [
                'title' => 'Ex: Jornal da Cidade - Edição 25',
                'edition' => 'Ex: Edição 25 - Março 2026',
                'content' => 'Descrição da edição (opcional)...',
            ],
        };
    }
    
    // Helper para saber se campo é obrigatório na publicação
    public function isRequiredForPublishing(string $field): bool
    {
        $rules = $this->validationRules(true);
        return isset($rules[$field]) && in_array('required', $rules[$field]);
    }
}