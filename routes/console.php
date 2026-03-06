<?php
// routes/console.php
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;

use App\Models\Newsletter;
use App\Jobs\ProcessNewsletterJob;
use App\Domains\Content\Services\ArticleService;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote');


// Publicar artigos agendados a cada minuto
    // $schedule->command('articles:publish-scheduled')->everyMinute();
    
    // // Gerar sitemap diariamente
    // $schedule->command('sitemap:generate')->daily();
    
    // // Limpar cache de views antigas
    // $schedule->command('view:clear')->weekly();

// Schedule::command('newsletter:process-scheduled')
//     ->everyMinute()
//     ->withoutOverlapping()
//     ->runInBackground();

Schedule::call(function () {

    // Cria a instância do service
    $articleService = app(ArticleService::class);

    // Log::info('Verificando artigos agendados...');

    // Processa os artigos cuja data de publicação já passou
    $count = $articleService->processScheduledArticles();

    // Log::info("{$count} artigos publicados com sucesso.");

})->everyMinute(); // executa a cada minuto

// Processar newsletters agendadas a cada minuto
Schedule::call(function () {

Log::info('Verificando newsletters agendadas...');
    // Pega newsletters agendadas que devem ser processadas
    // Limitamos a 15 para evitar sobrecarga
    $newsletters = Newsletter::where('status', 'scheduled')
        ->where('scheduled_at', '<=', now())
        ->limit(15)
        ->get();

    foreach ($newsletters as $newsletter) {

        // Marca como enviando
        $newsletter->update(['status' => 'sending']);

        // Processa a newsletter
        // Dispatch direto para o job, mas usando sync
        // pois não podemos usar queue:work
        ProcessNewsletterJob::dispatchSync($newsletter);

        // Log::info("Processed newsletter #{$newsletter->id}");
    }
})->everyMinute(); // roda a cada minuto

// Schedule::call(function () {
//     Log::info('Cron de teste funcionando', [
//         'time' => now()
//     ]);
// })->everyMinute();