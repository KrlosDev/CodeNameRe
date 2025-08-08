@push('JS')
<script>
    function eliminarCasas(){
        $(".loader").addClass("hidden");
  
        $("[name=_method]").val("POST");
        $("#casas-label-delete").html("Eliminar Casa");  
        $("#casas-modal-delete").modal();


    }

    function eli(){
        $("#dele").submit();
    }
    
</script>
@endpush

<div class="modal fade" id="casas-modal-delete" tabindex="-1" role="dialog" aria-labelledby="casas-modal-delete" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title" id="casas-label-delete">Eliminar</h3>
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
                    <p id="s">¿Está seguro que desea eliminar la casa seleccionada?</p>
                </div>
            </div>

            <div class="modal-footer no-border">
              
                   
      
                    
                    <button class="btn btn-danger" id="btn-action2" onchange="eli()">Eliminar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                
            </div>
        </div>
    </div>
</div>