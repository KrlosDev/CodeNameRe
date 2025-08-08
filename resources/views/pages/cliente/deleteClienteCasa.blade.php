@push('JS')
<script>
    function quitarCasa(url,url2,id_cliente){

  
        $(".loader").addClass("hidden");
        $("#cliente-casa-form-quitar").removeClass("hidden");
        $("[name=_method]").val("DELETE");
        $("#cliente-casa-label-quitar").html("Desasignar Propiedad");
        $("#cliente-casa-form-quitar").attr("action", url);
        $("#clientquitar").val(id_cliente);
        $( ":input" ).prop('readonly', false);
        $( "select" ).prop('disabled', false);
        $("[id=casaquitar]").show();


        $.get(url2,function(data,status){
                data=JSON.parse(data);

                $("#casaquitar").empty();
                for (i=0; i< data.length; i++){ 
                                       
                    $("#casaquitar").append('<option value='+data[i].id_casa+'>'+data[i].codigo_casa+'</option>');
                };
                $(".loader").addClass("hidden");
                $("#status-form-asignar").removeClass("hidden");                
        });
        
        $("#cliente-casa-modal-quitar").modal();        
    }

    
</script>
@endpush

<div class="modal fade" id="cliente-casa-modal-quitar" tabindex="-1" role="dialog" aria-labelledby="cliente-casa-modal-quitar" aria-hidden="true">
    <div class="modal-dialog  modal-big">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title" id="cliente-casa-label-quitar">Asignar</h3>
            </div>
            <div class="loader text-center">
                <i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>
                <span class="sr-only">Loading...</span>
            </div>

        <form class="form-horizontal margin-top hidden" method="POST" id='cliente-casa-form-quitar'>
            {!! csrf_field() !!}
            <input type="hidden" name="_method" value="POST">
            <input type="hidden" name="clientquitar" id="clientquitar" value="">


            <div class="modal-body no-border">
                <p>Seleccione las opciones deseadas para asignar la casa correspondiente</p>
                    
                    <div class="form-group">
                    <div class="col-sm-6 col-xs-12 margin-top">
                        <label for="casaquitar">Propiedades</label>
                        <select class="form-control select2" name="casaquitar" id="casaquitar" required>
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