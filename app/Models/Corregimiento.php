<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class Corregimiento
 * @package App\Models
 *
 * @property int $id
 * @property string $nombre
 * @property int $id_distrito
 * @property string $created_at
 * @property string $updated_at
 * @property Distrito $distrito
 * @property Collection $cliente
 * @property Codeudor $codeudor
 *
 */
class Corregimiento extends Model
{
    protected $table = 'corregimientos';

    protected $fillable = ['nombre', 'id_distrito'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function distrito()
    {
        return $this->belongsTo(Distrito::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cliente()
    {
        return $this->hasMany(Cliente::class, 'id', 'id_corregimiento');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function codeudor()
    {
        return $this->belongsTo(Codeudor::class);
    }
}
