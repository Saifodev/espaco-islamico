<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Newsletter;
use App\Models\NewsletterSubscriber;
use App\Models\NewsletterDelivery;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class NewsletterHistorySeeder extends Seeder
{
    private int $historyMonths = 24;
    private int $batchSize = 100;

    public function run(int $historyMonths = 24): void
    {
        $this->historyMonths = $historyMonths;
        $start = microtime(true);

        $this->command->info("Gerando histórico de newsletters para os últimos {$this->historyMonths} meses...");

        // Criar assinantes
        $subscribersCount = $this->createSubscribers();
        
        // Criar newsletters
        $newslettersCount = $this->createNewsletters($subscribersCount);

        $time = round(microtime(true) - $start, 2);
        
        $this->command->newLine();
        $this->command->info("✓ Gerados {$subscribersCount} assinantes");
        $this->command->info("✓ Geradas {$newslettersCount} newsletters");
        $this->command->info("✓ Tempo total: {$time}s");
    }

    private function createSubscribers(): int
    {
        $this->command->info('Criando assinantes da newsletter...');
        
        $totalSubscribers = $this->historyMonths * 15; // 15 novos assinantes por mês
        $bar = $this->command->getOutput()->createProgressBar($totalSubscribers);
        $bar->start();

        $subscribers = [];
        $now = now();
        
        for ($i = 0; $i < $totalSubscribers; $i += $this->batchSize) {
            $batch = [];
            $batchSize = min($this->batchSize, $totalSubscribers - $i);
            
            for ($j = 0; $j < $batchSize; $j++) {
                $subscribedAt = $this->randomDateInPast();
                $isActive = rand(1, 100) > 20; // 80% ativos
                
                $batch[] = [
                    'email' => fake()->unique()->safeEmail(),
                    'name' => fake()->name(),
                    'is_active' => $isActive,
                    'subscribed_at' => $subscribedAt,
                    'unsubscribed_at' => $isActive ? null : $this->randomDateAfter($subscribedAt),
                    'created_at' => $subscribedAt,
                    'updated_at' => $subscribedAt,
                ];
                
                $bar->advance();
            }
            
            NewsletterSubscriber::insert($batch);
            
            // Limpar memória
            unset($batch);
            
            if ($i % 500 === 0) {
                gc_collect_cycles();
            }
        }

        $bar->finish();
        $this->command->newLine();

        return $totalSubscribers;
    }

    private function createNewsletters(int $subscribersCount): int
    {
        $this->command->info('Criando newsletters e deliveries...');
        
        $authorId = User::query()->value('id') ?? User::factory()->create()->id;
        
        // 1-2 newsletters por mês
        $totalNewsletters = (int) ($this->historyMonths * 1.5);
        $subscriberIds = NewsletterSubscriber::where('is_active', true)->pluck('id', 'email')->toArray();
        
        $bar = $this->command->getOutput()->createProgressBar($totalNewsletters);
        $bar->start();

        $deliveriesBatch = [];
        $deliveryCount = 0;

        for ($i = 0; $i < $totalNewsletters; $i++) {
            $sentAt = $this->randomDateInPast();
            $status = $sentAt->isPast() ? 'sent' : 'scheduled';
            
            $newsletter = Newsletter::create([
                'subject' => $this->generateNewsletterSubject($sentAt),
                'content' => $this->generateNewsletterContent($sentAt),
                'status' => $status,
                'scheduled_at' => $status === 'scheduled' ? $sentAt : null,
                'sent_at' => $status === 'sent' ? $sentAt : null,
                'created_by' => $authorId,
                'created_at' => $sentAt,
                'updated_at' => $sentAt,
            ]);

            // Criar deliveries em batch
            $subscribersForDelivery = array_slice(
                array_keys($subscriberIds), 
                0, 
                min(count($subscriberIds), rand(50, 200)), // 50-200 recipients
                true
            );

            foreach ($subscribersForDelivery as $email) {
                $deliveriesBatch[] = [
                    'newsletter_id' => $newsletter->id,
                    'email' => $email,
                    'status' => rand(1, 100) > 5 ? 'sent' : 'failed', // 95% success
                    'sent_at' => $sentAt,
                    'created_at' => $sentAt,
                    'updated_at' => $sentAt,
                ];
                
                $deliveryCount++;

                // Inserir em batch quando acumular
                if (count($deliveriesBatch) >= $this->batchSize) {
                    NewsletterDelivery::insert($deliveriesBatch);
                    $deliveriesBatch = [];
                }
            }

            $bar->advance();

            // Limpeza periódica
            if ($i % 10 === 0) {
                if (!empty($deliveriesBatch)) {
                    NewsletterDelivery::insert($deliveriesBatch);
                    $deliveriesBatch = [];
                }
                gc_collect_cycles();
            }
        }

        // Inserir deliveries restantes
        if (!empty($deliveriesBatch)) {
            NewsletterDelivery::insert($deliveriesBatch);
        }

        $bar->finish();
        $this->command->newLine();
        
        $this->command->info("✓ Gerados {$deliveryCount} deliveries");

        return $totalNewsletters;
    }

    private function generateNewsletterSubject(Carbon $date): string
    {
        $templates = [
            "Newsletter {$date->format('F/Y')}",
            "Novidades de {$date->format('M/Y')}",
            "Edição {$date->format('dmY')}",
            "Resumo do mês: {$date->format('F')}",
            "Últimas atualizações - {$date->format('d/m/Y')}",
        ];

        return $this->randomElement($templates);
    }

    private function generateNewsletterContent(Carbon $date): string
    {
        $paragraphs = rand(3, 8);
        $content = "<h1>Newsletter " . $date->format('d/m/Y') . "</h1>\n\n";
        
        for ($i = 0; $i < $paragraphs; $i++) {
            $content .= "<p>" . fake()->paragraphs(1, true) . "</p>\n\n";
            
            if ($i === 2) {
                $content .= "<h2>Destaques do mês</h2>\n\n";
            }
            
            if (rand(1, 100) > 70) {
                $content .= "<ul>\n";
                for ($j = 0; $j < rand(3, 6); $j++) {
                    $content .= "<li>" . fake()->sentence() . "</li>\n";
                }
                $content .= "</ul>\n\n";
            }
        }
        
        return $content;
    }

    private function randomDateInPast(): Carbon
    {
        $maxDays = $this->historyMonths * 30;
        return now()->subDays(rand(1, $maxDays));
    }

    private function randomDateAfter(Carbon $date): Carbon
    {
        $maxDays = min(30, $this->historyMonths * 30 - $date->diffInDays(now()));
        return $date->copy()->addDays(rand(1, $maxDays));
    }

    private function randomElement(array $array)
    {
        return $array[array_rand($array)];
    }
}