<?php

use App\Models\Configuracion;

if (! isset($user)) {
    $user = \Auth::user();
}

$configuracion_moneda = Configuracion::where('descripcion', Configuracion::CONFIGURACION_MONEDA)->first();
?>

@extends('layouts.default')

@section('content')

<div class="row">
<div class="col-md-12 ">
    <div class="card">
        <div class="header">
            <h4 class="title">Informe de Ventas</h4>
            <p class="category"></p>
        </div>
        <div class="content">
            <div class="row">
                <div class='col-xs-12 margin-top'>
                    <form class="form-horizontal">
                        <div class="col-md-4 col-sm-6 col-xs-12 margin-top div-select2-fix">
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
                        
                        <div class="col-md-4 col-sm-6 col-xs-12 margin-top div-select2-fix">
                            <label for="id_banco">Banco</label>
                            <select class="form-control" id="id_banco" name="id_banco">
                                <option value='0'>Todos</option>
                            @foreach($bancos as $banco)
                            @if($banco->id == $busqueda_banco)
                                <option value="{{$banco->id}}" selected>{{$banco->nombre}}</option>
                            @else
                                <option value="{{$banco->id}}">{{$banco->nombre}}</option>
                            @endif
                            @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-4 col-sm-6 col-xs-12 margin-top div-select2-fix">
                            <label for="id_proyecto">Proyecto</label>
                            <select class="form-control" id="id_proyecto" name="id_proyecto">
                                <option value='0'>Todos</option>
                            @foreach($proyectos as $proyecto)
                            @if($proyecto->id == $busqueda_proyecto)
                                <option value="{{$proyecto->id}}" selected>{{$proyecto->nombre}}</option>
                            @else
                                <option value="{{$proyecto->id}}">{{$proyecto->nombre}}</option>
                            @endif
                            @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-4 col-sm-6 col-xs-12 margin-top div-select2-fix">
                            <label for="id_casa_estado">Estado trámite</label>
                            <select class="form-control" id="id_casa_estado" name="id_casa_estado">
                                <option value='0'>Todos</option>
                            @foreach($estados as $key => $value)
                            @if($key == $busqueda_id_casa_estado)
                                <option value="{{$key}}" selected>{{$value}}</option>
                            @else
                                <option value="{{$key}}">{{$value}}</option>
                            @endif
                            @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-4 col-sm-6 col-xs-12 margin-top div-select2-fix">
                            <label for="asignadas">Asignadas</label>
                            @if($busqueda_asignadas)
                            <input type='checkbox' name='asignadas' id='asignadas' value='1' checked="" />
                            @else
                            <input type='checkbox' name='asignadas' id='asignadas' value='1' />
                            @endif
                        </div>
                        
                        <div class="col-md-4 col-sm-6 col-xs-12 margin-top div-select2-fix">
                            <label for="asignadas_bancos">Ordenar por bancos</label>
                            @if($busqueda_asignadas_bancos)
                            <input type='checkbox' name='asignadas_bancos' id='asignadas_bancos' value='1' checked="" />
                            @else
                            <input type='checkbox' name='asignadas_bancos' id='asignadas_bancos' value='1' />
                            @endif
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
                            @elseif($busqueda_asignadas_bancos)
                                <?php $last_banco = $casas[0]->bancos_nombre; ?>
                                @if(!$casas[0]->bancos_nombre)
                                    <h3>Sin asignar</h3>
                                @else
                                    <h3>{{$casas[0]->bancos_nombre}}</h3>
                                @endif
                                @foreach($casas as $casa)
                                @if($casa->bancos_nombre != $last_banco)
                                    @if(!$casa->bancos_nombre)
                                    <?php $last_banco = "Sin asignar"; ?>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-basic-striped table-hover table-basic-hover table-center">
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
                                <?php $last_banco = $casa->bancos_nombre; ?>
                                <h3>{{$last_banco}}</h3>
                                </body>
                                @endif
                                <tr>
                                    @if($casa->cliente()->first() && $casa->cliente()->first()->banco)
                                    <td>{{$casa->cliente()->first()->banco->nombre}}</td>
                                    @else
                                    <td>---</td>
                                    @endif
                                    <td>{{$casa->codigo}} {{$casa->banco_nombre}}</td>
                                    <td>{{$casa->mts2_total}}</td>
                                    @if($casa->cliente()->first())
                                    <td>{{$casa->cliente()->first()->nombre." ".$casa->cliente()->first()->apellido}}</td>
                                    <td>{{$casa->cliente()->first()->identificacion}}</td>
                                    @else
                                    <td>---</td>
                                    <td>---</td>
                                    @endif
                                    <td>{{$casa->recamaras}}</td>
                                    <td>{{$casa->getEstadoNombre()}}</td>
                                </tr>
                                @endforeach
                            @else
                                @foreach($casas as $casa)
                                <tr>
                                    @if($casa->cliente()->first() && $casa->cliente()->first()->banco)
                                    <td>{{$casa->cliente()->first()->banco->nombre}}</td>
                                    @else
                                    <td>---</td>
                                    @endif
                                    <td>{{$casa->codigo}} {{$casa->banco_nombre}}</td>
                                    <td>{{$casa->mts2_total}}</td>
                                    @if($casa->cliente()->first())
                                    <td>{{$casa->cliente()->first()->nombre." ".$casa->cliente()->first()->apellido}}</td>
                                    <td>{{$casa->cliente()->first()->identificacion}}</td>
                                    @else
                                    <td>---</td>
                                    <td>---</td>
                                    @endif
                                    <td>{{$casa->recamaras}}</td>
                                    <td>{{$casa->getEstadoNombre()}}</td>
                                </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                        @if(count($casas) > 0)
                        <div class="text-center">
                            {{$casas->appends(['cantidad' => $busqueda_cantidad, 'id_proyecto' => $busqueda_proyecto, 'id_banco' => $busqueda_banco, 
                            'id_casa_estado' => $busqueda_id_casa_estado, 'asignadas' => $busqueda_asignadas, 'asignadas_bancos' => $busqueda_asignadas_bancos])->render()}}
                        </div>
                        @endif
                    </div>
                </div>
                <div class="col-xs-12 margin-top text-center">
                    <a href="{{url('pdf/reporte/informeDeVentas?id_banco='.$busqueda_banco.'&id_proyecto='.$busqueda_proyecto.
                        '&id_casa_estado='.$busqueda_id_casa_estado.'&asignadas='.$busqueda_asignadas.'&asignadas_bancos='.$busqueda_asignadas_bancos)}}" class="btn btn-primary" title="Descargar">PDF</a>
                    <a href="{{url('excel/reporte/informeDeVentas?id_banco='.$busqueda_banco.'&id_proyecto='.$busqueda_proyecto.
                        '&id_casa_estado='.$busqueda_id_casa_estado.'&asignadas='.$busqueda_asignadas.'&asignadas_bancos='.$busqueda_asignadas_bancos)}}" class="btn btn-primary" title="Descargar Excel">Excel</a>
                
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<link rel="stylesheet" type="text/css" href="{{asset('dpd/select2-4.0.3/dist/css/select2.min.css')}}"/>

@push('JS')

<script src="{{asset('js/funciones.js')}}"></script>
<script src="{{asset('dpd/select2-4.0.3/dist/js/select2.min.js')}}"></script>

<script type='text/javascript'>
    $(document).ready(function() {
        $("#id_banco").select2();
        $("#id_proyecto").select2();
        $("#id_casa_estado").select2();
    });
</script>
@endpush


@stop
@section("modals")
@stop
