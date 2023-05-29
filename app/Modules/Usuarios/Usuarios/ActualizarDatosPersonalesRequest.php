<?php

namespace App\Modules\Usuarios\Usuarios;

use Illuminate\Foundation\Http\FormRequest;

class ActualizarDatosPersonalesRequest extends FormRequest
{
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
            'nombre'     => 'required|string',
            'apellido'   => 'required|string',
            'telefono'   => 'nullable|integer',
            'foto'       => 'nullable|file',
			'suscripto_notificaciones' => 'required|boolean',
        ];
    }
}
