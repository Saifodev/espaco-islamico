<?php
// app/Domains/Media/Actions/UploadPdfAction.php

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

        // Adicionar novo PDF
        $article->addMedia($file)
            ->usingFileName($file->getClientOriginalName())
            ->toMediaCollection('pdf');
    }
}