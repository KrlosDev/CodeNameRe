<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;
use Illuminate\Support\Collection;

/**
 * Class EjecutivoBancos
 * @package App\Models
 *
 * @property int $id
 * @property int $id_broker
 * @property int $id_user
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Broker $broker
 * @property User $user
 * @property Collection $cliente_casa
 *
 */
class EjecutivoBancos extends Model
{
    use SoftDeletes;

    protected $table = 'ejecutivos_bancos';

    protected $fillable = ['id_broker', 'id_user'];

    protected $dates = ['deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function broker()
    {
        return $this->belongsTo(Broker::class, 'id_broker', 'id');
    }

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
    public function clienteCasa()
    {
        return $this->hasMany(ClienteCasa::class);
    }
    
    /**
     * Retorna la cantidad de ejecutivos asignados al broker provisto
     *
     * @param int $id_broker
     * @return int
     */
    public static function getTotalAsignadosBroker($id_broker)
    {
        return EjecutivoBancos::where('id_broker', $id_broker)
            ->whereNull('deleted_at')
            ->count();
    }
    
    /**
     * Retorna la cantidad de ejecutivos de venta asignados a la constructora provista
     *
     * @param Constructora $constructora
     * @return int
     */
    public static function getTotalAsignadosConstructora($constructora)
    {
        $cantidad = 0;
        foreach ($constructora->brokers as $broker) {
            $cantidad += self::getTotalAsignadosBroker($broker->id);
        }
        return $cantidad;
    }
}
