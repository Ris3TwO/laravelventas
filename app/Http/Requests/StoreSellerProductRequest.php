<?php

namespace App\Http\Requests;

use App\Traits\ApiResponser;
use App\Http\Requests\Api\FormRequest;

class StoreSellerProductRequest extends FormRequest
{
    use ApiResponser;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|min:2|max:30',
            'description' => 'required|min:3',
            'quantity' => 'required|integer|min:1',
            'image' => 'required|image',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El :attribute es obligatorio.',
            'description.required' => 'La :attribute es obligatoria.',
            'quantity.required' => 'La :attribute es obligatoria.',
            'image.required' => 'La :attribute es obligatoria.',
            'name.min' => 'El :attribute debe ser mínimo 2.',
            'description.min' => 'La :attribute debe ser mínimo 3.',
            'quantity.min' => 'La :attribute debe ser mínimo 1.',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'nombre del producto',
            'description' => 'descripción del producto',
            'quantity' => 'cantidad del producto',
            'image' => 'imagen del producto'
        ];
    }
}
