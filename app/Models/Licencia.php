<?php

namespace App\Models;

use DateTime;
use DateTimeZone;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;
use Illuminate\Support\Collection;

/**
 * Class Licencia
 * @package App\Models
 *
 * @property int $id
 * @property string $fecha_vencimiento
 * @property string $fecha_suspension
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Constructora $constructora
 *
 */
class Licencia extends Model
{
    use SoftDeletes;

    protected $table = 'licencias';

    protected $fillable = ['fecha_vencimiento', 'fecha_suspension'];

    protected $dates = ['deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function constructora()
    {
        return $this->hasOne(Constructora::class, 'id_licencia', 'id');
    }
    
    /**
     * Chequea la licencia
     *
     */
    public function chequearLicencia()
    {
        $constructora = $this->constructora()->first();
        $user = $constructora->user()->first();
        $user->estado = User::NO_ACTIVO;
        $user->save();
        
        $this->restaurarLicencia($constructora);
    }
    
    /**
     * Chequea las licencias vencidas
     *
     * @param \DateTime $datetime
     */
    public static function chequearLicencias($datetime=null)
    {
        if ($datetime === null) {
            $datetime = new DateTime("now", new DateTimeZone(config('app.timezone')));
        }
        $licencias_vencidas = Licencia::whereDate('fecha_suspension', '<=', $datetime->format('Y-m-d'))->get();
        foreach ($licencias_vencidas as $licencia) {
            $licencia->chequearLicencia();
        }
    }

    /**
     * Restaura la licencia y activa el usuario nuevamente
     *
     * @param Constructora $constructora
     */
    public function restaurarLicencia($constructora=null)
    {
        if ($constructora===null) {
            $constructora = $this->constructora()->first();
        }
        $user = $constructora->user()
            ->where('users.estado', User::NO_ACTIVO)
            ->first();
        if (! $user) {
            return;
        }
        $user->estado = User::ACTIVO;
        $user->save();
    }
    
    /**
     * Restaura las licencias enviadas
     *
     * @param Collection $licencias
     */
    public static function restaurarLicencias($licencias)
    {
        /**
         * @var Licencia $licencia
         */
        foreach ($licencias as $licencia) {
            $licencia->restaurarLicencia();
        }
    }
}
