<?php

namespace App\Http\Controllers\user;

use App\User;
use Illuminate\Http\Request;
use App\Events\UserMailChanged;
use Illuminate\Auth\Events\Verified;
use App\Transformers\UserTransformer;
use Illuminate\Database\QueryException;
use App\Http\Controllers\ApiController;
use Illuminate\Foundation\Auth\VerifiesEmails;

class UserController extends ApiController
{
    use VerifiesEmails;

    public function __construct()
    {
        $this->middleware('client.credentials')->only(['store']);
        $this->middleware('auth:api')->except(['store']);
        $this->middleware('transform.input:' . UserTransformer::class)->only(['store', 'update']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $usuarios = User::all();

            return $this->showAll($usuarios);
        } catch (QueryException $ex) {
            if (!config('app.debug')) {
                return $this->errorResponse('Ocurrió un problema inesperado, intente nuevamente más tarde.', 500);
            }

            return $this->errorResponse($ex->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|min:2',
                'lastname' => 'required|min:2',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6|confirmed',
            ]);

            // Datos faltantes
            $request->admin = false;

            $usuario = User::create($request->all());

            $usuario->sendEmailVerificationNotification();

            return $this->showOne($usuario, 201);
        } catch (QueryException $ex) {
            if (!config('app.debug')) {
                return $this->errorResponse('Ocurrió un problema inesperado, intente nuevamente más tarde.', 500);
            }

            return $this->errorResponse($ex->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        try {
            return $this->showOne($user);
        } catch (QueryException $ex) {
            if (!config('app.debug')) {
                return $this->errorResponse('El recurso no se pudo obtener, intente nuevamente más tarde.', 409);
            }

            return $this->errorResponse($ex->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        try {
            $data = $request->validate([
                'name' => 'min:2',
                'lastname' => 'min:2',
                'email' => 'email|unique:users,email,' . $user->id,
                'password' => 'min:6|confirmed',
                'admin' => 'in: true, false',
            ]);

            if ($request->has('name')) {
                $user->name = $data['name'];
            }

            if ($request->has('lastname')) {
                $user->lastname = $data['lastname'];
            }

            if ($request->has('email') && $user->email != $data['email']) {
                $user->email = $data['email'];
                $user->email_verified_at = null;

                //Enviar correo de verificación
                UserMailChanged::dispatch($user);
            }

            if ($request->has('admin')) {
                if ($user->email_verified_at == null) {
                    return $this->errorResponse('Unicamente los usuarios verificados pueden cambiar su valor de administrador', 409);
                }

                $user->admin = $data['admin'];
            }

            if (!$user->isDirty()) {
                return $this->errorResponse('Se debe especificar al menos un valor diferente para actualizar', 422);
            }

            $user->save();

            return $this->showOne($user);
        } catch (QueryException $ex) {
            if (!config('app.debug')) {
                return $this->errorResponse('El recurso no se pudo actualizar de forma exitosa.', 409);
            }

            return $this->errorResponse($ex->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        try {
            $user->delete();

            return $this->showOne($user);
        } catch (QueryException $ex) {
            if (!config('app.debug')) {
                return $this->errorResponse('El recurso no se pudo eliminar de forma permanentemente.', 409);
            }

            return $this->errorResponse($ex->getMessage(), 500);
        }
    }

    // public function verify(Request $request)
    // {
    //     $user = User::findOrFail($request['id']);

    //     // if ($user->email_verified_at != null) {
    //     //     return $this->errorResponse('¡Este correo ya fue verificado!', 500);
    //     // }

    //     if ($user->hasVerifiedEmail()) {
    //         return $this->showMessage('¡El usuario ya tiene correo electrónico verificado!', 422);
    //     }

    //     $date = date("Y-m-d g:i:s");

    //     $user->email_verified_at = $date; // to enable the “email_verified_at field of that user be a current time stamp by mimicing the must verify email feature

    //     $user->save();

    //     return $this->showMessage('¡Correo electrónico verificado exitosamente!');
    // }
}
