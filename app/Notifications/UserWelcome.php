<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class UserWelcome extends Notification
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Bem-vindo ao portal!')
            ->greeting("Olá {$notifiable->name}!")
            ->line('Sua conta foi ativada com sucesso.')
            ->line('Agora você já pode acessar todas as funcionalidades disponíveis para seu perfil.')
            ->action('Acessar o Portal', route('admin.dashboard'))
            ->line('Obrigado por fazer parte da nossa equipe!');
    }
}