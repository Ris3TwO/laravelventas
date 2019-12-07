<?php

namespace App\Http\Controllers\User;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use App\Http\Controllers\ApiController;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Routing\Exceptions\InvalidSignatureException;

class VerificationApiController extends ApiController
{
    use VerifiesEmails;
    /**
     * Show the email verification notice.
     *
     */

    public function show()
    {
        //
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    public function verify(Request $request)
    {
        try {
            $userID = $request['id'];

            $user = User::findOrFail($userID);

            if ($user->hasVerifiedEmail()) {
                return $this->errorResponse('¡El usuario ya tiene correo electrónico verificado!', 422);
            }

            $date = date("Y-m-d g:i:s");

            $user->email_verified_at = $date; // to enable the “email_verified_at field of that user be a current time stamp by mimicing the must verify email feature

            $user->save();

            return $this->showMessage('¡Correo electrónico verificado exitosamente!');
        } catch (InvalidSignatureException $ex) {
            if (!config('app.debug')) {
                return $this->errorResponse('Al parecer el enlace no es válido.', 401);
            }

            return $this->errorResponse($ex->getMessage(), 500);
        }
    }

    /**
     * Resend the email verification notification.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    public function resend(Request $request)
    {
        $userID = $request['id'];

        $user = User::findOrFail($userID);

        if ($user->hasVerifiedEmail()) {
            return $this->errorResponse('¡El usuario ya tiene correo electrónico verificado!', 422);
        }

        $user->sendEmailVerificationNotification();

        return $this->showMessage('Se ha vuelto a enviar la notificación');
    }
}
