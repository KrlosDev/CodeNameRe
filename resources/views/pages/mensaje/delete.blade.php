 @push('JS')
<script>
    function eliminarMensaje(url){
        $(".loader").addClass("hidden");
        $("#mensajes-form-delete").removeClass("hidden");
        $("[name=_method]").val("DELETE");
        $("#mensajes-label-delete").html("Eliminar Mensajes");
        $("#mensajes-form-delete").attr("action", url);  
        $("#mensajes-modal-delete").modal();
    }
    
</script>
@endpush

<div class="modal fade" id="mensajes-modal-delete" tabindex="-1" role="dialog" aria-labelledby="mensajes-modal-delete" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title" id="mensajes-label-delete">Eliminar</h3>
            </div>
            <div class="loader text-center">
                <i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>
                <span class="sr-only">Loading...</span>
            </div>

            <div class="modal-body no-border">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                     <h4 id="div-inner-title"></h4>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="divModalDelete">
                    <p>¿Está seguro que desea eliminar el mensaje?</p>
                </div>
            </div>

            <div class="modal-footer no-border">
                <form class="form-horizontal hidden" method="POST" id='mensajes-form-delete' >
                    {!! csrf_field() !!}
                    <input type="hidden" name="_method" value="POST">
                    
                    <button class="btn btn-danger" id="btn-action" >Eliminar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                </form>
            </div>
        </div>
    </div>
</div>