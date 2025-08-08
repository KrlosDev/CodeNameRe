 @push('JS')
<script>
    function subirImagen(url){
        document.getElementById("proyectos-form-imagen").reset();       
        $(".loader").addClass("hidden");
        $("#proyectos-form-imagen").removeClass("hidden");
        $("[name=_method]").val("POST");
        $("#proyectos-label-imagen").html("Subir imagen");
        $("#proyectos-form-imagen").attr("action", url);  
        $("#proyectos-modal-imagen").modal();

        $("[id=datos_casa]").show();
        $("[id=codigo]").prop('readonly', false);

        $("[id=checkcasa]").hide();
        
        $("[id=descripcion]").prop('required',false);
        $("[id=nombre]").prop('required',true);
    }
    
</script>
@endpush

<div class="modal fade" id="proyectos-modal-imagen" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-big" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="proyectos-label-imagen"></h4>
            </div>
            <div class="loader text-center">
                <i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>
                <span class="sr-only">Loading...</span>
            </div>
            <form class="form-horizontal hidden" method="POST" id='proyectos-form-imagen' enctype='multipart/form-data' >
                {!! csrf_field() !!}
                <input type="hidden" name="_method" value="POST">
                
                <div class="modal-body">
                    <h4>Imagen</h4>
                    <div class="content table-full-width">
                        <table class="table table-striped table-striped-clarito" >
                            <thead>
                                <th></th>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                                <label for="nombre">Nombre</label>
                                                <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Nombre" value="{{old('nombre')}}" required maxlength="{{\App\Models\Imagen::MAX_LENGTH_NOMBRE}}" />
                                            </div>

                                            <div class="col-lg-4 col-sm-6 col-xs-12">
                                                <label for="descripcion">Descripción</label>
                                                <input type="text" class="form-control" name="descripcion" id="descripcion" placeholder="Descripción" value="{{old('descripcion')}}" maxlength="{{\App\Models\Imagen::MAX_LENGTH_DESCRIPCION}}" />
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                                <label for="imagen">Imagen</label>
                                                <input type="file" class="form-control" name="imagen" id="imagen" placeholder="Imagen" required accept=".png,.jpeg,.jpg,.gif" />
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table class="table table-striped table-striped-clarito" >
                            <thead>
                                <th></th>
                            </thead>
                            <tbody>
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