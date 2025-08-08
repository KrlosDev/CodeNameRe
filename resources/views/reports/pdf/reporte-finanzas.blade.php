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
    <style type="text/css">
        td, th {
            border: 1px solid #dddddd;
            text-align: center;
        }

        table {           
            border-collapse: collapse;          
        }
        body {
              font-family: "Roboto","Helvetica Neue",Arial,sans-serif;
              
        }
        tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        .col-lg-12{
            width: 100%;
        }

        .margin-top{
            margin-top: 0.8em;
        }

    </style>
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
                        <p class="category"></p>
                    </div>
                    <div class="content">
                        <div class="row">

                            <div class="col-lg-12 margin-top">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Concepto</th>
                                                <th>Monto</th>
                                                <th>Fecha de pago</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @if(count($pagos) > 0)
                                            @foreach($pagos as $pago)
                                            <?php $datetime = \App\Models\Funciones::createDateTimeObject($pago->realizado_at, 'Y-m-d'); ?>
                                            <tr>
                                                <td>{{$pago->cliente->nombre." ".$pago->cliente->apellido}}</td>
                                                <td>{{$tipos_pago[$pago->id_tipo_transaccion]}}</td>
                                                @if($busqueda_agrupar)
                                                <td>{{$pago->{'total_'.\App\Models\Pago::$tipos_pago_asociacion[$pago->id_tipo_transaccion]}.$configuracion_moneda->contenido}}</td>
                                                @else
                                                <td>{{$pago->monto.$configuracion_moneda->contenido}}</td>
                                                @endif
                                                <td>{{$datetime->format('d/m/Y')}}</td>
                                            </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="4">No se han registrado pagos...</td>
                                            </tr>
                                        @endif
                                        </tbody>
                                    </table>
                            </div>
                            <div class="col-lg-12 margin-top">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Concepto</th>
                                                <th>Monto</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @if(count($pagos) > 0)
                                            @foreach($totales_por_concepto as $key => $value)
                                            <tr>
                                                <td>{{$tipos_pago[$key]}}</td>
                                                <td>{{$value.$configuracion_moneda->contenido}}</td>
                                            </tr>
                                            @endforeach
                                            <tr>
                                                <td colspan="1"></td>
                                                <td>Total</td>
                                            </tr>
                                            <tr>
                                                <td colspan="1"></td>
                                                <td>{{array_sum(array_values($totales_por_concepto)).$configuracion_moneda->contenido}}</td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td colspan="2">No se han encontrado pagos...</td>
                                            </tr>
                                        @endif
                                        </tbody>
                                    </table>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>