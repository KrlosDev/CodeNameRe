
<div class="container-fluid">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
    </div>
    <div class="collapse navbar-collapse">
        <ul class="nav navbar-nav navbar-left">
            <li>
            </li>
        </ul>

        <ul class="nav navbar-nav navbar-right">
            <li >
            @if(Auth::user()->isBroker() || Auth::user()->isEjVentas())
                <a href="{{route('mensajes')}}" style="color: #fff;">
                    <i class="fa fa-envelope" aria-hidden="true" style="padding-right: 20px"> </i>
                    <span class="notification" id="nmsjs" >
                        {{Auth::user()->msjPorLeer()}} -->
                    </span>
                </a>
            @endif
            @if(Auth::user()->isCliente())
                <?php $clnt=Auth::user()->cliente; ?>
                <a title="Mensajes" href="javascript:crearMensaje('{{url('mensajes')}}','{{url('clientes/ventas/'.$clnt->id)}}')" style="color: #fff;">
                    <i class="fa fa-envelope" aria-hidden="true" style="padding-right: 20px"> </i>
                </a>
            @endif
            </li>
            <li>
                @if(Auth::user()->isConstructora() || Auth::user()->isBroker() || Auth::user()->isEjVentas())
                <a title="Notificaciones" href="{{route('notificaciones')}}" style="color: #fff;">
                    <i class="fa fa-bell" aria-hidden="true" style="padding-right: 20px"> </i>
                    <span class="notification" id="notificacion" >
                        {{App\Models\DocumentoCliente::cantidadVencidos()}}
                    </span>
                </a>
                @endif
            </li>
            @if(Auth::check())
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" style="color: #fff;">
                   <i class="fa fa-user-circle-o" aria-hidden="true" style="padding-right: 25px"></i> {{ Auth::user()->nombre }} <span class="caret"></span>
                </a>
                <ul class="dropdown-menu" role="menu">
                     <li>
                        <a href="javascript:editarUser('{{url('user')}}/{{Auth::user()->id}}')">
                            Editar cuenta
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/logout') }}"
                            onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();"
                        >
                            Cerrar sesión
                        </a>

                        <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </li>

                </ul>
            </li>
            @endif
        </ul>
    </div>
</div>

