@extends('layouts.default')

@section('content')

<div class="row">
<div class="col-md-12 ">
    <div class="card">
        <div class="header">
            @if(isset($client) && $client)
            <h4 class="title">Pagos de cliente: {{$client->nombre}}  {{$client->apellido}} </h4>
            @endif
            <p class="category"></p>
        </div>
        <div class="content table-full-width">
          <div class="table-responsive">
            <table class="table table-hover table-striped table-center">
                <thead>
                    <th>Pago</th>
                    <th>Casa</th>
                    <th>Forma de Pago</th>
                    <th>Tipo de Transacción</th>
                    <th>Monto</th>
                    <th>Descripción</th>
                    <th>Fecha</th>
                    <th><i class="fa fa-cogs fa-lg"></i></th>
                </thead>
                <tbody>
                @if(count($pagos) == 0)
                    <tr>
                        <td colspan="7">No se han encontrado resultados...</td>
                    </tr>
                @endif
                @foreach($pagos as $pago)
               
                    <tr>
                      <td>{{$pago->id}}</td>
                      <td>{{App\Models\Pago::getCodigoCasa($pago->id_casa)->codigo}}</td>
                      <td>{{App\Models\Pago::$formas_pago[$pago->id_forma_pago]}}</td>
                      <td>{{App\Models\Pago::$tipos_pago[$pago->id_tipo_transaccion]}}</td>
                      <td>{{$pago->monto}}</td>
                      <td>{{$pago->descripcion}}</td>
                      <td>{{date('d-m-Y',strtotime($pago->realizado_at))}}</td>
                      
                         <td>
                           @can('update', $pago)
                              <a title="Editar Pago" href="javascript:editarPago('{{url('pagos')}}/{{$pago->id}}')" >
                                  <i class="fa fa-pencil" aria-hidden="true"></i>
                              </a>

                            @endcan
                            
                            @can('delete', $pago)
                              <a title="Eliminar Pago" href="javascript:eliminarPago('{{url('pagos')}}/{{$pago->id}}')" class="color-remove" >
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
    @if(isset($client) && $client)
    <a href="{{url('clientes?nombres=&apellidos=&identificacion='.$client->identificacion)}}">
    @endif
    <center><button type="button" class="btn btn-primary" ><i class="fa fa-hand-o-left" aria-hidden="true"></i> Regresar</button></center> 
    </a>   
    <div align="center">{!! $pagos->render() !!}</div>
</div>
</div>



@stop
@section("modals")
  @include('pages.pago.create')
  @include('pages.pago.update')
  @include('pages.pago.delete')
@stop
