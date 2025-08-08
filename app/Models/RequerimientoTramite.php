<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class RequerimientoTramite
 * @package App\Models
 *
 * @property int $id
 * @property int $id_proyecto
 * @property int $cumplido
 * @property string $nombre
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Proyecto $proyecto
 */
class RequerimientoTramite extends Model
{
    use SoftDeletes;

    /**
     * @const string Constante que representa el límite de caracteres para el nombre
     */
    const MAX_LENGTH_NOMBRE = 40;

    protected $table = 'requerimientos_tramites';

    protected $fillable = ['id_proyecto','nombre'];

    protected $guarded = ['id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto', 'id');
    }

    /**
     * Returns if the requirement was accomplished
     *
     * @param int $id_casa
     *
     * @return boolean
     */
    public function isRequirementAccomplished($id_casa)
    {
        return CasaRequerimientoTramite::where('id_casa', $id_casa)
            ->where('id_requerimiento_tramite', $this->id)
            ->count() > 0;
    }
}
