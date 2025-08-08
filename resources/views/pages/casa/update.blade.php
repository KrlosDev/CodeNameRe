@push('JS')
<script>
    function editarCasa(url){
        $(".loader").removeClass("hidden");
        $("#casaUpdate-form").addClass("hidden");
        $("[name=_method]").val("PUT");
        $("#casaUpdate-form").attr("action", url);
        $("#casaUpdate-label").html("Editar Propiedad");
        $.get(url,function(data,status){
                data=JSON.parse(data);
               
                $('#codigo_u').val(data.codigo);
                $('#modelo_u').val(data.modelo);
                $('#lote_apto_u').val(data.lote_apto);
                $('#mts2_total_u').val(data.mts2_total);
                $('#mts2_construccion_u').val(data.mts2_construccion);
                $('#mts2_adicionales_u').val(data.mts2_adicionales);
                $('#recamaras_u').val(data.recamaras);
                $('#banos_u').val(data.banos);
                $('#valor_u').val(data.valor);
                $('#monto_separacion_u').val(data.monto_separacion);
                $('#monto_abono_inicial_u').val(data.monto_abono_inicial);
                $('#monto_mts2_adicional_u').val(data.monto_mts2_adicional);
                $(".loader").addClass("hidden");
                $("#casaUpdate-form").removeClass("hidden");
            });
        $("#casaUpdate-modal").modal();
    }
</script>
@endpush

<div class="modal fade" id="casaUpdate-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-big" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="casaUpdate-label"></h4>
            </div>
            <div class="loader text-center">
                <i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>
                <span class="sr-only">Loading...</span>
            </div>
            <form class="form-horizontal hidden" method="POST" id='casaUpdate-form' >
                {!! csrf_field() !!}
                <input type="hidden" name="_method" value="POST">
                

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
                                    <label for="codigo_u">Codigo</label>
                                    <input type="text" class="form-control" readonly name="codigo_u" id="codigo_u" placeholder="Código" value="{{old('codigo_u')}}"/>
                                </div>
                            </div>
                        </td>
                        </tr>

                        <tr>
                        <td>
                            <div class="form-group">
                                <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                    <label for="modelo_u">Modelo</label>
                                    <input type="text" class="form-control" name="modelo_u" id="modelo_u" required placeholder="Modelo" value="{{old('modelo_u')}}" maxlength="{{\App\Models\Casa::MAX_LENGTH_MODELO}}" />
                                </div>

                                <div class="col-lg-4 col-sm-6 col-xs-12">
                                    <label for="lote_apto_u">Lote de Apto</label>
                                    <input type="number" class="form-control" min=0 name="lote_apto_u" id="lote_apto_u" disabled="true" placeholder="Lote de Apto" value="{{old('lote_apto_u')}}"/>
                                </div>

                            </div>
                        </td>
                        </tr>

                        <tr>
                        <td>
                            <div class="form-group">
                                <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                    <label for="mts2_total_u">Mts2 Totales</label>
                                    <input type="number" class="form-control" min=0 step="0.01" name="mts2_total_u" id="mts2_total_u" required placeholder="Mts2 Totales" value="{{old('mts2_total_u')}}" />
                                </div>

                                <div class="col-lg-4 col-sm-6 col-xs-12">
                                    <label for="mts2_construccion_u">Mts2 Construcción</label>
                                    <input type="number" class="form-control" min=0 step="0.01" name="mts2_construccion_u" id="mts2_construccion_u" required placeholder="Mts2 Construcción" value="{{old('mts2_construccion_u')}}" />
                                </div>
                            </div>      
                        </td>
                        </tr>

                        <tr>
                        <td>
                            <div class="form-group">
                                <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                    <label for="mts2_adicionales_u">Mts2 Adicionales</label>
                                    <input type="number" class="form-control" min=0 step="0.01" name="mts2_adicionales_u" id="mts2_adicionales_u" required placeholder="Mts2 Adicionales" value="{{old('mts2_adicionales_u')}}" />
                                </div>
                            </div>      
                        </td>
                        </tr>

                        <tr>
                        <td>
                            <div class="form-group">
                                <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                    <label for="recamaras_u">Recamaras</label>
                                    <input type="number" class="form-control" min=0 name="recamaras_u" id="recamaras_u" required placeholder="Recamaras" value="{{old('recamaras_u')}}" />
                                </div>

                                <div class="col-lg-4 col-sm-6 col-xs-12">
                                    <label for="banos_u">Baños</label>
                                    <input type="number" class="form-control" min=0 name="banos_u" id="banos_u" required placeholder="Baños" value="{{old('banos_u')}}" />
                                </div>

                            </div> 
                        </td>
                        </tr>

                        <tr>
                        <td>
                            <div class="form-group">
                                <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                    <label for="valor_u">Valor</label>
                                    <input type="number" class="form-control" step="0.01" min=0 name="valor_u" id="valor_u" required placeholder="Valor" value="{{old('valor_u')}}" />
                                </div>


                                <div class="col-lg-4 col-sm-6 col-xs-12">
                                    <label for="monto_separacion_u">Monto de Separación</label>
                                    <input type="number" class="form-control" step="0.01" min=0 name="monto_separacion_u" id="monto_separacion_u" required placeholder="Monto de Separación" value="{{old('monto_separacion_u')}}" />
                                </div>
                            </div> 
                        </td>
                        </tr>

                        <tr>
                        <td>
                            <div class="form-group">
                                <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                    <label for="monto_abono_inicial_u">Monto de Abono Inicial</label>
                                    <input type="number" class="form-control" step="0.01" min=0 name="monto_abono_inicial_u" id="monto_abono_inicial_u" required placeholder="Monto de Abono Inicial" value="{{old('monto_abono_inicial_u')}}" />
                                </div>

                                <div class="col-lg-4 col-sm-6 col-xs-12">
                                    <label for="monto_mts2_adicional_u">Monto de Mts2 Adicional</label>
                                    <input type="number" class="form-control" step="0.01" min=0 name="monto_mts2_adicional_u" id="monto_mts2_adicional_u" required placeholder="Monto de Mts2 Adicional" value="{{old('monto_mts2_adicional_u')}}" />
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