<!doctype html>
<html>
<head lang="es">
    @include('includes.head')
</head>
<body>
<div class="wrapper">
    <div class="sidebar" data-color="smart" data-image="{{url('assets/img/sidebar-6.jpg')}}">

        <!--
            Tip 1: you can change the color of the sidebar using: data-color="blue | azure | green | orange | red | purple"
            Tip 2: you can also add an image using data-image tag

        -->
        @include('includes.sidebar')
    </div>


    <div class="main-panel">

        <nav class="navbar navbar-default navbar-fixed" style='background: rgb(2,68,126);'>
            @include('includes.header')
        </nav>

        <div class="content">
            @if (session('alert'))
                <div class="col-xs-12 no-padding" style="margin-top: 15px;">
                    <div class="callout callout-{{session('alert')["tipo"]}}" role="alert">
                        <h4>{{session('alert')["titulo"]}}</h4>
                        <p>{{session('alert')["mensaje"]}}</p>
                    </div>
                </div>
            @endif

            @if (count($errors) > 0)
                <div class="alert alert-danger col-xs-12 no-padding" style="margin-top: 15px;">
                    <div class="callout callout-danger">
                        <h4>Error al procesar los datos</h4>
                        <p>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        </p>
                    </div>
                </div>
            @endif
            <div class="container-fluid">
                <div class="row">
                    @yield('content')
                </div>
            </div>
        </div>

        <footer class="footer">
            @include('includes.footer')
        </footer>
    </div>

</div>

@include('pages.user.update')
@include('pages.mensaje.create')
@include('pages.mensaje.mostrarMensaje')
@yield('modals')



</body>
</html>




