@extends('layouts.default')
@can('getAll', 'App\Models\EjecutivoVentas')
@section('content')



<!-- Button trigger modal -->
<!-- <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal" data-backdrop="true">
  Launch demo modal
</button>
 -->


 <div class="row">
   <div class="col-md-12">
        <a href="javascript:crearEjventas('{{url('ejecutivo_ventas')}}')" class="btn btn-primary btn-fill"><i class="fa fa-plus" aria-hidden="true"></i> Nuevo Ejecutivo de ventas</a>
   </div>
 </div>

<div class="row">
<div class="col-md-12 ">
    <div class="card">
        <div class="header">
            <h4 class="title">Ejecutivos de Ventas</h4>
            <p class="category"></p>
        </div>
        <div class="content table-full-width">
          <div class="table-responsive">
            <table class="table table-hover table-striped table-center">
                <thead>
                    <th>Usuario</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th><i class="fa fa-cogs fa-lg"></i></th>
                </thead>
                <tbody>
                @if(count($ejventas) == 0)
                    <tr>
                        <td colspan="7">No se han encontrado resultados...</td>
                    </tr>
                @endif
                @foreach($ejventas as $ejecutivo)

                    <tr>

                      <td>{{$ejecutivo->user->name}}</td>              
                      <td>{{$ejecutivo->user->nombre}}</td>
                      <td>{{$ejecutivo->user->email}}</td>
                         <td>
                           @can('update', $ejecutivo)
                              <a title="Editar Ejecutivo de Ventas" href="javascript:editarEjventas('{{url('ejecutivo_ventas')}}/{{$ejecutivo->id}}')" >
                                  <i class="fa fa-pencil" aria-hidden="true"></i>
                              </a>

                              <a title="Eliminar Ejecutivo de Ventas" href="javascript:eliminarEjVentas('{{url('ejecutivo_ventas/'.$ejecutivo->id)}}}')" class="color-remove">
                                   <i class="fa fa-lg fa-times" aria-hidden="true"></i>
                               </a>
                            @endcan
                           @can('reporteEjecutivoDeVentas', $ejecutivo)
                              <a title="Reporte Ejecutivo Ventas" href="{{url('reporte/ejecutivoDeVentas/'.$ejecutivo->id)}}" >
                                  <i class="fa fa-line-chart" aria-hidden="true"></i>
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
    <div align="center">{!! $ejventas->render() !!}</div>
</div>
</div>



@stop
@section("modals")
  @include('pages.ejventas.create')
  @include('pages.ejventas.update')
    @include('pages.ejventas.delete')
@stop
@endcan