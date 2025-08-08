<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Pago
 * @package App\Models
 *
 * @const int TIPO_PAGO_MONTO_INICIAL
 * @const int TIPO_PAGO_MONTO_SEPARACION
 * @const int TIPO_PAGO_MTS_ADICIONALES
 * @const int FORMA_PAGO_TRANSFERENCIA
 * @const int FORMA_PAGO_EFECTIVO
 * @const int FORMA_PAGO_DEPOSITO
 * @const int FORMA_PAGO_TARJETA_CREDITO
 * @const int FORMA_PAGO_OTRO
 * @const int MAX_LENGTH_DESCRIPCION
 *
 * @static int $MONTO_INICAL
 * @static int $MONTO_SEPARACION
 * @static int $MTS_ADICIONALES
 * @static array $tipos_pago
 * @static array $tipos_pago_asociacion
 * @static array $formas_pago
 *
 * @property int $id
 * @property int $id_cliente
 * @property int $id_casa
 * @property int $id_forma_pago
 * @property string $descripcion
 * @property int $id_tipo_transaccion
 * @property double $monto
 * @property string $realizado_at
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Casa $casa
 * @property Cliente $cliente
 */
class Pago extends Model
{
    use SoftDeletes;

    const MAX_LENGTH_DESCRIPCION = 600;

    public static $MONTO_INICAL=1;
    public static $MONTO_SEPARACION=2;
    public static $MTS_ADICIONALES=3;
    
    /**
     * @var int Tipo de pago que representa el monto inicial
     */
    const TIPO_PAGO_MONTO_INICIAL = 1;
    /**
     * @var int Tipo de pago que representa el monto de separación
     */
    const TIPO_PAGO_MONTO_SEPARACION = 2;
    /**
     * @var int Tipo de pago que representa los métodos adicionales
     */
    const TIPO_PAGO_MTS_ADICIONALES = 3;
    /**
     *
     * @var array Arreglo que contiene los tipos de pagos
     */
    public static $tipos_pago = [
        self::TIPO_PAGO_MONTO_INICIAL => "Monto inicial",
        self::TIPO_PAGO_MONTO_SEPARACION => "Monto separación",
        self::TIPO_PAGO_MTS_ADICIONALES => "Metros adicionales",
    ];
    /**
     *
     * @var array Arreglo que contiene las asociaciones de los tipos de pago
     * con el nombre de la columna para la propiedad
     */
    public static $tipos_pago_asociacion = [
        self::TIPO_PAGO_MONTO_INICIAL => 'monto_separacion',
        self::TIPO_PAGO_MONTO_SEPARACION => 'monto_abono_inicial',
        self::TIPO_PAGO_MTS_ADICIONALES => 'monto_mts2_adicional',
    ];

    /**
     * @const int Constante que representa la forma de pago por transferencia
     */
    const FORMA_PAGO_TRANSFERENCIA = 1;
    /**
     * @const int Constante que representa la forma de pago por efectivo
     */
    const FORMA_PAGO_EFECTIVO= 2;
    /**
     * @const int Constante que representa la forma de pago por depósito
     */
    const FORMA_PAGO_DEPOSITO = 3;
    /**
     * @const int Constante que representa la forma de pago por tarjeta de crédito
     */
    const FORMA_PAGO_TARJETA_CREDITO = 4;
    /**
     * @const int Constante que representa la forma de pago por otro concepto
     */
    const FORMA_PAGO_OTRO = 5;
    /**
     * @var array
     */
    public static $formas_pago = [
        self::FORMA_PAGO_TRANSFERENCIA => "Transferencia",
        self::FORMA_PAGO_EFECTIVO => "Efectivo",
        self::FORMA_PAGO_DEPOSITO => "Depósito",
        self::FORMA_PAGO_TARJETA_CREDITO => "Tarjeta de crédito",
        self::FORMA_PAGO_OTRO => "Otro",
    ];

    protected $table = 'pagos';

    protected $fillable = ['id_cliente','id_casa', 'id_forma_pago', 'descripcion', 'id_tipo_transaccion', 'monto', 'realizado_at'];

    protected $dates = ['deleted_at'];

    /**
     * @return bool
     */
    public function isMontoInicial()
    {
        return $this->id_tipo_transaccion==self::$MONTO_INICAL;
    }

    /**
     * @return bool
     */
    public function isMontoSeparacion()
    {
        return $this->id_tipo_transaccion==self::$MONTO_SEPARACION;
    }

    /**
     * @return bool
     */
    public function isMtsAdicionales()
    {
        return $this->id_tipo_transaccion==self::$MTS_ADICIONALES;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function formaPago()
    {
        return $this->hasOne(FormaPago::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function casa()
    {
        return $this->belongsTo(Casa::class, 'id_casa', 'id');
    }

    /**
     * @return array
     */
    public static function getFormaPago()
    {
        return self::$formas_pago;
    }

    /**
     * @return array
     */
    public static function getTipoTransaccion()
    {
        return self::$tipos_pago;
    }

    /**
     * @param int $id
     * @return Casa
     */
    public static function getCodigoCasa($id)
    {
        return (Casa::select('codigo')->where('id', '=', $id)->first());
    }

    /**
     * Asigna y retorna el arreglo de pagos por pagar
     *
     * @param array $pagos_por_pagar
     * @param Casa $casa
     *
     * @return array
     */
    public static function asignarPagosPorPagar($pagos_por_pagar, $casa)
    {
        foreach (self::$tipos_pago_asociacion as $key => $value) {
            if ($key == self::TIPO_PAGO_MTS_ADICIONALES) {
                $pagos_por_pagar[$key] = $casa->{$value} * $casa->mts2_adicionales;
            } else {
                $pagos_por_pagar[$key] = $casa->{$value};
            }
        }
        return $pagos_por_pagar;
    }
    
    /**
     * Asigna y retorna el arreglo de pagos pendientes
     *
     * @param array $pagos_pendientes
     * @param array $pagos_por_pagar
     * @param array $pagos_efectuados
     *
     * @return array
     */
    public static function asignarPagosPendientes($pagos_pendientes, $pagos_por_pagar, $pagos_efectuados)
    {
        foreach (self::$tipos_pago_asociacion as $key => $value) {
            $pagos_pendientes[$key] = $pagos_por_pagar[$key] - $pagos_efectuados[$key];
        }
        return $pagos_pendientes;
    }
    
    /**
     * Retorna el total de los pagos realizados para la casa y el concepto
     * recibidos
     *
     * @param int $id_casa
     * @param int $id_tipo_transaccion
     * @return double
     */
    public static function getPagosCasa($id_casa, $id_tipo_transaccion)
    {
        return Pago::where('pagos.id_casa', '=', $id_casa)
            ->where('pagos.id_tipo_transaccion', '=', $id_tipo_transaccion)
            ->sum('pagos.monto');
    }
}
