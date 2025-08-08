@push('JS')
    <script>
        function crearRequerimientoTramite(url){
            document.getElementById("requerimiento-tramite-form").reset();
            $(".loader").addClass("hidden");
            $("#requerimiento-tramite-form").removeClass("hidden");
            $("[name=_method]").val("POST");
            $("#requerimiento-tramite-label").html("Nuevo Requerimiento");
            $("#requerimiento-tramite-form").attr("action", url);
            $("#requerimiento-tramite-modal").modal();

            $("[id=nombre]").prop('required',true);

        }
    </script>
@endpush

<div class="modal fade" id="requerimiento-tramite-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-big" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="requerimiento-tramite-label"></h4>
            </div>
            <div class="loader text-center">
                <i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>
                <span class="sr-only">Loading...</span>
            </div>
            <form class="form-horizontal hidden" method="POST" id='requerimiento-tramite-form' >
                {!! csrf_field() !!}
                <input type="hidden" name="_method" value="POST">


                <div class="modal-body">

                    <h4>Requerimiento</h4>
                    <div class="content table-full-width">
                        <table class="table table-striped table-striped-clarito" >
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                                <label for="nombre">Nombre</label>
                                                <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Nombre" value="{{old('nombre')}}" required maxlength="{{\App\Models\RequerimientoTramite::MAX_LENGTH_NOMBRE}}" />
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