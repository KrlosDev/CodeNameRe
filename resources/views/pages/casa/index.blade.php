@extends('layouts.default')

@section('content')

@push('JS')
<script language="JavaScript">
function toggle(source) {
  checkboxes = document.getElementsByName('cliente[]');
  for(var i=0, n=checkboxes.length;i<n;i++) {
    checkboxes[i].checked = source.checked;
  }
}
</script>
@endpush



<div class="row">
<div class="col-md-12 ">
    <div class="card">
        <div class="header">
          
          @if($proyecto == null)
            <h4 class="title">Propiedades</h4>            
           @else 
            
            <h4 class="title">Propiedades de {{$proyecto->nombre}}</h4>
           @endif
            <p class="category"></p>
        </div>
        <div class="content table-full-width">
            <div class='col-xs-12 no-padding'>
                <form class="form-horizontal">
                    <div class="col-md-4 col-sm-6 col-xs-12 margin-top">
                          <label for="cantidad">Cantidad</label>
                          <select class="form-control" id="cantidad" name="cantidad">
                          @foreach(\App\Models\Funciones::$cantidad_option as $key => $value)
                          @if($key == $busqueda_cantidad)
                          <option value='{{$key}}' selected>{{$value}}</option>
                          @else
                          <option value='{{$key}}'>{{$value}}</option>
                          @endif
                          @endforeach
                          </select>
                    </div>
                    
                    <div class="col-md-4 col-sm-6 col-xs-12 margin-top">
                        <label for="recamaras">Recámaras <a data-toggle="tooltip" title="Buscar con una cantidad mayor o igual a esta"><i class="fa fa-lg fa-info-circle" style="color:#56AEFF;cursor:pointer;" aria-hidden="true"></i></a></label>
                        <input type="number" class="form-control" min="0" step="1" id="recamaras" name="recamaras" value="{{$busqueda_recamaras}}" />
                    </div>
                    
                    <div class="col-md-4 col-sm-6 col-xs-12 margin-top">
                        <label for="banos">Baños <a data-toggle="tooltip" title="Buscar con una cantidad mayor o igual a esta"><i class="fa fa-lg fa-info-circle" style="color:#56AEFF;cursor:pointer;" aria-hidden="true"></i></a></label>
                        <input type="number" class="form-control" min="0" step="1" id="banos" name="banos" value="{{$busqueda_banos}}" />
                    </div>
                    
                    <div class="col-md-4 col-sm-6 col-xs-12 margin-top">
                        <label for="numero_casa">Número casa <a data-toggle="tooltip" title="Buscar por el código exacto de casa"><i class="fa fa-lg fa-info-circle" style="color:#56AEFF;cursor:pointer;" aria-hidden="true"></i></a></label>
                        <input type="text" class="form-control" id="numero_casa" name="numero_casa" value="{{$busqueda_numero_casa}}" />
                    </div>
                    
                    @if(\Auth::user()->isConstructora())
                    <div class="col-md-4 col-sm-6 col-xs-12 margin-top">
                        <label for="id_broker">Broker</label>
                        <select class="form-control" id="id_broker" name="id_broker" onchange="loadEstados($('#id_broker').value());">
                            <option value='0'>Todos</option>
                        @foreach($brokers as $brok)
                        @if($brok->id == $busqueda_broker)
                            <option value='{{$brok->id}}' selected>{{$brok->user()->first()->nombre}}</option>
                        @else
                            <option value='{{$brok->id}}'>{{$brok->user()->first()->nombre}}</option>
                        @endif
                        @endforeach
                        </select>
                    </div>
                    @endif
                    
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
                                @if($proyect->id == $busqueda_id_casa_estado)
                                    <option value='{{$estado->id}}' selected>{{$estado->nombre}}</option>
                                @else
                                    <option value='{{$estado->id}}'>{{$estado->nombre}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xs-12 margin-top text-center">
                        <button type='submit' class='btn btn-primary'>Buscar <i class='fa fa-lg fa-search'></i></button>
                    </div>
                </form>
            </div>
            <div class='col-xs-12 no-padding'>
                <div class="table-responsive">
                    <form method="POST" id='dele' action="{{url('casas/eliminar')}}">
                        {!! csrf_field() !!}
                        <input type="hidden" name="_method" value="POST" >

                        @if(Auth::user()->isConstructora())
                        <div  style="padding-left: 25px">
                            <button type="submit" class="btn btn-danger">Eliminar</button>
                        </div>
                        @endif
                        <table class="table table-hover table-center">
                            <thead>
                                @if(Auth::user()->isConstructora())
                                <th> <input type="checkbox" onClick="toggle(this)" /><br/></th>
                                @endif
                                <th>Código</th>
                                <!--th>Modelo</th-->
                                <th>Cliente</th>
                                <th>Telefono</th>
                                <th>Valor</th>
                                <th>Sep.<a data-toggle="tooltip" title="Monto de separación"><i class="fa fa-lg fa-info-circle" style="color:#56AEFF;cursor:pointer;" aria-hidden="true"></i></a></th>
                                <th>Abono Inicial</th>
                                <th>Tot. mts2 ad.<a data-toggle="tooltip" title="Total del costo de los metros cuadrados adicionales para la propiedad"><i class="fa fa-lg fa-info-circle" style="color:#56AEFF;cursor:pointer;" aria-hidden="true"></i></a></th>
                                <th>Pagado</th>
                                <th>Por pagar</th>
                                <th>Status</th>
                                @if(!Auth::user()->isBroker())
                                  <th>Broker</th>
                                @endif
                                <th><i class="fa fa-cogs fa-lg"></i></th>
                            </thead>
                            <tbody>
                            @if(count($casas) == 0)
                                <tr>
                                    <td colspan="12">No se han encontrado resultados...</td>
                                </tr>
                            @endif
                            @foreach($casas as $casa)
                                <tr @if(!($casa->id_casa_estado == null))
                                            class="success"
                                      @else
                                          class="danger"
                                      @endif
                                >
                                @if(Auth::user()->isConstructora())
                                    <td><input type="checkbox" name="cliente[]" id="cliente[]" value={{$casa->id}}></td>
                                @endif
                                    <td>{{$casa->codigo}}</td>
                                @if(count($casa->cliente)>0)
                                    <td>{{$casa->cliente[0]->nombre}} {{$casa->cliente[0]->apellido}}</td>
                                    <td>
                                    @if(count($casa->cliente[0]->telefonoCliente)>0)
                                        @foreach($casa->cliente[0]->telefonoCliente as $i=>$telefono)
                                            @if($i!=0)
                                            ,
                                            @endif
                                            {{$telefono->telefono}}
                                        @endforeach
                                    @else
                                        Ninguno
                                    @endif
                                    </td>
                                    @else
                                    <td>Ninguno</td>
                                    <td>Ninguno</td>
                                    @endif
                                    <td>{{$casa->valor}}</td>
                                    <td>{{$casa->monto_separacion}}</td>
                                    <td>{{$casa->monto_abono_inicial}}</td>
                                    <td>{{$casa->monto_mts2_adicional*$casa->mts2_adicionales}}</td>
                                    <td>{{number_format($casa->getMontoPagadoTotalTabla([\App\Models\Pago::TIPO_PAGO_MONTO_SEPARACION],2,",","."))}}</td>
                                    <td>{{number_format($casa->getMontoPorPagarTotalTabla([\App\Models\Pago::TIPO_PAGO_MONTO_SEPARACION],2,",","."))}}</td>
                                    <td>{{$casa->getEstadoNombre()}}</td>
                                    @if(!Auth::user()->isBroker())
                                    <td>
                                        @if(!($casa->id_broker == null))
                                            {{$casa->broker()->first()->user()->first()->nombre}}
                                        @endif
                                    </td>
                                    @endif
                                    <td>
                                        @can('store',\App\Models\Broker::class)
                                        <a title="Desasignar Propiedad" href="javascript:deasignarCasa('{{url('casas')}}/{{$casa->id."/desasignar"}}')" >
                                            <i class="fa fa-lg fa-wrench" aria-hidden="true"></i>
                                        </a>
                                        @endcan
                                        @can('update',$casa)
                                            <a title="Editar Propiedad" href="javascript:editarCasa('{{url('casas')}}/{{$casa->id}}')" >
                                                <i class="fa fa-lg fa-pencil" aria-hidden="true"></i>
                                            </a>
                                            <a title="Eliminar Propiedad" href="javascript:eliminarCasa('{{url('casas/'.$casa->id)}}}')" class="color-remove">
                                                <i class="fa fa-lg fa-times" aria-hidden="true"></i>
                                            </a>
                                            <a title="Editar checklist requerimientos" href="{{url('casas_requerimientos_tramites/'.$casa->id)}}" >
                                                <i class="fa fa-lg fa-asterisk" aria-hidden="true"></i>
                                            </a>
                                        @endcan
                                        @can('updateBroker',$casa)
                                            <a title="Editar Propiedad" href="javascript:editarCasaBroker('{{url('casas')}}/{{$casa->id}}/broker')" >
                                                <i class="fa fa-lg fa-pencil" aria-hidden="true"></i>
                                            </a>
                                        @endcan

                                        @can('asignarEjecutivos',$casa)
                                            @if($casa->id_cliente == null)
                                                <?php $i = 0; ?>
                                            @else
                                                <?php $i = $casa->id_cliente; ?>
                                            @endif
                                            <a title="Asignar Ejecutivos" href="javascript:asignarEjecutivos('{{url('casas/asignarEjecutivos')}}',{{$casa->id}},{{$i}})" class="text-success">
                                                <i class="fa fa-lg fa-plus" aria-hidden="true"></i>
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="text-center">
        {{$casas->appends(['cantidad' => $busqueda_cantidad, 'id_proyecto' => $busqueda_proyecto, 'broker' => $busqueda_broker, 'recamaras' => $busqueda_recamaras, 'banos' => $busqueda_banos,
        'numero_casa' => $busqueda_numero_casa, 'id_casa_estado' => $busqueda_id_casa_estado])->render()}}
    </div>
    <div class="col-xs-12 margin-top text-center">
      <a href="{{url('pdf/reporte/listaDePropiedades?broker='.$busqueda_broker.'&recamaras='.$busqueda_recamaras.'&banos='.$busqueda_banos.'&numero_casa='.$busqueda_numero_casa.'&id_proyecto='.$busqueda_proyecto.'&id_casa_estado='.$busqueda_id_casa_estado)}}" class="btn btn-primary" title="Descargar PDF">PDF</a>

      <a href="{{url('excel/reporte/propiedades?broker='.$busqueda_broker.'&recamaras='.$busqueda_recamaras.'&banos='.$busqueda_banos.'&numero_casa='.$busqueda_numero_casa.'&id_proyecto='.$busqueda_proyecto.'&id_casa_estado='.$busqueda_id_casa_estado)}}" class="btn btn-primary" title="Descargar Excel">Excel</a>
    </div>
</div>
</div>
    
<link rel="stylesheet" type="text/css" href="{{asset('dpd/select2-4.0.3/dist/css/select2.min.css')}}"/>


@stop
@section("modals")
  @include('pages.casa.update')
  @include('pages.casa.delete')
    @include('pages.casa.deletecasas')
  @include('pages.casa.asignarEjecutivos')
  @include('pages.casa.desasignar')
  @include('pages.casa.update-broker')



@stop

@push('JS')
<script src="{{asset('dpd/select2-4.0.3/dist/js/select2.min.js')}}"></script>
<script type='text/javascript'>
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();
        
        $('#id_proyecto').select2();
        $('#id_casa_estado').select2();

        function loadEstados(id_broker) {
            $.get("{{url('casas')}}/"+id_broker+"/casaEstados",function(data,status){
                data=JSON.parse(data);

                var html = '';
                for(var i = 0; i < data.length; i++) {
                    html+='<option value="'+data.id+'">'+data.nombre+'</option>';
                }

                $('#id_casa_estado').html(html);
            });
        }
    });
</script>
@endpush
