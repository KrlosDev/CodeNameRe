<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PorcentajeBroker
 * @package App\Models
 *
 * @property int $id
 * @property int $id_broker
 * @property int $id_proyecto
 * @property double $porcentaje
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Broker $broker
 *
 */
class PorcentajeBroker extends Model
{
    use SoftDeletes;
    
    protected $table = 'porcentaje_broker';

    protected $fillable = ['id_broker', 'id_proyecto', 'porcentaje'];

    protected $dates = ['deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function broker()
    {
        return $this->belongsTo(Broker::class, 'id', 'id_user');
    }
}
