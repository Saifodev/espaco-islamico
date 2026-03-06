<?php

namespace App\Jobs;

use App\Models\Newsletter;
use App\Models\NewsletterSubscriber;
use App\Models\NewsletterDelivery;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessNewsletterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600;
    public $tries = 1;

    protected Newsletter $newsletter;

    public function __construct(Newsletter $newsletter)
    {
        $this->newsletter = $newsletter;
    }

    public function handle()
    {
        if ($this->newsletter->status !== 'sending' && !$this->newsletter->isScheduled()) {
            Log::info('Newsletter não está em estado de envio', [
                'newsletter_id' => $this->newsletter->id,
                'status' => $this->newsletter->status
            ]);
            return;
        }

        $subscribers = NewsletterSubscriber::active()->get();

        if ($subscribers->isEmpty()) {

            $this->newsletter->update([
                'status' => 'cancelled',
                'sent_at' => null
            ]);

            Log::info('Nenhum assinante ativo para newsletter', [
                'newsletter_id' => $this->newsletter->id
            ]);

            return;
        }

        Log::info('Iniciando processamento da newsletter', [
            'newsletter_id' => $this->newsletter->id,
            'subscribers' => $subscribers->count()
        ]);

        foreach ($subscribers as $subscriber) {

            $delivery = NewsletterDelivery::create([
                'newsletter_id' => $this->newsletter->id,
                'email' => $subscriber->email,
                'status' => 'pending'
            ]);

            SendNewsletterJob::dispatchSync($delivery);
        }

        $this->finalizeNewsletter();
    }

    protected function finalizeNewsletter(): void
    {
        $pending = NewsletterDelivery::where('newsletter_id', $this->newsletter->id)
            ->where('status', 'pending')
            ->exists();

        if ($pending) {
            return;
        }

        $this->newsletter->update([
            'status' => 'sent',
            'sent_at' => now()
        ]);

        Log::info('Newsletter finalizada', [
            'newsletter_id' => $this->newsletter->id
        ]);
    }
}