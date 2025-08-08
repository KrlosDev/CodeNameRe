<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ConstructoraBroker
 * @package App\Models
 *
 * @property int $id
 * @property int $id_broker
 * @property int $id_constructora
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Broker $broker
 * @property Constructora $constructora
 *
 */
class ConstructoraBroker extends Model
{
    use SoftDeletes;

    protected $table = 'constructoras_brokers';

    protected $fillable = ['id_broker', 'id_constructora'];

    protected $dates = ['deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function broker()
    {
        return $this->belongsTo(Broker::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function constructora()
    {
        return $this->belongsTo(Constructora::class);
    }
}
