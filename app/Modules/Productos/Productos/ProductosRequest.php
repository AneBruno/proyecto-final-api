<?php

namespace App\Modules\Productos\Productos;

use Illuminate\Foundation\Http\FormRequest;

class ProductosRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'nombre'           => 'required|string',
            'tipo_producto_id' => 'required|integer',
            'unidad'           => 'required|string|in:UNIDADES,TONELADAS',
            'uso_frecuente'    => 'required|bool',
        ];
    }
}