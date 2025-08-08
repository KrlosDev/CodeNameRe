
<script>
    function crearMensaje(url,url2){
         document.getElementById("mensaje-create-form").reset();
        $(".loader").addClass("hidden");
        $("#mensaje-create-form").removeClass("hidden");
        $("[name=_method]").val("POST");
        $("#mensaje-label").html("Nuevo Mensaje");
        $("#mensaje-create-form").attr("action", url);

        $("[id=cl]").hide();
        $("[id=titulo]").prop('readonly', false);
            $("[id=aceptar]").show();
        $("[id=descripcion]").prop('readonly', false);
        $("[id=paragroup]").show();
        $("[id=tele]").hide();


        $.get(url2,function(data,status){
                data=JSON.parse(data);

                $("#para").empty();
                for (i=0; i< data.length; i++){
                    $("#para").append('<option value='+data[i].id_ej+'>'+data[i].nombre+'</option>');
                }
                
                $(".loader").addClass("hidden");
                $("#mensaje-create-form").removeClass("hidden");
        });
        
        $("#mensaje-modal").modal();
    

    }
    
</script>


<div class="modal fade" id="mensaje-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-big" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="mensaje-label"></h4>
            </div>

            <div class="loader text-center">
                <i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>
                <span class="sr-only">Loading...</span>
            </div>

            <form class="form-horizontal hidden" method="POST" id='mensaje-create-form' >
                {!! csrf_field() !!}
                <input type="hidden" name="_method" value="POST">

                <div class="modal-body">
                    <div class="form-group">
                        <div class="col-lg-6 col-lg-offset-3 col-sm-8 col-sm-offset-2 col-xs-12" id="cl">
                            <label for="cliente">Cliente</label>
                            <input type="text" class="form-control" name="cliente" id="cliente" />
                        </div>
                    </div>

                     <div class="form-group" id="paragroup">
                        <div class="col-lg-6 col-lg-offset-3 col-sm-8 col-sm-offset-2 col-xs-12">
                            <label for="para">Para</label>
                            <select class="form-control" name="para" id="para" required></select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-lg-6 col-lg-offset-3 col-sm-8 col-sm-offset-2 col-xs-12">
                            <label for="titulo">Título </label>
                            <input type="text" class="form-control" name="titulo" id="titulo" required />
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-lg-6 col-lg-offset-3 col-sm-8 col-sm-offset-2 col-xs-12">
                            <label for="descripcion">Descripción </label>
                            <textarea class="form-control" placeholder="Contenido del mensaje" name="descripcion" id="descripcion" rows="9" maxlength="{{\App\Models\Mensaje::MAX_LENGTH_DESCRIPCION}}" style="resize:none" required></textarea>
                        </div>
                    </div>

                    <div class="form-group" id="tele">
                        <div class="col-lg-6 col-lg-offset-3 col-sm-8 col-sm-offset-2 col-xs-12">

                            <label for="telefonoM">Teléfono</label>
                            <input type="text" class="form-control" name="telefonoM" id="telefonoM" />
                        </div>
                    </div>
                </div>     

                <div class="modal-footer" style='text-align: center;'>
                    <button type="submit" class="btn btn-primary">Aceptar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" title="Cerrar">Cerrar</button>
                </div>

            </form>
        </div>
    </div>
</div>