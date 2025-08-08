 @push('JS')
<script>
    function crearCasa(url,cid_proyecto,url2){
        document.getElementById("casa-crear-form").reset(); 
        $(".loader").addClass("hidden");
        $("#casa-crear-form").removeClass("hidden");
        $("[name=_method]").val("POST");
        $("#cid_proyecto").val(cid_proyecto);
        $("#casa-crear-label").html("Nueva Propiedad");
        $("#casa-crear-form").attr("action", url);


        $.get(url2,function(data,status){
                data=JSON.parse(data);
               
                $('#ccodigo').val(data.codigo);
                $('#cmodelo').val(data.modelo);
                $('#clote_apto').val(data.lote_apto);
                $('#cmts2_total').val(data.mts2_total);
                $('#cmts2_construccion').val(data.mts2_construccion);
                $('#cmts2_adicionales').val(data.mts2_adicionales);
                $('#crecamaras').val(data.recamaras);
                $('#cbanos').val(data.banos);
                $('#cvalor').val(data.valor);
                $('#cmonto_separacion').val(data.monto_separacion);
                $('#cmonto_abono_inicial').val(data.monto_abono_inicial);
                $('#cmonto_mts2_adicional').val(data.monto_abono_inicial);
                $(".loader").addClass("hidden");
                $("#casa-crear-form").removeClass("hidden");
        });  

        $("#casa-crear-modal").modal();

    }
    
</script>
@endpush

<div class="modal fade" id="casa-crear-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-big" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="casa-crear-label"></h4>
            </div>
            <div class="loader text-center">
                <i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>
                <span class="sr-only">Loading...</span>
            </div>
            <form class="form-horizontal hidden" method="POST" id='casa-crear-form' >
                {!! csrf_field() !!}
                <input type="hidden" name="_method" value="POST">
                <input type="hidden" name="cid_proyecto" id="cid_proyecto" value="">
                

                <div class="modal-body">
                        <h4>Propiedad</h4>
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
                                    <label for="cmodelo">Modelo</label>
                                    <input type="text" class="form-control" name="cmodelo" id="cmodelo" required placeholder="Modelo" value="{{old('cmodelo')}}" maxlength="{{\App\Models\Casa::MAX_LENGTH_MODELO}}" />
                                </div>

                                <div class="col-lg-4 col-sm-6 col-xs-12">
                                    <label for="ccantidad">Cantidad</label>
                                    <input type="number" class="form-control" required min=1 name="ccantidad" id="ccantidad"  placeholder="Cantidad"  value="{{old('ccantidad')}}"/>
                                </div>

                            </div>
                        </td>
                        </tr>

                        <tr>
                        <td>
                            <div class="form-group">
                                <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                    <label for="cmts2_total">Mts2 Totales</label>
                                    <input type="number" class="form-control" min=0 step="0.01" name="cmts2_total" id="cmts2_total" required placeholder="Mts2 Totales" value="{{old('cmts2_total')}}" />
                                </div>

                                <div class="col-lg-4 col-sm-6 col-xs-12">
                                    <label for="cmts2_construccion">Mts2 Construcción</label>
                                    <input type="number" class="form-control" min=0 step="0.01" name="cmts2_construccion" id="cmts2_construccion" required placeholder="Mts2 Construcción" value="{{old('cmts2_construccion')}}" />
                                </div>
                            </div>      
                        </td>
                        </tr>

                        <tr>
                        <td>
                            <div class="form-group">
                                <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                    <label for="cmts2_adicionales">Mts2 Adicionales</label>
                                    <input type="number" class="form-control" min=0 step="0.01" name="cmts2_adicionales" id="cmts2_adicionales" required placeholder="Mts2 Adicionales" value="{{old('cmts2_adicionales')}}" />
                                </div>
                            </div>      
                        </td>
                        </tr>

                        <tr>
                        <td>
                            <div class="form-group">
                                <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                    <label for="crecamaras">Recamaras</label>
                                    <input type="number" class="form-control" min=0 name="crecamaras" id="crecamaras" required placeholder="Recamaras" value="{{old('crecamaras')}}" />
                                </div>

                                <div class="col-lg-4 col-sm-6 col-xs-12">
                                    <label for="cbanos">Baños</label>
                                    <input type="number" class="form-control" min=0 name="cbanos" id="cbanos" required placeholder="Baños" value="{{old('cbanos')}}" />
                                </div>

                            </div> 
                        </td>
                        </tr>

                        <tr>
                        <td>
                            <div class="form-group">
                                <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                    <label for="cvalor">Valor</label>
                                    <input type="number" class="form-control" step="0.01" min=0 name="cvalor" id="cvalor" required placeholder="Valor" value="{{old('cvalor')}}" />
                                </div>


                                <div class="col-lg-4 col-sm-6 col-xs-12">
                                    <label for="cmonto_separacion">Monto de Separación</label>
                                    <input type="number" class="form-control" step="0.01" min=0 name="cmonto_separacion" id="cmonto_separacion" required placeholder="Monto de Separación" value="{{old('cmonto_separacion')}}" />
                                </div>
                            </div> 
                        </td>
                        </tr>

                        <tr>
                        <td>
                            <div class="form-group">
                                <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                    <label for="cmonto_abono_inicial">Monto de Abono Inicial</label>
                                    <input type="number" class="form-control" step="0.01" min=0 name="cmonto_abono_inicial" id="cmonto_abono_inicial" required placeholder="Monto de Abono Inicial" value="{{old('cmonto_abono_inicial')}}" />
                                </div>

                                <div class="col-lg-4 col-sm-6 col-xs-12">
                                    <label for="cmonto_mts2_adicional">Monto de Mts2 Adicional</label>
                                    <input type="number" class="form-control" step="0.01" min=0 name="cmonto_mts2_adicional" id="cmonto_mts2_adicional" required placeholder="Monto de Mts2 Adicional" value="{{old('cmonto_mts2_adicional')}}" />
                                </div>
                            </div> 
                        </td>
                        </tr>

                        </tbody>
                    </table>
              
                </div> <!-- div de content table -->
                </div>

                <div class="modal-footer" style='text-align: center;'>
                    <button type="submit" class="btn btn-primary">Aceptar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" title="Cancelar">Cancelar</button>
                </div>

            </form>
        </div>
    </div>
</div>