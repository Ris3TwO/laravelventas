<?php

namespace App\Notifications;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Lang;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;

class VerifyUpdatedEmail extends VerifyEmailBase

{

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $verificationUrl);
        }

        return (new MailMessage)
            ->subject(Lang::getFromJson('Confirmar nuevo correo electrónico'))
            ->line(Lang::getFromJson('Please click the button below to verify your email address.'))
            ->action(Lang::getFromJson('Verify Email Address'), $verificationUrl)
            ->line(Lang::getFromJson('Este mensaje fue enviado debido a que fue actualizado el correo asociado a este usuario.'));
    }

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param mixed $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verificationapi.verify',
            Carbon::now()->addMinutes(60),
            ['id' => $notifiable->getKey()]
        ); // this will basically mimic the email endpoint with get request
    }
}
