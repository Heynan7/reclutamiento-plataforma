<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InterviewDeepScheduled extends Notification implements ShouldQueue
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
        $urlAuth   = route('user.applications.show', $this->application->id);
        $urlSigned = \URL::temporarySignedRoute(
            'applications.show.signed',
            now()->addDays(7),
            ['application' => $this->application->id]
        );

        $fecha = optional($this->application->interview_at)->format('d/m/Y H:i');
        $medio = $this->application->interview_channel ?? 'a confirmar';
        $link  = $this->application->interview_link;

        return (new MailMessage)
            ->subject('🎤 Entrevista profunda programada')
            ->greeting('Hola ' . $notifiable->name)
            ->line('Has sido seleccionado(a) para una **entrevista profunda**.')
            ->line("📅 **Fecha:** {$fecha}\n💬 **Medio:** {$medio}")
            ->when($link, fn($mail) => $mail->line('🔗 **Enlace:** ' . $link))
            ->when($this->customMessage, fn($mail) => $mail->line($this->customMessage))
            ->action('Ver detalles (requiere login)', $urlAuth)
            ->line('O si prefieres:')
            ->action('Ver detalles (link directo válido 7 días)', $urlSigned)
            ->salutation("Saludos,\n**WR Consultoría**");
    }
}
