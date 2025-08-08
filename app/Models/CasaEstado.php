<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CasaEstado
 * @package App\Models
 *
 * @property int $id
 * @property int $id_broker
 * @property string $nombre
 * @property string $old_association
 * @property string $slug
 * @property string $created_at
 * @property string $updated_at
 * @property Broker $broker
 */
class CasaEstado extends Model
{
    /**
     * @const int Constante que representa el límite de caracteres que se pueden
     * ingresar en el campo nombre
     */
    const MAX_LENGTH_NOMBRE = 40;
    /**
     * @const int Constante que representa el límite de caracteres que se pueden
     * ingresar en el campo slug
     */
    const MAX_LENGTH_SLUG = 25;

    protected $table = 'casas_estados';

    protected $fillable = ['id_broker','nombre','old_association','slug'];

    protected $guarded = ['id'];

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function broker()
    {
        return $this->hasOne(Broker::class, 'id', 'id_broker');
    }
}
