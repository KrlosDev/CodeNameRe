<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Pais
 * @package App\Models
 *
 * @property int $id
 * @property string $nombre
 * @property string $name
 * @property string $nom
 * @property string $iso2
 * @property string $iso3
 * @property string $codigo_telefonico
 * @property string $created_at
 * @property string $updated_at
 */
class Pais extends Model
{
    protected $table = 'paises';

    protected $fillable = ['nombre', 'name', 'nom', 'iso2', 'iso3','codigo_telefonico'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cliente()
    {
        return $this->hasMany(Cliente::class, 'id', 'id_pais');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function documentocliente()
    {
        return $this->hasMany(DocumentoCliente::class, 'id', 'id_pais');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function documentocodeudor()
    {
        return $this->hasMany(DocumentoCodeudor::class, 'id', 'id_pais');
    }
}
