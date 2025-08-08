 @push('JS')
<script>
    function listarDocsCliente(url){
        $("#docs-cliente-label-listar").html("Documentos");
        $("#docs-cliente-modal-listar").modal();
        
        var tbody = $('#tabletr');
        var ths = $('#ths');
        ths.append('@can("store","App\Models\Cliente")<th> hell </th>@endcan');
        $.get(url,function(data,status){
            data=JSON.parse(data);
            html = "";

            $.each(data,function(doc) {
                 tbody.append('<td>' + doc.id_pais + '</td> @can("store","App\Models\Cliente") <td>' + doc.id_pais + '</td>@endcan');

            });
            //$('#docs-cliente-listar').html(html+"</tbody></table></div>");
            $(".loader").addClass("hidden");
            $("#docs-cliente-listar").removeClass("hidden");
        });
    }

</script>
@endpush

<div class="modal fade" id="docs-cliente-modal-listar" tabindex="-1" role="dialog" aria-labelledby="docs-cliente-modal-listar" aria-hidden="true">
    <div class="modal-dialog  modal-big">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title" id="docs-cliente-label-listar">Listar</h3>
            </div>
            <div class="loader text-center">
                <i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>
                <span class="sr-only">Loading...</span>
            </div>

            <div class="modal-body no-border">
                <div class='row'>
                <div class='col-xs-12 margin-top hidden' id='docs-cliente-listar'>
                    <div class='table-responsive'>
                        <table class='table table-striped table-hover table-center'>
                            <thead>
                                <th id="ths"></th>
                                <th>Tipo de Doc</th>
                                <th>Id Pais</th>
                                <th><i class='fa fa-cogs fa-lg'></th>
                            </thead>
                        <tbody >
                            <tr id="tabletr">
                                
                            </tr>

                        </tbody>
                        </table>
                    </div>
                    
                </div>
                </div>
            </div>

            <div class="modal-footer " style='text-align: center;'>                
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>              
            </div>
        </div>
    </div>
</div>

