<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;
use Illuminate\Support\Collection;

/**
 * Class Constructora
 * @package App\Models
 *
 * @const MAX_LENGTH_EJECUTIVOS_BROKERS
 *
 * @property int $id
 * @property int $id_licencia
 * @property int $id_user
 * @property int $max_brokers
 * @property int $max_ejecutivos_ventas
 * @property int $max_ejecutivos_bancos
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Licencia $licencia
 * @property Collection $proyecto
 * @property User $user
 * @property Collection $constructora_broker
 * @property Collection $casas
 * @property Collection $brokers
 */
class Constructora extends Model
{
    use SoftDeletes;

    const MAX_LENGTH_EJECUTIVOS_BROKERS = 5;

    protected $table = 'constructoras';

    protected $fillable = ['id_licencia', 'id_user', 'max_brokers', 'max_ej_ventas', 'max_ej_bancos'];

    protected $dates = ['deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function licencia()
    {
        return $this->hasOne(Licencia::class, 'id', 'id_licencia');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function proyecto()
    {
        return $this->hasMany(Proyecto::class, 'id_constructora', 'id');
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
    public function casas()
    {
        return $this->hasMany(Casa::class, 'id', 'id_constructora');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function brokers()
    {
        return $this->belongsToMany(Broker::class, 'constructoras_brokers', 'id_constructora', 'id_broker');
    }
}
