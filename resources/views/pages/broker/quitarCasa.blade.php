@push('JS')
<script>
    function quitarCasa(url,url2){

  
        $(".loader").addClass("hidden");
        $("#broker-casa-form-quitar").removeClass("hidden");
        $("[name=_method]").val("POST");
        $("#broker-casa-label-quitar").html("Desasignar Propiedad");
        $("#broker-casa-form-quitar").attr("action", url);
        //$("#clientquitar").val(id_broker);
        $( ":input" ).prop('readonly', false);
        $( "select" ).prop('disabled', false);
        $("[id=casaquitar]").show();


        $.get(url2,function(data,status){
                data=JSON.parse(data);
                
                $("#casaquitar").empty();
                
                for (i=0; i< data.length; i++){ 
                      
                    $("#casaquitar").append('<option value='+data[i].id+'>'+data[i].codigo+'</option>');
                };
                
  
                $(".loader").addClass("hidden");
                $("#status-form-asignar").removeClass("hidden");                
        });
        
        $("#broker-casa-modal-quitar").modal();        
    }

    
</script>
@endpush

<div class="modal fade" id="broker-casa-modal-quitar" tabindex="-1" role="dialog" aria-labelledby="broker-casa-modal-quitar" aria-hidden="true">
    <div class="modal-dialog  modal-big">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title" id="broker-casa-label-quitar">Asignar</h3>
            </div>
            <div class="loader text-center">
                <i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>
                <span class="sr-only">Loading...</span>
            </div>

        <form class="form-horizontal margin-top hidden" method="POST" id='broker-casa-form-quitar'>
            {!! csrf_field() !!}
            <input type="hidden" name="_method" value="POST">
            <!-- <input type="hidden" name="clientquitar" id="clientquitar" value=""> -->


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