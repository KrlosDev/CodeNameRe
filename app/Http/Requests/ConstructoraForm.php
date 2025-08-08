<?php

namespace App\Http\Requests;

use App\User;

class ConstructoraForm extends Request
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
                    'max_brokers' =>'required|integer',
                    'max_ejecutivos_ventas' =>'required|integer',
                    'max_ejecutivos_bancos' =>'required|integer',
                    'fecha_vencimiento' => 'required|date|after:today',
                    'fecha_suspension' => 'required|date|after:fecha_vencimiento',
                    'password' => 'required|confirmed',
                    'password_confirmation' => 'required',
                    'email'=>'email|max:'.User::MAX_LENGTH_EMAIL,
                ];
            case 'PUT':
                return [
                    'nombre' =>'required|max:'.User::MAX_LENGTH_NOMBRE,
                    'max_brokers' =>'required|integer',
                    'max_ejecutivos_ventas' =>'required|integer',
                    'max_ejecutivos_bancos' =>'required|integer',
                    'fecha_vencimiento' => 'required|date|after:today',
                    'fecha_suspension' => 'required|date|after:fecha_vencimiento',
                    'password' => 'confirmed',
                    'password_confirmation' => '',
                    'email'=>'email|max:'.User::MAX_LENGTH_EMAIL,
                ];
            default:return[];
        }
    }

    public function messages()
    {
        return [
            'required'  => 'El campo :attribute es necesario.',
            'max'       => 'El campo :attribute debe contener maximo :max caracteres.',
            'unique'    => 'El campo :attribute ya se encuentra en nuestra base de datos',
            'email'     => 'El campo :attribute debe ser un email.',
            'exists'    => 'El campo :attribute no existe.',
            'fecha_vencimiento.after'      => 'El campo fecha vencimiento debe ser una fecha posterior a hoy.',
            'password.required' => 'El campo :attribute es obligatorio.',
            'password.confirmed' => 'El campo :attribute no coincide.',
        ];
    }
}
