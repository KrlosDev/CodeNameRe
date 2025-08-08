<?php

namespace App\Models;

use App\User;
use Illuminate\Support\Facades\Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Mensaje
 * @package App\Models
 *
 * @property int $id
 * @property int $id_cliente
 * @property string $titulo
 * @property string $descripcion
 * @property int $id_broker
 * @property int $id_ej_ventas
 * @property int $leido_broker
 * @property int $leido_ventas
 * @property string $ventas_deleted_at
 * @property string $broker_deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Cliente $cliente
 * @property Broker $broker
 * @property EjecutivoVentas $ejVentas
 *
 */
class Mensaje extends Model
{
    use SoftDeletes;

    const MAX_LENGTH_TITULO = 100;

    const MAX_LENGTH_DESCRIPCION = 600;

    protected $table = 'mensajes';

    protected $fillable = ['id_cliente', 'titulo', 'descripcion', 'id_broker', 'id_ej_ventas', 'leido_broker','leido_ventas', 'ventas_deleted_at', 'broker_deleted_at'];

    protected $dates = ['deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'id', 'id_cliente');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function broker()
    {
        return $this->hasOne(Broker::class, 'id', 'id_broker');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function ejVentas()
    {
        return $this->hasOne(EjecutivoVentas::class, 'id', 'id_ej_ventas');
    }
    
    /**
     * Retorna el css indicado si el mensaje ha sido leído por el usuario o no
     *
     * @return string
     */
    public function isLeidoCss()
    {
        /**
         * @var User $user
         */
        $user = Auth::user();
        if ($user->isBroker()) {
            if ($this->leido_broker == 0) {
                return "info";
            }
        } elseif ($user->isEjVentas()) {
            if ($this->leido_ventas == 0) {
                return "info";
            }
        }
        return "";
    }
}
