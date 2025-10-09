<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

abstract class BaseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $application;
    protected $message;

    public function __construct($application, ?string $message = null)
    {
        $this->application = $application;
        $this->message = $message;
    }

    protected function appUrl(): string
    {
        return config('app.url');
    }

    protected function stateUrl(): string
    {
        return route('user.applications.show', $this->application->id);
    }
}

