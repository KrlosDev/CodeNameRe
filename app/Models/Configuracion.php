<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Configuracion
 * @package App\Models
 *
 * @static $ACTIVO int
 * @static $INACTIVO int
 *
 * @property int $id
 * @property string $descripcion $descripcion
 * @property int $estatus
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 */
class Configuracion extends Model
{
    use SoftDeletes;

    /**
     * @var int Estado activo del modelo
     */
    public static $ACTIVO=1;
    /**
     * @var int Estado inactivo del modelo
     */
    public static $INACTIVO=0;
    
    /**
     * @const string El nombre de la descripción para la configuración de moneda
     */
    const CONFIGURACION_MONEDA = 'moneda';
    /**
     * @const string El nombre de la descripción para la cantidad máxima de fotos
     * del proyecto
     */
    const CONFIGURACION_MAX_FOTOS_PROYECTO = 'max_fotos';
    
    protected $table = 'configuraciones';

    protected $fillable = ['descripcion', 'contenido'];

    protected $dates = ['deleted_at'];
}
