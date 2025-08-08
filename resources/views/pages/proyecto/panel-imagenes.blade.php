@extends('layouts.default')
@can('verImagenes', $proyecto)
@section('content')



@can('subirImagenes',$proyecto)
 <div class="row">
    <div class="col-md-12">
         <a href="javascript:subirImagen('{{url('proyectos/'.$proyecto->id.'/subirImagen')}}')" class="btn btn-primary btn-fill" @if(count($imagenes)==$limite_imagenes)disabled='true'@endif><i class="fa fa-plus" aria-hidden="true"></i> Cargar imagen</a>
    </div>
 </div>
@endcan

<div class="row">
    <div class="col-xs-12 ">
        <div class="card">
            <div class="header">
                <h4 class="title">{{$proyecto->nombre}}</h4>
                <p class="category">Imágenes</p>
            </div>
        </div>
    </div>
</div>

<div class="col-xs-12 ">
    <div class='col-xs-12 no-padding'>
    @foreach($imagenes as $imagen)
        <div class='col-lg-4 col-sm-6 col-xs-12'>
            <img class='img-responsive' src='{{url(\App\Models\Imagen::PATH)."/".$imagen->path}}' alt='{{$imagen->descripcion}}' title='{{$proyecto->nombre}}'/>
            <h3>
                {{$imagen->nombre}}
                @can('subirImagenes',$proyecto)
                <br>
                <a href="javascript:eliminarImagen('{{url("imagenes"."/".$imagen->id)}}')" class="btn btn-danger btn-fill btn-sm" title='Eliminar'><i class="fa fa-times" aria-hidden="true"></i> Eliminar</a>
                <a href="javascript:actualizarImagen('{{url("imagenes"."/".$imagen->id)}}')" class="btn btn-success btn-fill btn-sm" title='Editar'><i class="fa fa-edit" aria-hidden="true"></i> Actualizar</a>
                @endcan
            </h3>
            <p>{{$imagen->descripcion}}</p>
        </div>
    @endforeach
    </div>
    @if(Auth::user()->isCliente())
    <a href="{{url('reporte')}}">
    <center><button type="button" class="btn btn-primary" ><i class="fa fa-hand-o-left" aria-hidden="true"></i> Regresar</button></center> 
    </a>
    @endif 
</div>
    
<link rel="stylesheet" type="text/css" href="{{asset('dpd/select2-4.0.3/dist/css/select2.min.css')}}"/>



@stop
@section("modals")
@include('pages.proyecto.upload-imagen')
@include('pages.proyecto.update-imagen')
@include('pages.proyecto.delete-imagen')
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
