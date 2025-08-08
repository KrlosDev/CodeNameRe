@push('JS')
<script>
    function showCliente(url,url2){
        $(".loader").removeClass("hidden");
        $("#cliente-form").addClass("hidden");
        $("[name=_method]").val("PUT");
        $("#cliente-form").attr("action", url)
        $("#cliente-label").html("Mostrar Cliente");
        $( ":input" ).prop('readonly', true);
        $("#cliente-form-submit").prop('disabled', true);
        $("#cliente-form-submit").hide();
        $( "select" ).prop('disabled', true);
        $("[id=pass]").hide();
        $("#descargarC").attr("href", url2); 
        $("[id=casaid]").hide();
        $("[id=acp]").hide();
        $("[id=descargarC]").show();
        
        $("#codeudor").prop('disabled', true);

        $.get(url,function(data,status){
                data=JSON.parse(data);
                console.log(data);

                $('#name').val(data.user.name); 
                $('#nombre').val(data.user.nombre);

                $('#email').val(data.user.email);

                $('#apellido').val(data.apellido);
                $('#identificacionc').val(data.identificacion);
                $('#fecha_nacimiento').val(data.fecha_nacimiento);
                $('#estado_civil').val(data.estado_civil);
                $('#id_pais').val(data.id_pais);
                $('#telefono').val(data.telefonocliente[0].telefono);
                $('#id_distrito').val(data.id_distrito);
                $('#direccion').val(data.direccion);
                $('#notas').val(data.notas);
                $('#casa_apartamento').val(data.casa_apartamento);
                $('#tipo_trabajo').val(data.tipo_trabajo);
                $('#salario').val(data.salario);
                if(data.id_banco)
                    $('#id_banco').val(data.id_banco);
                if (data.tipo_trabajo == 2) {
                    $("#datos_trabajo").show();
                    $('#empresa').val(data.empresa);
                    $('#cargo_empresa').val(data.cargo_empresa);                
                    $('#años_laborando').val(data.anios_laborando);
                    $('#direccion_empresa').val(data.direccion_empresa);
                    $('#telefonos_empresa').val(data.telefonos_empresa);
                    $('#email_empresa').val(data.email_empresa);
                }
                else if (data.tipo_trabajo == 1) {
                    $("#datos_trabajo").hide();
                }
                
                //Referencias Personales
                if(data.referencias_personales && data.referencias_personales.length > 0) {
                    for(var i = 0; i < data.referencias_personales.length; i++) {
                        $('#ref_nombre_'+(i+1)).val(data.referencias_personales[i].nombre);
                        $('#ref_parentesco_'+(i+1)).val(data.referencias_personales[i].parentesco);
                        $('#ref_telefono_'+(i+1)).val(data.referencias_personales[i].telefono);
                    }
                }
                
                // codeudor
                if (data.codeudor.length != 0) {
                    $("#infoCpdeudor").show(); 
                      
                    $('#co_nombre').val(data.codeudor[0].nombre);
                    $('#co_apellido').val(data.codeudor[0].apellido);
                    $('#co_identificacion').val(data.codeudor[0].identificacion);
                    $('#co_fecha_nacimiento').val(data.codeudor[0].fecha_nacimiento);
                    $('#co_estado_civil').val(data.codeudor[0].estado_civil);
                    $('#co_id_pais').val(data.codeudor[0].id_pais);
                    $('#co_email').val(data.codeudor[0].email);

                    $('#co_id_distrito').val(data.codeudor[0].id_distrito);
                    $('#co_direccion').val(data.codeudor[0].direccion);
                    $('#co_casa_apartamento').val(data.codeudor[0].casa_apartamento);
                    $('#co_tipo_trabajo').val(data.codeudor[0].tipo_trabajo);
                    $('#co_salario').val(data.codeudor[0].salario);

                    if (data.codeudor[0].tipo_trabajo == 2) {
                        $("#infoCoEmpresa").show();
                        $('#co_empresa').val(data.codeudor[0].empresa);
                        $('#co_cargo_empresa').val(data.codeudor[0].cargo_empresa);
                       
                        $('#co_años_laborando').val(data.codeudor[0].anios_laborando);
                        $('#co_direccion_empresa').val(data.codeudor[0].direccion_empresa);
                        $('#co_telefonos_empresa').val(data.codeudor[0].telefonos_empresa);
                        $('#co_email_empresa').val(data.codeudor[0].email_empresa);
                    }   
                    else if (data.codeudor[0].tipo_trabajo == 1) {
                        $("#infoCoEmpresa").hide();                    
                    } 
                }
                else 
                    $("#infoCpdeudor").hide(); 

                $(".loader").addClass("hidden");
                $("#cliente-form").removeClass("hidden");
            });
        $("#cliente-modal").modal();

        $("#cliente-modal").on("hidden.bs.modal", function () {
            $( ":input" ).prop('readonly', false);
            $( "select" ).prop('disabled', false);
        });
    }
</script>
@endpush
