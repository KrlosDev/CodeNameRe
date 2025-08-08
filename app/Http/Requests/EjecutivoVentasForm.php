<?php

namespace App\Http\Requests;

use App\User;

class EjecutivoVentasForm extends Request
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
                    'name' =>'required|unique:users,name,NULL,id,deleted_at,NULL|max:'.User::MAX_LENGTH_NOMBRE,
                    'nombre' =>'required|max:'.User::MAX_LENGTH_NOMBRE,
                    'password' => 'required|confirmed',
                    'password_confirmation' => 'required',
                    'email'=>'email|max:100',
                    'id_broker'=>'required|exists:brokers,id',
                ];
            case 'PUT':
                return [
                    'nombre' =>'required|max:'.User::MAX_LENGTH_NOMBRE,
                    'password' => 'confirmed',
                    'password_confirmation' => '',
                    'email'=>'email|max:100',
                    'id_broker'=>'exists:brokers,id',

                ];
            default:return[];
        }
    }
}
