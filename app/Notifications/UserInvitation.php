<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class UserInvitation extends Notification
{
    use Queueable;

    protected $token;
    protected $password;

    public function __construct(string $token, string $password)
    {
        $this->token = $token;
        $this->password = $password;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $acceptUrl = route('invitation.accept', ['token' => $this->token]);

        return (new MailMessage)
            ->subject('Convite para acessar o portal')
            ->greeting('Olá!')
            ->line('Você foi convidado para acessar nosso portal institucional.')
            ->line('Use a senha temporária abaixo para fazer seu primeiro acesso:')
            ->line("**Senha temporária:** `{$this->password}`")
            ->action('Aceitar Convite', $acceptUrl)
            ->line('Por segurança, você será solicitado a alterar sua senha no primeiro acesso.')
            ->line('Se você não esperava este convite, ignore este email.');
    }
}