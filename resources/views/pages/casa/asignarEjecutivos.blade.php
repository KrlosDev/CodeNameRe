@push('JS')
<script>
    function asignarEjecutivos(url,id_casa,id_cliente){

        $(".loader").addClass("hidden");
        $("#ejecutivos-form-asignar").removeClass("hidden");
        $("[name=_method]").val("POST");
        $("#ejecutivos-label-asignar").html("Asignar Ejecutivos");
        $("#ejecutivos-form-asignar").attr("action", url);
        $("#id_casa").val(id_casa);
        
        $("#ejecutivos-modal-asignar").modal();
        
        
        if(id_cliente==0){
            $("[id=banco]").hide();
            $("#ej").val('0');
        }
        else{
            $("[id=banco]").show();  
             $("#ej").val('1');
        }
    }
    
</script>
@endpush

<div class="modal fade" id="ejecutivos-modal-asignar" tabindex="-1" role="dialog" aria-labelledby="ejecutivos-modal-asignar" aria-hidden="true">
    <div class="modal-dialog  modal-big">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title" id="ejecutivos-label-asignar">Asignar</h3>
            </div>
            <div class="loader text-center">
                <i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>
                <span class="sr-only">Loading...</span>
            </div>
            
            <form class="form-horizontal margin-top hidden" method="POST" id='ejecutivos-form-asignar'>
                {!! csrf_field() !!}
                <input type="hidden" name="_method" value="POST">
                <input type="hidden" name="id_casa" id="id_casa" value="">
                <input type="hidden" name="ej" id="ej" value="">

                <div class="modal-body no-border">
                    <p>Seleccione las opciones deseadas para asignar las ejecutivos correspondientes</p>
                        <div class="form-group">
                            @if(!Auth::user()->isEjVentas())
                                <div class="col-sm-6 col-xs-12 margin-top">
                                    <label for="id_ej_ventas">Ejecutivo de Ventas</label>
                                    <select class="form-control select2" name="id_ej_ventas" id="id_ej_ventas">
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

                <div class="modal-footer no-border" style='text-align: center;'>
                    <button type="submit" class="btn btn-primary">Aceptar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>