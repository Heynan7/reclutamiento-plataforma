<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;

class ApplicationClosed extends Notification implements ShouldQueue
{
    use Queueable;

    public $application;

    public function __construct($application)
    {
        $this->application = $application;
    }

    /**
     * Canal de notificación
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Contenido del correo
     */
    public function toMail($notifiable)
    {
        $jobTitle = $this->application->job->title ?? $this->application->job_title_snapshot;

        // Link normal (requiere login)
        $urlAuth = route('user.applications.show', $this->application->id);

        // Link firmado válido 7 días
        $urlSigned = URL::temporarySignedRoute(
            'applications.show.signed',
            Carbon::now()->addDays(7),
            ['application' => $this->application->id]
        );

        return (new MailMessage)
            ->subject('📌 Proceso cerrado — ' . $jobTitle)
            ->greeting('Hola ' . $notifiable->name . ',')
            ->line('El proceso de selección para la vacante **' . $jobTitle . '** ha finalizado.')
            ->line('Agradecemos sinceramente tu interés y el tiempo dedicado durante el proceso.')
            ->line('Puedes consultar los detalles finales de tu postulación con los siguientes enlaces:')
            ->action('Ver estado (requiere iniciar sesión)', $urlAuth)
            ->line('O, si prefieres, usa este enlace directo válido por 7 días:')
            ->action('Ver estado sin iniciar sesión', $urlSigned)
            ->line('Te invitamos a seguir pendiente de nuevas oportunidades publicadas en nuestra plataforma.')
            ->salutation("Saludos cordiales,\n**WR Consultoría**");
    }
}
