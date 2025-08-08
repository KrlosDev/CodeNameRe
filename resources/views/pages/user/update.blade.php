
<script>
    function editarUser(url){
        $(".loader").removeClass("hidden");
        $("#user-update-form").addClass("hidden");
        $("[name=_method]").val("PUT");
        $("#user-update-form").attr("action", url);
        $("#user-update-label").html("Editar Usuario");

        $("[id=upassword]").prop('required',false);
        $("[id=upassword_confirmation]").prop('required',false);

        $.get(url,function(data,status){
                data=JSON.parse(data);
                $('#uname').val(data.name);
                $('#unombre').val(data.nombre);
                $('#uemail').val(data.email);
                $(".loader").addClass("hidden");
                $("#user-update-form").removeClass("hidden");
            });
        $("#user-update-modal").modal();
            
    }
</script>

<div class="modal fade" id="user-update-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-big" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="user-update-label"></h4>
            </div>
            <div class="loader text-center">
                <i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>
                <span class="sr-only">Loading...</span>
            </div>
            <form class="form-horizontal hidden" method="POST" id='user-update-form' >
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

                            <div class="form-group" >

                                <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12" >
                                    <label for="unombre">Nombre</label>
                                    <input type="text" name="unombre" class="'form-control" maxlength="60" required/>
                                </div>

                                <div class="col-lg-4 col-sm-6 col-xs-12">
                                    <label for="uname">Nombre de usuario</label>
                                    <input type="text" name="uname" class="'form-control" maxlength="60" required/>
                                </div>

                            </div>

                            </td>
                        </tr>
                   
                        <tr>
                            <td>

                            <div class="form-group">
                                <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                    <label for="uemail">Nombre de usuario</label>
                                    <input type="email" name="uemail" class="'form-control" maxlength="100"/>
                                </div>
                            </div>

                            </td>
                        </tr>
                        <tr>
                            <td>

                            <div class="form-group">
                                <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                    <label for="upassword">Contraseña</label>
                                    <input class="form-control" name="upassword" type="password" id="upassword"  maxlength="100">
                                </div>

                                <div class="col-lg-4 col-sm-6 col-xs-12">
                                    <label for="upassword_confirmation">Confirmar contraseña</label>
                                    <input class="form-control" name="upassword_confirmation" type="password" id="upassword_confirmation"  maxlength="100">
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