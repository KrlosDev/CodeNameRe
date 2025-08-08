 @push('JS')
<script>
    function asignarCasas(url,id_proyecto){
        $(".loader").addClass("hidden");
        $("#proyectos-form-asignar").removeClass("hidden");
        $("[name=_method]").val("POST");
        $("#proyectos-label-asignar").html("Asignar Propiedades");
        $("#proyectos-form-asignar").attr("action", url);
        $("#id_proyecto_c").val(id_proyecto);
        $("#proyectos-modal-asignar").modal();
        
        $.get('{{url("proyectos")}}/'+id_proyecto+'{{"/propiedadesDisponibles"}}',function(data,status){
            data=JSON.parse(data);
            
            var html="<ul class='list-inline'>";
            $.each(data,function(index,value) {
                html+="<li>"+value.codigo+"</li>";
            });
            $('#propiedades-asignadas').html(html+"</ul>");
        });
    }
    
</script>
@endpush

<div class="modal fade" id="proyectos-modal-asignar" tabindex="-1" role="dialog" aria-labelledby="proyectos-modal-asignar" aria-hidden="true">
    <div class="modal-dialog  modal-big">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title" id="proyectos-label-asignar">Asignar</h3>
            </div>
            <div class="loader text-center">
                <i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>
                <span class="sr-only">Loading...</span>
            </div>

            <form class="form-horizontal margin-top hidden" method="POST" id='proyectos-form-asignar'>
                {!! csrf_field() !!}
                <input type="hidden" name="_method" value="POST">
                <input type="hidden" name="id_proyecto" id="id_proyecto_c" value="">
                    

            <div class="modal-body no-border">
                <p>Seleccione las opciones deseadas para asignar las Propiedades correspondientes</p>

                    <div class="form-group">
                    <div class="col-sm-6 col-xs-12 margin-top">
                        <label for="id_broker">Broker</label>
                        <select class="form-control select2" name="id_broker" id="broker" required='true'>
                        @foreach($brokers as $broker)
                            @if(old('id_broker') == $broker->id)
                            <option value="{{$broker->id}}" selected>{{$broker->user->nombre}}</option>
                            @else
                            <option value="{{$broker->id}}">{{$broker->user->nombre}}</option>
                            @endif
                        @endforeach
                        </select>
                    </div>
                    <div class="col-sm-6 col-xs-12 margin-top">
                        <label for="casas">Propiedades <a data-toggle="tooltip" title="Puede ingresar las Propiedades separadas por comas o introducir rangos de Propiedades. P.E: 1,3,5-10"><i class="fa fa-lg fa-info-circle" style="color:#56AEFF;cursor:pointer;" aria-hidden="true"></i></a></label>
                        <input class='form-control' type='text' name='casas' id='casas' placeholder='P.E: 2,4,5,8-10,12' value='{{old('casas')}}' required='true'>
                    </div>
                        <div class='col-xs-12 margin-top'>
                            <label for="propiedades-asignadas">Propiedades disponibles</label>
                            <div id='propiedades-asignadas'></div>
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