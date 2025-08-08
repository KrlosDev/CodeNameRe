<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class TelefonoCliente
 * @package App\Models
 *
 * @property int $id
 * @property int $id_cliente
 * @property string $telefono
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 */
class TelefonoCliente extends Model
{
    use SoftDeletes;

    protected $table = 'telefonos_clientes';

    protected $fillable = ['id_cliente', 'telefono'];

    protected $dates = ['deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id', 'id_cliente');
    }
}
