@push('JS')
    <script>
        function copiarRequerimientoTramite(url){
            document.getElementById("requerimiento-tramite-copiar-form").reset();
            $(".loader").addClass("hidden");
            $("#requerimiento-tramite-copiar-form").removeClass("hidden");
            $("[name=_method]").val("POST");
            $("#requerimiento-tramite-copiar-label").html("Copiar requerimientos");
            $("#requerimiento-tramite-copiar-form").attr("action", url);
            $("#requerimiento-tramite-copiar-modal").modal();

            $("[id=nombre]").prop('required',true);
            $("[id=cumplido]").prop('required',true);

        }
    </script>
@endpush

<div class="modal fade" id="requerimiento-tramite-copiar-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-big" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="requerimiento-tramite-copiar-label"></h4>
            </div>
            <div class="loader text-center">
                <i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>
                <span class="sr-only">Loading...</span>
            </div>
            <form class="form-horizontal hidden" method="POST" id='requerimiento-tramite-copiar-form' >
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
                                            <label for="id_proyecto">Nombre</label>
                                            <select class="form-control" name="id_proyecto" id="id_proyecto">
                                                @foreach($proyectos as $proyecto)
                                                    <option value="{{$proyecto->id}}">{{$proyecto->nombre}}</option>
                                                @endforeach
                                            </select>
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