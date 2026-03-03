<?php

namespace App\Console\Commands;

use App\Models\Newsletter;
use App\Jobs\ProcessNewsletterJob;
use Illuminate\Console\Command;

class ProcessScheduledNewsletters extends Command
{
    protected $signature = 'newsletter:process-scheduled';
    protected $description = 'Process scheduled newsletters';

    public function handle()
    {
        $newsletters = Newsletter::where('status', 'scheduled')
            ->where('scheduled_at', '<=', now())
            ->get();
        
        foreach ($newsletters as $newsletter) {
            $this->info("Processing newsletter #{$newsletter->id}");
            
            $newsletter->update(['status' => 'sending']);
            
            ProcessNewsletterJob::dispatch($newsletter);
        }
        
        $this->info('Done!');
    }
}