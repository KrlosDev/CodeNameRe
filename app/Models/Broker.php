<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * Class Broker
 * @package App\Models
 *
 * @property int $id
 * @property int $id_user
 * @property int $id_constructora
 * @property int $max_ej_ventas
 * @property int $max_ej_bancos
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property User $user
 * @property Collection $casa
 * @property Collection $casasEstados
 * @property Collection $ejecutivoVentas
 * @property Collection $porcentajeBroker
 * @property Collection $ejecutivoBancos
 * @property Collection $clientes
 * @property Collection $mjsLeidoBroker
 * @property Collection $constructora
 */
class Broker extends Model
{
    use SoftDeletes;

    protected $table = 'brokers';

    protected $fillable = ['id_user', 'id_constructora', 'max_ej_ventas', 'max_ej_bancos'];

    protected $dates = ['deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'id_user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function casa()
    {
        return $this->hasMany(Casa::class, 'id_broker', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function casasEstados()
    {
        return $this->hasMany(CasaEstado::class, 'id_broker', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ejecutivoVentas()
    {
        return $this->hasMany(EjecutivoVentas::class, 'id', 'id_broker');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function porcentajeBroker()
    {
        return $this->hasMany(PorcentajeBroker::class, 'id_broker', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ejecutivoBancos()
    {
        return $this->hasMany(EjecutivoBancos::class, 'id', 'id_broker');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'id_broker', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mjsLeidoBroker()
    {
        return $this->hasMany(MensajeLeidoBroker::class);
    }

    /**
     * @return mixed
     */
    public function constructora()
    {
        return $this->belongsToMany(Constructora::class, 'constructoras_brokers', 'id_broker', 'id_constructora')->withPivot('id');
    }

    /**
     * @param $id int
     * @return int
     */
    public static function msjPorLeer($id)
    {
        $mensajes = Mensaje::where('leido_broker', '=', 0)
            ->where('id_broker', '=', $id)
            ->whereNull('mensajes.broker_deleted_at')
            ->count();

        return $mensajes;
    }

    /**
     * Retorna las casas asignadas para el broker en el proyecto determinado
     *
     * @param int $id_proyecto
     * @return Collection
     */
    public function casasAsignadasEnProyecto($id_proyecto)
    {
        return $this->casa()->where('casas.id_proyecto', $id_proyecto)->get();
    }
    
    /**
     * Retorna la cantidad de casas asignadas para el broker en el proyecto determinado
     *
     * @param int $id_proyecto
     * @return int
     */
    public function countCasasAsignadasEnProyecto($id_proyecto)
    {
        return $this->casa()->where('casas.id_proyecto', $id_proyecto)->count();
    }
    
    /**
     * Retorna las casas asignadas y ocupadas para el broker que se encuentren ocupadas
     * parar el proyecto determinado
     *
     * @param int $id_proyecto
     * @return Collection
     */
    public function casasAsignadasOcupadasEnProyecto($id_proyecto)
    {
        return $this->casa()->where('casas.id_proyecto', $id_proyecto)
            ->whereNotNull('casas.id_casa_estado')
            ->get();
    }
    
    /**
     * Retorna la cantidad de casas asignadas y ocupadas para el broker que se encuentren ocupadas
     * parar el proyecto determinado
     *
     * @param int $id_proyecto
     * @return int
     */
    public function countCasasAsignadasOcupadasEnProyecto($id_proyecto)
    {
        return $this->casa()->where('casas.id_proyecto', $id_proyecto)
            ->whereNotNull('casas.id_casa_estado')
            ->count();
    }
    
    /**
     * Retorna las casas asignadas y disponibles para el broker que se encuentren ocupadas
     * parar el proyecto determinado
     *
     * @param int $id_proyecto
     * @return Collection
     */
    public function casasAsignadasDisponiblesEnProyecto($id_proyecto)
    {
        return $this->casa()->where('casas.id_proyecto', $id_proyecto)
            ->whereNull('casas.id_casa_estado')
            ->get();
    }
    
    /**
     * Retorna la cantidad de casas asignadas y disponibles para el broker que se encuentren ocupadas
     * parar el proyecto determinado
     *
     * @param int $id_proyecto
     * @return int
     */
    public function countCasasAsignadasDisponiblesEnProyecto($id_proyecto)
    {
        return $this->casa()->where('casas.id_proyecto', $id_proyecto)
            ->whereNull('casas.id_casa_estado')
            ->count();
    }
    
    /**
     * Retorna el PorcentajeProyecto para el proyecto determinado
     *
     * @param int $id_proyecto
     * @return PorcentajeBroker|mixed
     */
    public function porcentajeEnProyecto($id_proyecto)
    {
        return $this->porcentajeBroker()
            ->where('porcentaje_broker.id_proyecto', $id_proyecto)
            ->first();
    }
    
    /**
     * Retorna los proyectos donde se encuentra el broker asignado
     *
     * @return Proyecto[]
     */
    public function getProyectos()
    {
        $proyectos = [];
        $casas = $this->casa()->get();
        if (! empty($casas)) {
            $ids = array_map(function ($o) {
                return $o->id_proyecto;
            }, $casas->all());
            $proyectos = Proyecto::whereIn('proyectos.id', $ids)->get();
        }
        return $proyectos;
    }

    /**
     * Returns the total of monto_separacion for all Casas in Proyecto
     *
     * @param Proyecto $proyecto
     *
     * @return double
     */
    public function getTotalMontoSeparacionByProyecto(Proyecto $proyecto)
    {
        return $this->casa()
            ->where('casas.id_proyecto', $proyecto->id)
            ->sum('casas.monto_separacion');
    }

    /**
     * Returns the total of valor for all Casas in Proyecto
     *
     * @param Proyecto $proyecto
     *
     * @return double
     */
    public function getTotalMontoValorByProyecto(Proyecto $proyecto)
    {
        return $this->casa()
            ->where('casas.id_proyecto', $proyecto->id)
            ->sum('casas.valor');
    }

    /**
     * Returns an array initialized to 0 with all keys from the $estados and add $additionalKeys
     * to it
     *
     * @param Collection $states
     * @param array $additionalKeys
     *
     * @return array
     */
    public static function generateEmptyArrayWithEstados($states, $additionalKeys=['null'])
    {
        return array_fill_keys(
            array_merge(
                $states->map(function ($o) {
                    return $o->id;
                })->all(),
                $additionalKeys
            ),
            0
        );
    }
}
