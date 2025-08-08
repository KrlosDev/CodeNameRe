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
            <p class="category">Estado de cuenta</p>
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
                            <div class='col-md-9 col-xs-12'>
                                <label for="identificacion">Número de Identidad</label>
                                <input type="text" class="form-control" name="identificacion" id="identificacion" placeholder="Número de Identidad del cliente" value="{{$busqueda_identificacion}}" onblur="cargarPropiedades('{{url("/casas/deCliente")}}',this.value,'#id_propiedad');" />
                            </div>
                            <div class='col-md-3 col-xs-12' style='margin-top: 23px;'>
                                <button type="button" class="btn btn-primary" onclick="cargarPropiedades('{{url("/casas/deCliente")}}',$('#identificacion').val(),'#id_propiedad');">Cargar</button>
                            </div>
                        </div>
                        
                        <div class="col-md-4 col-sm-6 col-xs-12 margin-top">
                            <label for="id_propiedad">Propiedad</label>
                            <select class="form-control" id="id_propiedad" name="id_propiedad"></select>
                        </div>
                        
                        <div class="col-xs-12 margin-top text-center">
                            <button type='submit' class='btn btn-primary'>Buscar <i class='fa fa-lg fa-search'></i></button>
                        </div>
                    </form>
                </div>
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
<!--                                 <tr>
                                    <td>Por pagar</td>
                                    @foreach($pagos_por_pagar as $key => $value)
                                    <td>{{$value}}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td>Pagado</td>
                                    @foreach($pagos_efectuados as $key => $value)
                                    <td>{{$value}}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td>Pendiente</td>
                                    @foreach($pagos_pendientes as $key => $value)
                                    <td>{!!\App\Models\Funciones::colorearSaldoExacto($pagos_por_pagar[$key] - $pagos_efectuados[$key],0)!!}</td>
                                    @endforeach
                                </tr> -->

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
                @if($busqueda_identificacion && $busqueda_propiedad)
                <div class="col-xs-12 margin-top text-center">
                    <a href="{{url('pdf/reporte/estadoCuentaCliente?identificacion='.$busqueda_identificacion."&id_propiedad=".$busqueda_propiedad)}}" class="btn btn-primary" title="Descargar">PDF</a>
                    <a href="{{url('excel/reporte/estado-cuenta-cliente?identificacion='.$busqueda_identificacion."&id_propiedad=".$busqueda_propiedad)}}" class="btn btn-primary" title="Descargar">Excel</a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
</div>

<link rel="stylesheet" type="text/css" href="{{asset('dpd/jquery-ui-1.12.1.custom/jquery-ui.min.css')}}"/>
<!--link rel="stylesheet" type="text/css" href="{{asset('dpd/select2-4.0.3/dist/css/select2.min.css')}}"/-->

@push('JS')

<script src="{{asset('js/funciones.js')}}"></script>
<!--script src="{{asset('dpd/select2-4.0.3/dist/js/select2.min.js')}}"></script-->
<script src="{{asset('dpd/jquery-ui-1.12.1.custom/jquery-ui.min.js')}}"></script>

<script type='text/javascript'>
    $(document).ready(function() {
        //$("#id_cliente").select2();
        
        loadSearchAuto("#identificacion","{{url('/clientes/buscar')}}",3);
        
        cargarPropiedades('{{url("/casas/deCliente")}}',$('#identificacion').val(),'#id_propiedad');
    });
</script>
@endpush


@stop
@section("modals")
@stop
