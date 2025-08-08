 @push('JS')
<script>
    function deasignarCasa(url){
        $(".loader").addClass("hidden");
        $("#casa-form-desasignar").removeClass("hidden");
        $("[name=_method]").val("GET");
        $("#casa-label-desasignar").html("Desasignar Propiedad");
        $("#casa-form-desasignar").attr("action", url);  
        $("#casa-modal-desasignar").modal();
    }
    
</script>
@endpush

<div class="modal fade" id="casa-modal-desasignar" tabindex="-1" role="dialog" aria-labelledby="casa-modal-desasignar" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title" id="casa-label-desasignar">Desasignar</h3>
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
                    <p>¿Está seguro que desea desasignar la propiedad seleccionada?</p>
                </div>
            </div>

            <div class="modal-footer no-border">
                <form class="form-horizontal" method="GET" id='casa-form-desasignar' >
                    <button class="btn btn-warning" id="btn-action" >Desasignar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                </form>
            </div>
        </div>
    </div>
</div>