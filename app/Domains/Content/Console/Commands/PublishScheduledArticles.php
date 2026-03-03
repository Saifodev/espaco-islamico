<?php

namespace App\Domains\Content\Console\Commands;

use Illuminate\Console\Command;
use App\Domains\Content\Services\ArticleService;

class PublishScheduledArticles extends Command
{
    protected $signature = 'articles:publish-scheduled';
    protected $description = 'Publica artigos agendados cuja data de publicação já passou';

    public function handle(ArticleService $articleService): int
    {
        $this->info('Verificando artigos agendados...');

        $count = $articleService->processScheduledArticles();

        $this->info("{$count} artigos publicados com sucesso.");

        return Command::SUCCESS;
    }
}