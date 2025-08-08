@push('JS')
<script>
    function editarPago(url){  
      document.getElementById("pago-form").reset();
        $(".loader").removeClass("hidden");
        $("#pago-form").addClass("hidden");
        $("[name=_method]").val("PUT");
        $("#pago-form").attr("action", url);
        $("#pago-label").html("Editar Pago");
        $.get(url,function(data,status){
                data=JSON.parse(data);
        
                $("#casapago").html('<option value='+data[0].id_casa+'>'+data[1]+'</option>');
                $('#id_forma_pago').val(data[0].id_forma_pago);
                $('#id_tipo_transaccion').val(data[0].id_tipo_transaccion);
                $('#monto').val(data[0].monto);
                $('#descripcion').val(data[0].descripcion);
                $('#realizado_at').val(data[0].realizado_at);
                
                $(".loader").addClass("hidden");
                $("#pago-form").removeClass("hidden");
            });
        $("#pago-modal").modal();
        
        

    }
</script>
@endpush

