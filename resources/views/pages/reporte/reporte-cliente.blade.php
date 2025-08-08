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
                ['Pago', 'Monto'],
                ['Monto cobrado',{{1}}],
                ['Monto restante',{{1}}],
            ]);
            
            var options = {
                title: 'Progreso',
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
            <h4 class="title"></h4>
            <p class="category"></p>
        </div>
        <div class="content">
          <div class="row">
              <div class="col-lg-6">
                  <div id="piechart2" style="width: 600px; height: 500px; "></div>
              </div>
              <div class="col-lg-6">
                  <table class="table table-striped table-basic-striped table-hover table-basic-hover table-center">
                    <thead>
                        <tr>
                            <th>Descripción</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                       
                    </tbody>
                </table>
              </div>
          </div>

        </div>
    </div>
</div>
</div>




@stop
@section("modals")
@stop
