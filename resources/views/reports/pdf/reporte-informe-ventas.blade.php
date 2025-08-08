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
                        <h4 class="title">Informe de Ventas</h4>
                        @if($user->isBroker())
                        <p class="category">{{$user->broker()->first()->nombre}}</p>
                        @else
                        <p class="category">{{$user->nombre}}</p>
                        @endif
                    </div>
                    <div class="content">
                        <div class="row">
                            <div class="col-xs-12 margin-top">
                                <h4>Balance</h4>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Banco</th>
                                            <th>Casa</th>
                                            <th>Mts2 Totales</th>
                                            <th>Nombre</th>
                                            <th>Cédula</th>
                                            <th>Recámaras</th>
                                            <th>Estatus</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @if(count($casas) == 0)
                                    <tr>
                                        <td colspan='7'>No se han encontrado resultados...</td>
                                    </tr>
                                    @else
                                    @foreach($casas as $casa)
                                    <tr>
                                        @if($casa->cliente()->first() && $casa->cliente()->first()->banco)
                                        <td>{{$casa->cliente()->first()->banco()->first()->nombre}}</td>
                                        @else
                                        <td>---</td>
                                        @endif
                                        <td>{{$casa->codigo}}</td>
                                        <td>{{$casa->mts2_total}}</td>
                                        @if($casa->cliente()->first())
                                        <td>{{$casa->cliente()->first()->nombre." ".$casa->cliente()->first()->apellido}}</td>
                                        <td>{{$casa->cliente()->first()->identificacion}}</td>
                                        @else
                                        <td>---</td>
                                        <td>---</td>
                                        @endif
                                        <td>{{$casa->recamaras}}</td>
                                        @if(array_key_exists($casa->id_casa_estado,$estados))
                                            <td>{{$estados[$casa->id_casa_estado]}}</td>
                                        @else
                                            <td>Sin asignar</td>
                                        @endif
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