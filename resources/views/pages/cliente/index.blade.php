@extends('layouts.default')
@can('getAll', 'App\Models\Cliente')
@section('content')

<!-- Button trigger modal -->
<!-- <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal" data-backdrop="true">
  Launch demo modal
</button>
 -->

 @can('store', 'App\Models\Cliente')
 <div class="row">
   <div class="col-md-12">
        <a href="javascript:crearCliente('{{url('clientes')}}')" class="btn btn-primary btn-fill"><i class="fa fa-plus" aria-hidden="true"></i> Nuevo Cliente</a>
   </div>
 </div>
 @endcan

<div class="row">
<div class="col-md-12 ">
    <div class="card">
        <div class="header">
            <h4 class="title">Clientes</h4>
            <p class="category"></p>
        </div>
        <div class="content table-full-width">
            <div class='col-xs-12 no-padding'>
                <form class="form-horizontal">

                    <div class="col-md-4 col-sm-6 col-xs-12 margin-top">
                          <label for="nombres">Nombres <a data-toggle="tooltip" title="Buscar por nombre"><i class="fa fa-lg fa-info-circle" style="color:#56AEFF;cursor:pointer;" aria-hidden="true"></i></a></label>
                          <input type="text" class="form-control" min="0" step="1" id="nombres" name="nombres" value="{{$nombres}}" />
                    </div>
                    <div class="col-md-4 col-sm-6 col-xs-12 margin-top">
                          <label for="apellidos">Apellidos <a data-toggle="tooltip" title="Buscar por apellido"><i class="fa fa-lg fa-info-circle" style="color:#56AEFF;cursor:pointer;" aria-hidden="true"></i></a></label>
                          <input type="text" class="form-control" min="0" step="1" id="apellidos" name="apellidos" value="{{$apellidos}}" />
                    </div>
                    <div class="col-md-4 col-sm-6 col-xs-12 margin-top">
                          <label for="identificacion">Identficacion <a data-toggle="tooltip" title="Buscar por identificación"><i class="fa fa-lg fa-info-circle" style="color:#56AEFF;cursor:pointer;" aria-hidden="true"></i></a></label>
                          <input type="text" class="form-control" min="0" step="1" id="identificacion" name="identificacion" value="{{$identificacion}}" />
                    </div>

                    <div class="col-md-4 col-sm-6 col-xs-12 margin-top">
                        <label for="numero_casa">Número casa <a data-toggle="tooltip" title="Buscar por el código exacto de casa"><i class="fa fa-lg fa-info-circle" style="color:#56AEFF;cursor:pointer;" aria-hidden="true"></i></a></label>
                        <input type="text" class="form-control" id="numero_casa" name="numero_casa" value="{{$busqueda_numero_casa}}" />
                    </div>
                    <div class="col-md-4 col-sm-6 col-xs-12 margin-top">
                        <label for="id_proyecto">Proyecto</label>
                        <select class="form-control" id="id_proyecto" name="id_proyecto">
                            <option value='0'>Todos</option>
                        @foreach($proyectos as $proyect)
                        @if($proyect->id == $busqueda_proyecto)
                            <option value='{{$proyect->id}}' selected>{{$proyect->nombre}}</option>
                        @else
                            <option value='{{$proyect->id}}'>{{$proyect->nombre}}</option>
                        @endif
                        @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 col-sm-6 col-xs-12 margin-top">
                        <label for="id_casa_estado">Estado de trámite de la propiedad</label>
                        <select class="form-control" id="id_casa_estado" name="id_casa_estado">
                            <option value='0'>Todos</option>
                            <option value='sin_asignar' @if($busqueda_id_casa_estado==='sin_asignar') selected @endif>Sin asignar</option>
                        @foreach($estados as $estado)
                        @if($estado->id == $busqueda_id_casa_estado)
                            <option value='{{$estado->id}}' selected>{{$estado->nombre}}</option>
                        @else
                            <option value='{{$estado->id}}'>{{$estado->nombre}}</option>
                        @endif
                        @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-4 col-sm-6 col-xs-12 margin-top">
                          <label for="con_notas">Con notas <a data-toggle="tooltip" title="Buscar los clientes que tengan notas en el sistema"><i class="fa fa-lg fa-info-circle" style="color:#56AEFF;cursor:pointer;" aria-hidden="true"></i></a></label>
                          <input type="checkbox" id="con_notas" name="con_notas" @if($con_notas) checked="checked" @endif/>
                    </div>
                    
                    <div class="col-xs-12 margin-top text-center">
                        <button type='submit' class='btn btn-primary'>Buscar <i class='fa fa-lg fa-search'></i></button>
                    </div>
                </form>
            </div>
            <div class='col-xs-12 no-padding'>
                <div class="table-responsive">
            <table class="table table-hover  table-center">
                <thead>


                    <th>Casas</th>
                    

                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Correo</th>
                    <!-- <th>Identificación</th>   --> 
                     <th>Telefono</th>               
                    <th>Status</th>
                    @if(!(Auth::user()->isEjVentas() || Auth::user()->isEjBancos()))
                    <th title="Ejecutivo de Ventas">E.V</th>
                    <!-- <th>Status de Tramite</th> -->
                    @endif

                    <th><i class="fa fa-cogs fa-lg"></i></th>
                </thead>
                <tbody>
                @if(count($clientes) == 0)
                    <tr>
                        <td colspan="7">No se han encontrado resultados...</td>
                    </tr>
                @endif
                @foreach($clientes as $cliente)
                    <tr @if(count($cliente->clienteCasa)==0) class="danger" @endif>
                        <td>
                        @if(count($cliente->clienteCasa)!=0)
                            @foreach($cliente->clienteCasa as $i=>$house)
                                @if($i!=0)
                                    ,
                                @endif
                                    {{$house->codigo}}
                            @endforeach
                        @else
                            Ninguna
                        @endif
                        </td>
                        <td>{{$cliente->nombre}}</td>
                        <td>{{$cliente->apellido}}</td>
                        <td>{{$cliente->user->email}}</td>
                        <td>
                        @foreach($cliente->telefonoCliente as $telefono_cliente)
                            {{$telefono_cliente->telefono}}
                            <br>
                        @endforeach
                        </td>
                        <td>
                        @if(count($cliente->clienteCasa)!=0)
                            @foreach($cliente->clienteCasa as $i=>$house)
                            @if($i!=0)
                                ,
                            @endif
                                {{$house->getEstadoNombre()}}
                            @endforeach
                        @else
                            Ninguno
                        @endif
                        </td>
                        @if(!(Auth::user()->isEjVentas() || Auth::user()->isEjBancos()))
                        <td>
                            @if(count($cliente->clienteCasa)!=0)
                                @foreach($cliente->clienteCasa as $i=>$house)
                                @if($i!=0)
                                    ,
                                @endif

                                @if(count($house->ejecutivoVentas) != 0)
                                    {{$house->ejecutivoVentas->user->nombre}}
                                @else
                                    Ninguno
                                @endif
                                @endforeach
                            @else
                                Ninguno
                            @endif
                        </td>
                        @endif
                        <td>
                            <a title="Mostrar todos los datos del cliente" href="javascript:showCliente('{{url('clientes')}}/{{$cliente->id}}','{{url('pdf')}}/{{$cliente->id}}')" >
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </a>
                            @can('update', $cliente)

                            <a title="Editar Cliente" href="javascript:editarCliente('{{url('clientes')}}/{{$cliente->id}}')" >
                                <i class="fa fa-pencil" aria-hidden="true"></i>
                            </a>

                            @endcan
                            @can('delete', $cliente)

                            <a title="Eliminar Cliente" href="javascript:eliminarCliente('{{url('clientes/'.$cliente->id)}}}')" class="color-remove">
                                <i class="fa fa-lg fa-times" aria-hidden="true"></i>
                            </a>
                            @endcan


                            @can('asignarEstatus', 'App\Models\Casa')
                            <a title="Asignar Status de Tramite" href="javascript:anadirStatus('{{url('casas/asignarEstatus')}}', '{{url('clientescasa/getCasasCliente')}}/{{$cliente->id}}','{{url('casas/getEstatus')}}',{{$cliente->id}})" >
                                <i class="fa fa-flag-o" aria-hidden="true"></i>
                            </a>
                            @endcan

                            @can('store', 'App\Models\ClienteCasa')

                            <a title="Asignar Propiedades" href="javascript:asignarCasa('{{url('clientescasa')}}',{{$cliente->id}})" >
                                <i class="fa fa-plus" aria-hidden="true"></i>
                            </a>

                            <a title="Desasignar Propiedades" href="javascript:quitarCasa('{{url('clientescasa/'.$cliente->id)}}', '{{url('clientescasa/getCasasCliente')}}/{{$cliente->id}}',{{$cliente->id}})" >
                                <i class="pe-7s-trash" aria-hidden="true"></i>
                            </a>
                            @endcan

                            @can('store', 'App\Models\DocumentoCliente')
                            <a title="Subir Documentos" href="javascript:subirDocumento('{{url('documentos')}}',{{$cliente->id}})" >
                                <i class="fa fa-upload" aria-hidden="true"></i>
                            </a>
                            @endcan

                            @can('get', 'App\Models\DocumentoCliente')
                            <a title="Ver Documentos" href="{{url('documentos/cliente')}}/{{$cliente->id}}" >
                                <i class="fa fa-file-text-o" aria-hidden="true"></i>
                            </a>
                            @endcan

                            @can('store', 'App\Models\Pago')
                            <a title="Añadir Pagos" href="javascript:añadirPago('{{url('pagos')}}', '{{url('clientescasa/getCasasCliente')}}/{{$cliente->id}}',{{$cliente->id}})" class="text-success">
                                <i class="pe-7s-cash fa-lg" aria-hidden="true"></i>
                            </a>

                            <a title="Ver Pagos" href="{{url('pagos/cliente')}}/{{$cliente->id}}" class="text-success">
                                <i class="fa fa-list-ul" aria-hidden="true"></i>
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
    </div>
    <div align="center">{!! $clientes->appends(['nombres' => $nombres, 'apellidos' => $apellidos, 'identificacion' => $identificacion,'numero_casa' => $busqueda_numero_casa,'id_proyecto' => $busqueda_proyecto, 'id_casa_estado' => $busqueda_id_casa_estado,'con_notas' => $con_notas])->links() !!}</div>
    <div class="col-xs-12 margin-top text-center">
        <a href="{{url('pdf/reporte/listaDeClientes?nombres='.$nombres.'&apellidos='.$apellidos.'&identificacion='.$identificacion.'&numero_casa='.$busqueda_numero_casa.'&id_proyecto='.$busqueda_proyecto.'&id_casa_estado='.$busqueda_id_casa_estado.'&con_notas='.$con_notas)}}" class="btn btn-primary" title="Descargar PDF">PDF</a>

        <a href="{{url('excel/reporte/clientes?nombres='.$nombres.'&apellidos='.$apellidos.'&identificacion='.$identificacion.'&numero_casa='.$busqueda_numero_casa.'&id_proyecto='.$busqueda_proyecto.'&id_casa_estado='.$busqueda_id_casa_estado.'&con_notas='.$con_notas)}}" class="btn btn-primary" title="Descargar Excel">Excel</a>
    </div>
</div>
</div>

<link rel="stylesheet" type="text/css" href="{{asset('dpd/select2-4.0.3/dist/css/select2.min.css')}}"/>


@stop
@push('JS')
<script src="{{asset('dpd/select2-4.0.3/dist/js/select2.min.js')}}"></script>
<script type='text/javascript'>
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endpush

@section("modals")

  @include('pages.cliente.create')

  @can('store', 'App\Models\Cliente')
    @include('pages.cliente.update')
  @endcan
  
  @can('delete', 'App\Models\Cliente')
    @include('pages.cliente.delete')
  @endcan

  @can('store', 'App\Models\ClienteCasa')
    @include('pages.cliente.estado')
  @endcan

  @can('store', 'App\Models\DocumentoCliente')
    @include('pages.cliente.subirDoc')
  @endcan

  @include('pages.cliente.mostrarCliente')

  @can('store', 'App\Models\ClienteCasa')
    @include('pages.cliente.asignarCasa')
    @include('pages.cliente.deleteClienteCasa')
  @endcan

  @can('get', 'App\Models\DocumentoCliente')
    @include('pages.documento.listDocsCliente')
  @endcan 

  @can('store', 'App\Models\Pago')
    @include('pages.pago.create')
  @endcan
@stop
@endcan