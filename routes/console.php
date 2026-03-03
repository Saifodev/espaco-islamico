<?php
// routes/console.php
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote');


// Publicar artigos agendados a cada minuto
    // $schedule->command('articles:publish-scheduled')->everyMinute();
    
    // // Gerar sitemap diariamente
    // $schedule->command('sitemap:generate')->daily();
    
    // // Limpar cache de views antigas
    // $schedule->command('view:clear')->weekly();

Schedule::command('newsletter:process-scheduled')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();