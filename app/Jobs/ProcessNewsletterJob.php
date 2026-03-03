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

    public $timeout = 3600; // 1 hora
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

        // Busca assinantes ativos
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

        // Cria registros de entrega
        foreach ($subscribers as $subscriber) {
            $delivery = NewsletterDelivery::create([
                'newsletter_id' => $this->newsletter->id,
                'email' => $subscriber->email,
                'status' => 'pending'
            ]);

            // Dispara job individual para cada email
            SendNewsletterJob::dispatch($delivery)
                ->onQueue('newsletters');
        }

        $this->newsletter->update([
            'status' => 'sending'
        ]);

        Log::info('Processamento de newsletter iniciado', [
            'newsletter_id' => $this->newsletter->id,
            'subscribers' => $subscribers->count()
        ]);
    }
}