@extends('layouts.default')

@can('getAll', 'App\Models\Broker')
@section('content')



<!-- Button trigger modal -->
<!-- <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal" data-backdrop="true">
  Launch demo modal
</button>
 -->

@can('store', 'App\Models\Broker')
    <div class="row">
      <div class="col-md-12">
           <a href="javascript:crearBroker('{{url('brokers')}}')" class="btn btn-primary btn-fill"><i class="fa fa-plus" aria-hidden="true"></i> Nuevo Broker</a>
      </div>
    </div>
@endcan
<div class="row">
<div class="col-md-12 ">
    <div class="card">
        <div class="header">
            <h4 class="title">Brokers</h4>
            <p class="category"></p>
        </div>
        <div class="content table-full-width">
          <div class="table-responsive">
            <table class="table table-hover table-striped table-center">
                <thead>
                    
                    <th>Usuario</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Cantidad de Ejecutivos de Ventas</th>
                    <th>Cantidad de Ejecutivos de Banco</th>
                    <th><i class="fa fa-cogs fa-lg"></i></th>
                </thead>
                <tbody>
                @if(count($brokers) == 0)
                    <tr>
                        <td colspan="7">No se han encontrado resultados...</td>
                    </tr>
                @endif
                @foreach($brokers as $broker)

                    <tr>
                      
                      <td>{{$broker->user->name}}</td>              
                      <td>{{$broker->user->nombre}}</td>
                      <td>{{$broker->user->email}}</td>
                      <td>{{$broker->max_ej_ventas}}</td>
                      <td>{{$broker->max_ej_bancos}}</td>
                         <td>
                           @can('update', $broker)
                              <a title="Editar Broker" href="javascript:editarBroker('{{url('brokers')}}/{{$broker->id}}')" >
                                  <i class="fa fa-pencil" aria-hidden="true"></i>
                              </a>



                             <a title="Asignar % de Ganancia" href="javascript:asignarPorcentaje('{{url('brokers/porcentaje')}}',{{$broker->id}})" class="text-success">
                                 <i class="fa fa-percent" aria-hidden="true"></i>
                             </a>


                            @endcan
                            @can('update', $broker)
                            <a title="Desasignar Propiedades" href="javascript:quitarCasa('{{url('brokers/eliminar_casas/'.$broker->id)}}','{{url('casas/broker_casas/'.$broker->id)}}')" >
                                  <i class="pe-7s-trash" aria-hidden="true"></i>
                            </a>
                            @endcan
                            @can('delete', $broker)
                             <a title="Eliminar Broker" href="javascript:eliminarBroker('{{url('brokers/'.$broker->id)}}')" class="color-remove">
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
    <div align="center">{!! $brokers->render() !!}</div>
</div>
</div>



@stop
@section("modals")
  @include('pages.broker.create')
  @include('pages.broker.update')
  @include('pages.broker.delete')
  @include('pages.broker.porcentaje')
  @include('pages.broker.quitarCasa')
  
@stop
@endcan