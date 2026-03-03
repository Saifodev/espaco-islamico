<?php
// app/Domains/Media/Actions/DownloadYouTubeThumbnailAction.php

namespace App\Domains\Media\Actions;

use App\Domains\Content\Models\Article;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DownloadYouTubeThumbnailAction
{
    public function execute(Article $article): void
    {
        if (!$article->youtube_id) {
            return;
        }

        $thumbnailUrl = "https://img.youtube.com/vi/{$article->youtube_id}/maxresdefault.jpg";
        
        try {
            $response = Http::get($thumbnailUrl);
            
            if ($response->successful()) {
                $tempPath = tempnam(sys_get_temp_dir(), 'yt_') . '.jpg';
                file_put_contents($tempPath, $response->body());
                
                $article->addMedia($tempPath)
                    ->usingFileName("youtube_{$article->youtube_id}.jpg")
                    ->toMediaCollection('featured_image');
                
                unlink($tempPath);
            }
        } catch (\Exception $e) {
            logger()->error('Erro ao baixar thumbnail do YouTube', [
                'article_id' => $article->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}