<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public $application;

    public function __construct($application)
    {
        $this->application = $application;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $jobTitle = $this->application->job->title ?? $this->application->job_title_snapshot ?? 'una vacante';
        $urlAuth  = route('user.applications.show', $this->application->id);
        $urlSigned = \URL::temporarySignedRoute(
            'applications.show.signed',
            now()->addDays(7),
            ['application' => $this->application->id]
        );

        return (new MailMessage)
            ->subject('📄 Postulación recibida — ' . $jobTitle)
            ->greeting('Hola ' . $notifiable->name)
            ->line('Hemos recibido tu postulación a la vacante **' . $jobTitle . '**.')
            ->line('Nuestro equipo revisará tu perfil y te notificará si avanzas al siguiente paso.')
            ->action('Ver estado (requiere login)', $urlAuth)
            ->line('O si prefieres:')
            ->action('Ver estado (link directo válido 7 días)', $urlSigned)
            ->salutation("Saludos cordiales,\n**WR Consultoría**");
    }
}
