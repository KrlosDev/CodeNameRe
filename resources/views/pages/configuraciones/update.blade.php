@push('JS')
<script>
    function editarConfig(url){
        $(".loader").removeClass("hidden");
        $("#config-form").addClass("hidden");
        $("[name=_method]").val("PUT");
        $("#config-form").attr("action", url);
        $("#config-label").html("Editar Configuración");
        
        $.get(url,function(data,status){
                data=JSON.parse(data);
               
                $('#contenido').val(data.contenido);
                $(".loader").addClass("hidden");
                $("#config-form").removeClass("hidden");
            });
        $("#config-modal").modal();
    }
</script>
@endpush

<div class="modal fade" id="config-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-big" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="config-label"></h4>
            </div>
            <div class="loader text-center">
                <i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>
                <span class="sr-only">Loading...</span>
            </div>
            <form class="form-horizontal hidden" method="POST" id='config-form' >
                {!! csrf_field() !!}
                <input type="hidden" name="_method" value="POST">

                <div class="modal-body">
                    <h4>Configuración</h4>

                    <div class="form-group">
                        <div class="col-lg-4 col-sm-6 col-xs-12">
                            <label for="contenido">Contenido</label>
                            <input type="text" class="form-control" name="contenido" id="contenido" />
                        </div>
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