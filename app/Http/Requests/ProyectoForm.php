<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Input;
use App\Models\Proyecto;

class ProyectoForm extends Request
{
    /*public function all()
    {
        // Include the next line if you need form data, too.
        $request = Input::all();
        if ($this->route('id')) {
            $request['id'] = $this->route('id');
        }
        return $request;
    }*/

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
                'codigo' =>'required|max:'.Proyecto::MAX_LENGTH_CODIGO,
                'nombre' =>'required|max:'.Proyecto::MAX_LENGTH_NOMBRE,
                'estado' =>'required|integer',
                'descripcion' =>'required|max:'.Proyecto::MAX_LENGTH_DESCRIPCION,
                //'id_constructora' =>'required|integer|exists:constructoras,id',
                //Casa
                'mts2_total' =>'required|numeric|min:1',
                'mts2_construccion' =>'required|numeric|min:0',
                'recamaras' =>'required|integer|min:1',
                'banos' =>'required|integer|min:1',
                'monto_separacion' =>'required|numeric|min:1',
                'monto_abono_inicial' =>'required|numeric|min:1',
                'monto_mts2_adicional' =>'required|numeric|min:0',
                'valor' =>'required|numeric|min:1',
                'modelo'=>'required|string',
            ];
            case 'PUT':
                return [
                    'nombre' =>'required|max:'.Proyecto::MAX_LENGTH_NOMBRE,
                    'estado' =>'required|integer',
                    'descripcion' =>'required|max:'.Proyecto::MAX_LENGTH_DESCRIPCION,
                ];
            default:return[];
        }
    }


    public function messages()
    {
        return [
            'required' => 'El campo :attribute es necesario.',
            'max' => 'El campo :attribute debe contener maximo :max caracteres.',
            'unique'  => 'El campo :attribute ya se encuentra en nuestra base de datos',
            'email' => 'El campo :attribute debe ser un email.',
            'exists' => 'El campo :attribute no existe.',
            'numeric' => 'El campo :attribute debe ser un número válido.',
            'integer' => 'El campo :attribute debe ser un número válido.',
            'string' => 'El campo :attribute debe ser una cadena caracteres.',
            
        ];
    }
}
