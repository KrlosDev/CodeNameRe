@extends('layouts.default')
@can('getAll', 'App\Models\Proyecto')
@section('content')



@can('store','App\Models\Proyecto')
 <div class="row">
   <div class="col-md-12">
        <a href="javascript:crearProyecto('{{url('proyectos')}}')" class="btn btn-primary btn-fill"><i class="fa fa-plus" aria-hidden="true"></i> Nuevo proyecto</a>
   </div>
 </div>
@endcan



<div class="row">
<div class="col-md-12 ">
    <div class="card">
        <div class="header">
            <h4 class="title">Proyectos</h4>
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
                <div class="col-xs-12 margin-top text-center">
                    <button type='submit' class='btn btn-primary'>Buscar <i class='fa fa-lg fa-search'></i></button>
                </div>
            </form>
            </div>
            <div class='col-xs-12 no-padding'>
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-center" >
                        <thead>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>
                                @if(Auth::user()->isBroker())
                                    N° P. Asignadas
                                @else
                                    N° Propiedades
                                @endif</th>
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
                                <td>

                                    @if(Auth::user()->isBroker())
                                        <?php $bcasas=$brok->casa;
                                                $cuenta=0;
                                                foreach ($bcasas as $house) {
                                                    if ($house->id_proyecto == $proyecto->id) {
                                                        $cuenta=$cuenta+1;
                                                    }
                                                }

                                        ?>
                                        {{$cuenta}}
                                    @else
                                        {{\App\Models\Casa::where('casas.id_proyecto',$proyecto->id)->count()}}
                                    @endif
                                </td>
                                <td>{{$proyecto->getEstado($proyecto->estado)}}</td>
                                <td>
                                    @can('update',$proyecto)
                                         <a title="Editar Proyecto" href="javascript:editarProyecto('{{url('proyectos')}}/{{$proyecto->id}}')" >
                                             <i class="fa fa-lg fa-pencil" aria-hidden="true"></i>
                                         </a>
                                         <a title="Eliminar Proyecto" href="javascript:eliminarProyecto('{{url('proyectos/'.$proyecto->id)}}}')" class="color-remove">
                                             <i class="fa fa-lg fa-times" aria-hidden="true"></i>
                                         </a>
                                         <a title="Asignar Propiedades" href="javascript:asignarCasas('{{url('proyectos/asignar')}}',{{$proyecto->id}})" class="text-success">
                                             <i class="fa fa-lg fa-plus" aria-hidden="true"></i>
                                         </a>
                                         <a title="Listar Propiedades" href='{{url('casas/proyecto/'.$proyecto->id)}}' class="text-primary">
                                             <i class="fa fa-lg fa-home" aria-hidden="true"></i>
                                         </a>
                                         <a title="Ver requerimientos" href='{{url('requerimientos_tramites/'.$proyecto->id)}}' class="text-primary">
                                            <i class="fa fa-lg fa-file-text-o" aria-hidden="true"></i>
                                         </a>

                                          <a title="Añadir más propiedades al proyecto" href="javascript:crearCasa('{{url('casas')}}',{{$proyecto->id}},'{{url('casas/modelo_p/'.$proyecto->id)}}')" class="text-primary">
                                             <i class="fa fa-plus-circle fa-lg" aria-hidden="true"></i>
                                         </a>
                                    @endcan
                                     <a title="Ver detalles de proyecto" href='{{url('proyectos/'.$proyecto->id.'/imagenes')}}' >
                                         <i class="fa fa-lg fa-list-ul" aria-hidden="true"></i>
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
    <div align="center">{!! $proyectos->render() !!}</div>
    <div class="text-center">
        {{$proyectos->appends(['cantidad' => $busqueda_cantidad, 'id_proyecto' => $busqueda_proyecto])->render()}}
    </div>
</div>
</div>
    
<link rel="stylesheet" type="text/css" href="{{asset('dpd/select2-4.0.3/dist/css/select2.min.css')}}"/>



@stop
@section("modals")
  @can('getAll','App\Models\Proyecto')
    @include('pages.proyecto.update')
    @include('pages.proyecto.create')


    @include('pages.proyecto.delete')
    @include('pages.proyecto.asignar')
    @include('pages.casa.list')
    @include('pages.casa.create')
  @endcan
@stop


@push('JS')
<script src="{{asset('dpd/select2-4.0.3/dist/js/select2.min.js')}}"></script>
<script type='text/javascript'>
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();
        
        $('#id_proyecto').select2();
    });
</script>
@endpush

@endcan
