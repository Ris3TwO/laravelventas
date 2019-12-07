<?php

namespace App\Transformers;

use App\Buyer;
use League\Fractal\TransformerAbstract;

class BuyerTransformer extends TransformerAbstract
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
    public function transform(Buyer $buyer)
    {
        return [
            'identificador' => (int) $buyer->id,
            'nombre' => (string) $buyer->name,
            'apellido' => (string) $buyer->lastname,
            'correo' => (string) $buyer->email,
            'correoVerificado' => (string) $buyer->email_verified_at,
            'fechaCreacion' => (string) $buyer->created_at,
            'fechaActualizacion' => (string) $buyer->updated_at,
            'fechaEliminacion' => isset($buyer->deleted_at) ? (string) $buyer->deleted_at : null,
        ];
    }

    public static function originalAttribute($index)
    {
        $attribute = [
            'identificador' => 'id',
            'nombre' => 'name',
            'apellido' => 'lastname',
            'correo' => 'email',
            'correoVerificado' => 'email_verified_at',
            'fechaCreacion' => 'created_at',
            'fechaActualizacion' => 'updated_at',
            'fechaEliminacion' => 'deleted_at',
        ];

        return isset($attribute[$index]) ? $attribute[$index] : null;
    }
}
