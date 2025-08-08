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
            <h4 class="title">Documentos Vencidos</h4>
            <p class="category"></p>
        </div>
        <div class="content table-full-width">
          <div class="table-responsive">
            <table class="table table-hover table-striped table-center">
                <thead>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Telefono</th>
                    <th>Tipo documento</th>
                    <th>País</th>    
                    <th>Fecha de Expiración </th>   
                    
                </thead>
                <tbody>
                @if(count($docucliente) == 0)
                    <tr>
                        <td colspan="7">No se han encontrado resultados...</td>
                    </tr>
                @endif
                @foreach($docucliente as $docu)
                    <tr>
                      <td><a title="Ir a menu de cliente" href="{{url('clientes?nombres=&apellidos=&identificacion='.$docu->identificacion)}}">{{$docu->nombre}}</a></td>
                      <td>{{$docu->apellido}}</td>
                      <td>{{$docu->telefono}}</td>
                      <td>{{\App\Models\DocumentoCliente::$tipos[$docu->tipo_documento]}}</td>
                      <td>@if($docu->pais==null) Ninguno @else {{$docu->pais->nombre}} @endif</td> 
                    <!--  <td>{{date('d-m-Y',strtotime($docu->fecha_expiracion))}}</td> -->
                      <td>
                          <font color="red" title="Documento vencido">
                              {{date('d-m-Y',strtotime($docu->fecha_expiracion))}}
                          </font>
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



@stop
@section("modals")

@stop
