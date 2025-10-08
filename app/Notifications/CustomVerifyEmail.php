<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;

class CustomVerifyEmail extends BaseVerifyEmail implements ShouldQueue
{
    use Queueable;

    /**
     * Construir el correo de verificación en español.
     */
    protected function buildMailMessage($url): MailMessage
    {
        return (new MailMessage)
            ->subject('Verifica tu correo electrónico')
            ->greeting('¡Hola!')
            ->line('Gracias por registrarte en nuestra plataforma. Haz clic en el botón de abajo para verificar tu dirección de correo.')
            ->action('Verificar correo electrónico', $url)
            ->line('Si no creaste una cuenta, no necesitas realizar ninguna acción.')
            ->salutation("Saludos,\nWR Reclutamiento");
    }
}
