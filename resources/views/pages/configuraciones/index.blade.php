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
            <h4 class="title">Configuraciones</h4>
            <p class="category"></p>
        </div>
        <div class="content table-full-width">
          <div class="table-responsive">
            <table class="table table-hover table-striped table-center">
                <thead>
                    <th>Configuración</th>
                    <th>Descripcion</th>

                    <th>Contenido</th>
                    <th><i class="fa fa-cogs fa-lg"></i></th>
                </thead>
                <tbody>
                @if(count($configuraciones) == 0)
                    <tr>
                        <td colspan="7">No se han encontrado resultados...</td>
                    </tr>
                @endif
                @foreach($configuraciones as $config)

                    <tr>
                      <td>{{$config->id}}</td>
                      <td>{{$config->descripcion}}</td>              

                      <td>{{$config->contenido}}</td>

                                                         

                         <td>
                           @can('update', $config)
                           
                              <a title="Editar Configuración" href="javascript:editarConfig('{{url('configuraciones')}}/{{$config->id}}')" >
                                  <i class="fa fa-pencil" aria-hidden="true"></i>
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
</div>



@stop
@section("modals")

  @include('pages.configuraciones.update')

 
@stop
