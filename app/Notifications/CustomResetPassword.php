<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPassword extends ResetPassword
{
    public function toMail($notifiable)
    {
        $url = url('login') . '?' . http_build_query([
            'reset_token' => $this->token,
            'email'       => $notifiable->getEmailForPasswordReset(),
        ]);

        return (new MailMessage)
            ->subject('Password Reset Request')
            ->view('emails.reset-password', [
                'url'     => $url,
                'appName' => config('app.name'),
                'expire'  => config('auth.passwords.users.expire', 60),
            ]);
    }
}