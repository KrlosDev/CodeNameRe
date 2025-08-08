<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * Class Casa
 * @package App\Models
 *
 * @const ESTADO_POR_ASIGNAR
 * @const ESTADO_CARGANDO_REQUISITOS
 * @const ESTADO_BUSQUEDA_DE_CREDITO
 * @const ESTADO_ESPERA_APROBACION
 * @const ESTADO_APROBADO_ESPERA_CARTA_PROMESA
 * @const ESTADO_CARTA_PROMESA_RECIBIDA
 * @const ESTADO_FIRMA_DE_CONTRATO
 * @const ESTADO_ABONO_INICIAL
 * @const ESTADO_FIRMA_DE_ESCRITURA
 * @const ESTADO_ENTREGA_DE_CASA
 *
 * @static $estados array
 *
 * @property int $id
 * @property string $codigo
 * @property string $modelo
 * @property string $lote_apto
 * @property int $mts2_total
 * @property int $mts2_construccion
 * @property int $mts2_adicionales
 * @property int $recamaras
 * @property int $banos
 * @property int $id_proyecto
 * @property int $id_casa_estado
 * @property double $valor
 * @property double $monto_separacion
 * @property double $monto_abono_inicial
 * @property double $monto_mts2_adicional
 * @property int $id_broker
 * @property int $id_ej_ventas
 * @property int $estatus_tramite_cliente
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Broker $broker
 * @property Collection $cliente
 * @property EjecutivoVentas $ejecutivoVentas
 * @property Proyecto $proyecto
 * @property Constructora $constructora
 * @property Collection $pagos
 * @property CasaEstado $casa_estado
 *
 */
class Casa extends Model
{
    use SoftDeletes;

    const ESTADO_POR_ASIGNAR = 1;
    const ESTADO_CARGANDO_REQUISITOS = 2;
    const ESTADO_BUSQUEDA_DE_CREDITO = 3;
    const ESTADO_ESPERA_APROBACION = 4;
    const ESTADO_APROBADO_ESPERA_CARTA_PROMESA = 5;
    const ESTADO_CARTA_PROMESA_RECIBIDA = 6;
    const ESTADO_FIRMA_DE_CONTRATO = 7;
    const ESTADO_ABONO_INICIAL = 8;
    const ESTADO_FIRMA_DE_ESCRITURA = 9;
    const ESTADO_ENTREGA_DE_CASA = 10;
    /**
     *
     * @var array Arreglo que contiene los estados de la propiedad
     * Deprecated. Will stay for legacy
     */
    public static $estados = [
        self::ESTADO_POR_ASIGNAR => "Por asignar",
        self::ESTADO_CARGANDO_REQUISITOS => "Cargando requisitos",
        self::ESTADO_BUSQUEDA_DE_CREDITO => "Búsqueda de crédito",
        self::ESTADO_ESPERA_APROBACION => "Espera de aprobación",
        self::ESTADO_APROBADO_ESPERA_CARTA_PROMESA => "Espera carta promesa",
        self::ESTADO_CARTA_PROMESA_RECIBIDA => "Carta promesa recibida",
        self::ESTADO_FIRMA_DE_CONTRATO => "Firma de contrato",
        self::ESTADO_ABONO_INICIAL => "Abono inicial",
        self::ESTADO_FIRMA_DE_ESCRITURA => "Firma de escritura",
        self::ESTADO_ENTREGA_DE_CASA => "Entrega de casa",
    ];
    
    /**
     * @const int Constante que representa el máximo de caracteres para
     * el modelo
     */
    const MAX_LENGTH_MODELO = 15;
    
    protected $table = 'casas';

    protected $fillable = [
        'codigo','modelo', 'lote_apto', 'mts2_total', 'mts2_construccion', 'mts2_adicionales',
        'recamaras', 'banos', 'id_proyecto', 'id_constructora','valor', 'monto_separacion', 'monto_abono_inicial',
        'monto_mts2_adicional', 'id_broker', 'id_ej_ventas','estatus_tramite_cliente','id_casa_estado'
    ];

    protected $dates = ['deleted_at'];

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function broker()
    {
        return $this->belongsTo(Broker::class, 'id_broker', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function cliente()
    {
        return $this->belongsToMany(Cliente::class, 'clientes_casas', 'id_casa', 'id_cliente');
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function ejecutivoVentas()
    {
        return $this->hasOne(EjecutivoVentas::class, 'id', 'id_ej_ventas');
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function proyecto()
    {
        return $this->hasOne(Proyecto::class, 'id', 'id_proyecto');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function constructora()
    {
        return $this->belongsTo(Constructora::class, 'id_constructora', 'id');
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pagos()
    {
        return $this->hasMany(Pago::class, 'id_casa', 'id')
            ->whereNull('pagos.deleted_at');
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function casa_estado()
    {
        return $this->hasOne(CasaEstado::class, 'id', 'id_casa_estado');
    }

    /**
     * Retorna verdadero si la casa ya se encuentra asignada
     *
     * @param $id int
     *
     * @return bool
     */
    public function casasAsignada($id)
    {
        if (Casa::find($id)->id_broker == null) {
            return false;
        }
        return true;
    }

    /**
     * @return string
     */
    public function getBrokerName()
    {
        $broker = DB::table('brokers')->where('id', $this->id_broker)->first();
        $user   = DB::table('users')->select('users.nombre')->where('id', $broker->id_user)->first();

        return (string)$user->nombre;
    }

    /**
     * @return double
     */
    public function abonoInicialFaltante()
    {
        $montoTotal = 0;
        $pagos = Pago::where('id_tipo_transaccion', Pago::$MONTO_INICAL)
            ->where('id_casa', $this->id)
            ->get();
        foreach ($pagos as $pag) {
            $montoTotal = $montoTotal + $pag->monto;
        }

        return ($this->monto_abono_inicial - $montoTotal);
    }

    /**
     * @return double
     */
    public function separacionFaltante()
    {
        $montoTotal = 0;
        $pagos = Pago::where('id_tipo_transaccion', Pago::$MONTO_SEPARACION)
            ->where('id_casa', $this->id)
            ->get();
        foreach ($pagos as $pag) {
            $montoTotal = $montoTotal + $pag->monto;
        }

        return ($this->monto_separacion - $montoTotal);
    }

    /**
     * @return double
     */
    public function mtsFaltante()
    {
        $montoTotal = 0;
        $pagos = Pago::where('id_tipo_transaccion', Pago::$MTS_ADICIONALES)
            ->where('id_casa', $this->id)
            ->get();
        foreach ($pagos as $pag) {
            $montoTotal = $montoTotal + $pag->monto;
        }

        return ($this->monto_mts2_adicional - $montoTotal);
    }

    /**
     * @param $nombre string
     * @return string
     */
    public static function iniciales($nombre)
    {
        $notocar = [''];
        $trozos = explode(' ', $nombre);
        $iniciales = '';
        for ($i=0;$i<count($trozos);$i++) {
            if (in_array($trozos[$i], $notocar)) {
                $iniciales .= $trozos[$i]." ";
            } else {
                $iniciales .= substr($trozos[$i], 0, 1).". ";
            }
        }
        return $iniciales;
    }
    
    /**
     *
     * @param int $id_cliente
     * @return EjecutivoBancos
     */
    public function getEjBanco($id_cliente)
    {
        $cliente = $this->belongsToMany(Cliente::class, 'clientes_casas', 'id_casa', 'id_cliente')
            ->withPivot('id')
            ->wherePivot('deleted_at', null)
            ->wherePivot('id_cliente', $id_cliente)
            ->first();

        if (! $cliente) {
            return null;
        }

        return EjecutivoBancos::with(['user'])
            ->where('ejecutivos_bancos.id', $cliente->id_ej_bancos)
            ->first();
    }
    
    /**
     *
     * @param int $estado
     * @return double
     */
    public function getMontoPagado($estado, $from=true)
    {
        return $this->pagos()->where('pagos.id_tipo_transaccion', $estado)->sum('monto');
        /*$total = 0;
        foreach ($this->pagos as $pago) {
            //\Log::error("Pago ".$pago->id_forma_pago);
            if ($pago->id_tipo_transaccion == $estado) {
                $total+=$pago->monto;
            }
        }
        return $total;*/
    }
    
    /**
     *
     * @param int $estado
     * @param string $campo
     *
     * @return double
     */
    public function getMontoPorPagar($estado, $campo)
    {
        /*$asoc = $estado;
        if($estado == Pago::TIPO_PAGO_MONTO_INICIAL)
            $asoc = Pago::TIPO_PAGO_MONTO_SEPARACION;
        else if($estado == Pago::TIPO_PAGO_MONTO_SEPARACION)
            $asoc = Pago::TIPO_PAGO_MONTO_INICIAL;*/
        $monto_pagado = $this->getMontoPagado($estado, false);

        $monto_por_pagar = (float)$this->{$campo};

        if ($estado == Pago::TIPO_PAGO_MTS_ADICIONALES) {
            $monto_por_pagar = (float)((float)$this->monto_mts2_adicional * (float)$this->mts2_adicionales);
        }
        //\Log::error("Monto pagado $monto_pagado por pagar $monto_por_pagar para $campo ".Pago::$tipos_pago[$estado]);
        return (float)$monto_por_pagar - (float)$monto_pagado;
    }
    
    /**
     * Retorna el total pagado para los tipos de pago excepto los enviados
     *
     * @param array $except [optional]
     * @return double
     */
    public function getMontoPagadoTotalTabla($except = [])
    {
        $total = 0;
        foreach (array_keys(Pago::$tipos_pago) as $tipo_pago) {
            if (! in_array($tipo_pago, $except)) {
                $total+=$this->getMontoPagado($tipo_pago);
            }
        }
        return $total;
    }
    
    /**
     * Retorna el total por pagar para los tipos de pago excepto los enviados
     *
     * @return double
     */
    public function getMontoPorPagarTotalTabla($except = [])
    {
        $total = 0;
        foreach (array_keys(Pago::$tipos_pago) as $tipo_pago) {
            if (! in_array($tipo_pago, $except)) {
                $asoc = $tipo_pago;
                if ($tipo_pago == Pago::TIPO_PAGO_MONTO_INICIAL) {
                    $asoc = Pago::TIPO_PAGO_MONTO_SEPARACION;
                } elseif ($tipo_pago == Pago::TIPO_PAGO_MONTO_SEPARACION) {
                    $asoc = Pago::TIPO_PAGO_MONTO_INICIAL;
                }
                //\Log::error("Tipo pago ".Pago::$tipos_pago[$tipo_pago]." asoc $asoc tipo_pago $tipo_pago");
                $total+=$this->getMontoPorPagar($tipo_pago, Pago::$tipos_pago_asociacion[$asoc]);
            }
            //\Log::error("Total $total");
        }
        return $total;
    }

    /**
     * Returns the name of the casaEstado searching in the $broker's $casasEstados
     *
     * @param Collection $casasEstados
     *
     * @return string
     */
    public function getEstadoNombre($casasEstados=null)
    {
        if ($this->broker===null) {
            return 'Sin asignar a broker';
        }

        if ($casasEstados instanceof Collection || $casasEstados === null) {
            $casasEstados = ($casasEstados!==null) ? $casasEstados : $this->broker->casasEstados;
            /**
             * @var CasaEstado $casaEstado
             */
            foreach ($casasEstados as $casaEstado) {
                if ($casaEstado->id == $this->id_casa_estado) {
                    return $casaEstado->nombre;
                }
            }
        } else {
            foreach ($casasEstados as $casaEstado) {
                if ($casaEstado['id'] == $this->id_casa_estado) {
                    return $casaEstado['nombre'];
                }
            }
        }

        return 'Sin asignar';
    }

    /**
     * Count all the $casas and returns the $total array with the results
     * The $total array needs to be previously initialized
     *
     * @param Collection $casas
     * @param array $total
     */
    public static function countEstados($casas, &$total)
    {
        /**
         * @var Casa $casa
         */
        foreach ($casas as $casa) {
            if ($casa->id_casa_estado) {
                $total[$casa->id_casa_estado]++;
            } else {
                $total['null']++;
            }
        }
    }
}
