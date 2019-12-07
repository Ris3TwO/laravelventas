<?php

namespace App\Listeners;

use App\Mail\EmailVerification;
use App\Events\UserMailChanged;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendEmailVerification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserMailChanged  $event
     * @return void
     */
    public function handle(UserMailChanged $event)
    {
        // Mail::to($event->user)->queue(
        //     new EmailVerification($event->user)
        // );
        $event->user->sendUpdatedEmailVerificationNotification();
    }
}
