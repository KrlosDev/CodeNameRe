<?php

namespace App\Http\Requests;

use App\Models\CasaEstado;

class CasaEstadoForm extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method()) {
            case 'POST':
                return [
                    'nombre' =>'required|string|max:'.CasaEstado::MAX_LENGTH_NOMBRE,
                    'old_association' =>'integer',
                    'slug' =>'required|string|max:'.CasaEstado::MAX_LENGTH_SLUG,
                ];
            case 'PUT':
                return [
                    'nombre' =>'string|max:'.CasaEstado::MAX_LENGTH_NOMBRE,
                    'old_association' =>'integer',
                    'slug' =>'string|max:'.CasaEstado::MAX_LENGTH_SLUG,
                ];
            default:return[];
        }
    }

    public function messages()
    {
        return [
            'required' => 'El campo :attribute es necesario.',
            'max' => 'El campo :attribute debe contener maximo :max caracteres.',
            'exists' => 'El campo :attribute no existe.',
            'numeric' => 'El campo :attribute debe ser un número válido.',
            'integer' => 'El campo :attribute debe ser un número válido.',
            'string' => 'El campo :attribute debe ser una cadena caracteres.',
        ];
    }
}
