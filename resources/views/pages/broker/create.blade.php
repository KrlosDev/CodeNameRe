 @push('JS')
<script>
    function crearBroker(url){
        document.getElementById("brokers-form").reset(); 
        $(".loader").addClass("hidden");
        $("#brokers-form").removeClass("hidden");
        $("[name=_method]").val("POST");
        $("#brokers-label").html("Nueva Broker");
        $("#brokers-form").attr("action", url);  
        $("#brokers-modal").modal();
        $("[id=name]").prop('readonly', false);

        $("[id=nombre]").prop('required',true);
        $("[id=name]").prop('required',true);
        $("[id=email]").prop('required',false);
        $("[id=password]").prop('required',true);
        $("[id=password_confirmation]").prop('required',true);
        $("[id=max_ej_ventas]").prop('required',true);
        $("[id=max_ej_bancos]").prop('required',true);

    }


    
</script>
@endpush

<div class="modal fade" id="brokers-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-big" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="brokers-label"></h4>
            </div>
            <div class="loader text-center">
                <i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>
                <span class="sr-only">Loading...</span>
            </div>
            <form class="form-horizontal hidden" method="POST" id='brokers-form' >
                {!! csrf_field() !!}
                <input type="hidden" name="_method" value="POST">
                

                <div class="modal-body">
                        <h4>Datos de Usuario</h4>
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
                                        <label for="nombre">Nombre</label>
                                        <input type="text" class="form-control" name="nombre" id="nombre" maxlength="{{\App\User::MAX_LENGTH_NOMBRE}}" required />
                                    </div>

                                    <div class="col-lg-4 col-sm-6 col-xs-12">
                                        <label for="name">Nombre de Usuario</label>
                                        <input type="text" class="form-control" name="name" id="name" maxlength="{{\App\User::MAX_LENGTH_USERNAME}}" required />
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
                                        <label for="password">Password</label>
                                        <input class="form-control" name="password" type="password" id="password"  maxlength="100" >
                                    </div>

                                    <div class="col-lg-4 col-sm-6 col-xs-12">
                                        <label for="password_confirmation">Confirmar Password</label>
                                        <input class="form-control" name="password_confirmation" type="password"  id="password_confirmation" maxlength="100">
                                    </div>

                                </div>
                            </td>
                        </tr>

                            <hr>
                        <tr>
                        <td>
                            <h4>Datos de Broker</h4>
                        </td>
                        </tr>

                        <tr>
                        <td>                       
                            <div class="form-group">
                                <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                    <label for="max_ej_ventas">Número de Ejecutivos de Ventas</label>
                                    @if($usuario->isConstructora())
                                        <input type="number" class="form-control" name="max_ej_ventas" id="max_ej_ventas" min="1" max="{{$usuario->constructora->max_ejecutivos_ventas}}" />
                                    @endif
                                    @if($usuario->isAdministrador())
                                        <input type="number" class="form-control" name="max_ej_ventas" id="max_ej_ventas" min="1" max="999" />
                                    @endif
                                </div>

                                <div class="col-lg-4 col-sm-6 col-xs-12">
                                    <label for="max_ej_bancos">Número de Ejecutivos de Banco</label>
                                    @if($usuario->isConstructora())
                                        <input type="number" class="form-control" name="max_ej_bancos" id="max_ej_bancos" min="1" max="{{$usuario->constructora->max_ejecutivos_bancos}}" />
                                    @endif
                                    @if($usuario->isAdministrador())
                                        <input type="number" class="form-control" name="max_ej_bancos" id="max_ej_bancos" min="1" max="999" />
                                    @endif
                                </div>
                            </div>
                        </td>
                        </tr>

                        </tbody>
                    </table>
              
                </div> <!-- div de conte table -->
                </div>     

                <div class="modal-footer" style='text-align: center;'>
                    <button type="submit" class="btn btn-primary">Aceptar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" title="Cancelar">Cancelar</button>
                </div>

            </form>
        </div>
    </div>
</div>