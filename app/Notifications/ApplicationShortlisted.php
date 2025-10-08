<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationShortlisted extends Notification implements ShouldQueue
{
    use Queueable;

    public $application;
    public $customMessage;

    public function __construct($application, ?string $customMessage = null)
    {
        $this->application = $application;
        $this->customMessage = $customMessage;
    }

    public function via($notifiable) { return ['mail']; }

    public function toMail($notifiable)
    {
        $jobTitle  = $this->application->job->title ?? $this->application->job_title_snapshot ?? 'una vacante';
        $urlAuth   = route('user.applications.show', $this->application->id);
        $urlSigned = \URL::temporarySignedRoute(
            'applications.show.signed',
            now()->addDays(7),
            ['application' => $this->application->id]
        );

        return (new MailMessage)
            ->subject('⭐ Avanzaste a la siguiente etapa — ' . $jobTitle)
            ->greeting('¡Felicidades ' . $notifiable->name . '!')
            ->line('Tu perfil ha sido **preseleccionado** para continuar en el proceso de la vacante **' . $jobTitle . '**.')
            ->when($this->customMessage, fn($mail) => $mail->line($this->customMessage))
            ->action('Ver estado (requiere login)', $urlAuth)
            ->line('O si prefieres:')
            ->action('Ver estado (link directo válido 7 días)', $urlSigned)
            ->salutation("Saludos,\n**WR Consultoría**");
    }
}
