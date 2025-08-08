@extends('layouts.default')


@section('content')



<!-- Button trigger modal -->
<!-- <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal" data-backdrop="true">
  Launch demo modal
</button>
 -->


    <div class="row">
      <div class="col-md-12">
      @can('store','App\Models\Mensaje')
                          
           <a href="javascript:crearMensaje('{{url('mensajes')}}','{{url('clientes/ejecutivos')}}')" class="btn btn-primary btn-fill"><i class="fa fa-plus" aria-hidden="true"></i> Nuevo Mensaje</a>
      @endcan
      </div>
    </div>

<div class="row">
<div class="col-md-12 ">
    <div class="card">
        <div class="header">
            <h4 class="title">Mensajes</h4>
            <p class="category"></p>
        </div>
        <div class="content table-full-width">
          <div class="table-responsive">
            <table class="table table-hover table-striped table-center">
                <thead>
                    
                    <th>Cliente</th>
                    <th>Titulo</th>
                    <th>Fecha</th>
                    
                    <th><i class="fa fa-cogs fa-lg"></i></th>
                </thead>
                <tbody>
                    @if(count($mensajes) == 0)
                    <tr>
                        <td colspan="7">No se han encontrado resultados...</td>
                    </tr>
                    @endif
                
                    @foreach($mensajes as $mensaje)
                        <tr class='{!!$mensaje->isLeidoCss()!!}'>
                            @if($mensaje->cliente)
                            <td>{{$mensaje->cliente->nombre}}</td>
                            @else
                            <td>---</td>
                            @endif
                            <td>{{$mensaje->titulo}}</td>
                            <td>{{date('d-m-Y',strtotime($mensaje->created_at))}}</td>
                            <td>
                                <a title="Ver Mensaje" href="javascript:showMensaje('{{url('mensajes')}}/{{$mensaje->id}}')">
                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                </a>
                                <a title="Eliminar Mensaje" href="javascript:eliminarMensaje('{{url('mensajes')}}/{{$mensaje->id}}')"  class="color-remove">
                                    <i class="fa fa-lg fa-times" aria-hidden="true"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
          </div>
        </div>
    </div>
</div>
</div>



@stop
@section("modals")
  @include('pages.mensaje.delete')  
@stop
