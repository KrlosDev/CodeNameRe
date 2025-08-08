<?php

namespace App\Http\Requests;

class BrokerAsignarPorcentajeForm extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'id_broker' => 'required|exists:brokers,id',
            'id_proyecto' => 'required|exists:proyectos,id',
            'porcentaje' => 'required|numeric',
        ];
    }


    public function messages()
    {
        return [
            'required' => 'El campo :attribute es necesario.',
            'exists' => 'El campo :attribute no existe.',
            'integer' => 'El campo :attribute debe ser un número válido.',
            'email' => 'El campo :attribute debe ser un email.',
            'password.required' => 'El campo :attribute es obligatorio.',
            'password.confirmed' => 'El campo :attribute no coincide.',

        ];
    }
}
