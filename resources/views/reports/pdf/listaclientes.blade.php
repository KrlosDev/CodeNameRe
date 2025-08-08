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
              font-size: 10px;
        }
        tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }
    </style>
        
        <title>Lista de Clientes</title>
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
                        <h4 class="title">Lista de Clientes</h4>
                        @if($user->isBroker())
                        <p class="category">{{$user->broker()->first()->nombre}}</p>
                        @else
                        <p class="category">{{$user->nombre}}</p>
                        @endif
                    </div>
                    <div class="content">
                        <div class="row">
                            <div class="col-xs-12 margin-top">
                                <table>
                                    <thead>
                                        <tr>
                                        <th>Casas</th>
                                        

                                        <th>Nombre</th>
                                        <th>Apellido</th>
                                        <th>Correo</th>
                                        <!-- <th>Identificación</th>   --> 
                                         <th>Telefono</th>               
                                        <th>Status</th>
                                        @if(!(Auth::user()->isEjVentas() || Auth::user()->isEjBancos()))
                                        <th title="Ejecutivo de Ventas">E.V</th>
                                        <!-- <th>Status de Tramite</th> -->
                                        @endif  
                                        </tr>                  
                                    </thead>
                                    <tbody>
                                    @if(count($clientes) == 0)
                                        <tr>
                                            <td colspan="7">No se han encontrado resultados...</td>
                                        </tr>
                                    @endif
                                    @foreach($clientes as $cliente)
                                        <tr @if(count($cliente->clienteCasa)==0) class="danger" @endif>
                                            <td>
                                            @if(count($cliente->clienteCasa)!=0)
                                                @foreach($cliente->clienteCasa as $i=>$house)
                                                  @if($i!=0)
                                                  ,
                                                  @endif
                                                  {{$house->codigo}}
                                                @endforeach
                                            @else
                                                Ninguna
                                            @endif
                                            </td>
                                            <td>{{$cliente->nombre}}</td>
                                            <td>{{$cliente->apellido}}</td>
                                            <td>{{$cliente->user->email}}</td>
                                            <!-- <td>{{$cliente->identificacion}}</td> -->
                                            <td>
                                            @foreach($cliente->telefonoCliente as $telefono_cliente)
                                                {{$telefono_cliente->telefono}}
                                                <br>
                                            @endforeach
                                            </td>
                                            <td>
                                            @if(count($cliente->clienteCasa)!=0)
                                                @foreach($cliente->clienteCasa as $i=>$house)
                                                @if($i!=0)
                                                ,
                                                @endif
                                                {{$house->getEstadoNombre()}}
                                                @endforeach
                                            @else
                                                Ninguno
                                            @endif
                                            </td>
                                            @if(!(Auth::user()->isEjVentas() || Auth::user()->isEjBancos()))
                                            <td>
                                                @if(count($cliente->clienteCasa)!=0)
                                                    @foreach($cliente->clienteCasa as $i=>$house)
                                                        @if($i!=0)
                                                            ,
                                                        @endif

                                                        @if(count($house->ejecutivoVentas) != 0)
                                                            {{$house->ejecutivoVentas->user->nombre}}
                                                        @else
                                                            Ninguno
                                                        @endif
                                                    @endforeach
                                                @else
                                                    Ninguno
                                                @endif
                                            </td>
                                            @endif
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