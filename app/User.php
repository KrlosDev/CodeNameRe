<?php

namespace App;

use App\Models\Casa;
use App\Models\CasaEstado;
use App\Models\Cliente;
use App\Models\EjecutivoBancos;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Broker;
use App\Models\Constructora;
use App\Models\EjecutivoVentas;

/**
 * Class User
 * @package App
 *
 * @const int MAX_LENGTH_EMAIL
 * @const int MAX_LENGTH_PASSWORD
 * @const int MAX_LENGTH_USERNAME
 * @const int MAX_LENGTH_NOMBRE
 * @const int ADMINISTRADOR
 * @const int CONSTRUCTORA
 * @const int BROKERS
 * @const int EJ_VENTAS
 * @const int EJ_BANCOS
 * @const int CLIENTE
 *
 * @static array $estados
 * @static array $roles
 *
 * @property int $id
 * @property string $email
 * @property string $password
 * @property string $name
 * @property string $nombre
 * @property int $id_rol
 * @property int $estado
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Cliente $cliente
 * @property Constructora $constructora
 * @property Broker $broker
 * @property EjecutivoVentas $ejecutivoVentas
 * @property EjecutivoBancos $ejecutivoBancos
 */
class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    /**
     * @const int Constante que representa el máximo de caracteres para
     * el email
     */
    const MAX_LENGTH_EMAIL = 100;
    /**
     * @const int Constante que representa el máximo de caracteres para
     * el password
     */
    const MAX_LENGTH_PASSWORD = 100;
    /**
     * @const int Constante que representa el máximo de caracteres para
     * el nombre de usuario
     */
    const MAX_LENGTH_USERNAME = 60;
    /**
     * @const int Constante que representa el máximo de caracteres para
     * el nombre
     */
    const MAX_LENGTH_NOMBRE = 60;

    /**
     * @const int Constante que representa el estado activo
     */
    const ACTIVO = 1;
    /**
     * @const int Constante que representa el estado no activo
     */
    const NO_ACTIVO = 2;
    /**
     * @var array
     */
    public static $estados = [
        self::ACTIVO => "Activo",
        self::NO_ACTIVO => "No activo",
    ];

    const ADMINISTRADOR = 1;
    const CONSTRUCTORA = 2;
    const BROKERS = 3;
    const EJ_VENTAS = 4;
    const EJ_BANCOS = 5;
    const CLIENTE = 6;

    /**
     * @var array
     */
    public static $roles = [
        self::ADMINISTRADOR => "Administrador",
        self::CONSTRUCTORA => "Constructora",
        self::BROKERS => "Brokers",
        self::EJ_VENTAS => "Ejecutivo de Ventas",
        self::EJ_BANCOS => "Ejecutivo de Bancos",
        self::CLIENTE => "Cliente",
    ];

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['email', 'password', 'name', 'nombre', 'id_rol','estado'];

    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * @return bool
     */
    public function isAdministrador()
    {
        return $this->id_rol==self::ADMINISTRADOR;
    }

    /**
     * @return bool
     */
    public function isConstructora()
    {
        return $this->id_rol==self::CONSTRUCTORA;
    }

    /**
     * @return bool
     */
    public function isBroker()
    {
        return $this->id_rol==self::BROKERS;
    }

    /**
     * @return bool
     */
    public function isEjVentas()
    {
        return $this->id_rol==self::EJ_VENTAS;
    }

    /**
     * @return bool
     */
    public function isEjBancos()
    {
        return $this->id_rol==self::EJ_BANCOS;
    }

    /**
     * @return bool
     */
    public function isCliente()
    {
        return $this->id_rol==self::CLIENTE;
    }
    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id', 'id_user');
    }
    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function constructora()
    {
        return $this->belongsTo(Constructora::class, 'id', 'id_user');
    }
    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function broker()
    {
        return $this->belongsTo(Broker::class, 'id', 'id_user');
    }
    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ejecutivoVentas()
    {
        return $this->belongsTo(EjecutivoVentas::class, 'id', 'id_user');
    }
    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ejecutivoBancos()
    {
        return $this->belongsTo(EjecutivoBancos::class, 'id', 'id_user');
    }

    /**
     * Returns the amount of unread messages for user
     *
     * @return int
     */
    public function msjPorLeer()
    {
        if ($this->isBroker()) {
            $cantidad = Broker::msjPorLeer($this->broker()->first()->id);
            return  $cantidad;
        } elseif ($this->isEjVentas()) {
            $cantidad = EjecutivoVentas::msjPorLeer($this->ejecutivoVentas()->first()->id);
            return  $cantidad;
        }

        return 0;
    }

    /**
     * El retorna el broker del usuario provisto, si tiene
     *
     * @return Broker|User
     */
    public function getBroker()
    {
        if ($this->isBroker()) {
            return $this->broker;
        } elseif ($this->isEjVentas()) {
            return $this->ejecutivoVentas->broker;
        } elseif ($this->isEjBancos()) {
            return $this->ejecutivoBancos->broker;
        }
        return null;
    }

    /**
     * El retorna la constructora del usuario provisto, si tiene
     *
     * @return Constructora|boolean
     */
    public function getConstructora()
    {
        if ($this->isAdministrador()) {
            return true;
        }
        if ($this->isConstructora()) {
            return $this->constructora;
        }
        if ($this->isBroker()) {
            return $this->broker->constructora->first();
        }
        if ($this->isEjVentas()) {
            return $this->ejecutivoVentas->broker->constructora->first();
        }
        if ($this->isEjBancos()) {
            return $this->ejecutivoBancos->broker->constructora->first();
        }
        if ($this->isCliente()) {
            return $this->cliente->broker->constructora->first();
        }

        return null;
    }

    /**
     * Returns the ids from whole brokers for it's user
     *
     * @return array
     */
    public function getBrokersIds()
    {
        $brokers = [];
        if ($this->isConstructora()) {
            $brokers = $this->getConstructora()->brokers->pluck(['id'])->toArray();
        } elseif ($this->isEjVentas()) {
            $brokers[] = $this->ejecutivoVentas->broker->id;
        } else {
            $brokers[] = $this->broker->id;
        }

        return $brokers;
    }

    /**
     * Returns the casas estados for its user
     *
     * @return \Illuminate\Support\Collection|mixed
     */
    public function getCasasEstados()
    {
        $casasEstados = [];
        if ($this->isBroker() || $this->isEjVentas() || $this->isEjBancos()) {
            return $this->getBroker()->casasEstados;
        }
        if ($this->isCliente()) {
            /**
             * @var Casa $casa
             */
            //Check this code. Needs more testing
            foreach ($this->cliente->clienteCasa as $casa) {
                if ($casa->casa_estado) {
                    $casasEstados[] = $casa->casa_estado;
                }
            }
        }
        if ($this->isConstructora()) {
            /**
             * @var Broker $broker
             */
            foreach ($this->constructora->brokers as $broker) {
                /**
                 * @var CasaEstado $casaEstado
                 */
                foreach ($broker->casasEstados as $casaEstado) {
                    $casaEstado->nombre = "{$casaEstado->broker->user->nombre} - {$casaEstado->nombre}";
                    $casasEstados[] = $casaEstado;
                }
            }
        }

        return collect($casasEstados);
    }
}
