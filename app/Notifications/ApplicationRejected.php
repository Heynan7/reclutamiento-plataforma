<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationRejected extends Notification implements ShouldQueue
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
            ->subject('❌ Proceso finalizado — ' . $jobTitle)
            ->greeting('Hola ' . $notifiable->name)
            ->line('Agradecemos sinceramente tu participación en el proceso de **' . $jobTitle . '**.')
            ->line('En esta ocasión, el proceso ha concluido con otro candidato, pero valoramos mucho tu interés.')
            ->when($this->customMessage, fn($mail) => $mail->line($this->customMessage))
            ->line('Te invitamos a mantenerte atento(a) a futuras oportunidades.')
            ->salutation("Gracias por postularte,\n**WR Consultoría**");
    }
}
