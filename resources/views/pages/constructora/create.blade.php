 @push('JS')
<script>
    function crearCronstructora(url){
        document.getElementById("constructoras-form").reset();   
        $(".loader").addClass("hidden");
        $("#constructoras-form").removeClass("hidden");
        $("[name=_method]").val("POST");
        $("#constructoras-label").html("Nueva Constructora");
        $("#constructoras-form").attr("action", url);  
        $("#constructoras-modal").modal();
        $("[id=name]").prop('readonly', false);

        $("[id=password]").prop('required',true);
        $("[id=password_confirmation]").prop('required',true);

    }
    
</script>
@endpush

<div class="modal fade" id="constructoras-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-big" role="document" >
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="constructoras-label"></h4>
            </div>
            <div class="loader text-center">
                <i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>
                <span class="sr-only">Loading...</span>
            </div>
            <form class="form-horizontal hidden" method="POST" id='constructoras-form' >
                {!! csrf_field() !!}
                <input type="hidden" name="_method" value="POST">
                

                <div class="modal-body">

                <div class="content table-full-width">

                    <table class="table table-striped table-striped-clarito" >

                        <thead>
                            <th></th>
                        </thead>

                        <tbody>
            
                            <h4>Datos de Usuario</h4>

                            <tr>
                                <td>
                                    <div class="form-group">

                                        <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                            <label for="name">Nombre de Usuario</label>
                                            <input type="text" class="form-control" name="name" id="name" maxlength="{{\App\User::MAX_LENGTH_USERNAME}}" required />
                                        </div>

                                        <div class="col-lg-4 col-sm-6 col-xs-12">
                                            <label for="nombre">Nombre</label>
                                            <input type="text" class="form-control" name="nombre" id="nombre" maxlength="{{\App\User::MAX_LENGTH_NOMBRE}}" required />
                                        </div>
                                    </div>
                                </td>
                            </tr>                        

                            <tr>
                                <td>
                                    <div class="form-group">
                                        <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                            <label for="email">Email</label>
                                            <input type="email" class="form-control" name="email" id="email" maxlength="{{\App\User::MAX_LENGTH_EMAIL}}" />
                                        </div>
                                    </div>
                                </td>
                            </tr>  

                            <tr>
                                <td>
                                    <div class="form-group">
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

                            <hr>

                            <tr>
                                <td>
                                    <h4>Datos de Constructora</h4>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <div class="form-group">
                                        <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                            <label for="max_brokers">Número de Brokers</label>
                                            <input type="number" class="form-control" name="max_brokers" id="max_brokers" min="0" maxlength="99999" required />
                                        </div>

                                        <div class="col-lg-4 col-sm-6 col-xs-12">
                                            <label for="max_ejecutivos_ventas">Número de Ejecutivos de Ventas</label>
                                            <input type="number" class="form-control" name="max_ejecutivos_ventas" id="max_ejecutivos_ventas" min="0" maxlength="99999" required />
                                        </div>

                                    </div>
                                </td>
                            </tr>
                       

                            <tr>
                                <td>
                                    <div class="form-group">

                                        <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                            <label for="max_ejecutivos_bancos">Número de Ejecutivos de Banco</label>
                                            <input type="number" class="form-control" name="max_ejecutivos_bancos" id="max_ejecutivos_bancos" min="0" maxlength="99999" required />
                                        </div>

                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <div class="form-group">
                                        <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                            <label for="fecha_vencimiento">Fecha de Vencimiento</label>
                                            <input type="date" class="form-control" name="fecha_vencimiento" id="fecha_vencimiento" required />
                                        </div>

                                        <div class="col-lg-4 col-sm-6 col-xs-12">
                                            <label for="fecha_suspension">Fecha de Suspensión</label>
                                            <input type="date" class="form-control" name="fecha_suspension" id="fecha_suspension" required />
                                        </div>
                                    </div>
                                </td>
                            </tr>

                        </tbody>
                    </table>
              
                </div>

                </div>     

                <div class="modal-footer" style='text-align: center;'>
                    <button type="submit" class="btn btn-primary">Aceptar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" title="Cancelar">Cancelar</button>
                </div>

            </form>
        </div>
    </div>
</div>