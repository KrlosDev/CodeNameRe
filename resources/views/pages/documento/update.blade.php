<?php

/**
 * Description of update
 *
 * @author Luis Valencia <luisvalencia@vccodes.com.ve>
 * @company VC Codes <contacto@vccodes.com.ve>
 */
?>
@push('JS')
<script>
    function editarDocumento(url){
        document.getElementById("documentos-form").reset();   
        $(".loader").addClass("hidden");
        $("[name=_method]").val("PUT");
        $("#documentos-label").html("Actualizar documento");
        $("#documentos-form").attr("action", url);
        
        $.get(url,function(data,status){
            data=JSON.parse(data);
            $('#fecha_expiracion').val(data.fecha_expiracion);
            $(".loader").addClass("hidden");
            $("#documentos-form").removeClass("hidden");
        });
        $('#fecha_expiracion').datepicker({dateFormat:'yy-mm-dd'});
        $("#documentos-modal").modal();
    }
</script>
@endpush

<div class="modal fade" id="documentos-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-big" role="document" >
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="documentos-label"></h4>
            </div>
            <div class="loader text-center">
                <i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>
                <span class="sr-only">Loading...</span>
            </div>
            <form class="form-horizontal hidden" method="POST" id='documentos-form' >
                {!! csrf_field() !!}
                <input type="hidden" name="_method" value="POST">
                
                <div class="modal-body">
                    <div class="content table-full-width">
                        <table class="table table-striped table-striped-clarito" >
                            <thead>
                                <th></th>
                            </thead>
                            <tbody>
                                <h4>Datos de Usuario</h4>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                                <label for="fecha_expiracion">Fecha de Expiración</label>
                                                <input type="text" class="form-control" placeholder="Fecha de Expiración" id="fecha_expiracion" name="fecha_expiracion"/>
                                            </div>
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