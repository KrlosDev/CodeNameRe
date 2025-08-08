<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Banco;
use App\Models\Broker;
use App\Models\Casa;
use App\Models\CasaEstado;
use App\Models\CasaRequerimientoTramite;
use App\Models\Cliente;
use App\Models\ClienteCasa;
use App\Models\Configuracion;
use App\Models\Constructora;
use App\Models\DocumentoCliente;
use App\Models\DocumentoCodeudor;
use App\Models\EjecutivoBancos;
use App\Models\EjecutivoVentas;
use App\Models\Imagen;
use App\Models\Mensaje;
use App\Models\Pago;
use App\Models\Proyecto;
use App\Models\RequerimientoTramite;
use App\Policies\BancoPolicy;
use App\Policies\BrokerPolicy;
use App\Policies\CasaPolicy;
use App\Policies\CasaEstadoPolicy;
use App\Policies\CasaRequerimientoTramitePolicy;
use App\Policies\ClienteCasaPolicy;
use App\Policies\ClientePolicy;
use App\Policies\ConfiguracionPolicy;
use App\Policies\ConstructoraPolicy;
use App\Policies\DocumentoPolicy;
use App\Policies\EjecutivoBancosPolicy;
use App\Policies\EjecutivoVentasPolicy;
use App\Policies\ImagenPolicy;
use App\Policies\MensajePolicy;
use App\Policies\PagoPolicy;
use App\Policies\ProyectoPolicy;
use App\Policies\RequerimientoTramitePolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
        Banco::class => BancoPolicy::class,
        Broker::class => BrokerPolicy::class,
        Casa::class => CasaPolicy::class,
        CasaEstado::class => CasaEstadoPolicy::class,
        CasaRequerimientoTramite::class => CasaRequerimientoTramitePolicy::class,
        Cliente::class => ClientePolicy::class,
        ClienteCasa::class =>ClienteCasaPolicy::class,
        Configuracion::class => ConfiguracionPolicy::class,
        Constructora::class => ConstructoraPolicy::class,
        DocumentoCliente::class =>DocumentoPolicy::class,
        DocumentoCodeudor::class =>DocumentoPolicy::class,
        EjecutivoBancos::class => EjecutivoBancosPolicy::class,
        EjecutivoVentas::class => EjecutivoVentasPolicy::class,
        Imagen::class => ImagenPolicy::class,
        Mensaje::class => MensajePolicy::class,
        Pago::class => PagoPolicy::class,
        Proyecto::class => ProyectoPolicy::class,
        RequerimientoTramite::class => RequerimientoTramitePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
