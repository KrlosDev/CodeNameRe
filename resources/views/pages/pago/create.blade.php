 @push('JS')
<script>
    function añadirPago(url,url2,id_cliente){
        document.getElementById("pago-form").reset(); 
        $(".loader").addClass("hidden");
        $("#pago-form").removeClass("hidden");
        $("[name=_method]").val("POST");
        $("#pago-label").html("Nuevo Pago");
        $("#pago-form").attr("action", url);
        $("#cl_id").val(id_cliente);


        $.get(url2,function(data,status){
                data=JSON.parse(data);

                $("#casapago").empty();
                for (i=0; i< data.length; i++){ 
                                       
                    $("#casapago").append('<option value='+data[i].id_casa+'>'+data[i].codigo_casa+'</option>');
                };
                
                $(".loader").addClass("hidden");
                $("#status-form-asignar").removeClass("hidden");
        });
  
        $("#pago-modal").modal();



    }
    
</script>
@endpush

<div class="modal fade" id="pago-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-big" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="pago-label"></h4>
            </div>

            <div class="loader text-center">
                <i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>
                <span class="sr-only">Loading...</span>
            </div>

            <form class="form-horizontal hidden" method="POST" id='pago-form' >
                {!! csrf_field() !!}
                <input type="hidden" name="_method" value="POST">
                <input type="hidden" name="cl_id" id="cl_id" value="">

                <div class="modal-body">
                    <table class="table table-striped table-striped-clarito" >
                        <thead>
                            <th></th>
                        </thead>

                        <tbody>
                            <tr>
                                <td>
                                    <div class="form-group" >

                                        <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12" >
                                            <label for="casapago">Propiedades</label>
                                            <select class="form-control" name="casapago" id="casapago"></select>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <div class="form-group" >
                                        <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12" >
                                            <?php $forma=App\Models\Pago::getFormaPago(); ?>
                                            <label for="id_forma_pago">Forma de pago</label>
                                            <select class="form-control" name="id_forma_pago" id="id_forma_pago">
                                                @foreach($forma as $key => $value)
                                                    <option value="{{$key}}">{{$value}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-lg-4 col-sm-6 col-xs-12">
                                            <?php $trans=App\Models\Pago::getTipoTransaccion(); ?>
                                            <label for="id_tipo_transaccion">Tipo de Transacción</label>
                                            <select class="form-control" name="id_tipo_transaccion" id="id_tipo_transaccion">
                                                @foreach($trans as $key => $value)
                                                    <option value="{{$key}}">{{$value}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                       
                            <tr>
                                <td>
                                    <div class="form-group">

                                        <div class="col-lg-4 col-lg-offset-2 col-sm-12 col-xs-12">
                                            <label for="monto">Monto</label>
                                            <input type="number" class="form-control" name="monto" min="1" step="0.01" required />
                                        </div>

                                        <div class="col-lg-4  col-sm-12 col-xs-12">
                                            <label for="realizado_at">Fecha</label>
                                            <input type="date" class="form-control" name="realizado_at" required />
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <div class="form-group">
                                        <div class="col-lg-8 col-lg-offset-2 col-sm-12 col-xs-12">
                                            <label for="descripcion">Descripción</label>
                                            <textarea class="form-control" name="descripcion" maxlength="{{\App\Models\Pago::MAX_LENGTH_DESCRIPCION}}" rows="6" style="resize:none" required></textarea>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>

                <div class="modal-footer" style='text-align: center;'>
                    <button type="submit" class="btn btn-primary">Aceptar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" title="Cancelar">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
