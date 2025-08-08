@push('JS')
<script>
    function asignarCasa(url,id_cliente){

        $(".loader").addClass("hidden");
        $("#cliente-casa-form-asignar").removeClass("hidden");
        $("[name=_method]").val("POST");
        $("#cliente-casa-label-asignar").html("Añadir Propiedad");
        $("#cliente-casa-form-asignar").attr("action", url);
        $("#clientanadir").val(id_cliente);
        $( ":input" ).prop('readonly', false);
        $( "select" ).prop('disabled', false);
        $("[id=casaid]").show();
        
        $("#cliente-casa-modal-asignar").modal();        
    }

    
</script>
@endpush

<div class="modal fade" id="cliente-casa-modal-asignar" tabindex="-1" role="dialog" aria-labelledby="cliente-casa-modal-asignar" aria-hidden="true">
    <div class="modal-dialog  modal-big">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title" id="cliente-casa-label-asignar">Asignar</h3>
            </div>
            <div class="loader text-center">
                <i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>
                <span class="sr-only">Loading...</span>
            </div>

        <form class="form-horizontal margin-top hidden" method="POST" id='cliente-casa-form-asignar'>
            {!! csrf_field() !!}
            <input type="hidden" name="_method" value="POST">
            <input type="hidden" name="clientanadir" id="clientanadir" value="">


            <div class="modal-body no-border">
                <p>Seleccione las opciones deseadas para asignar la casa correspondiente</p>
                    
                    <div class="form-group">
                        <div class="col-sm-6 col-xs-12 margin-top">
                            <label for="casaid">Propiedades</label>
                            <select class="form-control select2" name="casaid" id="casaid" required>
                            @foreach($casas as $casa)
                                @if(old('casaid') == $casa->id)
                                <option value="{{$casa->id}}" selected>{{$casa->codigo}}</option>
                                @else
                                <option value="{{$casa->id}}">{{$casa->codigo}}</option>
                                @endif
                            @endforeach
                            </select>
                        </div>
                        
                        @if(!Auth::user()->isEjVentas())
                        <div class="col-sm-6 col-xs-12 margin-top">
                            <label for="id_ej_ventas">Ejecutivo de Ventas</label>
                            <select class="form-control select2" name="id_ej_ventas" id="id_ej_ventas">
                                <option value='0'>Ninguno</option>
                            @foreach($ejVentas as $ejVenta)
                                @if(old('id_ej_ventas') == $ejVenta->id)
                                <option value="{{$ejVenta->id}}" selected>{{$ejVenta->user->nombre}}</option>
                                @else
                                <option value="{{$ejVenta->id}}">{{$ejVenta->user->nombre}}</option>
                                @endif
                            @endforeach
                            </select>
                        </div>
                        @endif

                        <div id='banco' class="col-sm-6 col-xs-12 margin-top">
                            <label for="id_ej_bancos">Ejecutivo de Banco</label>
                            <select class="form-control select2" name="id_ej_bancos" id="id_ej_bancos">
                                <option value='0'>Ninguno</option>
                            @foreach($ejBancos as $ejBanco)
                                @if(old('id_ej_bancos') == $ejBanco->id)
                                <option value="{{$ejBanco->id}}" selected>{{$ejBanco->user->nombre}}</option>
                                @else
                                <option value="{{$ejBanco->id}}">{{$ejBanco->user->nombre}}</option>
                                @endif
                            @endforeach
                            </select>
                        </div>
                    </div>
            </div>

            <div class="modal-footer no-borde" style='text-align: center;'>
                <button type="submit" class="btn btn-primary">Aceptar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
            </div>
        </form>
        </div>
    </div>
</div>