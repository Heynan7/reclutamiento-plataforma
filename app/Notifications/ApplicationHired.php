<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationHired extends Notification implements ShouldQueue
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
        $jobTitle = $this->application->job->title ?? $this->application->job_title_snapshot;
        return (new MailMessage)
            ->subject('✅ ¡Felicidades! Has sido contratado')
            ->greeting('¡Enhorabuena ' . $notifiable->name . '!')
            ->line('Nos complace informarte que has sido **seleccionado(a)** para la vacante **' . $jobTitle . '**.')
            ->when($this->customMessage, fn($mail) => $mail->line($this->customMessage))
            ->line('Nuestro equipo se pondrá en contacto contigo para los pasos finales.')
            ->salutation("Con gratitud,\n**WR Consultoría**");
    }
}
