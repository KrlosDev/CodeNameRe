<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class DocumentoCodeudor
 * @package App\Models
 *
 * @const TIPO_CEDULA int
 * @const TIPO_CARTA_TRABAJO int
 * @const TIPO_FICHA_SEGURO_SOCIAL int
 * @const TIPO_COMPROBANTE_PAGO int
 * @const TIPO_DECLARACIONES_RENTA int
 * @const TIPO_TALONARIO int
 * @const TIPO_CARTA_RPROMESA int
 * @const TIPO_OTRO int
 *
 * @static $tipos array
 *
 * @property int $id
 * @property int $id_co_deudores
 * @property int $id_pais
 * @property int $tipo_documento
 * @property string $fecha_expiracion
 * @property string $src
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Codeudor $codeudor
 * @property Pais $pais
 *
 */
class DocumentoCodeudor extends Model
{
    use SoftDeletes;
    
    /**
     * @const int Constante que representa el tipo de documento cédula
     */
    const TIPO_CEDULA = 1;
    /**
     * @const int Constante que representa el tipo de documento carta de trabajo
     */
    const TIPO_CARTA_TRABAJO = 2;
    /**
     * @const int Constante que representa el tipo de documento seguro social
     */
    const TIPO_FICHA_SEGURO_SOCIAL = 3;
    /**
     * @const int Constante que representa el tipo de documento comprobante de pago
     */
    const TIPO_COMPROBANTE_PAGO = 4;
    /**
     * @const int Constante que representa el tipo de documento declaraciones renta
     */
    const TIPO_DECLARACIONES_RENTA = 5;
    /**
     * @const int Constante que representa el tipo de documento talonario
     */
    const TIPO_TALONARIO = 6;
    /**
     * @const int Constante que representa el tipo de documento carta promesa
     */
    const TIPO_CARTA_RPROMESA = 7;
    /**
     * @const int Constante que representa el tipo de documento otro
     */
    const TIPO_OTRO = 8;

    /**
     * @var array
     */
    public static $tipos = [
        self::TIPO_CEDULA => "Cédula",
        self::TIPO_CARTA_TRABAJO => "Carta de Trabajo",
        self::TIPO_FICHA_SEGURO_SOCIAL => "Ficha de Seguro Social",
        self::TIPO_COMPROBANTE_PAGO => "Comprobantes de Pago",
        self::TIPO_DECLARACIONES_RENTA => "Declaraciones de Renta",
        self::TIPO_TALONARIO => "Talonario",
        self::TIPO_CARTA_RPROMESA => "Carta Promesa",
        self::TIPO_OTRO => "Otro",
    ];

    protected $table = 'documentos_codeudores';

    protected $fillable = ['id_co_deudores', 'src', 'fecha_expiracion', 'tipo_documento'];

    protected $dates = ['deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function codeudor()
    {
        return $this->belongsTo(Codeudor::class, 'id', 'id_co_deudores');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pais()
    {
        return $this->belongsTo(Pais::class, 'id_pais', 'id');
    }
}
