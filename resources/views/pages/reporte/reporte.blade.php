<?php

use App\Models\Configuracion;

if (! isset($user)) {
    $user = \Auth::user();
}

$configuracion_moneda = Configuracion::where('descripcion', Configuracion::CONFIGURACION_MONEDA)->first();
?>

@extends('layouts.default')
@push('JS')

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    @if($user->isAdministrador() || $user->isConstructora())
    <script type="text/javascript">
      google.charts.load("current", {packages:["corechart"]});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Propiedad', 'Cantidad'],
          ['Propiedades disponibles',{{count($casas)-$casas_ocupadas}}],
          ['Propiedades ocupadas',{{$casas_ocupadas}}],
          

        ]);

        var options = {
          title: 'Progreso',
          //pieHole: 0.4,
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart2'));
        chart.draw(data, options);
      }
    </script>
    @endif
@endpush
@section('content')



<!-- Button trigger modal -->
<!-- <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal" data-backdrop="true">
  Launch demo modal
</button>
 -->

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
                <div class="col-lg-6">
                    <div id="piechart2" style="width: 600px; height: 500px; "></div>
                </div>
                <div class="col-lg-6">
                    <h3>Reporte</h3>
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
                    <h3>Propiedades</h3>
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
                <div class="col-xs-12">
                    <h3>Brokers</h3>
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
                                /**
                                 * @var \App\Models\Broker $broker
                                 */
                                $broker = $brok->broker;
                                $casas_asignadas_broker = $broker->countCasasAsignadasEnProyecto($proyecto->id);
                                //$total_separacion =$casas_asignadas_broker*$casas[0]->monto_separacion;
                                $total_separacion = $broker->getTotalMontoSeparacionByProyecto($proyecto);
                                $porcentaje_proyecto  =$broker->porcentajeEnProyecto($proyecto->id);
                                if ($porcentaje_proyecto==null) {
                                    $porcentaje_broker = 0.05; ?>
                            <tr>
                                <td colspan='9'>
                                    El broker <b>{{$broker->user->nombre}}</b> no tiene porcentaje configurado para este proyecto
                                </td>
                            </tr>
                            <?php
                                    continue;
                                } else {
                                    $porcentaje_broker = $broker->porcentajeEnProyecto($proyecto->id)->porcentaje/100;
                                }
                                //$total_comision = $casas_asignadas_broker*($casas[0]->valor*$porcentaje_broker);
                                $total_comision = $broker->getTotalMontoValorByProyecto($proyecto);
                            ?>
                            <tr>
                                <td>{{$broker->user->nombre}}</td>
                                <td>{{$casas_asignadas_broker}}</td>
                                <td>{{$broker->countCasasAsignadasOcupadasEnProyecto($proyecto->id)}}</td>
                                <td>{{$broker->countCasasAsignadasDisponiblesEnProyecto($proyecto->id)}}</td>
                                <td>{{$porcentaje_broker*100}}</td>
                                <td>{{$total_separacion.$configuracion_moneda->contenido}}</td>
                                <td>{{$total_comision.$configuracion_moneda->contenido}}</td>
                                <td>{{($total_separacion + $total_comision).$configuracion_moneda->contenido}}</td>
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
                    @if($id)
                    <div class="col-xs-12 margin-top text-center">
                        <a href="{{url('pdf/reporte/detallado/'.$id)}}" class="btn btn-primary" title="Descargar">Descargar</a>
                    </div>
                    @endif  
                </div>
            @else
<!--             <div class="col-lg-6">
                <table class="table table-striped table-basic-striped table-hover table-basic-hover table-center">
                    <thead>
                        <tr>
                            <th>Descripción</th>
                            <th>Concepto</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($pagos) == 0)
                        <tr>
                            <td colspan='3'>Aún no ha realizado ningún pago...</td>
                        </tr>
                        @endif
                        @foreach($pagos as $pago)
                        <tr>
                            <th>{{$pago->descripcion}}</th>
                            @foreach($formas_pago as $forma_pago)
                            @if($forma_pago->id == $pago->id_forma_pago)
                                <th>{{$forma_pago->nombre}}</th>
                            @endif
                            @endforeach
                            <th>{{$pago->monto}}</th>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div> -->
                <div class="col-xs-12 margin-top">
                    <h3>Balance</h3>
                    <div class="table-responsive">
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
                </div>
                <div class="col-xs-12 margin-top">
                    <h3>Pagos</h3>
                    <div class="table-responsive">
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
                        @if(count($pagos) > 0)
                        <div class='text-center'>{!! $pagos->render() !!}</div>
                        @endif
                    </div>
                </div>
                <div class="col-xs-12 margin-top text-center">
                    @if($id)
                    <a href="{{url('pdf/reporte/detallado/'.$id)}}" class="btn btn-primary" title="Descargar">Descargar</a>
                    @endif
                    <a href="{{url('reporte')}}" class="btn btn-primary"><i class="fa fa-hand-o-left" aria-hidden="true"></i> Regresar</a>  
                </div>
            @endif
          </div>

        </div>
    </div>
</div>
</div>




@stop
@section("modals")
@stop
