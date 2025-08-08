@extends('layouts.default')

@section('content')

<?php
    /**
     * @var \App\User $user
     */
    $user=\Illuminate\Support\Facades\Auth::user();
?>

    <div class="row">
        <div class="col-md-12">
            <a href="javascript:crearCasaEstado('{{url("casas_estados")}}')" class="btn btn-primary btn-fill"><i class="fa fa-plus" aria-hidden="true"></i> Nuevo estado</a>

        </div>
    </div>

    <div class="row">
        <div class="col-md-12 ">
            <div class="card">
                <div class="header">
                    <h4 class="title">Estados</h4>
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
                                    @if($user->isConstructora())
                                    <th>Broker</th>
                                    @endif
                                    <th>Nombre</th>
                                    <th>Slug</th>
                                    <th><i class="fa fa-cogs fa-lg"></i></th>
                                </thead>
                                <tbody>
                                @if(count($casasEstados) == 0)
                                    <tr>
                                        <td colspan="3">No se han encontrado resultados...</td>
                                    </tr>
                                @endif
                                @foreach($casasEstados as $casaEstado)
                                    <tr>
                                    @can('store',\App\Models\CasaEstado::class)
                                        @if($user->isConstructora())
                                            <td>{{$casaEstado->broker->user->name}}</td>
                                        @endif
                                        <td>{{$casaEstado->nombre}}</td>
                                        <td>{{$casaEstado->slug}}</td>
                                        <td>
                                            <a title="Editar Estado" href="javascript:editarCasaEstado('{{"casas_estados/{$casaEstado->id}"}}')" >
                                                <i class="fa fa-lg fa-pencil" aria-hidden="true"></i>
                                            </a>
                                            <a title="Eliminar Estado" href="javascript:eliminarCasaEstado('{{url('casas_estados/'.$casaEstado->id)}}}')" class="color-remove">
                                                <i class="fa fa-lg fa-times" aria-hidden="true"></i>
                                            </a>
                                        </td>
                                    @endcan
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center">
                            {{$casasEstados->appends(['cantidad' => $busqueda_cantidad])->render()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



@stop
@section("modals")
    @can('store',\App\Models\CasaEstado::class)
        @include('pages.casa-estado.create')
        @include('pages.casa-estado.update')
        @include('pages.casa-estado.delete')
    @endcan
@stop

@push('JS')
    <script type='text/javascript'>
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush