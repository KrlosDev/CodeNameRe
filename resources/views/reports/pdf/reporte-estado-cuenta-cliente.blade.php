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
                        <h4 class="title">Pagos</h4>
                        <p class="category">Estado de cuenta</p>
                    </div>
                    <div class="content">
                        <div class="row">
                            <div class="col-lg-12 margin-top">
                                <h4>Balance</h4>
                                <table class="table table-striped table-basic-striped table-hover table-basic-hover table-center">
                                    <thead>
                                        <tr>
                                            <th>Descripción</th>
                                            @foreach($tipos_pago as $key => $value)
                                            <th>{{$value}}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Por pagar</td>

                                            @if(count($pagos_por_pagar) > 0)
                                                <td>{{$pagos_por_pagar[2]}}</td>
                                                <td>{{$pagos_por_pagar[1]}}</td>
                                                <td>{{$pagos_por_pagar[3]}}</td>
                                            @endif
                                        </tr>
                                        <tr>
                                            <td>Pagado</td>
                                            @foreach($pagos_efectuados as $key => $value)
                                            <td>{{$value}}</td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td>Pendiente</td>
                                           @if(count($pagos_por_pagar) > 0)
                                                <td>{!!\App\Models\Funciones::colorearSaldoExacto($pagos_por_pagar[2] - $pagos_efectuados[1],0)!!}</td>
                                                <td>{!!\App\Models\Funciones::colorearSaldoExacto($pagos_por_pagar[1] - $pagos_efectuados[2],0)!!}</td>
                                                <td>{!!\App\Models\Funciones::colorearSaldoExacto($pagos_por_pagar[3] - $pagos_efectuados[3],0)!!}</td>
                                            @endif
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                            <div class="col-lg-12 margin-top">
                                <h4>Pagos</h4>
                                <table class="table table-striped table-basic-striped table-hover table-basic-hover table-center">
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Concepto</th>
                                            <th>Monto</th>
                                            <th>Fecha de pago</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @if(count($pagos) == 0)
                                        <tr>
                                            <td colspan="4">No se han registrado pagos...</td>
                                        </tr>
                                    @else
                                        @foreach($pagos as $pago)
                                        <?php $datetime = \App\Models\Funciones::createDateTimeObject($pago->realizado_at, 'Y-m-d'); ?>
                                        <tr>
                                            <td>{{$pago->cliente->nombre." ".$pago->cliente->apellido}}</td>
                                            <td>{{$tipos_pago[$pago->id_tipo_transaccion]}}</td>
                                            <td>{{$pago->monto.$configuracion_moneda->contenido}}</td>
                                            <td>{{$datetime->format('d/m/Y')}}</td>
                                        </tr>
                                        @endforeach
                                    @endif
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