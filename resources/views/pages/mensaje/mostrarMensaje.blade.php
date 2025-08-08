
<script>
    function showMensaje(url){

       

        $(".loader").removeClass("hidden");
        $("#mensaje-form").addClass("hidden");
        $("[name=_method]").val("PUT");
        $("#mensaje-form").attr("action", url)
        $("#mensaje-label").html("Mensaje");
                $("[id=cl]").show();
                $("[id=titulo]").prop('readonly', true);
                $("[id=cliente]").prop('readonly', true);
                $("[id=descripcion]").prop('readonly', true);
               $("[id=aceptar]").hide();
               $("[id=paragroup]").hide();
                $("[id=tele]").show();
                $("[id=telefonoM]").prop('readonly', true);


        $.get(url,function(data,status){
                data=JSON.parse(data);

                $('#cliente').val(data.nombre);
                $('#titulo').val(data.titulo);
                $('#descripcion').val(data.descripcion);
                 $("[id=telefonoM]").val(data.telefono);
            
                // if(data.leido_broker == 0){
                //    // 
                //    var d=$('#nmsjs').html()-1;
                //    $('#descripcion').val(d);
                //    $('#nmsjs').html(d);
                // }
                // else{
                //     $('#descripcion').val("");
                // }


                $(".loader").addClass("hidden");
                $("#mensaje-form").removeClass("hidden");


            });



        $("#mensaje-modal").modal();




            
    }
</script>

