<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeNewsletterMail extends Mailable
{
    use Queueable, SerializesModels;

    public $email;

    /**
     * Create a new message instance.
     */
    public function __construct($email)
    {
        $this->email = $email;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this->subject('Bem-vindo à nossa newsletter')
                    ->html(
                        <<<HTML
                        <!DOCTYPE html>
                        <html>
                        <head>
                            <title>Bem-vindo à Newsletter</title>
                            <meta charset='utf-8'>
                        </head>
                        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;'>
                            <h2 style='color: #2c3e50;'>Obrigado por subscrever!</h2>
                            
                            <p>Olá,</p>
                            
                            <p>Confirmamos a tua subscrição da newsletter com o email: <strong>{$this->email}</strong></p>
                            
                            <p>A partir de agora vais começar a receber novidades, promoções e informações importantes sobre os nossos produtos e serviços.</p>
                            
                            <p>Se não foste tu que subscreveste ou se pretenderes cancelar a subscrição, 
                            podes fazê-lo a qualquer momento clicando no link de "Cancelar subscrição" que estará presente em todos os emails que enviarmos.</p>
                            
                            <br>
                            <p>Atenciosamente,<br><strong>Equipa {$_ENV['APP_NAME']}</strong></p>
                            
                            <hr style='border: 1px solid #eee; margin: 20px 0;'>
                            
                            <p style='font-size: 12px; color: #777;'>
                                Este é um email automático, por favor não responda a esta mensagem.
                            </p>
                        </body>
                        </html>
                        HTML
                    );
    }
}