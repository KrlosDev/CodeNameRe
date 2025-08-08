<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Panel de Control</title>

    <!-- Styles -->

    <link href="{{url('css/app.css')}}" rel="stylesheet">
    <link href="{{url('css/app1.css')}}" rel="stylesheet">



    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>


    <style>
            .centrado-porcentual {
                position: absolute;
                left: 50%;
                top: 40%;
                transform: translate(-50%, -50%);
                -webkit-transform: translate(-50%, -50%);
            }
    </style>
</head>
<body style="background: url('{{url('assets/img/slider-img-2.jpg')}}')">


<div class="container">
    <div class="row">
        <div class="col-lg-5 col-md-7 col-sm-10 col-xs-12 centrado-porcentual">
            <div class='col-xs-12 text-center' style='height:250px;'>
                <img src='{{url("assets/img/Logo.png")}}' alt='Mi Hogar Panamá' title='Mi Hogar Panamá' />
            </div>
            <div class='col-xs-12 text-center margin-top' style='height:250px;'>
                <div class="panel panel-default" style="background: rgba(255,255,255,0.2);">
                    <div class="panel-body" style="padding-top: 40px;">
                        <div class='col-xs-12 margin-top'>
                            <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
                                {{ csrf_field() }}

                                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                    <label for="name" class="col-md-4 control-label" style="color:white;font-size:16px;font-weight: 600;text-shadow:1px 1px black;">Usuario</label>

                                    <div class="col-md-6">
                                        <input id="name" tabindex="1" type="text" class="form-control" name="name" value="{{ old('name') }}" style="background: white;color:black;font-weight: bold;font-size:16px;border-radius:0;" required autofocus>

                                        @if ($errors->has('name'))
                                            <span class="help-block" style="color: white;text-shadow: 1px 1px black;">
                                                <strong>{{ $errors->first('name') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                    <label for="password" class="col-md-4 control-label" style="color:white;font-size:16px;font-weight: 600;text-shadow:1px 1px black;">Contraseña</label>

                                    <div class="col-md-6">
                                        <input id="password" tabindex="2" type="password" class="form-control" name="password" style="background: white;color:black;font-weight: bold;font-size:16px;border-radius:0;" required>

                                        @if ($errors->has('password'))
                                        <span class="help-block" style="color: white;text-shadow: 1px 1px black;">
                                                <strong>{{ $errors->first('password') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-4">
                                        <div class="checkbox">
                                            <label style="color:white;font-size:16px;font-weight: 600;text-shadow:1px 1px black;">
                                                <input type="checkbox" name="remember"> Recordarme
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-xs-12 text-center">
                                        <button type="submit" class="btn btn-lg btn-primary" style="border: 2px;border-radius:0;" name="Ingresar" id="Ingresar">
                                            Ingresar
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @if ($errors->has('name') || $errors->has('password'))
            <div class='col-xs-12 text-center margin-top3'></div>
            @endif
            <div class='col-xs-12 text-center margin-top3'>
                <a href='http://www.mihogar.com.pa/infoventas/' style='color:white;font-size:16px;font-weight: 600;text-shadow:1px 1px black;'>Información para Promotoras y Brokers</a>
            </div>
        </div>
    </div>
</div>
    
<!-- Scripts -->
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-86848661-3', 'auto');
  ga('send', 'pageview');

</script>
</body>
</html>
