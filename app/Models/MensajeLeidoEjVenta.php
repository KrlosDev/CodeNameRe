<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class MensajeLeidoEjVenta
 * @package App\Models
 *
 * @property int $id
 * @property int $id_mensaje
 * @property int $id_broker
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property EjecutivoVentas $ejecutivoVentas
 *
 */
class MensajeLeidoEjVenta extends Model
{
    use SoftDeletes;

    protected $table = 'mensajes_leidos_ej_ventas';

    protected $fillable = ['id_mensaje', 'id_ej_ventas'];

    protected $dates = ['deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ejecutivoVentas()
    {
        return $this->belongsTo(EjecutivoVentas::class);
    }
}
