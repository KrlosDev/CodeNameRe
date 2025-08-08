<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Input;
use App\Models\ReferenciaPersonal;

class ReferenciaPersonalForm extends Request
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
        switch ($this->method()) {
        
        case 'POST':
            return [
                'nombre' =>'required|max:'.ReferenciaPersonal::MAX_LENGTH_NOMBRE,
                'parentesco' =>'required|max:'.ReferenciaPersonal::MAX_LENGTH_PARENTESCO,
                'telefono' =>'required|max:'.ReferenciaPersonal::MAX_LENGTH_TELEFONO,
            ];
        case 'PUT':
            return [
                'nombre' =>'max:'.ReferenciaPersonal::MAX_LENGTH_NOMBRE,
                'parentesco' =>'max:'.ReferenciaPersonal::MAX_LENGTH_PARENTESCO,
                'telefono' =>'max:'.ReferenciaPersonal::MAX_LENGTH_TELEFONO,
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
