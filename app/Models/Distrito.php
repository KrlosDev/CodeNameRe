<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Distrito
 * @package App\Models
 *
 * @property int $id
 * @property string $nombre
 * @property int $id_provincia
 * @property string $created_at
 * @property string $updated_at
 * @property Provincia $provincia
 * @property Corregimiento $corregimiento
 *
 */
class Distrito extends Model
{
    protected $table = 'distritos';

    protected $fillable = ['nombre', 'id_provincia'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function provincia()
    {
        return $this->belongsTo(Provincia::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function corregimiento()
    {
        return $this->hasOne(Corregimiento::class);
    }
}
