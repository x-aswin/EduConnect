<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomResetPassword extends Notification
{
    public function __construct(protected string $token)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Reset your ' . config('app.name') . ' password')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('We received a request to reset your ' . config('app.name') . ' password.')
            ->action('Reset Password', $resetUrl)
            ->line('If you did not request a password reset, no action is needed.')
            ->salutation('Regards, ' . config('app.name'));
    }
}