<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * Class Proyecto
 * @package App\Models
 *
 * @const int MAX_LENGTH_CODIGO
 * @const int MAX_LENGTH_NOMBRE
 * @const int MAX_LENGTH_DESCRIPCION
 * @const int ESTADO_ACTIVO
 * @const int ESTADO_SUSPENDIDO
 * @const int ESTADO_CULMINADO
 *
 * @static array $estados
 *
 * @property int $id
 * @property string $codigo
 * @property string $nombre
 * @property int $estado
 * @property string $descripcion
 * @property int $id_constructora
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Constructora $constructora
 * @property Collection $casas
 * @property Collection $imagenes
 * @property Collection $requerimientosTramites
 *
 */
class Proyecto extends Model
{
    use SoftDeletes;

    /**
     * @const int
     */
    const ESTADO_ACTIVO=1;
    /**
     * @const int
     */
    const ESTADO_SUSPENDIDO=2;
    /**
     * @const int
     */
    const ESTADO_CULMINADO=3;
    /**
     * @var array
     */
    public static $estados = [
        self::ESTADO_ACTIVO => "Activo",
        self::ESTADO_SUSPENDIDO => "Suspendido",
        self::ESTADO_CULMINADO => "Culminado",
    ];

    /**
     * @const int Constante que representa el máximo de caracteres para
     * el código
     */
    const MAX_LENGTH_CODIGO = 10;
    /**
     * @const int Constante que representa el máximo de caracteres para
     * el nombre
     */
    const MAX_LENGTH_NOMBRE = 100;
    /**
     * @const int Constante que representa el máximo de caracteres para
     * la descripción
     */
    const MAX_LENGTH_DESCRIPCION = 300;

    protected $table = 'proyectos';

    protected $fillable = ['codigo', 'nombre', 'estado', 'descripcion', 'id_constructora'];

    protected $dates = ['deleted_at'];

    /**
     * @return bool
     */
    public function isActivo()
    {
        return $this->estado==self::ESTADO_ACTIVO;
    }

    /**
     * @return bool
     */
    public function isSuspendido()
    {
        return $this->estado==self::ESTADO_SUSPENDIDO;
    }

    /**
     * @return bool
     */
    public function isCulminado()
    {
        return $this->estado==self::ESTADO_CULMINADO;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function constructora()
    {
        return $this->belongsTo(Constructora::class, 'id_constructora', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function casas()
    {
        return $this->hasMany(Casa::class, 'id_proyecto', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function imagenes()
    {
        return $this->belongsToMany(Imagen::class, 'proyectos_imagenes', 'id_proyecto', 'id_imagen')->withPivot('id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function requerimientosTramites()
    {
        return $this->hasMany(RequerimientoTramite::class, 'id_proyecto', 'id');
    }
    
    /**
     * Retorna la cantidad de casas asignadas a brokers actualmente en el proyecto
     *
     * @return int
     */
    public function getCasasAsignadas()
    {
        return Proyecto::join('casas', 'proyectos.id', '=', 'casas.id_proyecto')
            ->whereNotNull('casas.id_broker')
            ->whereNull('casas.deleted_at')
            ->where('proyectos.id', $this->id)
            ->count();
    }
    
    /**
     * Retorna las propiedades disponibles en el proyecto
     *
     * @return Collection
     */
    public function getPropiedadesDisponibles()
    {
        return $this->casas()->whereNull('casas.id_broker')->get();
    }
    
    /**
     * Retorna las propiedades ocupadas en el proyecto
     *
     * @return Collection
     */
    public function getPropiedadesOcupadas()
    {
        return $this->casas()->whereNotNull('casas.id_broker')->get();
    }

    /**
     * @param int $i
     * @return string
     */
    public function getEstado($i)
    {
        return self::$estados[$i];
    }
}
