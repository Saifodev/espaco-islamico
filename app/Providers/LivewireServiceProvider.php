<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

use App\Domains\Content\Http\Livewire\Admin\ArticleTable;

class LivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component(
            'admin.article-table',
            ArticleTable::class
        );
    }
}