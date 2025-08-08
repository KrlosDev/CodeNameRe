@push('JS')
<script>
    function editarCliente(url){
        document.getElementById("cliente-form").reset(); 
        $(".loader").removeClass("hidden");
        $("#cliente-form").addClass("hidden");
        $("[name=_method]").val("PUT");
        $("#cliente-form").attr("action", url)
        $("#cliente-label").html("Editar Cliente");
        $( ":input" ).prop('readonly', false);
        $( "select" ).prop('disabled', false);
        $("[id=name]").prop('readonly', true);
        $("[id=casaid]").hide();
        $("[id=acp]").show();
        $("#codeudor").show();
        $("[id=pass]").show();        
        $("[id=password]").prop('required',false);
        $("[id=password_confirmation]").prop('required',false);
                $("[id=descargarC]").hide();

        $('#id_casa').select2();
        
        $("#codeudor").prop('disabled', false);
        $('#estado_casa').hide();

        $("#cliente-form-submit").prop('disabled', false);
        $("#cliente-form-submit").show();

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
                if(data.telefonocliente[0])
                    $('#telefono').val(data.telefonocliente[0].telefono);
                $('#id_distrito').val(data.id_distrito);
                $('#direccion').val(data.direccion);
                $('#notas').val(data.notas);
                $('#casa_apartamento').val(data.casa_apartamento);
                $('#tipo_trabajo').val(data.tipo_trabajo);
                $('#salario').val(data.salario);
                if(data.id_banco)
                    $('#id_banco').val(data.id_banco);
                
                if(data.empresa)
                    $('#empresa').val(data.empresa);
                if (data.cargo_empresa)
                    $('#cargo_empresa').val(data.cargo_empresa);
                if (data.anios_laborando)
                    $('#años_laborando').val(data.anios_laborando);
                if (data.direccion_empresa)
                    $('#direccion_empresa').val(data.direccion_empresa);
                if (data.telefonos_empresa)
                    $('#telefonos_empresa').val(data.telefonos_empresa);
                if (data.email_empresa)
                    $('#email_empresa').val(data.email_empresa);
                
                //Referencias Personales
                if(data.referencias_personales && data.referencias_personales.length > 0) {
                    $("#toggle_referencias").prop('checked',true);
                    toggleReferenciasPersonales();
                    for(var i = 0; i < data.referencias_personales.length; i++) {
                        $('#ref_id_'+(i+1)).val(data.referencias_personales[i].id);
                        $('#ref_nombre_'+(i+1)).val(data.referencias_personales[i].nombre);
                        $('#ref_parentesco_'+(i+1)).val(data.referencias_personales[i].parentesco);
                        $('#ref_telefono_'+(i+1)).val(data.referencias_personales[i].telefono);
                    }
                }
                
                // codeudor
                if (data.codeudor.length != 0) {
                    $("#codeudor").prop('checked', true);                  
                    $('#co_nombre').val(data.codeudor[0].nombre);
                    $('#co_apellido').val(data.codeudor[0].apellido);
                    $('#co_identificacion').val(data.codeudor[0].identificacion);
                    $('#co_fecha_nacimiento').val(data.codeudor[0].fecha_nacimiento);
                    $('#co_estado_civil').val(data.codeudor[0].estado_civil);
                    $('#co_id_pais').val(data.codeudor[0].id_pais);
                  
                    $('#co_id_distrito').val(data.codeudor[0].id_distrito);
                    $('#co_telefono').val(data.codeudor[0].telefono);
                    $('#co_direccion').val(data.codeudor[0].direccion);
                    $('#co_casa_apartamento').val(data.codeudor[0].casa_apartamento);
                    $('#co_tipo_trabajo').val(data.codeudor[0].tipo_trabajo);
                    $('#co_empresa').val(data.codeudor[0].empresa);
                    $('#co_cargo_empresa').val(data.codeudor[0].cargo_empresa);
                    $('#co_salario').val(data.codeudor[0].salario);
                    $('#co_años_laborando').val(data.codeudor[0].anios_laborando);
                    $('#co_direccion_empresa').val(data.codeudor[0].direccion_empresa);
                    $('#co_telefonos_empresa').val(data.codeudor[0].telefonos_empresa);
                    $('#co_email_empresa').val(data.codeudor[0].email_empresa);
                    $('#co_email').val(data.codeudor[0].email);
                } 
                else 
                    $("#codeudor").prop('checked', false);                  

                toggleCodeudor();
                toggleRequireTrabajo();
                toggleRequireCodeudorTrabajo();
                $(".loader").addClass("hidden");
                $("#cliente-form").removeClass("hidden");
            });
        $("#cliente-modal").modal();


        toggleCodeudor();
        toggleRequireTrabajo();
        toggleRequireCodeudorTrabajo();
        toggleReferenciasPersonales();

        $("#codeudor").change(function () {
                toggleCodeudor();
        });

        
        $("#tipo_trabajo").change(function () {
                toggleRequireTrabajo();
        });

        $("#co_tipo_trabajo").change(function () {
                toggleRequireCodeudorTrabajo();
        });

        $("#toggle_referencias").change(function () {
                toggleReferenciasPersonales();
        });
            
    }
</script>
@endpush