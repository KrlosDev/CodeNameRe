<?php

namespace App\Http\Requests;

use App\Models\Mensaje;

class MensajeForm extends Request
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
                    'titulo' =>'required|max:'.Mensaje::MAX_LENGTH_TITULO,
                    'descripcion' =>'required|max:'.Mensaje::MAX_LENGTH_DESCRIPCION,
                    'para' =>'required|exists:ejecutivos_ventas,id',
                ];
            
            default:return[];
        }
    }
    
    public function messages()
    {
        return [
            'required' => 'El campo :attribute es necesario.',
 
        ];
    }
}
