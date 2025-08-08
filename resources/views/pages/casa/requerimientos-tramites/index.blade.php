@extends('layouts.default')

@section('content')

    <div class="row">
        <div class="col-md-12 ">
            <div class="card">
                <div class="header">
                    <h4 class="title">Requerimientos de {{$casa->codigo}}</h4>
                    <p class="category">
                        <br>
                    </p>
                </div>
                <div class='col-xs-12 no-padding'>
                    <div class="content table-full-width">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped table-center">
                                <thead>
                                <th>Nombre</th>
                                <th>Cumplido</th>
                                <th><i class="fa fa-cogs fa-lg"></i></th>
                                </thead>
                                <tbody>
                                @if(count($requerimientosTramites) == 0)
                                    <tr>
                                        <td colspan="3">No se han encontrado resultados. Parece que el proyecto todavía no tiene requerimientos asignados.</td>
                                    </tr>
                                @endif
                                @foreach($requerimientosTramites as $requerimientoTramite)
                                    <tr>
                                        <td>
                                            {{$requerimientoTramite->nombre}}
                                            @if($requerimientoTramite->isRequirementAccomplished($casa->id))
                                                <i class="fa fa-lg fa-check color-good"></i>
                                            @else
                                                <i class="fa fa-lg fa-ban color-remove"></i>
                                            @endif
                                        </td>
                                        <td>@if($requerimientoTramite->isRequirementAccomplished($casa->id)) Sí @else No @endif</td>
                                        <td>
                                            <a title="@if($requerimientoTramite->isRequirementAccomplished($casa->id)) Marcar no cumplido @else Marcar cumplido @endif"
                                               href="{{url("casas_requerimientos_tramites/{$casa->id}/{$requerimientoTramite->id}/toggle")}}" >
                                                <i class="fa fa-lg fa-refresh" aria-hidden="true"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center">
                            {{$requerimientosTramites->appends(['cantidad' => $busqueda_cantidad])->render()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



@stop
@section("modals")

@stop

@push('JS')
    <script type='text/javascript'>
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush