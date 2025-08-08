<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ConstructorasBroker
 * @package App\Models
 *
 * @property int $id
 * @property int $id_broker
 * @property int $id_constructora
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 */
class ConstructorasBroker extends Model
{
    use SoftDeletes;
     
    protected $table = 'brokers';

    protected $fillable = ['id_broker', 'id_constructora', 'created_at', 'updated_at', 'deleted_at'];

    protected $dates = ['deleted_at'];
}
