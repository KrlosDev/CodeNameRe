<?php

namespace App\Http\Requests;

use App\Models\Imagen;

class ImagenForm extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "descripcion"       =>      "max:".Imagen::MAX_LENGTH_DESCRIPCION,
            "nombre"            =>      "required|max:".Imagen::MAX_LENGTH_NOMBRE,
            "path"              =>      "max:".Imagen::MAX_LENGTH_PATH,
            "archivo"           =>      "image|mimes:".implode(",", Imagen::$formatos_permitidos)."|max:".Imagen::MAX_PHOTO_SIZE,
        ];
    }
    
    public function messages()
    {
        return [
            'required'          =>      'El campo :attribute es necesario.',
            'max'               =>      'El campo :attribute debe contener maximo :max caracteres.',
            'unique'            =>      'El campo :attribute ya se encuentra en nuestra base de datos',
            'email'             =>      'El campo :attribute debe ser un email.',
            'exists'            =>      'El campo :attribute no existe.',
            'numeric'           =>      'El campo :attribute debe ser un número válido.',
            'integer'           =>      'El campo :attribute debe ser un número válido.',
            'string'            =>      'El campo :attribute debe ser una cadena caracteres.',
            
        ];
    }
}
