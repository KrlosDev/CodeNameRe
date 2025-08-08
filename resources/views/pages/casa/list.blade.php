 @push('JS')
<script>
    function listarCasas(url){
        $("#casas-label-listar").html("Listar Casas");
        $("#casas-modal-listar").modal();
        $.get(url,function(data,status){
            data=JSON.parse(data);
            html = "<div class='table-responsive'><table class='table table-striped table-hover table-center'><thead>"+
                "<tr><th>Código</th><th>Modelo</th><th>Valor</th><th>Mts2 Total</th><th>Broker</th></tr><tbody>";
            $.each(data,function(index,casa) {
                html += "<tr><td>"+casa.codigo+"</td><td>"+casa.modelo+"</td><td>"+casa.valor+"</td><td>"+casa.mts2_total+
                    "</td><td>"+((casa.broker===null)?"Sin asignar":casa.broker.id_user)+"</td></tr>";
            });
            $('#casas-form-listar').html(html+"</tbody></table></div>");
            $(".loader").addClass("hidden");
            $("#casas-form-listar").removeClass("hidden");
        });
    }
    
</script>
@endpush

<div class="modal fade" id="casas-modal-listar" tabindex="-1" role="dialog" aria-labelledby="casas-modal-listar" aria-hidden="true">
    <div class="modal-dialog  modal-big">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title" id="casas-label-listar">Listar</h3>
            </div>
            <div class="loader text-center">
                <i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>
                <span class="sr-only">Loading...</span>
            </div>

            <div class="modal-body no-border">
                <div class='col-xs-12 margin-top hidden' id='casas-form-listar'>
                    
                </div>
            </div>

            <div class="modal-footer no-border">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>