<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Models\NewsletterDelivery;
use App\Mail\NewsletterMail;
use Illuminate\Support\Facades\Log;

class SendNewsletterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = 60;
    public $timeout = 30;

    protected NewsletterDelivery $delivery;

    public function __construct(NewsletterDelivery $delivery)
    {
        $this->delivery = $delivery;
    }

    public function handle()
    {
        if ($this->delivery->status !== 'pending') {
            return;
        }

        try {
            Mail::to($this->delivery->email)
                ->send(new NewsletterMail($this->delivery->newsletter));

            $this->delivery->markAsSent();

            Log::info('Newsletter enviada com sucesso', [
                'newsletter_id' => $this->delivery->newsletter_id,
                'email' => $this->delivery->email
            ]);

        } catch (\Exception $e) {
            $this->delivery->markAsFailed($e->getMessage());

            Log::error('Erro ao enviar newsletter', [
                'newsletter_id' => $this->delivery->newsletter_id,
                'email' => $this->delivery->email,
                'error' => $e->getMessage()
            ]);

            if ($this->attempts() < $this->tries) {
                $this->release($this->backoff * $this->attempts());
            } else {
                throw $e;
            }
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::critical('Falha permanente no envio de newsletter', [
            'delivery_id' => $this->delivery->id,
            'email' => $this->delivery->email,
            'error' => $exception->getMessage()
        ]);
    }
}