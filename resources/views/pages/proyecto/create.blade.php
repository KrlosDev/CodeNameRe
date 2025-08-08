 @push('JS')
<script>
    function crearProyecto(url){
        document.getElementById("proyectos-form").reset();       
        $(".loader").addClass("hidden");
        $("#proyectos-form").removeClass("hidden");
        $("[name=_method]").val("POST");
        $("#proyectos-label").html("Nuevo Proyecto");
        $("#proyectos-form").attr("action", url);  
        $("#proyectos-modal").modal();

        $("[id=datos_casa]").show();
        $("[id=codigo]").prop('readonly', false);

        $("[id=checkcasa]").hide();


                $("[id=modelo]").prop('required',true);
                $("[id=cantidad]").prop('required',true);
                $("[id=mts2_total]").prop('required',true);
                $("[id=mts2_construccion]").prop('required',true);
                $("[id=recamaras]").prop('required',true);
                $("[id=banos]").prop('required',true);
                $("[id=valor]").prop('required',true);
                $("[id=monto_separacion]").prop('required',true);
                $("[id=monto_abono_inicial]").prop('required',true);
                $("[id=monto_mts2_adicional]").prop('required',true);

    }
    
</script>
@endpush

<div class="modal fade" id="proyectos-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-big" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="proyectos-label"></h4>
            </div>
            <div class="loader text-center">
                <i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>
                <span class="sr-only">Loading...</span>
            </div>
            <form class="form-horizontal hidden" method="POST" id='proyectos-form' >
                {!! csrf_field() !!}
                <input type="hidden" name="_method" value="POST">
                

                <div class="modal-body">

                    <h4>Proyecto</h4>
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
                                    <label for="nombre">Nombre del Proyecto</label>
                                    <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Nombre del Proyecto" value="{{old('nombre')}}" required maxlength="{{\App\Models\Proyecto::MAX_LENGTH_NOMBRE}}" />
                                </div>

                                <div class="col-lg-4 col-sm-6 col-xs-12">
                                    <label for="codigo">Código</label>
                                    <input type="text" class="form-control" name="codigo" id="codigo" placeholder="Código" value="{{old('codigo')}}" required maxlength="{{\App\Models\Proyecto::MAX_LENGTH_CODIGO}}" />
                                </div>
                            </div>
                            </td>
                            </tr>

                            <tr>
                            <td>                            
                            <div class="form-group">
                                <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                    <label for="descripcion">Descripción</label>
                                    <textarea class="form-control" name="descripcion" id="descripcion_c" rowspan="3" maxlength="{{\App\Models\Proyecto::MAX_LENGTH_DESCRIPCION}}">{{old('descripcion')}}</textarea>
                                </div>

                                <div class="col-lg-4 col-sm-6 col-xs-12">
                                    <label for="estado">Estado</label>
                                  <!--   <input type="text" class="form-control" name="estado" id="codigo" placeholder="Estado" value="{{old('estados')}}" /> -->
                                    <select class="form-control" name="estado" id="estado" required>
                                        @foreach($estados as $key => $value)
                                            <option value="{{$key}}">{{$value}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            </td>
                            </tr>



                            </tbody>
                    </table>


                            <div class="form-group" id="checkcasa">
                                <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                    <label for="casa" title="Esta opción modficará todas las propiedades del proyecto">Editar propiedades</label>
                                    <input type="checkbox" name="casa" id="casa"/>
                                </div>

                            </div> 

                    
                    <table class="table table-striped table-striped-clarito" id="datos_casa">

                        <thead>
                            <th></th>
                        </thead>

                        <tbody>
                            <div >
                                <tr>
                                <td>
                                <h4>Propiedades</h4>
                                </td>
                                </tr>

                                <tr>
                                <td>                                
                                <div class="form-group">
                                    <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                        <label for="modelo">Modelo</label>
                                        <input type="text" class="form-control" name="modelo" id="modelo"  placeholder="Modelo" value="{{old('modelo')}}" maxlength="{{\App\Models\Casa::MAX_LENGTH_MODELO}}" />
                                    </div>

                                    <div class="col-lg-4 col-sm-6 col-xs-12">
                                        <label for="cantidad">Cantidad total de propiedades</label>
                                        <input type="number" min="1" step="1" class="form-control" name="cantidad" id="cantidad_c"  placeholder="Cantidad total de propiedades" value="{{old('cantidad')}}" />
                                    </div>

                                </div>
                                </td>
                                </tr>

                                <tr>
                                <td>                                
                                <div class="form-group">
                                    <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                        <label for="mts2_total">Mts2 Total</label>
                                        <input type="number" min="1" step="0.01" class="form-control" name="mts2_total" id="mts2_total" placeholder="Mts2 total para cada propiedad"  value="{{old('mts2_total')}}" />
                                    </div>

                                    <div class="col-lg-4 col-sm-6 col-xs-12">
                                        <label for="mts2_total">Mts2 Construcción</label>
                                        <input type="number" min="0" step="0.01" class="form-control" name="mts2_construccion" id="mts2_construccion" placeholder="Mts2 construcción para cada propiedad"  value="{{old('mts2_construccion')}}" />
                                    </div>
                                </div>
                                </td>
                                </tr>

                                <tr>
                                <td>                                
                                <div class="form-group">
                                    <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                        <label for="recamaras">Recámaras</label>
                                        <input type="number" min="1" step="1" class="form-control" name="recamaras" id="recamaras" placeholder="Cantidad de recámaras"  value="{{old('recamaras')}}" />
                                    </div>

                                    <div class="col-lg-4 col-sm-6 col-xs-12">
                                        <label for="banos">Baños</label>
                                        <input type="number" min="1" step="1" class="form-control" name="banos" id="banos" placeholder="Baños"  value="{{old('banos')}}" />
                                    </div>
                                </div>
                                </td>
                                </tr>

                                <tr>
                                <td> 
                                <div class="form-group">
                                    <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                        <label for="valor">Valor</label>
                                        <input type="number" min="1" step="0.01" class="form-control" name="valor" id="valor" placeholder="Valor"  value="{{old('valor')}}" />
                                    </div>

                                    <div class="col-lg-4 col-sm-6 col-xs-12">
                                        <label for="monto_separacion">Monto separación</label>
                                        <input type="number" min="1" step="0.01" class="form-control" name="monto_separacion" id="monto_separacion" placeholder="Monto separación"  value="{{old('monto_separacion')}}" />
                                    </div>

                                </div>
                                </td>
                                </tr>

                                <tr>
                                <td>
                                <div class="form-group">
                                    <div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">
                                        <label for="monto_abono_inicial">Monto abono adicional</label>
                                        <input type="number" min="1" step="0.01" class="form-control" name="monto_abono_inicial" id="monto_abono_inicial" placeholder="Monto de abono inicial"  value="{{old('monto_abono_inicial')}}" />
                                    </div> 
                                                               
                                    <div class="col-lg-4 col-sm-6 col-xs-12">
                                        <label for="monto_mts2_adicional">Monto mts2 adicionales</label>
                                        <input type="number" min="1" step="0.01" class="form-control" name="monto_mts2_adicional" id="monto_mts2_adicional" placeholder="Monto mts adicional"  value="{{old('monto_mts2_adicional')}}" />
                                    </div>
                                </div>
                                </td>
                                </tr>

                            </div>
                            
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