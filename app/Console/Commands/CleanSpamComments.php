<?php
// app/Console/Commands/CleanSpamComments.php

namespace App\Console\Commands;

use App\Domains\Content\Models\Comment;
use Illuminate\Console\Command;

class CleanSpamComments extends Command
{
    protected $signature = 'comments:clean-spam {--days=30 : Delete spam older than X days}';
    protected $description = 'Clean old spam comments';

    public function handle()
    {
        $days = $this->option('days');
        
        $count = Comment::spam()
            ->where('created_at', '<', now()->subDays($days))
            ->delete();

        $this->info("Deleted {$count} spam comments older than {$days} days.");
    }
}