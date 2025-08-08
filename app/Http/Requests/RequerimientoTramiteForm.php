<?php

namespace App\Http\Requests;

use App\Models\RequerimientoTramite;

class RequerimientoTramiteForm extends Request
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
                    //'id_proyecto' =>'required|exists:proyectos,id|integer',
                    'nombre' =>'required|max:'.RequerimientoTramite::MAX_LENGTH_NOMBRE,
                ];
            case 'PUT':
                return [
                    //'id_proyecto' =>'exists:proyectos,id|integer',
                    'nombre' =>'max:'.RequerimientoTramite::MAX_LENGTH_NOMBRE,
                ];
            default:return[];
        }
    }


    public function messages()
    {
        return [
            'exists' => 'El campo :attribute no existe.',
            'required' => 'El campo :attribute es necesario.',
            'max' => 'El campo :attribute debe contener maximo :max caracteres.',
            'integer'=> 'El campo :attribute debe ser un número con formato correcto.',
        ];
    }
}
