<?php

namespace App\Http\Requests;

use App\User;
use Illuminate\Support\Facades\Input;

class UserForm extends Request
{
    public function all()
    {
        // Include the next line if you need form data, too.
        $request = Input::all();
        if ($this->route('id')) {
            $request['id'] = $this->route('id');
        }
        return $request;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'email'=>'email|max:100',
            'password'=>'required|max:100',
            'name' =>'required|max:60|unique:users,name,NULL,id,deleted_at,NULL',
            'nombre' =>'required|max:'.User::MAX_LENGTH_NOMBRE,
            'id_rol' =>'required|integer|exists:roles',
            
        ];
    }

    public function messages()
    {
        return [
            'required' => 'El campo :attribute es necesario.',
            'max' => 'El campo :attribute debe contener maximo :max caracteres.',
            'unique'  => 'El campo :attribute ya se encuentra en nuestra base de datos',
            'email' => 'El campo :attribute debe ser un email.',
            'exists' => 'El campo :attribute no existe.',
        ];
    }
}
