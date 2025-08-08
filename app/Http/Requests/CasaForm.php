<?php

namespace App\Http\Requests;

class CasaForm extends Request
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
                    'cmts2_total' =>'required|numeric|min:1',
                    'cmts2_construccion' =>'required|numeric|min:0',
                    'cmts2_adicionales' =>'numeric|min:0',
                    'crecamaras' =>'required|integer|min:1',
                    'cbanos' =>'required|integer|min:1',
                    'cmonto_separacion' =>'required|numeric|min:1',
                    'cmonto_abono_inicial' =>'required|numeric|min:1',
                    'cmonto_mts2_adicional' =>'required|numeric|min:0',
                    'cvalor' =>'required|numeric|min:1',
                    'cmodelo'=>'required|string',
               ];
            case 'PUT':
                return [
                    'mts2_total_u' =>'sometimes|numeric|min:1',
                    'mts2_construccion_u' =>'sometimes|numeric|min:0',
                    'mts2_adicionales_u' =>'numeric|min:0',
                    'recamaras_u' =>'sometimes|integer|min:1',
                    'banos_u' =>'sometimes|integer|min:1',
                    'monto_separacion_u' =>'numeric|min:1|nullable',
                    'monto_abono_inicial_u' =>'numeric|min:1|nullable',
                    'monto_mts2_adicional_u' =>'numeric|min:0',
                    'valor_u' =>'numeric|min:1',
                    'modelo_u'=>'string',
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
