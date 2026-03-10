<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Content\Models\Comment;
use App\Domains\Content\Models\Article;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CommentsHistorySeeder extends Seeder
{
    private int $historyMonths = 24;
    private int $batchSize = 200;
    private array $userAgents = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15',
        'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X) AppleWebKit/605.1.15',
        'Mozilla/5.0 (Linux; Android 11; SM-G991B) AppleWebKit/537.36',
    ];

    public function run(int $historyMonths = 24): void
    {
        $this->historyMonths = $historyMonths;
        $start = microtime(true);

        $this->command->info("Gerando comentários históricos para os últimos {$this->historyMonths} meses...");

        $articles = Article::where('published_at', '>=', now()->subMonths($this->historyMonths))
            ->where('status', 'published')
            ->pluck('published_at', 'id')
            ->toArray();

        if (empty($articles)) {
            $this->command->warn('Nenhum artigo encontrado para comentários. Execute ArticleHistorySeeder primeiro.');
            return;
        }

        $totalComments = count($articles) * rand(5, 15); // 5-15 comentários por artigo
        $bar = $this->command->getOutput()->createProgressBar($totalComments);
        $bar->start();

        $comments = [];
        $commentId = 1;
        $parentMap = []; // Mapear IDs de comentários originais para duplicatas

        foreach ($articles as $articleId => $publishedAt) {
            $articlePublishedAt = Carbon::parse($publishedAt);
            $commentsForArticle = rand(3, 20);

            for ($i = 0; $i < $commentsForArticle; $i++) {
                $createdAt = $this->randomDateAfterPublish($articlePublishedAt);
                $status = $this->randomStatus();

                $comment = [
                    'commentable_type' => Article::class,
                    'commentable_id' => $articleId,
                    'name' => fake()->name(),
                    'email' => rand(1, 100) > 30 ? fake()->safeEmail() : null, // 70% com email
                    'content' => fake()->paragraphs(rand(1, 3), true),
                    'ip_address' => fake()->ipv4(),
                    'user_agent' => $this->randomElement($this->userAgents) . fake()->randomElement(['', ' Chrome/91.0', ' Firefox/89.0']),
                    'referer' => rand(1, 100) > 70 ? fake()->url() : null,
                    'metadata' => json_encode([
                        'location' => rand(1, 100) > 50 ? fake()->city() : null,
                        'device' => rand(1, 100) > 50 ? fake()->randomElement(['mobile', 'desktop', 'tablet']) : null,
                    ]),
                    'status' => $status,
                    'parent_id' => null,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];

                $comments[] = $comment;

                // Alguns comentários têm respostas (10%)
                if (rand(1, 100) <= 10 && count($comments) > 0) {
                    // Precisamos simular o ID, então guardamos referência
                    $parentMap[$commentId] = [
                        'article_id' => $articleId,
                        'created_at' => $createdAt,
                    ];

                    $reply = [
                        'commentable_type' => Article::class,
                        'commentable_id' => $articleId,
                        'name' => fake()->name(),
                        'email' => rand(1, 100) > 30 ? fake()->safeEmail() : null,
                        'content' => fake()->paragraphs(1, true),
                        'ip_address' => fake()->ipv4(),
                        'user_agent' => $this->randomElement($this->userAgents),
                        'referer' => null,
                        'metadata' => json_encode(['in_reply_to' => true]),
                        'status' => $this->randomStatus(),
                        'parent_id' => $commentId, // Referência ao comentário pai
                        'created_at' => $createdAt->copy()->addMinutes(rand(5, 1440)),
                        'updated_at' => $createdAt->copy()->addMinutes(rand(5, 1440)),
                    ];

                    $comments[] = $reply;
                    $totalComments++;
                    $commentId++;
                }

                $commentId++;

                // Inserir batch quando acumular
                if (count($comments) >= $this->batchSize) {
                    Comment::insert($comments);
                    $bar->advance(count($comments));
                    $comments = [];

                    // Limpar memória
                    // if (count($comments) % 500 === 0) {
                    //     gc_collect_cycles();
                    // }

                    static $inserted = 0;
                    $inserted += $this->batchSize;

                    if ($inserted % 1000 === 0) {
                        gc_collect_cycles();
                    }
                }
            }
        }

        // Inserir comentários restantes
        if (!empty($comments)) {
            Comment::insert($comments);
            $bar->advance(count($comments));
        }

        $bar->finish();
        $this->command->newLine();

        $time = round(microtime(true) - $start, 2);
        $this->command->info("✓ Gerados {$totalComments} comentários em {$time}s");
    }

    private function randomDateAfterPublish(Carbon $publishDate): Carbon
    {
        $maxDays = min(
            $publishDate->diffInDays(now()),
            rand(1, 180) // Máximo 6 meses após publicação
        );

        return $publishDate->copy()->addDays(rand(0, $maxDays));
    }

    private function randomStatus(): string
    {
        $rand = rand(1, 100);

        return match (true) {
            $rand <= 70 => 'approved',  // 70% aprovados
            $rand <= 85 => 'pending',   // 15% pendentes
            $rand <= 95 => 'spam',      // 10% spam
            default => 'trash',          // 5% lixeira
        };
    }

    private function randomElement(array $array)
    {
        return $array[array_rand($array)];
    }
}
