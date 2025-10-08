<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PsychotestAssigned extends Notification implements ShouldQueue
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

        $cta = $this->application->psychotest_link ?: $urlAuth;

        return (new MailMessage)
            ->subject('🧠 Prueba psicométrica asignada')
            ->greeting('Hola ' . $notifiable->name)
            ->line('Para continuar con tu postulación, completa la **prueba psicométrica**.')
            ->when($this->customMessage, fn($mail) => $mail->line($this->customMessage))
            ->action('Realizar prueba', $cta)
            ->line('También puedes revisar el estado de tu postulación:')
            ->action('Ver estado (requiere login)', $urlAuth)
            ->line('O si prefieres:')
            ->action('Ver estado (link directo válido 7 días)', $urlSigned)
            ->salutation("Saludos,\n**WR Consultoría**");
    }
}
