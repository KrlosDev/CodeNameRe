<?php

namespace App\Http\Requests;

use App\User;
use App\Models\Broker;
use Illuminate\Support\Facades\Auth;

class BrokerForm extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        $constructora = $user->getConstructora();
        if ($constructora === true) {
            $broker = Broker::find(\Route::current()->id);

            $constructora = $broker->constructora->first();
        }

        switch ($this->method()) {
            case 'POST':
                return [
                    'name' =>'required|unique:users,name,NULL,id,deleted_at,NULL|max:'.User::MAX_LENGTH_NOMBRE,
                    'nombre' =>'required|max:'.User::MAX_LENGTH_NOMBRE,
                    'password' => 'required|confirmed',
                    'password_confirmation' => 'required',
                    'email'=>'email|max:100',

                    'max_ej_ventas' =>'required|integer|max:'.$constructora->max_ejecutivos_ventas,
                    'max_ej_bancos' =>'required|integer|max:'.$constructora->max_ejecutivos_bancos,
                ];
            case 'PUT':
                return [
                    'nombre' =>'required|max:'.User::MAX_LENGTH_NOMBRE,
                    'password' => 'confirmed',
                    'password_confirmation' => '',
                    'email'=>'email|max:100',

                    'max_ej_ventas' =>'required|integer|max:'.$constructora->max_ejecutivos_ventas,
                    'max_ej_bancos' =>'required|integer|max:'.$constructora->max_ejecutivos_bancos,

                ];
            default:return[];
        }
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
