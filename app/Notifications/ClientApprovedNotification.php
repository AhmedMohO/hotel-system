<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Account Has Been Approved!')
            ->greeting('Hello '.$notifiable->name.'!')
            ->line('We are pleased to inform you that your account has been approved.')
            ->line('You can now log in and make reservations at our hotel.')
            ->action('Login Now', url('/client/login'))
            ->line('Thank you for choosing us. We look forward to serving you.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Your account has been approved.',
            'approved_at' => now()->toDateTimeString(),
        ];
    }
}

