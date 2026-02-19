<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ForgotUsernameNotification extends Notification
{
    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('email.username.subject'))
            ->greeting(__('email.greeting'))
            ->line(__('email.username.line1'))
            ->line(__('email.username.line2', ['name' => $notifiable->name]))
            ->line(__('email.username.line3'))
            ->salutation(__('email.regards') . "\n" . __('email.team'));
    }
}
