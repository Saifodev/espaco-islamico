<?php

namespace App\Domains\Content\Enums;

enum ContentStatus: string
{
    case DRAFT = 'draft';
    case SCHEDULED = 'scheduled';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Rascunho',
            self::SCHEDULED => 'Agendado',
            self::PUBLISHED => 'Publicado',
            self::ARCHIVED => 'Arquivado',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::DRAFT => 'gray',
            self::SCHEDULED => 'yellow',
            self::PUBLISHED => 'green',
            self::ARCHIVED => 'red',
        };
    }

    public function canBeViewedByPublic(): bool
    {
        return $this === self::PUBLISHED;
    }
}