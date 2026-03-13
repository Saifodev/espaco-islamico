<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Número de meses para gerar conteúdo histórico
     * Altere aqui para controlar a quantidade de conteúdo
     */
    private int $historyMonths = 24;

    public function run(): void
    {
        // $this->call(RolesAndPermissionsSeeder::class);
        // $this->call(UserSeeder::class);
        // $this->call(CategorySeeder::class);
        // $this->call(TagSeeder::class);
        
        $this->command->info('========================================');
        $this->command->info('INICIANDO SEED COM CONTEÚDO HISTÓRICO');
        $this->command->info("Período: {$this->historyMonths} meses");
        $this->command->info('========================================');

        // Desabilitar query log para performance
        DB::disableQueryLog();

        // Seeders em ordem (respeitando dependências)
        $seeders = [
            RolesAndPermissionsSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            TagSeeder::class,
            
            // Depois conteúdo histórico
            ArticleHistorySeeder::class => ['months' => $this->historyMonths],
            NewsletterHistorySeeder::class => ['months' => $this->historyMonths],
            CommentsHistorySeeder::class => ['months' => $this->historyMonths],
        ];

        foreach ($seeders as $seeder => $params) {
            if (is_array($params)) {
                $this->command->info("\nExecutando: " . class_basename($seeder));
                
                if ($seeder === ArticleHistorySeeder::class) {
                    $this->call($seeder, false, ['months' => $this->historyMonths]);
                } elseif ($seeder === NewsletterHistorySeeder::class) {
                    $this->call($seeder, false, ['months' => $this->historyMonths]);
                } elseif ($seeder === CommentsHistorySeeder::class) {
                    $this->call($seeder, false, ['months' => $this->historyMonths]);
                }
            } else {
                $this->command->info("\nExecutando: " . class_basename($params));
                $this->call($params);
            }
            
            // Limpeza após cada seeder
            if (function_exists('gc_collect_cycles')) {
                gc_collect_cycles();
            }
        }

        $this->command->info("\n========================================");
        $this->command->info('SEED CONCLUÍDO COM SUCESSO!');
        $this->command->info('========================================');
        
        // Estatísticas finais
        $this->showStats();
    }

    private function showStats(): void
    {
        $this->command->table(['Tipo', 'Quantidade'], [
            ['Artigos', \App\Domains\Content\Models\Article::count()],
            ['Comentários', \App\Domains\Content\Models\Comment::count()],
            ['Assinantes Newsletter', \App\Models\NewsletterSubscriber::count()],
            ['Newsletters', \App\Models\Newsletter::count()],
            ['Deliveries', \App\Models\NewsletterDelivery::count()],
        ]);
    }
}