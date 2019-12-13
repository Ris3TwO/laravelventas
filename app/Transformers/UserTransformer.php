<?php

namespace App\Transformers;

use App\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        //
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        //
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(User $user)
    {
        return [
            'identificador' => (int) $user->id,
            'nombres' => (string) $user->name,
            'apellidos' => (string) $user->lastname,
            'correo' => (string) $user->email,
            'correoVerificado' => isset($user->email_verified_at) ? (string) $user->email_verified_at : null,
            'esAdministrador' => ($user->admin == true),
            'fechaCreacion' => (string) $user->created_at,
            'fechaActualizacion' => (string) $user->updated_at,
            'fechaEliminacion' => isset($user->deleted_at) ? (string) $user->deleted_at : null,

            'links' => [
                [
                    'rel' => 'self',
                    'href' => route('users.show', $user->id),
                ],
            ],
        ];
    }

    public static function originalAttribute($index)
    {
        $attribute = [
            'identificador' => 'id',
            'nombres' => 'name',
            'apellidos' => 'lastname',
            'correo' => 'email',
            'correoVerificado' => 'email_verified_at',
            'esAdministrador' => 'admin',
            'fechaCreacion' => 'created_at',
            'fechaActualizacion' => 'updated_at',
            'fechaEliminacion' => 'deleted_at',
        ];

        return isset($attribute[$index]) ? $attribute[$index] : null;
    }

    public static function transformedAttribute($index)
    {
        $attribute = [
            'id' => 'identificador',
            'name' => 'nombres',
            'lastname' => 'apellidos',
            'email' => 'correo',
            'email_verified_at' => 'correoVerificado',
            'admin' => 'esAdministrador',
            'created_at' => 'fechaCreacion',
            'updated_at' => 'fechaActualizacion',
            'deleted_at' => 'fechaEliminacion',
        ];

        return isset($attribute[$index]) ? $attribute[$index] : null;
    }
}
