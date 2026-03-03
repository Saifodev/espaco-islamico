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

class ArticleSeeder extends Seeder
{
    private array $images = [];
    private array $gallery = [];
    private array $pdfs = [];
    private array $youtube = [];

    public function run(): void
    {
        $start = microtime(true);

        $this->command->info('Loading assets...');
        $this->loadAssets();

        $this->command->info('Preparing data...');

        $authorId = User::query()->value('id')
            ?? User::factory()->create()->id;

        $categoriesByType = Category::all()
            ->groupBy('belongs_to')
            ->map(fn ($c) => $c->pluck('id')->all())
            ->toArray();

        $tagIds = Tag::pluck('id')->all();

        $total = 20;

        $bar = $this->command->getOutput()->createProgressBar($total);
        $bar->start();

        for ($i = 0; $i < $total; $i++) {

            $type = $this->randomType();

            $edition = null;
            if ($type === ContentType::NEWSPAPER) {
                $edition = "Edição de " . Carbon::now()->subDays(rand(1, 30))->format('d/m/Y');
            }

            $article = Article::create([
                'type' => $type,
                'title' => fake()->sentence(4),
                'slug' => Str::random(12),
                'excerpt' => fake()->sentence(20),
                'content' => fake()->paragraphs(15, true),
                'edition' => $edition,
                'status' => ContentStatus::PUBLISHED,
                'published_at' => now(),
                'author_id' => $authorId,
                'reading_time' => rand(2, 10),
                'views_count' => rand(0, 10000),
                'youtube_url' => $type === ContentType::VIDEO ? $this->random($this->youtube) : null,
            ]);

            /*
            |---------------------------------------
            | categorias (1 query só)
            |---------------------------------------
            */
            if (isset($categoriesByType[$type->value])) {
                $article->categories()->attach(
                    $this->random($categoriesByType[$type->value])
                );
            }

            /*
            |---------------------------------------
            | tags (array puro, mais rápido)
            |---------------------------------------
            */
            if ($tagIds) {
                $article->tags()->attach(
                    array_rand(array_flip($tagIds), rand(1, min(3, count($tagIds))))
                );
            }

            /*
            |---------------------------------------
            | mídia
            |---------------------------------------
            */
            $this->attachMediaFast($article, $type);

            $bar->advance();
        }

        $bar->finish();

        $time = round(microtime(true) - $start, 2);

        $this->command->newLine();
        $this->command->info("Finished in {$time}s");
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
            default  => ContentType::NEWSPAPER,
        };
    }
}