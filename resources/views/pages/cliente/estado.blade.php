@push('JS')
<script>
    function anadirStatus(url,url2,url3,id_cliente){

        $(".loader").removeClass("hidden");
        $("#status-form-asignar").removeClass("hidden");
        $("[name=_method]").val("POST");
        $("#status-label-asignar").html("Actualizar Status de Tramite");
        $("#status-form-asignar").attr("action", url);
        $("#id_cliente").val(id_cliente);
        $("[id=st]").hide();

       
        $.get(url2,function(data,status){
                data=JSON.parse(data);

                if (!(data.length==0)) {
                    $("[id=st]").show();
                }
    
                $("#casas").empty();
                for (i=0; i< data.length; i++){ 
                                       
                    $("#casas").append('<option value='+data[i].id_casa+'>'+data[i].codigo_casa+'</option>');
                };
                selectEstatus(url3);
                $(".loader").addClass("hidden");
                $("#status-form-asignar").removeClass("hidden");
                
        });

  
        
        $("#status-modal-asignar").modal();  
        $( ":input" ).prop('readonly', false);
        $( "select" ).prop('disabled', false);
        
        
        $("#casas").change(function () {
        selectEstatus(url3);
        });
    }

        function selectEstatus(url) {
           var selection = $("[id=casas]").val();
           var nurl = url+"/"+selection;
          // $("#pp").html(nurl);
           $.get(nurl,function(data,status){
                data=JSON.parse(data);
                if (!(data.length==0)) {
                    if(!(data.id_casa_estado==0)){
                       // $("#pp").html(data.id_casa_estado);
                        $("#status").val(data.id_casa_estado);
                    }
                    else{
                         $("#status").val(1);
                    }
                } 
        });            
    }


    
</script>
@endpush

<div class="modal fade" id="status-modal-asignar" tabindex="-1" role="dialog" aria-labelledby="status-modal-asignar" aria-hidden="true">
    <div class="modal-dialog  modal-big">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title" id="status-label-asignar">Asignar</h3>
            </div>
            <div class="loader text-center">
                <i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>
                <span class="sr-only">Loading...</span>
            </div>

                <form class="form-horizontal margin-top hidden" method="POST" id='status-form-asignar'>
                    {!! csrf_field() !!}
                    <input type="hidden" name="_method" value="POST">
                    <input type="hidden" name="id_cliente" id="id_cliente" value="">
                    

            <div class="modal-body">
                <p id="pp">Seleccione las opciones deseadas para asignar el Status Correspondiente</p>
                    <div class="form-group">
                        <div class="col-sm-6 col-xs-12 margin-top">
                            <label for="casas">Propiedades</label>
                            <select class="form-control" name="casas" id="casas" required></select>
                        </div>
                        
                        <div id="st">
                        <div class="col-sm-6 col-xs-12 margin-top">
                            <label for="status">Status</label>
                            <select class="form-control" name="status" id="status">
                                @foreach($estados as $estado)
                                    <option value="{{$estado->id}}">{{$estado->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                        </div>

                    </div>
                
            </div>

            <div class="modal-footer" style='text-align: center;'>
                <button class="btn btn-primary" id="btn-action" >Asignar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
            </div>
            </form>
        </div>
    </div>
</div>