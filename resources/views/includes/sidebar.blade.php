<div class="sidebar-wrapper">
    <div class="logo">
        <a class="simple-text">
            Panel de Control
        </a>
    </div>
    
    <ul class="nav">
        @can('getAll', 'App\Models\Constructora')
            <li class="{{ Request::is('constructoras') ? 'active' : '' }}">
                <a href="{{url("constructoras")}}">
                   <i class="fa fa-industry"></i>
                    <p>Constructora</p>
                </a>
            </li>
        @endcan

        @can('getAll', 'App\Models\Configuracion')
            <li class="{{ Request::is('configuraciones') ? 'active' : '' }}">
                <a href="{{url("configuraciones")}}">
                   <i class="pe-7s-config"></i>
                    <p>Configuración</p>
                </a>
            </li>
        @endcan

        @can('getAll', 'App\Models\Proyecto')
            <li class="{{ Request::is('proyectos') ? 'active' : '' }}">
                <a href="{{url("proyectos")}}">
                    <i class="fa fa-pencil-square-o"></i>
                    <p>Proyectos</p>
                </a>
            </li>
        @endcan
        @can('getAll', 'App\Models\Broker')
            <li class="{{ Request::is('brokers') ? 'active' : '' }}">
                <a href="{{url("brokers")}}">
                    <i class="fa fa-briefcase"></i>
                    <p>Brokers</p>
                </a>
            </li>
        @endcan
        
        @can('getAll', 'App\Models\EjecutivoVentas')
            <li class="{{ Request::is('ejecutivo_ventas') ? 'active' : '' }}">
                <a href="{{url("ejecutivo_ventas")}}">
                    <i class="fa fa-briefcase"></i>

                    <p>Ejecutivos de Ventas</p>
                </a>
            </li>
        @endcan
        
        @can('getAll', 'App\Models\EjecutivoBancos')
            <li class="{{ Request::is('ejecutivo_bancos') ? 'active' : '' }}">
                <a href="{{url("ejecutivo_bancos")}}">
                    <i class="fa fa-university"></i>
                    <p>Ejecutivos de Bancos</p>
                </a>
            </li>
        @endcan

        @can('getAll', 'App\Models\Casa')
            <li class="{{ Request::is('casas') ? 'active' : '' }}">
                <a href="{{url("casas")}}">
                    <i class="fa fa-home"></i>
                    <p>Propiedades</p>
                </a>
            </li>
        @endcan

        @can('getAll', 'App\Models\Cliente')
            <li class="{{ Request::is('clientes') ? 'active' : '' }}">
                <a href="{{url("clientes")}}">
                    <i class="fa fa-users"></i>
                    <p>Clientes</p>
                </a>
            </li>
        @endcan

        @can('getAll', 'App\Models\Banco')
            <li class="{{ Request::is('bancos') ? 'active' : '' }}">
                <a href="{{url("bancos")}}">
                    <i class="fa fa-university"></i>
                    <p>Bancos</p>
                </a>
            </li>
        @endcan

        @if(Auth::user()->isBroker() || Auth::user()->isConstructora() || Auth::user()->isAdministrador() || Auth::user()->isCliente() )
            <li class="{{ Request::is('reporte') ? 'active' : '' }}">
                <a href="{{url("reporte")}}">
                    <i class="fa fa-line-chart"></i>
                    <p>Reportes</p>
                </a>
            </li>
        @endcan
        
        @can('reporteFinanzas', 'App\Models\Pago')
            <li class="{{ Request::is('reporte/finanzas') ? 'active' : '' }}">
                <a href="{{url("reporte/finanzas")}}">
                    <i class="fa fa-dollar"></i>
                    <p>Finanzas</p>
                </a>
            </li>
        @endcan
        
        @can('estadoCuentaCliente', 'App\Models\Pago')
            <li class="{{ Request::is('reporte/estadoCuentaCliente') ? 'active' : '' }}">
                <a href="{{url("reporte/estadoCuentaCliente")}}">
                    <i class="fa fa-calculator"></i>
                    <p>Estado de cuenta</p>
                </a>
            </li>
        @endcan

        @can('reporteInformeDeVentas', 'App\Models\Casa')
            <li class="{{ Request::is('reporte/informeDeVentas') ? 'active' : '' }}">
                <a href="{{url("reporte/informeDeVentas")}}">
                    <i class="fa fa-list-ul"></i>
                    <p>Informe de Ventas</p>
                </a>
            </li>
        @endcan
        
        @can('reporteEjecutivoDeVentasPropio', 'App\Models\EjecutivoVentas')
            <li class="{{ Request::is('reporte/ejecutivoDeVentas/'.Auth::user()->ejecutivoVentas()->first()->id) ? 'active' : '' }}">
                <a href="{{url("reporte/ejecutivoDeVentas/".Auth::user()->ejecutivoVentas()->first()->id)}}">
                    <i class="fa fa-list-ul"></i>
                    <p>Mi reporte</p>
                </a>
            </li>
        @endcan


        @can('store', 'App\Models\CasaEstado')
            <li class="{{ Request::is('casas_estados') ? 'active' : '' }}">
                <a href="{{url("casas_estados")}}">
                    <i class="fa fa-asterisk"></i>
                    <p>Estados de P.</p>
                </a>
            </li>
        @endcan
    </ul>
</div>
