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
                        <h4 class="title">Lista de Propiedades</h4>
                        @if($user->isBroker())
                        <p class="category">{{$user->broker()->first()->nombre}}</p>
                        @else
                        <p class="category">{{$user->nombre}}</p>
                        @endif
                    </div>
                    <div class="content">
                        <div class="row">
                            <div class="col-xs-12 margin-top">
                              <table class="table table-hover table-center">
                                  <thead>
                                        <tr>
                                        @if(Auth::user()->isConstructora())
                                        <th> <input type="checkbox" onClick="toggle(this)" /><br/></th>
                                        @endif
                                        <th>Código</th>
                                        <!--th>Modelo</th-->
                                        <th>Cliente</th>
                                        <th>Telefono</th>
                                        <th>Valor</th>
                                        <th>Monto de Separación</th>
                                        <th>Abono Inicial</th>
                                        <th>Tot. mts2 ad.<a data-toggle="tooltip" title="Total del costo de los metros cuadrados adicionales para la propiedad"><i class="fa fa-lg fa-info-circle" style="color:#56AEFF;cursor:pointer;" aria-hidden="true"></i></a></th>
                                        <th>Pagado</th>
                                        <th>Por pagar</th>
                                        <th>Status</th>
                                        @if(!Auth::user()->isBroker())
                                          <th>Broker</th>
                                        @endif
                                      </tr>
                                  </thead>
                                  <tbody>
                                  @if(count($casas) == 0)
                                      <tr>
                                          <td colspan="12">No se han encontrado resultados...</td>
                                      </tr>
                                  @endif
                                  @foreach($casas as $casa)

                                      <tr
                                            @if($estados->first() && !($casa->id_casa_estado != $estados->first()->id))
                                                  class="success"
                                            @else 
                                                class="danger"
                                            @endif >

                                           @if(Auth::user()->isConstructora())  
                                           <td><input type="checkbox" name="cliente[]" id="cliente[]" value={{$casa->id}}></td>
                                          @endif
                                          
                                        <td>{{$casa->codigo}}</td>
                                        @if(count($casa->cliente)>0)
                                        <td>{{$casa->cliente[0]->nombre}} {{$casa->cliente[0]->apellido}}</td>
                                        <td>
                                            @if(count($casa->cliente[0]->telefonoCliente)>0)
                                              @foreach($casa->cliente[0]->telefonoCliente as $i=>$telefono)
                                                @if($i!=0)
                                                ,
                                                @endif
                                              {{$telefono->telefono}}
                                              @endforeach
                                            @else
                                              Ninguno
                                            @endif
                                        </td>
                                        @else
                                        <td>Ninguno</td>
                                        <td>Ninguno</td>
                                        @endif
                                        
                                        <td>{{$casa->valor}}</td>
                                        <td>{{$casa->monto_separacion}}</td>
                                        <td>{{$casa->monto_abono_inicial}}</td>
                                        <td>{{$casa->monto_mts2_adicional*$casa->mts2_adicionales}}</td>
                                        <td>{{$casa->getMontoPagadoTotalTabla([\App\Models\Pago::TIPO_PAGO_MONTO_INICIAL])}}</td>
                                        <td>{{$casa->getMontoPorPagarTotalTabla([\App\Models\Pago::TIPO_PAGO_MONTO_INICIAL])}}</td>
                                        @if($casa->id_casa_estado)
                                            <td>{{$casa->getEstadoNombre()}}</td>
                                        @else
                                            <td>Sin asignar</td>
                                        @endif
                                        
                                        @if(!Auth::user()->isBroker())
                                        
                                          <td>@if(!($casa->id_broker == null))
                                                  {{$casa->broker()->first()->user()->first()->nombre}}
                                              @endif </td>
                                        @endif

                                      </tr>

                                  @endforeach
                                  
                                  </form>
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