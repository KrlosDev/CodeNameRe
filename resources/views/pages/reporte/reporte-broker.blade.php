<?php
if (! isset($user)) {
    $user = \Auth::user();
}
?>

@extends('layouts.default')
@push('JS')

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load("current", {packages:["corechart"]});
        google.charts.setOnLoadCallback(drawChart);
        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Libres', 'Ocupadas'],
                ['Propiedades Libres',{{$libres}}],
                ['Propiedades Ocupadas',{{$ocupadas}}],
            ]);
            
            var options = {
                title: 'Progreso de propiedades',
                //pieHole: 0.4,
            };
            
            var chart = new google.visualization.PieChart(document.getElementById('piechart2'));
            chart.draw(data, options);
        }
    </script>
    
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
            <h4 class="title">Proyecto: {{$proyecto->nombre}}</h4>
            <p class="category"></p>
        </div>
        <div class="content">
          <div class="row">
              <div class="col-lg-6">
                  <div id="piechart2" style="width: 600px; height: 500px; "></div>
              </div>
              <div class="col-lg-6">
                  <table class="table table-striped table-basic-striped table-hover table-basic-hover table-center">
                    
                  <tbody>
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
                                <td>Propiedades del Proyecto</td>
                                <td>{{$casasproyecto}}</td>
                            </tr>
                            <tr>
                                <td>Propiedades Asignadas</td>
                                <td>{{$asignadas}}</td>
                            </tr>
<!-- 
                            <tr>
                                <td>Otras Propiedades</td>
                                <td>{{$otras_propiedades}}</td>
                            </tr> -->

                             <tr>
                                <td>Propiedades Disponibles</td>
                                <td>{{$libres}}</td>
                            </tr>
                            <tr>
                                <td>Propiedades Ocupadas</td>
                                <td>{{$ocupadas}}</td>
                            </tr>
                        </tbody>
                    </table>
                    <br>
                    <h3>Propiedad</h3>
                    <table class="table table-striped table-basic-striped table-hover table-basic-hover table-center">
                        <thead>
                            <tr>
                                <th>Habitaciones</th>
                                <th>Baños</th>
                                <th>Mts2</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(true)
                            <tr>
                                <td>{{$modelo->recamaras}}</td>
                                <td>{{$modelo->banos}}</td>
                                <td>{{$modelo->mts2_total}}</td>
                            </tr>
                            @else
                            <tr>
                                <td colspan="2">Este proyecto no tiene propiedades actualmente...</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                    <br>
                    <h3>Ingresos</h3>
                    <table class="table table-striped table-basic-striped table-hover table-basic-hover table-center">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Total</th>
                                <th>Recibido </th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(true)
                            <tr>
                                <td>{{'Total Abono Inicial'}}</td>
                                <td>{{$abonoinicial_total}}</td>
                                <td>{{$abonoinicial_recibido}}</td>
                            </tr>
                            <tr>
                                <td>{{'Total Metros Adicionales'}}</td>
                                <td>{{$mts2_total}}</td>
                                <td>{{$mts2_recibidos}}</td>
                            </tr>
                            <tr>
                                <td>{{'Total Reserva'}}</td>
                                <td>{{$total_reserva}}</td>
                                <td>{{$reserva_recibido}}</td>
                            </tr>
                            @else
                            <tr>
                                <td colspan="2">Este proyecto no tiene propiedades actualmente...</td>
                            </tr>
                            @endif
                            
                        </tbody>
                    </table>
                </div>
                </tbody>
                </table>
              </div>
              
              <div class="row">
                  <div class="col-lg-6">
                      <table class="table table-striped table-basic-striped table-hover table-basic-hover table-center">
                          <h3>Reporte por Trámite</h3>
                    <table class="table table-striped table-basic-striped table-hover table-basic-hover table-center">
                        <thead>
                            <tr>
                                <th>Descripción</th>
                                <th>Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Sin asignar</td>
                                <td>{{$total['null']}} ({{$total_asignadas['null']}})</td>
                            </tr>
                            @foreach($estados as $estado)
                            <tr>
                                <td>{{$estado->nombre}}</td>
                                <td>{{$total[$estado->id]}} ({{$total_asignadas[$estado->id]}})</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                          
                      </table>
                  </div>
                  
              </div>
              
          </div>

        </div>
        <div  class="col-xs-12 margin-top text-center">
            <a href="{{url('pdf/reporte/ganancias/'.$proyecto->id.'/'.$id_broker)}}" class="btn btn-primary" title="Descargar">Descargar</a>
        </div>
    </div>
</div>
</div>




@stop
@section("modals")
@stop
