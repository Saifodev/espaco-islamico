<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $newPassword;

    public function __construct(string $newPassword)
    {
        $this->newPassword = $newPassword;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Sua senha foi redefinida - ' . config('app.name'))
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('Sua senha foi redefinida por um administrador do sistema.')
            ->line('**Sua nova senha temporária é:**')
            ->line('`' . $this->newPassword . '`')
            ->line('Por segurança, altere esta senha assim que possível.')
            ->action('Acessar o Sistema', url('/login'))
            ->line('Se você não solicitou esta alteração, entre em contato com o administrador imediatamente.')
            ->salutation('Atenciosamente, Equipe ' . config('app.name'));
    }
}