<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * Class Cliente
 * @package App\Models
 *
 * @const MAX_LENGTH_NOTAS int
 * @const INDEPENDIENTE int
 * @const EMPLEADO int
 * @const SOLTERO int
 * @const CASADO int
 * @const DIVORCIADO int
 * @const UNION_LIBRE int
 * @const VIUDO int
 * @const CASA int
 * @const APARTAMENTO int
 *
 * @static $estados_trabajo array
 * @static $estados_civiles array
 * @static $estados_tipos_residencia array
 *
 * @property int $id
 * @property string $nombre
 * @property string $apellido
 * @property string $identificacion
 * @property string $fecha_nacimiento
 * @property int $estado_civil
 * @property int $id_pais
 * @property int $id_broker
 * @property int $id_corregimiento
 * @property int $id_distrito
 * @property string $direccion
 * @property int $casa_apartamento
 * @property string $email
 * @property int $id_user
 * @property int $tipo_trabajo
 * @property string $empresa
 * @property string $cargo_empresa
 * @property double $salario
 * @property int $anios_laborando
 * @property string $direccion_empresa
 * @property string $telefonos_empresa
 * @property string $email_empresa
 * @property string $notas
 * @property int $id_banco
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property User $user
 * @property Pais $pais
 * @property Collection $pago
 * @property Broker $broker
 * @property Collection $telefonoCliente
 * @property Distrito $distrito
 * @property Collection $mensaje
 * @property Collection $clienteCasa
 * @property Collection $documentoCliente
 * @property ClienteCodeudor $clienteCodeudor
 * @property Collection $referenciasPersonales
 * @property Collection $banco
 * @property Collection $codeudor
 */
class Cliente extends Model
{
    use SoftDeletes;

    /**
     * @const int Constante que representa el máximo de caracteres que puede
     * ingresarse en notas
     */
    const MAX_LENGTH_NOTAS=500;
    const MAX_LENGTH_APELLIDO=60;
    const MAX_LENGTH_IDENTIFICACION=20;
    const MAX_LENGTH_TELEFONO=20;
    const MAX_LENGTH_DIRECCION=100;
    const MAX_LENGTH_EMPRESA_NOMBRE=100;
    const MAX_LENGTH_EMPRESA_CARGO=35;
    const MAX_LENGTH_EMPRESA_DIRECCION=100;
    const MAX_LENGTH_EMPRESA_EMAIL=100;
    const MAX_LENGTH_EMPRESA_TELEFONO=100;

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
     * @var int Representa el estado soltero
     */
    const SOLTERO=1;
    /**
     * @var int Representa el estado casado
     */
    const CASADO=2;
    /**
     * @var int Representa el estado divorciado
     */
    const DIVORCIADO=3;
    /**
     * @var int Representa el estado unión libre
     */
    const UNION_LIBRE=4;
    /**
     * @var int Representa el estado viudo
     */
    const VIUDO=5;

    /**
     * @var array
     */
    public static $estados_civiles = [
        self::SOLTERO => 'Soltero',
        self::CASADO => 'Casado',
        self::DIVORCIADO => 'Divorciado',
        self::UNION_LIBRE => 'Unión libre',
        self::VIUDO => 'Viudo',
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

    protected $table = 'clientes';

    protected $fillable = ['nombre', 'apellido', 'identificacion', 'fecha_nacimiento', 'estado_civil', 'id_pais', 'id_corregimiento', 'direccion', 'casa_apartamento', 'email', 'id_user','tipo_trabajo','empresa','cargo_empresa', 'salario', 'anios_laborando', 'direccion_empresa', 'telefonos_empresa', 'email_empresa', 'notas', 'id_banco'];

    protected $dates = ['deleted_at'];

    /**
     * Retorna verdadero si el tipo de trabajo es independiente
     *
     * @return bool
     */
    public function isIndependiente()
    {
        return $this->tipo_trabajo==self::INDEPENDIENTE;
    }

    /**
     * Retorna verdadero si el tipo de trabajo es empleado
     *
     * @return bool
     */
    public function isEmpleado()
    {
        return $this->tipo_trabajo==self::EMPLEADO;
    }

    /**
     * Retorna verdadero si el estado civil es soltero
     *
     * @return bool
     */
    public function isSoltero()
    {
        return $this->estado_civil==self::SOLTERO;
    }

    /**
     * Retorna verdadero si el estado civil es casado
     *
     * @return bool
     */
    public function isCasado()
    {
        return $this->estado_civil==self::CASADO;
    }

    /**
     * Retorna verdadero si el estado civil es unión libre
     *
     * @return bool
     */
    public function isUnionLibre()
    {
        return $this->estado_civil==self::UNION_LIBRE;
    }

    /**
     * Retorna verdadero si el estado civil es viudo
     *
     * @return bool
     */
    public function isViudo()
    {
        return $this->estado_civil==self::VIUDO;
    }

    /**
     * Retorna verdadero si el estado civil es divorciado
     *
     * @return bool
     */
    public function isDivorciado()
    {
        return $this->estado_civil==self::DIVORCIADO;
    }

    /**
     * Retorna verdadero si el tipo de vivienda es casa
     *
     * @return bool
     */
    public function isCasa()
    {
        return $this->casa_apartamento==self::CASA;
    }

    /**
     * Retorna verdadero si el tipo de vivienda es apartamento
     *
     * @return bool
     */
    public function isApartamento()
    {
        return $this->casa_apartamento==self::APARTAMENTO;
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'id_user');
    }
    
    /**
     *
     * @return  \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pais()
    {
        return $this->belongsTo(Pais::class, 'id_pais', 'id');
    }
    
    /**
     *
     * @return  \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pago()
    {
        return $this->hasMany(Pago::class, 'id_cliente', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function broker()
    {
        return $this->belongsTo(Broker::class, 'id_broker', 'id');
    }
    
    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function telefonoCliente()
    {
        return $this->hasMany(TelefonoCliente::class, 'id_cliente', 'id');
    }
    
    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function distrito()
    {
        return $this->belongsTo(Distrito::class, 'id_distrito', 'id');
    }
    
    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mensaje()
    {
        return $this->hasMany(Mensaje::class, 'id_cliente', 'id');
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function clienteCasa()
    {
        return $this->belongsToMany(Casa::class, 'clientes_casas', 'id_cliente', 'id_casa')->withPivot(['id','id_ej_bancos'])->wherePivot('deleted_at', null);
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function documentoCliente()
    {
        return $this->hasMany(DocumentoCliente::class, 'id_cliente', 'id');
    }
    
    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function clienteCodeudor()
    {
        return $this->hasOne(ClienteCodeudor::class);
    }
    
    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function banco()
    {
        return $this->hasOne(Banco::class, 'id', 'id_banco')
            ->whereNull('bancos.deleted_at');
    }
    
    /**
     *
     * @return  \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function referenciasPersonales()
    {
        return $this->hasMany(ReferenciaPersonal::class, 'id_cliente', 'id')
            ->whereNull('referencias_personales.deleted_at');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function codeudor()
    {
        return $this->belongsToMany(Codeudor::class, 'clientes_codeudores', 'id_cliente', 'id_codeudor');
    }

    /**
     * @return array
     */
    public static function getEstadosCiviles()
    {
        return self::$estados_civiles;
    }

    /**
     * @return array
     */
    public static function getTipoTrabajos()
    {
        return self::$estados_trabajo;
    }

    /**
     * @return array
     */
    public static function getTipoVivienda()
    {
        return self::$estados_tipos_residencia;
    }

    /**
     * Returns the name associated to this index in the array
     *
     * @param $i int
     * @return string
     */
    public static function getNombreTrabajo($i)
    {
        return self::$estados_trabajo[$i];
    }

    /**
     * Returns the name associated to this index in the array
     *
     * @param $i int
     * @return string
     */
    public static function getNombreVivienda($i)
    {
        return self::$estados_tipos_residencia[$i];
    }

    /**
     * Returns the name associated to this index in the array
     *
     * @param $i int
     * @return string
     */
    public static function getNombreCivil($i)
    {
        return self::$estados_civiles[$i];
    }
}
