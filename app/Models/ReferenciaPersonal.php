<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ReferenciaPersonal
 * @package App\Models
 *
 * @const int MAX_LENGTH_NOMBRE
 * @const int MAX_LENGTH_PARENTESCO
 * @const int MAX_LENGTH_TELEFONO
 * @const int MAX_REFERENCIAS
 *
 * @property int $id
 * @property int $id_cliente
 * @property string $nombre
 * @property string $parentesco
 * @property string $telefono
 * @property string $created_at
 * @property string $updated_at
 * @property Cliente $cliente
 */
class ReferenciaPersonal extends Model
{
    /**
     * @const int Constante que representa el límite de caracteres que se pueden
     * ingresar en el campo nombre
     */
    const MAX_LENGTH_NOMBRE = 50;
    /**
     * @const int Constante que representa el límite de caracteres que se pueden
     * ingresar en el campo nombre
     */
    const MAX_LENGTH_PARENTESCO = 20;
    /**
     * @const int Constante que representa el límite de caracteres que se pueden
     * ingresar en el campo nombre
     */
    const MAX_LENGTH_TELEFONO = 15;
    /**
     * @const int Constante que representa el máximo de referencias personales
     * para un cliente
     */
    const MAX_REFERENCIAS = 3;
    
    protected $table = 'referencias_personales';
 
    protected $fillable = ['id_cliente','nombre','parentesco','telefono'];
 
    protected $guarded = ['id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'id', 'id_cliente')
            ->whereNull('clientes.deleted_at');
    }
    
    /**
     *
     * @param int $numero
     * @return ReferenciaPersonal
     */
    public static function generarReferencia($numero)
    {
        $referencia = new ReferenciaPersonal();
        $referencia->numero = $numero;
        return $referencia;
    }
}
