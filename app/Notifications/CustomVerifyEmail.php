<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomVerifyEmail extends Notification
{
    public function __construct(protected string $verificationUrl)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Verify your ' . config('app.name') . ' email address')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Please verify your email address to activate your ' . config('app.name') . ' account.')
            ->action('Verify Email Address', $this->verificationUrl)
            ->line('If you did not create this account, you can safely ignore this email.')
            ->salutation('Regards, ' . config('app.name'));
    }
}