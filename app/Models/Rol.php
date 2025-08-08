<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

/**
 * Class Rol
 * @package App\Models
 *
 * @property int $id
 * @property string $nombre
 * @property string $created_at
 * @property string $updated_at
 * @property User $user
 */
class Rol extends Model
{
    protected $table = 'roles';

    protected $fillable = ['nombre'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
