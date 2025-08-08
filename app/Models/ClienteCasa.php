<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ClienteCasa
 * @package App\Models
 *
 * @property int $id
 * @property int $id_cliente
 * @property int $id_casa
 * @property int $id_ej_bancos
 * @property string $created_at
 * @property_at string $updated
 * @property Cliente $cliente
 *
 */
class ClienteCasa extends Model
{
    protected $table = 'clientes_casas';

    protected $fillable = ['id_cliente', 'id_casa', 'id_ej_bancos'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
