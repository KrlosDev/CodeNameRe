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
        <a href="javascript:crearEjbanco('{{url('ejecutivo_bancos')}}')" class="btn btn-primary btn-fill"><i class="fa fa-plus" aria-hidden="true"></i> Nuevo Ejecutivo de Banco</a>
   </div>
 </div>

<div class="row">
<div class="col-md-12 ">
    <div class="card">
        <div class="header">
            <h4 class="title">Ejecutivos de Banco</h4>
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
                @if(count($ejbancos) == 0)
                    <tr>
                        <td colspan="7">No se han encontrado resultados...</td>
                    </tr>
                @endif
                @foreach($ejbancos as $ejecutivo)

                    <tr>
                      <td>{{$ejecutivo->user->name}}</td>              
                      <td>{{$ejecutivo->user->nombre}}</td>
                      <td>{{$ejecutivo->user->email}}</td>
                         <td>
                           @can('update', $ejecutivo)
                              <a title="Editar Ejecutivo de Banco" href="javascript:editarEjbanco('{{url('ejecutivo_bancos')}}/{{$ejecutivo->id}}')" >
                                  <i class="fa fa-pencil" aria-hidden="true"></i>
                              </a>

                              <a title="Eliminar Ejecutivo de Banco" href="javascript:eliminarEjBanco('{{url('ejecutivo_bancos/'.$ejecutivo->id)}}}')" class="color-remove">
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
    <div align="center">{!! $ejbancos->render() !!}</div>
</div>
</div>



@stop
@section("modals")
  @include('pages.ejbanco.create')
  @include('pages.ejbanco.update')
  @include('pages.ejbanco.delete')
@stop
@endcan