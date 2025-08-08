<?php

namespace App\Http\Requests;

/**
 * Class CasaRequerimientoTramiteForm
 * @package App\Http\Requests
 *
 * @property int $id_casa
 * @property int $id_requerimiento_tramite
 */
class CasaRequerimientoTramiteForm extends Request
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
                    'id_casa' =>'required|exists:casas,id,NULL,id,deleted_at,NULL|integer',
                    'id_requerimiento_tramite' =>'required|exists:requerimientos_tramites,id,NULL,id,deleted_at,NULL|integer',
                ];
            case 'PUT':
                return [
                    'id_casa' =>'exists:casas,id,NULL,id,deleted_at,NULL|integer',
                    'id_requerimiento_tramite' =>'exists:requerimientos_tramites,id,NULL,id,deleted_at,NULL|integer',
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
