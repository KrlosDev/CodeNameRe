@extends('layouts.default')
@section('content')



<!-- Button trigger modal -->
<!-- <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal" data-backdrop="true">
  Launch demo modal
</button>
 -->

<?php
if (! isset($user)) {
    $user = \Auth::user();
}
?>
@if(in_array($user->id_rol,[\App\User::ADMINISTRADOR,\App\User::CONSTRUCTORA,\App\User::BROKERS]))
<div class="row">
<div class="col-md-12 ">
    <div class="card">
        <div class="header">
            <h4 class="title">Proyectos</h4>
            <p class="category"></p>
        </div>
        <div class="content table-full-width">
             <div class="table-responsive">
                <table class="table table-hover table-striped table-center">
                    <thead>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>N° Propiedades</th>
                        <th>Estado</th>
                        <th><i class="fa fa-cogs fa-lg"></i></th>
                    </thead>
                    <tbody>
                    @if(count($proyectos) == 0)
                        <tr>
                            <td colspan="6">No se han encontrado resultados...</td>
                        </tr>
                    @endif
                    @foreach($proyectos as $proyecto)

                        <tr>
                          <td>{{$proyecto->codigo}}</td>              
                          <td>{{$proyecto->nombre}}</td>
                          <td>{{$proyecto->descripcion}}</td>
                          <td>{{\App\Models\Casa::where('casas.id_proyecto',$proyecto->id)->count()}}</td>
                          <td>{{$proyecto->getEstado($proyecto->estado)}}</td>
                            <td>
                                @if($user->isBroker())
                                <a title="Detallar Reporte" href='{{url('reporte/ganancias/'.$proyecto->id.'/'.$user->broker->id)}}' >
                                    <i class="fa fa-line-chart fa-lg" aria-hidden="true"></i>
                                </a>
                                @else
                                <a title="Detallar Reporte" href='{{url('reporte/'.$proyecto->id)}}' >
                                    <i class="fa fa-line-chart fa-lg" aria-hidden="true"></i>
                                </a>
                                @endif
                            </td>
                        </tr>

                    @endforeach
                    </tbody>
                </table>
              </div>
        </div>
    </div>
    @if(!in_array($user->id_rol,[\App\User::BROKERS]))
      <div align="center">{!! $proyectos->render() !!}</div>
    @endif
</div>
</div>
@endif

@if($user->id_rol==App\User::CLIENTE)
      <div class="row">
      <div class="col-md-12 ">
          <div class="card">
              <div class="header">
                  <h4 class="title">Mi banco</h4>
                  @if($banco)
                  <p class="category">{{$banco->nombre}}</p>
                  @else
                  <p class="category">No encontrado</p>
                  @endif
              </div>
              <div class="header">
                  <h4 class="title">Propiedades</h4>
                  <p class="category"></p>
              </div>
              <div class="content table-full-width">
                   <div class="table-responsive">
                        <table class="table table-hover table-striped table-center">
                            <thead>
                                <th>Código</th>
                                <th>Modelo</th>
                                <th>Recamaras</th>
                                <th>Baños</th>
                                <th>MTS2 Total</th>
                                <th>Valor</th>
                                <th>Monto de Separación</th>
                                <th>Abono Inicial</th>
                                <th>MTS2 Adicional</th>
                                <th>Ej Ventas</th>
                                <th>Ej Banco</th>
                                <th>Estado</th>
                                <th><i class="fa fa-cogs fa-lg"></i></th>
                            </thead>
                            <tbody>
                            @if(count($casas) == 0)
                                <tr>
                                    <td colspan="13">No se han encontrado resultados...</td>
                                </tr>
                            @endif

                           @foreach($casas as $casa)

                                <tr>
                                    <td>{{$casa->codigo}}</td>              
                                    <td>{{$casa->modelo}}</td>
                                    <td>{{$casa->recamaras}}</td>
                                    <td>{{$casa->banos}}</td>
                                    <td>{{$casa->mts2_total}}</td>
                                    <td>{{$casa->valor}}</td>
                                    <td>{{$casa->monto_separacion}}</td>
                                    <td>{{$casa->monto_abono_inicial}}</td>
                                    <td>{{$casa->monto_mts2_adicional*$casa->mts2_adicionales}}</td>
                                    @if($casa->ejecutivoVentas)
                                    <td>{{$casa->ejecutivoVentas->user()->first()->nombre}}</td>
                                    @else
                                    <td><i>N/A</i></td>
                                    @endif
                                    @if($casa->getEjBanco($casa->cliente[0]->id))
                                    <td>{{$casa->getEjBanco($casa->cliente[0]->id)->user->name}}</td>
                                    @else
                                    <td><i>N/A</i></td>
                                    @endif
                                    <td>{{$estados[$casa->id_casa_estado]}}</td>

                                    <td>                            
                                          <a title="Detallar Reporte" href='{{url('reporte/'.$casa->id)}}' >
                                              <i class="fa fa-line-chart fa-lg" aria-hidden="true"></i>
                                          </a>
                                          <a title="Ver detalles de proyecto" href='{{url('proyectos/'.$casa->proyecto->id.'/imagenes')}}' class="color-good" >
                                              <i class="fa fa-lg fa-list-ul" aria-hidden="true"></i>
                                          </a>      

                                    </td>
                                </tr>

                            @endforeach
                            </tbody>
                        </table>
                    </div>
              </div>
            <div align="center">{!! $casas->render() !!}</div>
            
            <div class="header">
                <h4 class="title">Documentos</h4>
                <p class="category"></p>
            </div>
            <div class="content table-full-width">
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-center">
                        <thead>
                            <th>Documento</th>
                            <th>Tipo documento</th>
                            <th>País</th>
                            <th>Fecha de Expiración </th>
                        </thead>
                        <tbody>
                        @if(count($docucliente) == 0)
                            <tr>
                                <td colspan="4">No se han encontrado documentos...</td>
                            </tr>
                        @endif
                        @foreach($docucliente as $docu)
                            <tr>
                                <td>{{$docu->id}}</td>
                                <td>{{\App\Models\DocumentoCliente::$tipos[$docu->tipo_documento]}}</td>
                                <td>@if($docu->pais==null) Ninguno @else {{$docu->pais->nombre}} @endif</td> 
                                <td>
                                @if(strcmp($hoy,$docu->fecha_expiracion) >= 0)
                                    <font color="red" title="Documento vencido">{{date('d-m-Y',strtotime($docu->fecha_expiracion))}}</font>
                                @else
                                    {{date('d-m-Y',strtotime($docu->fecha_expiracion))}}
                                @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div align="center">{!! $docucliente->render() !!}</div>
                </div>
            </div>
        </div>
    </div>
    </div>
            
@endif
@if($user->id_rol==App\User::EJ_VENTAS)
      <div class="row">
      <div class="col-md-12 ">
          <div class="card">
              <div class="header">
                  <h4 class="title">Clientes</h4>
                  <p class="category"></p>
              </div>
              <div class="content table-full-width">
                   <div class="table-responsive">
                        <table class="table table-hover table-striped table-center">
                            <thead>
                                <th>Casa</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Telefono</th>
                                <th>Status</th>
                                <th><i class="fa fa-cogs fa-lg"></i></th>
                            </thead>
                            <tbody>
                                @foreach($clientes as $cliente)
                                <tr>
                                    <td>{{$cliente->codigo}}</td>
                                    <td>{{$cliente->nombre}}</td>
                                    <td>{{$cliente->apellido}}</td>
                                    <td>{{$cliente->telefono}}</td>
                                    @if($cliente->id_casa_estado)
                                        <td>{{$cliente->getEstadoNombre()}}</td>
                                    @else
                                        <td>Sin asignar</td>
                                    @endif

                                    <td>
                                       <a title="Detallar Reporte" href='{{url('reporte/cliente/'.$cliente->id)}}' >
                                           <i class="fa fa-line-chart fa-lg" aria-hidden="true"></i>
                                       </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
              </div>
          </div>
          <div align="center">{!! $clientes->render() !!}</div>
      </div>
      </div>
@endif
@stop
@section("modals")

  
@stop
