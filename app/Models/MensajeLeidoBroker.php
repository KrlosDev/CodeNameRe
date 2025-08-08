<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class MensajeLeidoBroker
 * @package App\Models
 *
 * @property int $id
 * @property int $id_mensaje
 * @property int $id_broker
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Broker $broker
 *
 */
class MensajeLeidoBroker extends Model
{
    use SoftDeletes;

    protected $table = 'mensajes_leidos_broker';

    protected $fillable = ['id_mensaje', 'id_broker'];

    protected $dates = ['deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function broker()
    {
        return $this->belongsTo(Broker::class);
    }
}
