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
                        @if($user->isAdministrador() || $user->isConstructora())
                        <h4 class="title">Proyecto: {{$proyecto->nombre}}</h4>
                        @else
                        <h4 class="title">Propiedad: {{$casa->codigo}}</h4>
                        @endif
                        <p class="category"></p>
                    </div>
                    <div class="content">
                        <div class="row">
                            @if($user->isAdministrador() || $user->isConstructora())
                            <div class="col-lg-12">
                                <h4>Reporte</h4>
                                <table class="table table-striped table-basic-striped table-hover table-basic-hover table-center">
                                    <thead>
                                        <tr>
                                            <th>Descripción</th>
                                            <th>Cantidad</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Total de propiedades</td>
                                            <td>{{count($casas)}}</td>
                                        </tr>
                                        <tr>
                                            <td>Total ocupadas</td>
                                            <td>{{$casas_ocupadas}}</td>
                                        </tr>
                                         <tr>
                                            <td>Total disponibles</td>
                                            <td>{{count($casas)-$casas_ocupadas}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <h4>Propiedades</h4>
                                <table class="table table-striped table-basic-striped table-hover table-basic-hover table-center">
                                    <thead>
                                        <tr>
                                            <th>Habitaciones</th>
                                            <th>Baños</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($casas) > 0)
                                        <tr>
                                            <td>{{$casas[0]->recamaras}}</td>
                                            <td>{{$casas[0]->banos}}</td>
                                        </tr>
                                        @else
                                        <tr>
                                            <td colspan="2">Este proyecto no tiene propiedades actualmente...</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-lg-12">
                                <h4>Brokers</h4>
                                <table class="table table-striped table-basic-striped table-hover table-basic-hover table-center">
                                    <thead>
                                        <tr>
                                            <th>Broker</th>
                                            <th>N° Asignadas</th>
                                            <th>N° Vendidas</th>
                                            <th>Disponibles</th>
                                            <th>% Comisión</th>
                                            <th>Total separación</th>
                                            <th>Total comisión</th>
                                            <th>Total</th>
                                            <th><i class="fa fa-cogs fa-lg"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($brokers) > 0)
                                        @foreach($brokers as $brok)
                                        <?php
                                            $broker = $brok->broker;
                                            $casas_asignadas_broker = $broker->countCasasAsignadasEnProyecto($proyecto->id);
                                            $total_separacion =$casas_asignadas_broker*$casas[0]->monto_separacion;
                                            $porcentaje_model = $broker->porcentajeEnProyecto($proyecto->id);
                                            if ($porcentaje_model != null) {
                                                $porcentaje_broker = $porcentaje_model->porcentaje/100;
                                                $total_comision = $casas_asignadas_broker*($casas[0]->valor*$porcentaje_broker);
                                            } else {
                                                $porcentaje_broker = $total_comision = null;
                                            }
                                        ?>
                                        <tr>
                                            <td>{{$broker->user->nombre}}</td>
                                            <td>{{$casas_asignadas_broker}}</td>
                                            <td>{{$broker->countCasasAsignadasOcupadasEnProyecto($proyecto->id)}}</td>
                                            <td>{{$broker->countCasasAsignadasDisponiblesEnProyecto($proyecto->id)}}</td>
                                            @if($porcentaje_broker != null)
                                                <td>{{$porcentaje_broker*100}}</td>
                                                <td>{{$total_separacion.$configuracion_moneda->contenido}}</td>
                                                <td>{{$total_comision.$configuracion_moneda->contenido}}</td>
                                                <td>{{($total_separacion + $total_comision).$configuracion_moneda->contenido}}</td>
                                            @else
                                                <td colspan="4">No configurado</td>
                                            @endif
                                            @if($user->isConstructora())
                                            <td>
                                            <a title="Detallar Reporte" href='{{url('/reporte/ganancias/'.$proyecto->id.'/'.$broker->id)}}' >
                                                <i class="fa fa-line-chart fa-lg" aria-hidden="true"></i>
                                            </a>
                                            @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                        @else
                                        <tr>
                                            <td colspan="8">Este proyecto no tiene brokers actualmente...</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            @else
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
                                            <td>{{$pagos_por_pagar[2]}}</td>
                                            <td>{{$pagos_por_pagar[1]}}</td>
                                            <td>{{$pagos_por_pagar[3]}}</td>
                                        </tr>
                                        <tr>
                                            <td>Pagado</td>
                                            @foreach($pagos_efectuados as $key => $value)
                                            <td>{{$value}}</td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td>Pendiente</td>
                                            <td>{!!\App\Models\Funciones::colorearSaldoExacto($pagos_por_pagar[2] - $pagos_efectuados[1],0)!!}</td>
                                            <td>{!!\App\Models\Funciones::colorearSaldoExacto($pagos_por_pagar[1] - $pagos_efectuados[2],0)!!}</td>
                                            <td>{!!\App\Models\Funciones::colorearSaldoExacto($pagos_por_pagar[3] - $pagos_efectuados[3],0)!!}</td>
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
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>