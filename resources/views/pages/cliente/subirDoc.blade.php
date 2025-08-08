@push('JS')
<script>
    function subirDocumento(url,cliente){
        $(".loader").addClass("hidden");
        $("#subir-docs-form").removeClass("hidden");
        $("[name=_method]").val("POST");
        $("#subir-docs-label").html("Nuevo Documento");
        $("#subir-docs-form").attr("action", url);  
        $("#clienteD").val(cliente);
        $("#subir-docs-modal").modal();

        $( ":input" ).prop('readonly', false);
        $( "select" ).prop('disabled', false);

        
        $("[id=id_documento]").change(function () {
	        toggleFields();
	});
        
           $('#imagen').change(function (){
                var sizeByte = this.files[0].size;
                var siezekiloByte = parseInt(sizeByte / 1024);

                if(siezekiloByte > $(this).attr('size')){
                    alert('El tamaño supera el limite permitido');
                    $(this).val('');
                }
            });
        
        
    }


	function toggleFields() {
		   var selection = $("[id=id_documento]").val();
		   if(selection == 1){
                       $("[id=identificacion]").show();
                   }
			
                   else{ 
                        $("[id=id_pais_identificacion]").val(0);		
                       $("[id=identificacion]").hide();
		    }
	}

	var loadFile = function(event) {
    	var output = document.getElementById('output');
    	output.src = URL.createObjectURL(event.target.files[0]);
  	};
</script>
@endpush

<div class="modal fade" id="subir-docs-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-big" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="subir-docs-label"></h4>
            </div>
            <div class="loader text-center">
                <i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>
                <span class="sr-only">Loading...</span>
            </div>
            <form class="form-horizontal hidden" method="POST" id='subir-docs-form' enctype="multipart/form-data" >
                {!! csrf_field() !!}
                <input type="hidden" name="_method" value="POST">
               	<input type="hidden" name="clienteD" id="clienteD" value="">
                

                <div class="modal-body">
                
                <div class="content table-full-width">

                    <table class="table table-striped table-striped-clarito" >
                        <thead>
                            <th></th>
                        </thead>

                        <tbody>
                            <h4 id="pas">Documentos</h4>
                            <tr>
                                <td>
                                    <div class="form-group">
                                        <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                            <label for="usuario">Cliente</label>
                                            <input type="radio" name="usuario" id="usuario" value="1" selected>
                                            <label for="usuario">Codeudor</label>
                                            <input type="radio" name="usuario" id="usuario" value="2" selected>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <div class="form-group">
                                        <div class="col-lg-3 col-lg-offset-1 col-sm-3 col-xs-12">
                                            <label for="id_documento">Tipo de Documentos</label>
                                            <select class="form-control" name="id_documento" id="id_documento">
                                                @foreach($tipo_documentos as $key => $value)
                                                    <option value="{{$key}}">{{$value}}</option>
                                                @endforeach
                                            </select>
                                        </div>


                                        <div id="identificacion">
                                            <div class="col-lg-3 col-sm-3 col-xs-12">
                                                <label for="id_pais_identificacion">Pais de Identificación</label>
                                                <select class="form-control" name="id_pais_identificacion" id="id_pais_identificacion">
                                                    @foreach($paises as $key => $value)
                                                        <option value="{{$key}}">{{$value}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div>
                                            <div class="col-lg-3 col-sm-3 col-xs-12">
                                                <label for="fecha_expiracion">Fecha de Expiración</label>
                                                <input required type="date" class="form-control" name="fecha_expiracion" id="fecha_expiracion" />
                                            </div>
                                        </div>

                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <div class="form-group">
                                        <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                            <label for="imagen">Seleccione el documento</label>
                                            <input type="file" class="form-control" name="imagen" id="imagen" accept=".jpeg,.jpg,.pdf,.txt, .doc,.docx, .xls, .xlsx" size="8192" required />
                                        </div>
                                    </div>
                                </td>
                            </tr>

                        </tbody>
                    </table>  <!-- Table  --> 
                    </div> <!-- content table full width -->

                </div>     

                <div class="modal-footer" style='text-align: center;'>
                    <button type="submit" class="btn btn-primary">Aceptar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" title="Cancelar">Cancelar</button>
                </div>

            </form>
        </div>
    </div>
</div>