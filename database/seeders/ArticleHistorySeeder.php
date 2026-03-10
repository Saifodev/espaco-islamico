<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Domains\Content\Models\Article;
use App\Domains\Content\Models\Category;
use App\Domains\Content\Models\Tag;
use App\Models\User;
use Carbon\Carbon;
use App\Domains\Content\Enums\ContentStatus;
use App\Domains\Content\Enums\ContentType;
use Illuminate\Support\Facades\DB;

class ArticleHistorySeeder extends Seeder
{
    private array $images = [];
    private array $gallery = [];
    private array $pdfs = [];
    private array $youtube = [];

    /**
     * Número de meses para gerar conteúdo histórico
     */
    private int $historyMonths = 24; // Padrão: 2 anos

    /**
     * Tamanho do batch para inserções
     */
    private int $batchSize = 50;

    public function run(int $historyMonths = 24): void
    {
        $this->historyMonths = $historyMonths;
        $start = microtime(true);

        $this->command->info("Gerando conteúdo histórico para os últimos {$this->historyMonths} meses...");
        $this->command->warn("Data atual: " . now()->format('d/m/Y H:i:s'));
        $this->command->warn("Data mais antiga: " . now()->subMonths($this->historyMonths)->format('d/m/Y H:i:s'));

        $this->command->info('Loading assets...');
        $this->loadAssets();

        $this->command->info('Preparing data...');

        $authorId = User::query()->value('id')
            ?? User::factory()->create()->id;

        $categoriesByType = Category::all()
            ->groupBy('belongs_to')
            ->map(fn($c) => $c->pluck('id')->all())
            ->toArray();

        $tagIds = Tag::pluck('id')->all();

        // Calcular distribuição de conteúdo por mês
        $totalArticles = $this->calculateTotalArticles();
        $monthlyDistribution = $this->calculateMonthlyDistribution($totalArticles);

        $this->command->info("Total de artigos a serem gerados: {$totalArticles}");

        $bar = $this->command->getOutput()->createProgressBar($totalArticles);
        $bar->start();

        $articlesCreated = 0;

        // Gerar conteúdo para cada mês
        foreach ($monthlyDistribution as $monthData) {
            if ($monthData['count'] <= 0) continue;

            // Criar artigos em batches
            for ($i = 0; $i < $monthData['count']; $i += $this->batchSize) {
                $batchCount = min($this->batchSize, $monthData['count'] - $i);

                // Criar artigos do batch
                for ($j = 0; $j < $batchCount; $j++) {
                    $this->createArticle(
                        $monthData['date'],
                        $authorId,
                        $categoriesByType,
                        $tagIds
                    );

                    $articlesCreated++;

                    // Progresso a cada 10 artigos para não sobrecarregar
                    if ($articlesCreated % 10 === 0) {
                        $bar->advance(10);
                    }
                }

                // Limpar modelos do Doctrine/Fluent para liberar memória
                if (method_exists(DB::connection(), 'flushQueryLog')) {
                    DB::flushQueryLog();
                }

                // Forçar garbage collection
                if ($articlesCreated % 200 === 0) {
                    gc_collect_cycles();
                }
            }
        }

        $bar->finish();

        $time = round(microtime(true) - $start, 2);
        $memory = round(memory_get_peak_usage(true) / 1024 / 1024, 2);

        $this->command->newLine();
        $this->command->info("✓ Gerados {$articlesCreated} artigos em {$time}s");
        $this->command->info("✓ Pico de memória: {$memory} MB");
        $this->command->info("✓ Período: " . now()->subMonths($this->historyMonths)->format('d/m/Y') . " até " . now()->format('d/m/Y'));
    }

    /**
     * Calcular número total de artigos baseado nos meses
     */
    private function calculateTotalArticles(): int
    {
        // Média de 20 artigos por mês, ajustável
        $basePerMonth = 20;

        // Adicionar variação aleatória mas previsível
        return (int) ($this->historyMonths * $basePerMonth * (1 + (rand(-20, 20) / 100)));
    }

    /**
     * Distribuir artigos pelos meses de forma realista
     */
    private function calculateMonthlyDistribution(int $totalArticles): array
    {
        $distribution = [];
        $startDate = now()->subMonths($this->historyMonths)->startOfMonth();

        for ($i = 0; $i <= $this->historyMonths; $i++) {
            $date = $startDate->copy()->addMonths($i);

            // Não gerar para meses futuros
            if ($date->isFuture()) {
                continue;
            }

            // Distribuição realista: mais conteúdo recente
            $weight = $i / $this->historyMonths; // 0 a 1
            $baseCount = (int) ($totalArticles / ($this->historyMonths + 1));

            // Meses mais recentes têm mais conteúdo
            $monthCount = (int) ($baseCount * (0.5 + $weight));

            // Adicionar variação
            $monthCount = (int) ($monthCount * (0.8 + (rand(0, 40) / 100)));

            // Garantir mínimo de 1 artigo
            $monthCount = max(1, $monthCount);

            $distribution[] = [
                'date' => $date,
                'count' => $monthCount
            ];
        }

        // Ajustar para somar exatamente o total
        $totalDistributed = array_sum(array_column($distribution, 'count'));
        $scale = $totalArticles / $totalDistributed;

        foreach ($distribution as &$month) {
            $month['count'] = (int) round($month['count'] * $scale);
        }

        return $distribution;
    }

    private function createArticle(
        Carbon $monthDate,
        int $authorId,
        array $categoriesByType,
        array $tagIds
    ): void {
        $type = $this->randomType();

        // Data aleatória dentro do mês
        $publishedAt = $this->randomDateInMonth($monthDate);

        $edition = null;
        if ($type === ContentType::NEWSPAPER) {
            $edition = "Edição de " . $publishedAt->format('d/m/Y');
        }

        $article = Article::create([
            'type' => $type,
            'title' => $this->generateTitle($type, $publishedAt),
            'slug' => Str::random(12) . '-' . $publishedAt->timestamp,
            'excerpt' => fake()->paragraphs(rand(2, 4), true),
            'content' => fake()->paragraphs(rand(10, 25), true),
            'edition' => $edition,
            'status' => ContentStatus::PUBLISHED,
            'published_at' => $publishedAt,
            'author_id' => $authorId,
            'reading_time' => rand(2, 15),
            'views_count' => $this->calculateViewsByAge($publishedAt),
            'youtube_url' => $type === ContentType::VIDEO ? $this->random($this->youtube) : null,
            'created_at' => $publishedAt,
            'updated_at' => $publishedAt,
        ]);

        // Categorias
        if (isset($categoriesByType[$type->value])) {
            $categoryIds = $this->randomSubset(
                $categoriesByType[$type->value],
                rand(1, min(3, count($categoriesByType[$type->value])))
            );
            $article->categories()->attach($categoryIds);
        }

        // Tags
        if ($tagIds) {
            $tagSubset = $this->randomSubset($tagIds, rand(1, min(3, count($tagIds))));
            $article->tags()->attach($tagSubset);
        }

        // Mídia
        $this->attachMediaFast($article, $type);
    }

    /**
     * Gerar título com variação por período
     */
    private function generateTitle(ContentType $type, Carbon $date): string
    {
        $templates = [
            ContentType::ARTICLE->value => [
                "Análise de {$date->format('F/Y')}: {subject}",
                "Guia completo de {subject}",
                "Tutorial: {subject} passo a passo",
                "Como {subject} mudou em {$date->year}",
            ],
            ContentType::NEWS->value => [
                "Notícia: {subject} - {$date->format('d/m/Y')}",
                "Últimas sobre {subject}",
                "Atualização: {subject}",
                "Novidades em {subject}",
            ],
            ContentType::VIDEO->value => [
                "Vídeo: {subject} - {$date->format('m/Y')}",
                "Assista: {subject} completo",
                "Tutorial em vídeo: {subject}",
            ],
            ContentType::NEWSPAPER->value => [
                "Jornal {$date->format('d/m/Y')} - {subject}",
                "Edição {$date->format('dmY')}: {subject}",
            ],
        ];

        $subjects = [
            'Laravel',
            'PHP',
            'JavaScript',
            'Vue.js',
            'React',
            'MySQL',
            'APIs REST',
            'Microservices',
            'Docker',
            'Kubernetes',
            'DevOps',
            'UX Design',
            'UI Design',
            'Figma',
            'Adobe XD',
            'Sketch',
            'Marketing Digital',
            'SEO',
            'Social Media',
            'Content Marketing',
            'Agile',
            'Scrum',
            'Kanban',
            'Product Management'
        ];

        $template = $this->random($templates[$type->value] ?? $templates[ContentType::ARTICLE->value]);

        return str_replace(
            ['{subject}', '{date}'],
            [$this->random($subjects), $date->format('d/m/Y')],
            $template
        );
    }

    /**
     * Calcular visualizações baseado na idade do conteúdo
     */
    private function calculateViewsByAge(Carbon $publishedAt): int
    {
        $daysOld = $publishedAt->diffInDays(now());

        // Conteúdo mais antigo tem menos views (decadência exponencial)
        $baseViews = rand(100, 5000);
        $decayFactor = exp(-$daysOld / 365); // Decai 63% em 1 ano

        return (int) ($baseViews * $decayFactor * (0.5 + rand(0, 100) / 100));
    }

    /**
     * Data aleatória dentro do mês (nunca futura)
     */
    private function randomDateInMonth(Carbon $monthDate): Carbon
    {
        $start = $monthDate->copy()->startOfMonth();
        $end = min(
            $monthDate->copy()->endOfMonth(),
            now() // Não permitir datas futuras
        );

        return Carbon::createFromTimestamp(
            rand($start->timestamp, $end->timestamp)
        );
    }

    /**
     * Selecionar subconjunto aleatório de um array
     */
    private function randomSubset(array $array, int $count): array
    {
        $count = min($count, count($array));
        $keys = array_rand($array, $count);

        if ($count === 1) {
            return [$array[$keys]];
        }

        return array_map(fn($key) => $array[$key], $keys);
    }

    /*
    |--------------------------------------------------------------------------
    | MEDIA (mais rápido possível)
    |--------------------------------------------------------------------------
    */

    private function attachMediaFast(Article $article, ContentType $type): void
    {
        $this->featured($article);

        if ($type === ContentType::ARTICLE) {
            $this->many($article, $this->gallery, 'gallery', 2, 4);
            $this->many($article, $this->pdfs, 'documents', 0, 2);
        }

        if ($type === ContentType::NEWSPAPER) {
            $this->many($article, $this->pdfs, 'documents', 1, 2);
        }
    }

    private function featured(Article $article): void
    {
        if (!$this->images) return;

        $article->addMedia($this->random($this->images))
            ->preservingOriginal()
            ->toMediaCollection('featured_image');
    }

    private function many($article, array $files, string $collection, int $min, int $max): void
    {
        if (!$files) return;

        $count = rand($min, min($max, count($files)));

        for ($i = 0; $i < $count; $i++) {
            $article->addMedia($files[array_rand($files)])
                ->preservingOriginal()
                ->toMediaCollection($collection);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS (sem collections)
    |--------------------------------------------------------------------------
    */

    private function loadAssets(): void
    {
        $base = database_path('seeders/assets');

        $this->images  = glob("$base/images/*") ?: [];
        $this->gallery = glob("$base/gallery/*") ?: [];
        $this->pdfs    = glob("$base/pdfs/*") ?: [];

        $file = "$base/youtube.txt";

        if (file_exists($file)) {
            $this->youtube = array_filter(array_map('trim', file($file)));
        }
    }

    private function random(array $arr)
    {
        return $arr[array_rand($arr)];
    }

    private function randomType(): ContentType
    {
        $r = mt_rand(1, 100);

        return match (true) {
            $r <= 50 => ContentType::ARTICLE,
            $r <= 60 => ContentType::VIDEO,
            $r <= 70 => ContentType::NEWS,
            default  => ContentType::NEWSPAPER,
        };
    }
}
