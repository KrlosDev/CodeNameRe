@push('JS')
<script>
    function editarProyecto(url){
        
        $("#casa").prop('checked', false);
        $(".loader").removeClass("hidden");
        $("#proyectos-form").addClass("hidden");
        $("[name=_method]").val("PUT");
        $("#proyectos-form").attr("action", url);
        $("#proyectos-label").html("Editar Proyecto");
        $.get(url,function(data,status){
                data=JSON.parse(data);
                $('#codigo').val(data[0].codigo);
                $('#nombre').val(data[0].nombre);
                $('#descripcion_c').val(data[0].descripcion);
                $('#estado').val(data[0].estado);

                if (data[2] != 0) {
                    $('#cantidad_c').val(data[2]);
                    $("[id=checkcasa]").show();
                    $("#casa").prop('checked', true);

                    $('#modelo').val(data[1].modelo);
                    $('#mts2_total').val(data[1].mts2_total);
                    $('#mts2_construccion').val(data[1].mts2_construccion);
                    $('#recamaras').val(data[1].recamaras);
                    $('#banos').val(data[1].banos);
                    $('#valor').val(data[1].valor);
                    $('#monto_separacion').val(data[1].monto_separacion);
                    $('#monto_abono_inicial').val(data[1].monto_abono_inicial);
                    $('#monto_mts2_adicional').val(data[1].monto_mts2_adicional);
                }
                else {
                    $("[id=checkcasa]").hide();
                }

                toggleCasa();
                $(".loader").addClass("hidden");
                $("#proyectos-form").removeClass("hidden");
            });
        $("#proyectos-modal").modal();
        $("[id=datos_casa]").hide();
        $("[id=codigo]").prop('readonly', true);


        $("#casa").change(function () {
                toggleCasa();
        });

    }


        function toggleCasa() {

        if( $("#casa").prop('checked')){
                $("#datos_casa").show();

                $("[id=modelo]").prop('required',true);
                $("[id=cantidad]").prop('required',false);
                $("[id=mts2_total]").prop('required',true);
                $("[id=mts2_construccion]").prop('required',true);
                $("[id=recamaras]").prop('required',true);
                $("[id=banos]").prop('required',true);
                $("[id=valor]").prop('required',true);
                $("[id=monto_separacion]").prop('required',true);
                $("[id=monto_abono_inicial]").prop('required',true);
                $("[id=monto_mts2_adicional]").prop('required',true);

           }
            else{
                    $("#datos_casa").hide();

                    $("[id=modelo]").prop('required',false);
                    $("[id=cantidad]").prop('required',false);
                    $("[id=mts2_total]").prop('required',false);
                    $("[id=mts2_construccion]").prop('required',false);
                    $("[id=recamaras]").prop('required',false);
                    $("[id=banos]").prop('required',false);
                    $("[id=valor]").prop('required',false);
                    $("[id=monto_separacion]").prop('required',false);
                    $("[id=monto_abono_inicial]").prop('required',false);
                    $("[id=monto_mts2_adicional]").prop('required',false);
            }
    }
</script>
@endpush

