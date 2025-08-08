<?php

namespace App\Http\Requests;

use App\Models\DocumentoCliente;

/**
 * Class DocumentosForm
 * @package App\Http\Requests
 *
 * @property int usuario
 * @property int clienteD
 * @property int id_documento
 * @property int id_pais_identificacion
 * @property string fecha_expiracion
 */
class DocumentosForm extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id_documento' => 'required|integer|in:'.implode(',', array_keys(DocumentoCliente::$tipos)),
            'id_pais_identificacion' => 'required_if:id_documento,'.DocumentoCliente::TIPO_CEDULA.'|integer|min:1|exists:paises,id',
            'imagen' => 'required',
            'fecha_expiracion' => 'required|date',
            'clienteD' => 'required|int',
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
            'numeric' => 'El campo :attribute debe ser un número válido.',
            'integer' => 'El campo :attribute debe ser un número válido.',
            'string' => 'El campo :attribute debe ser una cadena caracteres.',
            'required_if' => 'El campo país debe ser seleccionado.',
            'in' => 'El tipo de documento ingresado no es válido.',
            
        ];
    }
}
