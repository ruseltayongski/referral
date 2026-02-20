<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

class CustomVerifyEmail extends VerifyEmail
{
    /**
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verify Your Email - DOH Telemedicine')
            ->view('emails.verify-email', [
                'greeting' => 'Welcome to DOH Central Visayas Telemedicine!',
                'verificationUrl' => $verificationUrl,
                'salutation' => 'Best regards, Department of Health Central Visayas'
            ]);
    }

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        $hash = sha1($notifiable->getEmailForVerification());
        
        return URL::route('verification.verify', [
            'id'   => $notifiable->getKey(),
            'hash' => $hash,
        ]);
    }
}