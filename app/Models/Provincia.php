<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Provincia
 * @package App\Models
 *
 * @property int $id
 * @property string $nombre
 * @property string $created_at
 * @property string $updated_at
 * @property Distrito $distrito
 *
 */
class Provincia extends Model
{
    protected $table = 'provincias';

    protected $fillable = ['nombre'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function distrito()
    {
        return $this->hasOne(Distrito::class);
    }
}
