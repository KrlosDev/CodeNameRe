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
            <h4 class="title">Reporte de Ejecutivo de Ventas</h4>
            <p class="category">{{$ejecutivoDeVentas->user()->first()->nombre}}</p>
        </div>
        <div class="content">
            <div class="row">
                <div class='col-xs-12 margin-top'>
                    <form class="form-horizontal">
                        <div class="col-md-4 col-sm-6 col-xs-12 margin-top">
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
                        
                        <div class="col-xs-12 margin-top text-center">
                            <button type='submit' class='btn btn-primary'>Buscar <i class='fa fa-lg fa-search'></i></button>
                        </div>
                    </form>
                </div>
                <div class="col-xs-12 margin-top">
                    <h3>Reporte</h3>
                    @if($busqueda_proyecto)
                        <h4>Total casas proyecto <b>{{$total_casas}}</b></h4>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-striped table-basic-striped table-hover table-basic-hover table-center">
                            <thead>
                                <tr>
                                    <th>Descripción</th>
                                    <th>Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Casas asignadas</td>
                                    <td>{{array_sum($casas_estados->map(function($o){return $o->total;})->all())}}</td>
                                </tr>
                                @foreach($casas_estados as $casa)
                                <tr>
                                    <td>{{$estados[$casa->id_casa_estado]}}</td>
                                    <td>{{$casa->total}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-xs-12 margin-top text-center">
                    <a href="{{url('pdf/reporte/ejecutivoDeVentas/'.$ejecutivoDeVentas->id."?id_proyecto=".$busqueda_proyecto)}}" class="btn btn-primary" title="Descargar">Descargar</a>
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
    });
</script>
@endpush


@stop
@section("modals")
@stop
