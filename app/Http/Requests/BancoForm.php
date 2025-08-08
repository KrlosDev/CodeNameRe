<?php

namespace App\Http\Requests;

use App\Models\Banco;

class BancoForm extends Request
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
                    'nombre' =>'required|max:'.Banco::MAX_LENGTH_NOMBRE,
                ];
            case 'PUT':
                return [
                    'nombre' =>'max:'.Banco::MAX_LENGTH_NOMBRE,
                ];
            default:return[];
        }
    }


    public function messages()
    {
        return [
            'required' => 'El campo :attribute es necesario.',
            'max' => 'El campo :attribute debe contener maximo :max caracteres.',
        ];
    }
}
