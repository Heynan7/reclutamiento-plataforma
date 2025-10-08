<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Queue\SerializesModels;

class CustomResetPassword extends BaseResetPassword implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function toMail($notifiable): MailMessage
    {
        $resetUrl = route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        $minutes = config('auth.passwords.' . config('auth.defaults.passwords') . '.expire');

        return (new MailMessage)
            ->subject('Restablece tu contraseña')
            ->greeting('¡Hola!')
            ->line('Recibiste este correo porque solicitaste restablecer tu contraseña en nuestra plataforma.')
            ->action('Restablecer contraseña', $resetUrl)
            ->line("Este enlace expirará en {$minutes} minutos.")
            ->line('Si no solicitaste el restablecimiento, no es necesario realizar ninguna acción.')
            ->salutation("Saludos,\nWR Reclutamiento");
    }
}
