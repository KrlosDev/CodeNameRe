@extends('layouts.default')

@section('content')

 <div class="row">
   <div class="col-md-12">
        <a href="javascript:crearBanco('{{url('bancos')}}')" class="btn btn-primary btn-fill"><i class="fa fa-plus" aria-hidden="true"></i>Nuevo Banco</a>
   </div>
 </div>

<div class="row">
<div class="col-md-12 ">
    <div class="card">
        <div class="header">
            <h4 class="title">Bancos</h4>
            <p class="category"></p>
        </div>
        <div class="content table-full-width">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <th>Nombre</th>
                        <th><i class="fa fa-cogs fa-lg"></i></th>
                    </thead>
                    <tbody>
                    @if(count($bancos) == 0)
                        <tr>
                            <td colspan="2">No se han encontrado resultados...</td>
                        </tr>
                    @endif
                    @foreach($bancos as $banco)
                        <tr>
                            <td>{{$banco->nombre}}</td>
                            <td>
                            @can('update', $banco)
                                 <a title="Editar Banco" href="javascript:editarBanco('{{url('bancos')}}/{{$banco->id}}')" >
                                     <i class="fa fa-pencil" aria-hidden="true"></i>
                                 </a>
                                 <a title="Eliminar Banco" href="javascript:eliminarBanco('{{url('bancos/'.$banco->id)}}}')" class="color-remove">
                                    <i class="fa fa-lg fa-times" aria-hidden="true"></i>
                                 </a>
                            @endcan
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div align="center">{!! $bancos->render() !!}</div>
</div>
</div>

@stop
@section("modals")
  @include('pages.banco.create')
  @include('pages.banco.update')
  @include('pages.banco.delete')
@stop