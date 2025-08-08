<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class Banco
 * @package App\Models
 *
 * @property int $id
 * @property string $nombre
 * @property string $created_at
 * @property string $updated_at
 * @property $usuarios Collection
 */
class Banco extends Model
{
    /**
     * @const int Constante que representa el límite de caracteres que se pueden
     * ingresar en el campo nombre
     */
    const MAX_LENGTH_NOMBRE = 50;
    
    protected $table = 'bancos';
 
    protected $fillable = ['nombre'];
 
    protected $guarded = ['id'];
    
    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function usuarios()
    {
        return $this->hasMany(User::class, 'id_banco', 'id');
    }
}
