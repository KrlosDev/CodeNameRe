<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class RequerimientoTramite
 * @package App\Models
 *
 * @property int $id
 * @property int $id_casa
 * @property int $id_requerimiento_tramite
 * @property string $created_at
 * @property string $updated_at
 * @property Casa $casa
 * @property RequerimientoTramite $requerimiento_tramite
 */
class CasaRequerimientoTramite extends Model
{
    protected $table = 'casas_requerimientos_tramites';

    protected $fillable = ['id_casa','id_requerimiento_tramite'];

    protected $guarded = ['id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function casa()
    {
        return $this->belongsTo(Casa::class, 'id_casa', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function requerimiento_tramite()
    {
        return $this->belongsTo(RequerimientoTramite::class, 'id_requerimiento_tramite', 'id');
    }
}
