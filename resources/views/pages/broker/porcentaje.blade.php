@push('JS')
<script>
    function asignarPorcentaje(url,id_broker){
        $(".loader").addClass("hidden");
        $("#porcentaje-form-asignar").removeClass("hidden");
        $("[name=_method]").val("POST");
        $("#porcentaje-label-asignar").html("Asignar % de Ganancia");
        $("#porcentaje-form-asignar").attr("action", url);
        $("#id_broker").val(id_broker);
        $("#porcentaje-modal-asignar").modal();
        
        cargarPorcentaje($('#proyecto').val()); 
    }
    
    function cargarPorcentaje(id_proyecto) {
        $.get('{{url("brokers")}}/'+$('#id_broker').val()+"/obtenerPorcentaje/"+id_proyecto,function(data,status){
            data=JSON.parse(data);
            
            if(data.porcentaje)
                $('#porcentaje').val(data.porcentaje.porcentaje);
            else
                $('#porcentaje').val('');
        });
    }
</script>
@endpush

<div class="modal fade" id="porcentaje-modal-asignar" tabindex="-1" role="dialog" aria-labelledby="porcentaje-modal-asignar" aria-hidden="true">
    <div class="modal-dialog  modal-big">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title" id="porcentaje-label-asignar">Asignar</h3>
            </div>
            <div class="loader text-center">
                <i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>
                <span class="sr-only">Loading...</span>
            </div>

            <form class="form-horizontal margin-top hidden" method="POST" id='porcentaje-form-asignar'>
                    {!! csrf_field() !!}
                    <input type="hidden" name="_method" value="POST">
                    <input type="hidden" name="id_broker" id="id_broker" value="">

            <div class="modal-body no-border">
                <p>Seleccione las opciones deseadas para asignar el Porcentaje correspondiente</p>
                

                    <div class="form-group">
                    <div class="col-sm-6 col-xs-12 margin-top">
                        <label for="id_proyecto">Proyecto</label>
                        <select class="form-control select2" name="id_proyecto" id="proyecto" required='true' onchange="cargarPorcentaje(this.value);">
                        @foreach($proyectos as $proyecto)
                            @if(old('id_proyecto') == $proyecto->id)
                            <option value="{{$proyecto->id}}" selected>{{$proyecto->nombre}}</option>
                            @else
                            <option value="{{$proyecto->id}}">{{$proyecto->nombre}}</option>
                            @endif
                        @endforeach
                        </select>
                    </div>
                    <div class="col-sm-6 col-xs-12 margin-top">
                        <label for="porcentaje">Porcentaje </label>
                        <input class='form-control' type='number' name='porcentaje' id='porcentaje' placeholder='$' min="0" step="0.01" max="100" value='{{old('porcentaje')}}' required='true'>
                    </div>
                </div>
                    

            </div>

            <div class="modal-footer no-border" style='text-align: center;'>
                <button type="submit" class="btn btn-primary">Aceptar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
            </div>
            </form>
        </div>
    </div>
</div>