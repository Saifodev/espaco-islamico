<?php

namespace App\Domains\Media\Actions;

use App\Domains\Content\Models\Article;
use Illuminate\Http\UploadedFile;

class UploadPdfAction
{
    public function execute(Article $article, UploadedFile $file): void
    {
        // Remover PDF anterior se existir
        if ($article->hasMedia('pdf')) {
            $article->clearMediaCollection('pdf');
        }

        // Adicionar novo PDF sem manipulações
        $article->addMedia($file)
            ->usingFileName($file->getClientOriginalName())
            ->withManipulations([]) // Desativa qualquer manipulação
            ->preservingOriginal() // Preserva o arquivo original
            ->toMediaCollection('pdf');
    }
}