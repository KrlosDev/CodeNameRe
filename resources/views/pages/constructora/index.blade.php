@extends('layouts.default')

@section('content')
@can('getAll', 'App\Models\Constructora')
<!-- Button trigger modal -->
<!-- <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal" data-backdrop="true">
  Launch demo modal
</button>
 -->


 <div class="row">
   <div class="col-md-12">
        <a href="javascript:crearCronstructora('{{url('constructoras')}}')" class="btn btn-primary btn-fill"><i class="fa fa-plus" aria-hidden="true"></i>Nueva Constructora</a>
   </div>
 </div>

<div class="row">
<div class="col-md-12 ">
    <div class="card">
        <div class="header">
            <h4 class="title">Constructoras</h4>
            <p class="category"></p>
        </div>
        <div class="content table-full-width">
          <div class="table-responsive">
            <table class="table table-hover table-striped ">
                <thead>
                  <th>Usuario</th>
                	<th>Nombre</th>
                	<th>Correo</th>
                	<th>Cant. de Brokers</th>
                	<th title="Cantidad de Ejecutivos de Ventas">Cant. de E.V</th>
                  <th title="Cantidad de Ejecutivos de Banco">Cant. de E.B</th>
                  <th>Fecha de vencimiento</th>
                  <th>Fecha de Suspención</th>
                  <th><i class="fa fa-cogs fa-lg"></i></th>
                </thead>
                <tbody>
                @if(count($constructoras) == 0)
                    <tr>
                        <td colspan="7">No se han encontrado resultados...</td>
                    </tr>
                @endif
                @foreach($constructoras as $contruc)

                    <tr >

                      <td>{{$contruc->user->name}}</td>              
                      <td>{{$contruc->user->nombre}}</td>
                      <td>{{$contruc->user->email}}</td>
                      <td>{{$contruc->max_brokers}}</td>
                      <td>{{$contruc->max_ejecutivos_ventas}}</td>
                      <td>{{$contruc->max_ejecutivos_bancos}}</td>
                      @if($contruc->licencia)
                        <td>{{$contruc->licencia->fecha_vencimiento}}</td>
                        <td>{{$contruc->licencia->fecha_suspension}}</td>
                      @else
                        <td>Ninguna</td>
                        <td>Ninguna</td>
                      @endif
                         <td>
                           @can('update', $contruc)
                              <a title="Editar Constructora" href="javascript:editarConstructora('{{url('constructoras')}}/{{$contruc->id}}')" >
                                  <i class="fa fa-pencil" aria-hidden="true"></i>
                              </a>
                              <a title="Eliminar Constructora" href="javascript:eliminarConstructora('{{url('constructoras/'.$contruc->id)}}}')" class="color-remove">
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
        <div align="center">{!! $constructoras->render() !!}</div>
</div>
</div>



@stop
@section("modals")
  @include('pages.constructora.create')
  @include('pages.constructora.update')
  @include('pages.constructora.delete')
@stop
@endcan