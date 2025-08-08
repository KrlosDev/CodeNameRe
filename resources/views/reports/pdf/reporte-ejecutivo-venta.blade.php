<?php

use App\Models\Configuracion;

if (! isset($user)) {
    $user = \Auth::user();
}

$configuracion_moneda = Configuracion::where('descripcion', Configuracion::CONFIGURACION_MONEDA)->first();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <!-- Bootstrap core CSS     -->
        <link href="{{url('assets/css/bootstrap.min.css')}}" rel="stylesheet" />
        <!-- Animation library for notifications   -->
        <link href="{{url('assets/css/animate.min.css')}}" rel="stylesheet"/>
        <!--  Light Bootstrap Table core CSS    -->
        <link href="{{url('assets/css/light-bootstrap-dashboard.css')}}" rel="stylesheet"/>
        <!--     Fonts and icons     -->
        <link href="{{url('css/app1.css')}}" rel="stylesheet" />
        <link rel="stylesheet" type="text/css" href="{{asset('dpd/jquery-ui-1.12.1.custom/jquery-ui.min.css')}}"/>
        <title>Reporte de Ganancias Broker</title>
        <style>
            td {
                padding: 0px 0px 0px 20px!important;
            }
            td p {
                margin: 0px;
            }
            h4 {
                font-size: 18px;
                margin: 2px 0px 2px 0px;
                padding: 2px 0px 2px 0px;
            }
        </style>
    </head>
    <body>
        <div class="row">
            <div class="col-md-12 ">
                <div class="card">
                    <div class="header">
                        <h4 class="title">Reporte de Ejecutivo de Ventas</h4>
                        <p class="category">{{$ejecutivoDeVentas->user()->first()->nombre}}</p>
                    </div>
                    <div class="content">
                        <div class="row">
                            <div class="col-lg-12 margin-top">
                                <h3>Reporte</h3>
                                @if($busqueda_proyecto)
                                    <h4>Total casas proyecto <b>{{$total_casas}}</b></h4>
                                @endif
                                <table class="table table-striped table-basic-striped table-hover table-basic-hover table-center">
                                    <thead>
                                        <tr>
                                            <th>Descripción</th>
                                            <th>Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Casas asignadas</td>
                                            <td>{{count($casas)}}</td>
                                        </tr>
                                        @foreach($casas_estados as $casa)
                                        <tr>
                                            <td>{{$estados[$casa->id_casa_estado]}}</td>
                                            <td>{{$casa->total}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>