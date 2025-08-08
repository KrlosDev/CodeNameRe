<?php

use App\Models\Configuracion;

if (! isset($user)) {
    $user = \Auth::user();
}

$configuracion_moneda = Configuracion::where('descripcion', Configuracion::CONFIGURACION_MONEDA)->first();
?>

@extends('layouts.default')

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
            <h4 class="title">Pagos</h4>
            <p class="category"></p>
        </div>
        <div class="content">
            <div class="row">
                <div class='col-xs-12 margin-top'>
                    <form class="form-horizontal">
                        <div class="col-md-4 col-sm-6 col-xs-12 margin-top">
                              <label for="cantidad">Cantidad</label>
                              <select class="form-control" id="cantidad" name="cantidad">
                              @foreach(\App\Models\Funciones::$cantidad_option as $key => $value)
                              @if($key == $busqueda_cantidad)
                                <option value='{{$key}}' selected>{{$value}}</option>
                              @else
                                <option value='{{$key}}'>{{$value}}</option>
                              @endif
                              @endforeach
                              </select>
                        </div>
                        
                        <div class="col-md-4 col-sm-6 col-xs-12 margin-top">
                            <label for="fecha_min">Fecha inicial</label>
                            <input type='text' class='form-control' name='fecha_min' id='fecha_min' placeholder='Fechas' value='{{$busqueda_fecha_min}}' />
                        </div>
                        
                        <div class="col-md-4 col-sm-6 col-xs-12 margin-top">
                            <label for="fecha_max">Fecha final</label>
                            <input type='text' class='form-control' name='fecha_max' id='fecha_max' placeholder='Fechas' value='{{$busqueda_fecha_max}}' />
                        </div>
                        
                        <div class="col-md-4 col-sm-6 col-xs-12 margin-top">
                            <label for="id_proyecto">Proyecto</label>
                            <select class="form-control" id="id_proyecto" name="id_proyecto">
                                <option value='0'>Todos</option>
                            @foreach($proyectos as $proyect)
                            @if($proyect->id == $busqueda_proyecto)
                                <option value='{{$proyect->id}}' selected>{{$proyect->nombre}}</option>
                            @else
                                <option value='{{$proyect->id}}'>{{$proyect->nombre}}</option>
                            @endif
                            @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-4 col-sm-6 col-xs-12 margin-top">
                              <label for="id_tipo_transaccion">Concepto</label>
                              <select class="form-control" id="id_tipo_transaccion" name="id_tipo_transaccion">
                                  <option value='0'>Todos</option>
                              @foreach($tipos_pago as $key => $value)
                              @if($key == $busqueda_tipo_pago)
                                <option value='{{$key}}' selected>{{$value}}</option>
                              @else
                                <option value='{{$key}}'>{{$value}}</option>
                              @endif
                              @endforeach
                              </select>
                        </div>
                        
                        <div class="col-xs-12 margin-top text-center">
                            <button type='submit' class='btn btn-primary'>Buscar <i class='fa fa-lg fa-search'></i></button>
                        </div>
                    </form>
                </div>
                <div class="col-xs-12 margin-top">
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
                            @if(count($pagos) > 0)
                                @foreach($pagos as $pago)
                                <?php $datetime = \App\Models\Funciones::createDateTimeObject($pago->created_at, 'Y-m-d H:i:s'); ?>
                                <tr>
                                    <td>{{$pago->cliente->nombre." ".$pago->cliente->apellido}}</td>
                                    <td>{{$tipos_pago[$pago->id_tipo_transaccion]}}</td>
                                    <td>{{$pago->monto.$configuracion_moneda->contenido}}</td>
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
                </div>
                <div class="col-xs-12 margin-top">
                    <div class="table-responsive">
                        <table class="table table-striped table-basic-striped table-hover table-basic-hover table-center">
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
                                    <td colspan="4">No se han encontrado pagos...</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                        <div class='text-center'>{!! $pagos->render() !!}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<link rel="stylesheet" type="text/css" href="{{asset('dpd/jquery-ui-1.12.1.custom/jquery-ui.min.css')}}"/>

@push('JS')

<script src="{{asset('dpd/jquery-ui-1.12.1.custom/jquery-ui.min.js')}}"></script>
<script type='text/javascript'>
    $(document).ready(function() {
        $( "#fecha_min" ).datepicker({dateFormat:'dd/mm/yy'});
        $( "#fecha_max" ).datepicker({dateFormat:'dd/mm/yy'});
    });
</script>
@endpush


@stop
@section("modals")
@stop
