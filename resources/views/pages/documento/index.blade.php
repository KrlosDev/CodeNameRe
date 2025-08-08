@extends('layouts.default')

@section('content')



<!-- Button trigger modal -->
<!-- <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal" data-backdrop="true">
  Launch demo modal
</button>
 -->



<div class="row">
<div class="col-md-12 ">
    <div class="card">
        <div class="header">
            <h4 class="title">Documentos: {{$cliente}}</h4>
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
                    <th><i class="fa fa-cogs fa-lg"></i></th>
                </thead>
                <tbody>
                @if(count($docucliente) == 0)
                    <tr>
                        <td colspan="7">No se han encontrado resultados...</td>
                    </tr>
                @endif
                @foreach($docucliente as $docu)

                    <tr>
                      <td>{{$docu->id}}</td>
                      <td>{{\App\Models\DocumentoCliente::$tipos[$docu->tipo_documento]}}</td>
                      <td>@if($docu->pais==null) Ninguno @else {{$docu->pais->nombre}} @endif</td> 
                    <!--  <td>{{date('d-m-Y',strtotime($docu->fecha_expiracion))}}</td> -->
                      <td>@if(strcmp($hoy,$docu->fecha_expiracion) >= 0)  <font color="red" title="Documento vencido">{{date('d-m-Y',strtotime($docu->fecha_expiracion))}}</font> @else {{date('d-m-Y',strtotime($docu->fecha_expiracion))}} @endif</td> 
                        <td>
                            @can('get',$docu)
                               <a title="Descargar" href="{{url('documentos')}}/{{$docu->src}}" target="_blank">
                                   <i class="fa fa-download" aria-hidden="true"></i>
                               </a>

                            @endcan
                            @can('update', $docu)
                                <a title="Actualizar Documento" href="javascript:editarDocumento('{{url('documentos/data/cliente/'.$docu->id)}}')" class="color-good">
                                    <i class="fa fa-lg fa-pencil" aria-hidden="true"></i>
                                </a>
                                <a title="Eliminar Documento" href="javascript:eliminarDocumento('{{url('documentos/cliente/'.$docu->id)}}')" class="color-remove">
                                    <i class="fa fa-lg fa-times" aria-hidden="true"></i>
                                </a>
                            @endcan
                        </td>
                      
                    </tr>

                @endforeach
                </tbody>
            </table>
            <div align="center">{!! $docucliente->render() !!}</div>

        
              <h4 class="title" style="padding-left: 15px">Documentos: Codeudor</h4>

            <table class="table table-hover table-striped table-center">
                <thead>
                    <th>Documento</th>
                    <th>Tipo_documento</th>
                    <th>País</th>   
                    <th>Fecha de Expiración </th> 
                    <th><i class="fa fa-cogs fa-lg"></i></th>
                </thead>
                <tbody>
                @if(count($docucodeudor) == 0)
                    <tr>
                        <td colspan="7">No se han encontrado resultados...</td>
                    </tr>
                @endif
                @foreach($docucodeudor as $codocu)

                    <tr>
                      <td>{{$codocu->id}}</td>
                      <td>{{\App\Models\DocumentoCodeudor::$tipos[$docu->tipo_documento]}}</td>
                      <td>@if($codocu->pais==null) Ninguno @else {{$codocu->pais->nombre}} @endif</td>
                      <!--<td>{{date('d-m-Y',strtotime($codocu->fecha_expiracion))}}</td> -->
                      <td>@if(strcmp($hoy,$docu->fecha_expiracion) > 0)  <font color="red" title="Documento vencido">{{date('d-m-Y',strtotime($docu->fecha_expiracion))}}</font> @else {{date('d-m-Y',strtotime($docu->fecha_expiracion))}} @endif</td>            
                         <td>

                            @can('get',$docu)
                                <a title="Descargar" href="{{url('documentos')}}/{{$codocu->src}}" target="_blank">
                                    <i class="fa fa-download" aria-hidden="true"></i>
                                </a>

                            @endcan
                            @can('update', $docu)
                                <a title="Actualizar Documento" href="javascript:editarDocumento('{{url('documentos/data/codeudor/'.$codocu->id)}}')" class="color-good">
                                    <i class="fa fa-lg fa-pencil" aria-hidden="true"></i>
                                </a>
                                <a title="Eliminar Documento" href="javascript:eliminarDocumento('{{url('documentos/codeudor/'.$codocu->id)}}')" class="color-remove">
                                    <i class="fa fa-lg fa-times" aria-hidden="true"></i>
                                </a>
                            @endcan
                         </td>
                      
                    </tr>

                @endforeach
                </tbody>
            </table>
            @if(!count($docucodeudor) == 0)
            <div align="center">{!! $docucodeudor->render() !!}</div>
            @endif
          </div>
        </div>
    </div> 
</div>
    <a href="{{url('clientes?nombres=&apellidos=&identificacion='.$client->identificacion)}}">
    <center><button type="button" class="btn btn-primary" ><i class="fa fa-hand-o-left" aria-hidden="true"></i> Regresar</button></center> 
    </a>
</div>
 
<link rel="stylesheet" type="text/css" href="{{asset('dpd/jquery-ui-1.12.1.custom/jquery-ui.min.css')}}"/>

@push('JS')
<script src="{{asset('dpd/jquery-ui-1.12.1.custom/jquery-ui.min.js')}}"></script>
@endpush

@stop
@section("modals")
   @include('pages.documento.delete')
   @include('pages.documento.update')
@stop
