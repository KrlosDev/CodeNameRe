<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ClienteCodeudor
 * @package App\Models
 *
 * @property int $id
 * @property int $id_cliente
 * @property int $id_codeudor
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Cliente $cliente
 *
 */
class ClienteCodeudor extends Model
{
    use SoftDeletes;

    protected $table = 'clientes_codeudores';

    protected $fillable = ['id_cliente', 'id_codeudor'];

    protected $dates = ['deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
