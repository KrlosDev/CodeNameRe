@push('JS')
<script>
    function crearCliente(url){
        $("#infoCpdeudor").hide();

        $("#datos_trabajo").hide();
        
        $("#infoCoEmpresa").hide();

        $("#cliente-form-submit").prop('disabled', false);
        $("#cliente-form-submit").show();

         document.getElementById("cliente-form").reset(); 
        


        $(".loader").addClass("hidden");
        $("#cliente-form").removeClass("hidden");
        $("[name=_method]").val("POST");
        $("#clientes-label").html("Nuevo Cliente");
        $("#cliente-form").attr("action", url);  
 
        $("#cliente-modal").modal();
        $( ":input" ).prop('readonly', false);
        $( "select" ).prop('disabled', false);
        $("#codeudor").show();
        $("[id=pass]").show();
        $("[id=casaid]").show();
        $("[id=acp]").show();
        $("[id=descargarC]").hide();
        $("#codeudor").prop('disabled', false);

        $("[id=password]").prop('required',true);
        $("[id=password_confirmation]").prop('required',true);
        $('#estado_casa').show();

        $('#id_casa').select2();

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

        $('#id_pais').val(176);
    }
    
    function toggleReferenciasPersonales() {
        
        if( $("#toggle_referencias").prop('checked')){
            $("#referencias_personales").show();
        }
        else {
            $("#referencias_personales").hide();
        }
    }

    function toggleCodeudor() {

        if( $("#codeudor").prop('checked')){
                $("#infoCpdeudor").show();

                $("[id=co_nombre]").prop('required',true);
                $("[id=co_apellido]").prop('required',true);
                $("[id=co_identificacion]").prop('required',true);
                $("[id=co_email]").prop('required',false);
                $("[id=co_fecha_nacimiento]").prop('required',true);
                $("[id=co_estado_civil]").prop('required',true);
                $("[id=co_id_pais]").prop('required',true);
                $("[id=co_id_distrito]").prop('required',true);
                $("[id=co_telefono]").prop('required',false);
                $("[id=co_direccion]").prop('required',true);
                $("[id=co_casa_apartamento]").prop('required',true);
                $("[id=co_tipo_trabajo]").prop('required',true);
                $("[id=co_salario]").prop('required',true);

           }
            else{
                    $("#infoCpdeudor").hide();
                    $("[id=co_nombre]").prop('required',false);
                    $("[id=co_apellido]").prop('required',false);
                    $("[id=co_identificacion]").prop('required',false);
                    $("[id=co_email]").prop('required',false);
                    $("[id=co_fecha_nacimiento]").prop('required',false);
                    $("[id=co_estado_civil]").prop('required',false);
                    $("[id=co_id_pais]").prop('required',false);
                    $("[id=co_id_distrito]").prop('required',false);
                    $("[id=co_telefono]").prop('required',false);
                    $("[id=co_direccion]").prop('required',false);
                    $("[id=co_casa_apartamento]").prop('required',false);
                    $("[id=co_tipo_trabajo]").prop('required',false);
                    $("[id=co_salario]").prop('required',false);

                    $("[id=co_empresa]").prop('required',false);
                    $("[id=co_cargo_empresa]").prop('required',false);
                    $("[id=co_años_laborando]").prop('required',false);
                    $("[id=co_direccion_empresa]").prop('required',false);
                    $("[id=co_email_empresa]").prop('required',false);
                    $("[id=co_telefonos_empresa]").prop('required',false);

            }
    }

     function toggleRequireTrabajo() {
            
            if( $("#tipo_trabajo").val()==2){
                $("#datos_trabajo").show();
                $("#datos_trabajo :input").attr("disabled",false);
            

                $("[id=empresa]").prop('required',true);
               
                $("[id=cargo_empresa]").prop('required',true);
                $("[id=años_laborando]").prop('required',true);
                $("[id=direccion_empresa]").prop('required',true);
                $("[id=email_empresa]").prop('required',false);
                $("[id=telefonos_empresa]").prop('required',false);
           }

            else{
                $("#datos_trabajo").hide();
                $("#datos_trabajo :input").attr("disabled",true);

                $("[id=empresa]").prop('required',false);
                $("[id=cargo_empresa]").prop('required',false);
                $("[id=años_laborando]").prop('required',false);
                $("[id=direccion_empresa]").prop('required',false);
                $("[id=email_empresa]").prop('required',false);
                $("[id=telefonos_empresa]").prop('required',false);
            }
     }

    function toggleRequireCodeudorTrabajo() {
            
            if( $("#co_tipo_trabajo").val()==2){
                $("#infoCoEmpresa").show();
                $("[id=co_empresa]").prop('required',true);
                $("[id=co_cargo_empresa]").prop('required',true);
                $("[id=co_años_laborando]").prop('required',true);
                $("[id=co_direccion_empresa]").prop('required',true);
                $("[id=co_email_empresa]").prop('required',false);
                $("[id=co_telefonos_empresa]").prop('required',false);
           }

            else{
                $("#infoCoEmpresa").hide();
                $("[id=co_empresa]").prop('required',false);
                $("[id=co_cargo_empresa]").prop('required',false);
                $("[id=co_años_laborando]").prop('required',false);
                $("[id=co_direccion_empresa]").prop('required',false);
                $("[id=co_email_empresa]").prop('required',false);
                $("[id=co_telefonos_empresa]").prop('required',false);
            }
     }
    
</script>
@endpush

<div class="modal fade" id="cliente-modal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-big" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="clientes-label"></h4>
            </div>
            <div class="loader text-center">
                <i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>
                <span class="sr-only">Loading...</span>
            </div>
            <form class="form-horizontal hidden" method="POST" id='cliente-form' >
                {!! csrf_field() !!}
                <input type="hidden" name="_method" value="POST">
                

                <div class="modal-body">
                        <h4 id="pp">Datos de Usuario</h4>

                <div class="content table-full-width">
                    <table class="table table-striped table-striped-clarito" >
                        <thead>
                            <th></th>
                        </thead>

                        <tbody>

                            <tr>
                                <td>
                                    <div class="form-group">
                                        <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                            <label for="name">Nombre de Usuario</label>
                                            <input type="text" class="form-control" name="name" id="name" maxlength="{{\App\User::MAX_LENGTH_USERNAME}}" required />
                                        </div>

                                        <div class="col-lg-4 col-sm-6 col-xs-12">
                                            <label for="email">Correo</label>
                                            <input type="email" class="form-control" name="email" id="email" maxlength="{{\App\User::MAX_LENGTH_EMAIL}}" />
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <div class="form-group" id="pass">
                                        <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                            <label for="password">Contraseña</label>
                                            <input class="form-control" name="password" type="password" id="password"  maxlength="100">
                                        </div>

                                        <div class="col-lg-4 col-sm-6 col-xs-12">
                                            <label for="password_confirmation">Confirmar contraseña</label>
                                            <input class="form-control" name="password_confirmation" type="password" id="password_confirmation"  maxlength="100">
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr id="casaid">
                                <td>
                                    <div class="form-group">
                                        <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12" >
                                            <label for="id_casa">Propiedades</label>

                                            <select class="form-control select2" name="id_casa" id="id_casa" style="width:100%;" >
                                                    <option value="">Ninguna</option>
                                                @foreach($casas as $casa)
                                                    @if(old('id_casa') == $casa->id)
                                                    <option value="{{$casa->id}}" selected>{{$casa->codigo}}</option>
                                                    @else
                                                    <option value="{{$casa->id}}">{{$casa->codigo}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-4 col-sm-6 col-xs-12" >
                                            <label for="estado_casa">Estado Trámite</label>
                                            <select class="form-control select2" name="estado_casa" id="estado_casa">
                                                @foreach($estados as $estado)
                                                    @if(old('estado_casa') == $estado->id)
                                                    <option value="{{$estado->id}}" selected>{{$estado->nombre}}</option>
                                                    @else
                                                    <option value="{{$estado->id}}">{{$estado->nombre}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <hr>

                            <tr>
                            <td>
                            <h4>Datos de Personales de Cliente</h4>
                            </td>
                            </tr>


                            <tr>
                                <td>
                                    <div class="form-group">
                                        <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                            <label for="nombre">Nombre</label>
                                            <input type="text" class="form-control" name="nombre" id="nombre" maxlength="{{\App\User::MAX_LENGTH_NOMBRE}}" required />
                                        </div>

                                        <div class="col-lg-4 col-sm-6 col-xs-12">
                                            <label for="apellido">Apellido</label>
                                            <input type="text" class="form-control" name="apellido" id="apellido" maxlength="{{\App\Models\Cliente::MAX_LENGTH_APELLIDO}}" required />
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <div class="form-group">
                                        <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                            <label for="identificacionc">Identificación</label>
                                            <input type="text" class="form-control" name="identificacionc" id="identificacionc" maxlength="{{\App\Models\Cliente::MAX_LENGTH_IDENTIFICACION}}" required />
                                        </div>
                                        <div class="col-lg-4 col-sm-6 col-xs-12">
                                            <label for="telefono">Teléfono de Personal</label>
                                            <input type="text" class="form-control" name="telefono" id="telefono" maxlength="{{\App\Models\Cliente::MAX_LENGTH_TELEFONO}}" required />
                                        </div>

                                    </div>
                                </td>
                            </tr>



                            <tr>
                                <td>
                                    <div class="form-group">
                                        <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                            <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                                            <input type="date" class="form-control" name="fecha_nacimiento" id="fecha_nacimiento" required />
                                        </div>
                                        <div class="col-lg-4 col-sm-6 col-xs-12">
                                            <label for="estado_civil">Estado Civil</label>
                                            <select class="form-control" id="estado_civil" name="estado_civil">
                                                @foreach($estados_civiles as $key => $value)
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
                                        <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                            <label for="id_pais">País</label>
                                            <select class="form-control" id="id_pais" name="id_pais">
                                                @foreach($paises as $key => $value)
                                                    <option value="{{$key}}">{{$value}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-4 col-sm-6 col-xs-12">
                                            <label for="id_distrito">Distrito</label>
                                            <select class="form-control" id="id_distrito" name="id_distrito">
                                                @foreach($distritos as $key => $value)
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
                                        <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                            <label for="direccion">Dirección</label>
                                            <input type="text" class="form-control" name="direccion" id="direccion" maxlength="{{\App\Models\Cliente::MAX_LENGTH_DIRECCION}}" />
                                        </div>
                                        <div class="col-lg-4 col-sm-6 col-xs-12">
                                            <label for="casa_apartamento">Casa/Apartamento</label>
                                            <select class="form-control" id="casa_apartamento" name="casa_apartamento">
                                                @foreach($casapto as $key => $value)
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
                                        <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                            <label for="id_banco">Banco</label>
                                            <select class="form-control" id="id_banco" name="id_banco">
                                                @foreach($bancos as $key => $value)
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
                                        <div class="col-lg-8 col-lg-offset-2 col-sm-12 col-xs-12">
                                            <label for="notas">Notas</label>
                                            <textarea class="form-control" id="notas" name="notas" maxlength="{{\App\Models\Cliente::MAX_LENGTH_NOTAS}}" rows="5"></textarea>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <h4>Agregar referencias personales <input type='checkbox' id='toggle_referencias' /></h4>
                                </td>
                            </tr>

                        </tbody>
                        <tbody id="referencias_personales">
                            @foreach($referencias as $referencia)
                            <tr>
                                <td>
                                    <div class="col-lg-10 col-lg-offset-2 col-sm-12 col-xs-12 no-padding">
                                        <h4>Referencia {{$referencia->numero}}</h4>
                                    </div>
                                    <div class="form-group">
                                        <input type='hidden' id='ref_id_{{$referencia->numero}}' name='ref_id_{{$referencia->numero}}' value='' />
                                        <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                            <label for="{{'ref_nombre_'.$referencia->numero}}">Nombre</label>
                                            <input type="text" class="form-control" name="{{'ref_nombre_'.$referencia->numero}}" id="{{'ref_nombre_'.$referencia->numero}}" maxlength="{{\App\Models\ReferenciaPersonal::MAX_LENGTH_NOMBRE}}" />
                                        </div>

                                        <div class="col-lg-4 col-sm-6 col-xs-12">
                                            <label for="{{'ref_parentesco_'.$referencia->numero}}">Parentesco</label>
                                            <input type="text" class="form-control" name="{{'ref_parentesco_'.$referencia->numero}}" id="{{'ref_parentesco_'.$referencia->numero}}" maxlength="{{\App\Models\ReferenciaPersonal::MAX_LENGTH_PARENTESCO}}" />
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <div class="form-group">
                                        <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                            <label for="{{'ref_telefono_'.$referencia->numero}}">Teléfono</label>
                                            <input type="text" class="form-control" name="{{'ref_telefono_'.$referencia->numero}}" id="{{'ref_telefono_'.$referencia->numero}}" maxlength="{{\App\Models\ReferenciaPersonal::MAX_LENGTH_TELEFONO}}" />
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach

                    </tbody>
                    </body>
                            <tr>
                                <td>
                                    <h4>Datos de Trabajo</h4>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <div class="form-group">
                                        <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                            <label for="tipo_trabajo">Tipo Trabajo</label>
                                            <select class="form-control" id="tipo_trabajo" name="tipo_trabajo" required>
                                                @foreach($tipoTrabajo as $key => $value)
                                                    <option value="{{$key}}">{{$value}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-lg-4 col-sm-6 col-xs-12">
                                            <label for="salario">Salario</label>
                                            <input type="number" class="form-control" name="salario" id="salario" min="0" step="0.001" required />
                                        </div>
                                    </div>
                                </td>
                            </tr>

                    </tbody>
                    <tbody id="datos_trabajo">

                            <tr>
                                <td>
                                    <div class="form-group">
                                        <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                            <label for="empresa">Empresa</label>
                                            <input type="text" class="form-control" name="empresa" id="empresa" maxlength="{{\App\Models\Cliente::MAX_LENGTH_EMPRESA_NOMBRE}}" placeholder="Nombre de la empresa" />
                                        </div>

                                        <div class="col-lg-4 col-sm-6 col-xs-12">
                                            <label for="cargo_empresa">Cargo empresa (Ocupación)</label>
                                            <input type="text" class="form-control" name="cargo_empresa" id="cargo_empresa" maxlength="{{\App\Models\Cliente::MAX_LENGTH_EMPRESA_CARGO}}" placeholder="Cargo en la empresa" />
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <div class="form-group">
                                        <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                            <label for="años_laborando">Años laborando</label>
                                            <input type="number" class="form-control" name="años_laborando" id="años_laborando" min="0" max="120" step="0.01"/>
                                        </div>
                                        <div class="col-lg-4 col-sm-6 col-xs-12">
                                            <label for="direccion_empresa">Dirección de Empresa</label>
                                            <input type="text" class="form-control" name="direccion_empresa" id="direccion_empresa" maxlength="{{\App\Models\Cliente::MAX_LENGTH_EMPRESA_DIRECCION}}" />
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <div class="form-group">
                                        <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                            <label for="email_empresa">Email de Empresa</label>
                                            <input type="email" class="form-control" name="email_empresa" id="email_empresa" maxlength="{{\App\Models\Cliente::MAX_LENGTH_EMPRESA_EMAIL}}" />
                                        </div>
                                        <div class="col-lg-4 col-sm-6 col-xs-12">
                                            <label for="telefonos_empresa">Teléfono de Empresa</label>
                                            <input type="text" class="form-control" name="telefonos_empresa" id="telefonos_empresa" maxlength="{{\App\Models\Cliente::MAX_LENGTH_EMPRESA_TELEFONO}}" />
                                        </div>
                                    </div>
                                </td>
                            </tr>
                    </tbody>
                </table>

            <div class="form-group">
                <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                    <label for="codeudor">Añadir Codeudor</label>
                    <input type="checkbox" name="codeudor" id="codeudor" />
                </div>

            </div>
                

                <table class="table table-striped table-striped-clarito" id="infoCpdeudor">
                        <thead>
                            <th></th>
                        </thead>

                        <tbody>
                
                        
                        <tr>
                            <td>
                                <h4>Datos de Codeudor</h4>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <div class="form-group">
                                    <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                        <label for="co_nombre">Nombre</label>
                                        <input type="text" class="form-control" name="co_nombre" id="co_nombre" maxlength="{{\App\Models\Codeudor::MAX_LENGTH_NOMBRE}}" />
                                    </div>

                                    <div class="col-lg-4 col-sm-6 col-xs-12">
                                        <label for="co_apellido">Apellido</label>
                                        <input type="text" class="form-control" name="co_apellido" id="co_apellido" maxlength="{{\App\Models\Codeudor::MAX_LENGTH_APELLIDO}}" />
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <div class="form-group">
                                    <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                        <label for="co_identificacion">Identificación</label>
                                        <input type="text" class="form-control" name="co_identificacion" id="co_identificacion" maxlength="{{\App\Models\Codeudor::MAX_LENGTH_IDENTIFICACION}}" />
                                    </div>
                                    <div class="col-lg-4 col-sm-6 col-xs-12">
                                        <label for="co_email">Email</label>
                                        <input type="email" class="form-control" name="co_email" id="co_email" maxlength="{{\App\Models\Codeudor::MAX_LENGTH_EMAIL}}" />
                                    </div>

                                </div>
                            </td>
                        </tr>
            
                        <tr>
                            <td>
                                <div class="form-group">
                                    <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                        <label for="co_fecha_nacimiento">Fecha de Nacimiento</label>
                                        <input type="date" class="form-control" name="co_fecha_nacimiento" id="co_fecha_nacimiento" />
                                    </div>
                                    <div class="col-lg-4 col-sm-6 col-xs-12">
                                        <label for="co_estado_civil">Estado Civil</label>
                                        <select class="form-control" id="co_estado_civil" name="co_estado_civil" required>
                                            @foreach($estados_civiles as $key => $value)
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
                                    <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                        <label for="co_id_pais">País</label>
                                        <select class="form-control" id="co_id_pais" name="co_id_pais" required>
                                            @foreach($paises as $key => $value)
                                                <option value="{{$key}}">{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-4 col-sm-6 col-xs-12">
                                        <label for="co_id_distrito">Distrito</label>
                                        <select class="form-control" id="co_id_distrito" name="co_id_distrito" required>
                                            @foreach($distritos as $key => $value)
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
                                    <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                        <label for="co_direccion">Dirección</label>
                                        <input type="text" class="form-control" name="co_direccion" id="co_direccion" maxlength="{{\App\Models\Codeudor::MAX_LENGTH_DIRECCION}}" />
                                    </div>
                                    <div class="col-lg-4 col-sm-6 col-xs-12">
                                        <label for="co_casa_apartamento">Casa/Apartamento</label>
                                        <select class="form-control" id="co_casa_apartamento" name="co_casa_apartamento" required>
                                            @foreach($casapto as $key => $value)
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
                                    <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                        <label for="co_telefono">Teléfono</label>
                                        <input type="text" class="form-control" name="co_telefono" id="co_telefono" maxlength="{{\App\Models\Codeudor::MAX_LENGTH_TELEFONO}}" />
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <h4>Datos de Trabajo del Codeudor</h4>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <div class="form-group">
                                    <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                        <label for="co_tipo_trabajo">Tipo Trabajo</label>
                                        <select class="form-control" id="co_tipo_trabajo" name="co_tipo_trabajo" required>
                                            @foreach($tipoTrabajo as $key => $value)
                                                <option value="{{$key}}">{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-4 col-sm-6 col-xs-12">
                                        <label for="co_salario">Salario</label>
                                        <input type="number" class="form-control" name="co_salario" id="co_salario" min="0" step="0.01" />
                                    </div>
                                </div>
                            </td>
                        </tr>

                        </tbody>
                        <tbody id="infoCoEmpresa">
                

                            <tr>
                                <td>
                                    <div class="form-group">
                                        <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                            <label for="co_empresa">Empresa</label>
                                            <input type="text" class="form-control" name="co_empresa" id="co_empresa" maxlength="{{\App\Models\Codeudor::MAX_LENGTH_EMPRESA_NOMBRE}}" />
                                        </div>

                                        <div class="col-lg-4 col-sm-6 col-xs-12">
                                            <label for="co_cargo_empresa">Cargo</label>
                                            <input type="text" class="form-control" name="co_cargo_empresa" id="co_cargo_empresa" maxlength="{{\App\Models\Codeudor::MAX_LENGTH_EMPRESA_CARGO}}" />
                                        </div>

                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <div class="form-group">
                                        <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                            <label for="co_años_laborando">Años Laborando</label>
                                            <input type="number" class="form-control" name="co_años_laborando" id="co_años_laborando" min="0" max="120" step="0.01" />
                                        </div>
                                        <div class="col-lg-4 col-sm-6 col-xs-12">
                                            <label for="co_direccion_empresa">Dirección de Empresa</label>
                                            <input type="text" class="form-control" name="co_direccion_empresa" id="co_direccion_empresa" maxlength="{{\App\Models\Codeudor::MAX_LENGTH_EMPRESA_DIRECCION}}" />
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <div class="form-group">
                                        <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                            <label for="co_email_empresa">Email de Empresa</label>
                                            <input type="email" class="form-control" name="co_email_empresa" id="co_email_empresa" maxlength="{{\App\Models\Codeudor::MAX_LENGTH_EMPRESA_EMAIL}}" />
                                        </div>
                                        <div class="col-lg-4 col-sm-6 col-xs-12">
                                            <label for="co_telefonos_empresa">Teléfono  de Empresa</label>
                                            <input type="text" class="form-control" name="co_telefonos_empresa" id="co_telefonos_empresa" maxlength="{{\App\Models\Codeudor::MAX_LENGTH_EMPRESA_TELEFONOS}}" />
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>  <!-- Segundo Table  --> 
                    </div> <!-- content table full width -->
                </div>

                <div class="modal-footer" style='text-align: center;'>
                    <button type="submit" class="btn btn-primary" id="cliente-form-submit">Aceptar</button>

                    @can('getAll', 'App\Models\Cliente')
                        <a href="" title="descargarC" id="descargarC" class="button btn btn-success"> Descargar</a>
                    @endcan

                    <button type="button" class="btn btn-default" data-dismiss="modal" title="Cancelar">Cancelar</button>
                </div>

            </form>
        </div>
    </div>
</div>
