@extends('layouts.default')

@section('content')

<div class="row">
    <div class="col-md-12">
        <a href="javascript:crearRequerimientoTramite('{{url("requerimientos_tramites/{$proyecto->id}")}}')" class="btn btn-primary btn-fill"><i class="fa fa-plus" aria-hidden="true"></i> Nuevo requerimiento</a>
        <a href="javascript:copiarRequerimientoTramite('{{url("requerimientos_tramites/{$proyecto->id}/copiar")}}')" class="btn btn-success btn-fill"><i class="fa fa-copy" aria-hidden="true"></i> Copiar requerimientos</a>
    </div>
</div>

<div class="row">
    <div class="col-md-12 ">
        <div class="card">
            <div class="header">
                <h4 class="title">Requerimientos de {{$proyecto->nombre}}</h4>
                <p class="category"></p>
            </div>
            <div class='col-xs-12 no-padding'>
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
                    <div class="col-xs-12 margin-top text-center">
                        <button type='submit' class='btn btn-primary'>Buscar <i class='fa fa-lg fa-search'></i></button>
                    </div>
                </form>
            </div>
            <div class='col-xs-12 no-padding'>
                <div class="content table-full-width">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-center">
                            <thead>
                                <th>Nombre</th>
                                <th><i class="fa fa-cogs fa-lg"></i></th>
                            </thead>
                            <tbody>
                            @if(count($requerimientosTramites) == 0)
                                <tr>
                                    <td colspan="3">No se han encontrado resultados...</td>
                                </tr>
                            @endif
                            @foreach($requerimientosTramites as $requerimientoTramite)
                                <tr>
                                    <td>
                                        {{$requerimientoTramite->nombre}}
                                    </td>
                                    <td>
                                        <a title="Editar Requerimiento" href="javascript:editarRequerimientoTramite({{"requerimientos_tramites/{$requerimientoTramite->id}"}})" >
                                            <i class="fa fa-lg fa-pencil" aria-hidden="true"></i>
                                        </a>
                                        <a title="Eliminar Requerimiento" href="javascript:eliminarRequerimientoTramite('{{url('requerimientos_tramites/'.$requerimientoTramite->id)}}}')" class="color-remove">
                                            <i class="fa fa-lg fa-times" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center">
                        {{$requerimientosTramites->appends(['cantidad' => $busqueda_cantidad])->render()}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



@stop
@section("modals")
    @include('pages.proyecto.requerimientos-tramites.create')
    @include('pages.proyecto.requerimientos-tramites.copiar')
    @include('pages.proyecto.requerimientos-tramites.update')
    @include('pages.proyecto.requerimientos-tramites.delete')
@stop

@push('JS')
    <script type='text/javascript'>
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush