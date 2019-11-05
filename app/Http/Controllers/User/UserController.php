<?php

namespace App\Http\Controllers\user;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class UserController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $usuarios = User::all();

        return $this->showAll($usuarios);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|min:2',
            'lastname' => 'required|min:2',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed'
        ]);

        // Datos faltantes
        $data['verified'] = 0;
        $data['verification_token'] = User::generateVerificationToken();
        $data['admin'] = false;

        $usuario = User::create($data);

        return $this->showOne($usuario, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $usuario = User::findOrFail($id);

        return $this->showOne($usuario);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'name' => 'min:2',
            'lastname' => 'min:2',
            'email' => 'email|unique:users,email,' . $user->id,
            'password' => 'min:6|confirmed',
            'admin' => 'in: true, false',
        ]);

        if ($request->has('name'))
        {
            $user->name = $data['name'];
        }

        if ($request->has('lastname'))
        {
            $user->lastname = $data['lastname'];
        }

        if ($request->has('email') && $user->email != $data['email'])
        {
            $user->verified = "0";
            $user->verification_token = User::generateVerificationToken();
            $user->email = $data['email'];
        }

        if ($request->has('admin'))
        {
            if ($user->verified != 1)
            {
                return $this->errorResponse('Unicamente los usuarios verificados pueden cambiar su valor de administrador', 409);
            }

            $user->admin = $data['admin'];
        }

        if (!$user->isDirty()) 
        {
            return $this->errorResponse('Se debe especificar al menos un valor diferente para actualizar', 422);
        }

        $user->save();

        return $this->showOne($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return $this->showOne($user);
    }
}
