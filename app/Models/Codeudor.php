<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * Class Codeudor
 * @package App\Models
 *
 * @const INDEPENDIENTE int
 * @const EMPLEADO int
 * @const ESTADO_CIVIL_SOLTERO int
 * @const ESTADO_CIVIL_CASADO int
 * @const ESTADO_CIVIL_DIVORCIADO int
 * @const ESTADO_CIVIL_UNION_LIBRE int
 * @const ESTADO_CIVIL_VIUDO int
 * @const CASA int
 * @const APARTAMENTO int
 *
 * @property int $id
 * @property string $nombre
 * @property string $apellido
 * @property string $identificacion
 * @property string $fecha_nacimiento
 * @property int $estado_civil
 * @property int $id_pais
 * @property int $id_corregimiento
 * @property int $id_distrito
 * @property string $telefono
 * @property string $direccion
 * @property int $casa_apartamento
 * @property string $email
 * @property int $tipo_trabajo
 * @property string $empresa
 * @property string $cargo_empresa
 * @property double $salario
 * @property int $anios_laborando
 * @property string $direccion_empresa
 * @property string $telefonos_empresa
 * @property string $email_empresa
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Corregimiento $corregimiento
 * @property Pais $pais
 * @property Collection $documentoCodeudor
 * @property Collection $cliente
 *
 */
class Codeudor extends Model
{
    use SoftDeletes;
    
    /**
     * @const int Constante que representa el máximo de caracteres que puede
     * ingresarse en telefono
     */
    const MAX_LENGTH_TELEFONO = 15;
    const MAX_LENGTH_NOMBRE = 60;
    const MAX_LENGTH_APELLIDO = 60;
    const MAX_LENGTH_IDENTIFICACION = 20;
    const MAX_LENGTH_EMAIL = 100;
    const MAX_LENGTH_DIRECCION = 100;
    const MAX_LENGTH_EMPRESA_NOMBRE = 100;
    const MAX_LENGTH_EMPRESA_CARGO = 35;
    const MAX_LENGTH_EMPRESA_DIRECCION = 100;
    const MAX_LENGTH_EMPRESA_EMAIL = 100;
    const MAX_LENGTH_EMPRESA_TELEFONOS = 100;
    /**
     * @const int Constante que representa el estado civil soltero
     */
    const ESTADO_CIVIL_SOLTERO = 1;
    /**
     * @const int Constante que representa el estado civil soltero
     */
    const ESTADO_CIVIL_CASADO = 2;
    /**
     * @const int Constante que representa el estado civil soltero
     */
    const ESTADO_CIVIL_DIVORCIADO = 3;
    /**
     * @const int Constante que representa el estado civil soltero
     */
    const ESTADO_CIVIL_UNION_LIBRE = 4;
    /**
     * @const int Constante que representa el estado civil soltero
     */
    const ESTADO_CIVIL_VIUDO = 5;
    /**
     *
     * @var array Arreglo que contiene los estados civiles
     */
    public static $estados_civiles = [
        self::ESTADO_CIVIL_SOLTERO  => 'SOLTERO',
        self::ESTADO_CIVIL_CASADO => 'CASADO',
        self::ESTADO_CIVIL_DIVORCIADO => 'DIVORCIADO',
        self::ESTADO_CIVIL_UNION_LIBRE => 'UNION_LIBRE',
        self::ESTADO_CIVIL_VIUDO => 'VIUDO'
    ];

    /**
     * @var int Representa el estado independiente
     */
    const INDEPENDIENTE=1;
    /**
     * @var int Representa el estado empleado
     */
    const EMPLEADO=2;

    /**
     * @var array
     */
    public static $estados_trabajo =[
        self::INDEPENDIENTE => 'Indepediente',
        self::EMPLEADO => 'Empleado',
    ];

    /**
     * @var int Representa el estado casa
     */
    const CASA=1;
    /**
     * @var int Representa el estado apartamento
     */
    const APARTAMENTO=2;

    /**
     * @var array
     */
    public static $estados_tipos_residencia = [
        self::CASA => 'Casa',
        self::APARTAMENTO => 'Apartamento ',
    ];

    protected $table = 'codeudores';

    protected $fillable = ['nombre', 'apellido', 'identificacion', 'fecha_nacimiento', 'estado_civil', 'id_pais', 'id_corregimiento', 'telefono', 'direccion', 'casa_apartamento', 'email','tipo_trabajo','empresa','cargo_empresa', 'salario', 'anios_laborando', 'direccion_empresa', 'telefonos_empresa', 'email_empresa'];

    protected $dates = ['deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function corregimiento()
    {
        return $this->belongsTo(Corregimiento::class, 'id_corregimiento', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pais()
    {
        return $this->belongsTo(Pais::class, 'id_pais', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function documentoCodeudor()
    {
        return $this->hasMany(DocumentoCodeudor::class, 'id_co_deudores', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function cliente()
    {
        return $this->belongsToMany(Cliente::class, 'clientes_codeudores', 'id_cliente', 'id_codeudor')->withPivot('id');
    }

    /**
     * @return array
     */
    public static function getEstadosCiviles()
    {
        return self::$estados_civiles;
    }

    /**
     * @param $i
     * @return string
     */
    public static function getNombreVivienda($i)
    {
        return self::$estados_tipos_residencia[$i];
    }

    /**
     * @param $i int
     * @return string
     */
    public static function getNombreTrabajo($i)
    {
        return self::$estados_trabajo[$i];
    }

    /**
     *
     * @param $i int
     * @return string
     */
    public static function getNombreCivil($i)
    {
        return self::$estados_civiles[$i];
    }
}
