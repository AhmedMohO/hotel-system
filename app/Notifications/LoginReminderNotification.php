<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoginReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public ?string $clientName = null)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $name = $this->clientName ?: 'there';

        return (new MailMessage)
            ->subject('We miss you at Haven Luxury Hotel')
            ->greeting("Hello {$name},")
            ->line('It has been a while since your last login.')
            ->line('Log in to explore new offers and manage your reservations.')
            ->action('Login Now', url('/login'))
            ->line('Thank you for choosing Haven Luxury Hotel.');
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
