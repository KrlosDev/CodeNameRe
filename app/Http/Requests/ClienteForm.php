<?php

namespace App\Http\Requests;

use App\Models\Cliente;
use App\User;
use Illuminate\Http\Request as Req;

/**
 * @property string $name
 * @property string $nombre
 * @property string $password
 * @property string $password_confirmation
 * @property string $email
 * @property string $apellido
 * @property string $identificacion
 * @property string $fecha_nacimiento
 * @property int $estado_civil
 * @property int $id_pais
 * @property int $id_distrito
 * @property string $direccion
 * @property int $tipo_trabajo
 * @property string $empresa
 * @property string $cargo_empresa
 * @property double $salario
 * @property int $años_laborando
 * @property string $direccion_empresa
 * @property string $telefonos_empresa
 * @property string $email_empresa
 * @property string $notas
 */
class ClienteForm extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @param Req $request
     *
     * @return array
     */
    public function rules(Req $request)
    {
        switch ($this->method()) {
            case 'POST':
                return [
                    'name' =>'required|unique:users,name,NULL,id,deleted_at,NULL|max:'.User::MAX_LENGTH_NOMBRE,
                    'nombre' =>'required|max:'.User::MAX_LENGTH_NOMBRE,
                    'password' => 'required|confirmed',
                    'password_confirmation' => 'required',
                    'email'=>'sometimes|nullable|email|max:100',
                    'apellido' =>'required|max:'.User::MAX_LENGTH_NOMBRE,
                    'identificacionc' =>'required|max:25',
                    'fecha_nacimiento' =>'required|date|before:today',
                    'estado_civil' =>'required|integer|min:1',
                    'id_pais' =>'required|integer|min:1',
                    'id_distrito' =>'required|integer|min:1',
                    'direccion' =>'required|max:100',
                    'tipo_trabajo' =>'required|integer|min:1',
                    'empresa' =>'required_if:tipo_trabajo,'.Cliente::EMPLEADO.'|max:'.User::MAX_LENGTH_NOMBRE,
                    'cargo_empresa' =>'required_if:tipo_trabajo,'.Cliente::EMPLEADO.'|max:35',
                    'salario' =>'required_if:tipo_trabajo,'.Cliente::EMPLEADO.'|numeric|min:1',
                    'años_laborando' =>'required_if:tipo_trabajo,'.Cliente::EMPLEADO.'|numeric|min:0',
                    'direccion_empresa' =>'required_if:tipo_trabajo,'.Cliente::EMPLEADO.'|max:100',
                    'telefonos_empresa' =>'max:100',
                    'email_empresa' =>'sometimes|nullable|email|max:100',
                    'notas'=>'max:'.Cliente::MAX_LENGTH_NOTAS,
                ];
            case 'PUT':
                return [
                    'nombre' =>'required|max:'.User::MAX_LENGTH_NOMBRE,
                    'password' => 'confirmed',
                    'password_confirmation' => 'required_with:password|same:password',
                    'email'=>'sometimes|nullable|email|max:100',
                    'apellido' =>'required|max:'.User::MAX_LENGTH_NOMBRE,
                    'empresa' =>'sometimes|required|max:'.User::MAX_LENGTH_NOMBRE,
                    'cargo_empresa' =>'sometimes|required|max:35',
                    'identificacionc' =>'required|max:25',
                    'fecha_nacimiento' =>'required|date|before:today',
                    'estado_civil' =>'required|integer|min:1',
                    'id_pais' =>'required|integer|min:1|exists:paises,id',
                    'id_distrito' =>'required|integer|min:1|exists:distritos,id',
                    'direccion' =>'required|max:100',
                    'tipo_trabajo' =>'required|integer|min:1',
                    'salario' =>'required|numeric|min:1',
                    'años_laborando' =>'sometimes|required|numeric|min:0',
                    'direccion_empresa' =>'sometimes|required|max:100',
                    'telefonos_empresa' =>'sometimes|max:100',
                    'email_empresa' =>'sometimes|nullable|email|max:100',
                    'notas'=>'max:'.Cliente::MAX_LENGTH_NOTAS,
                    'id_banco'=>$request->id_banco != 0 ?'sometimes|exists:bancos,id': '',
               ];
            default:return[];
        }
    }
    
    public function messages()
    {
        return [
            'name.unique' => 'El nombre de usuario ingresado ya se encuentra registrado en el sistema.',
            'required' => 'El campo :attribute es necesario.',
            'max' => 'El campo :attribute debe contener maximo :max caracteres.',
            'min' => 'El campo :attribute debe contener mínimo :min caracteres.',
            'unique'  => 'El campo :attribute ya se encuentra en nuestra base de datos',
            'email' => 'El campo :attribute debe ser un email.',
            'exists' => 'El campo :attribute no existe.',
            'numeric' => 'El campo :attribute debe ser un número válido.',
            'integer' => 'El campo :attribute debe ser un número válido.',
            'string' => 'El campo :attribute debe ser una cadena caracteres.',
            'same' => 'El campo :attribute debe ser idéntico.',
            
        ];
    }
}
