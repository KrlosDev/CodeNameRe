@push('JS')
<script>
    function editarCasaBroker(url){
        $(".loader").removeClass("hidden");
        $("#casaUpdateBroker-form").addClass("hidden");
        $("[name=_method]").val("PUT");
        $("#casaUpdateBroker-form").attr("action", url);
        $("#casaUpdateBroker-label").html("Editar Propiedad");
        $.get(url,function(data,status){
                data=JSON.parse(data);

            $('#monto_separacion_u').val(data.monto_separacion);
            $('#monto_abono_inicial_u').val(data.monto_abono_inicial);
                $(".loader").addClass("hidden");
                $("#casaUpdateBroker-form").removeClass("hidden");
            });
        $("#casaUpdateBroker-modal").modal();
    }
</script>
@endpush

<div class="modal fade" id="casaUpdateBroker-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-big" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="casaUpdateBroker-label"></h4>
            </div>
            <div class="loader text-center">
                <i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>
                <span class="sr-only">Loading...</span>
            </div>
            <form class="form-horizontal hidden" method="POST" id='casaUpdateBroker-form' >
                {!! csrf_field() !!}
                <input type="hidden" name="_method" value="POST">
                <div class="modal-body">
                    <h4>Propiedad</h4>
                    <div class="content table-full-width">
                        <table class="table table-striped table-striped-clarito" >
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                                <label for="monto_separacion_u">Monto de Separación</label>
                                                <input type="number" class="form-control" step="0.01" min=0 name="monto_separacion_u" id="monto_separacion_u" placeholder="Monto de Separación" value="{{old('monto_separacion_u')}}" />
                                            </div>

                                            <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                                <label for="monto_abono_inicial_u">Monto de Abono Inicial</label>
                                                <input type="number" class="form-control" step="0.01" min=0 name="monto_abono_inicial_u" id="monto_abono_inicial_u" placeholder="Monto de Abono Inicial" value="{{old('monto_abono_inicial_u')}}" />
                                            </div>

                                            <div class="col-lg-4 col-sm-6 col-xs-12"></div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer" style='text-align: center;'>
                    <button type="submit" class="btn btn-primary">Aceptar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" title="Cancelar">Cancelar</button>
                </div>

            </form>
        </div>
    </div>
</div>