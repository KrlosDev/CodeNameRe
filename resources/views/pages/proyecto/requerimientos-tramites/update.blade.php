@push('JS')
    <script>
        function editarRequerimientoTramite(url){

            $("#casa").prop('checked', false);
            $(".loader").removeClass("hidden");
            $("#requerimiento-tramite-form").addClass("hidden");
            $("[name=_method]").val("PUT");
            $("#requerimiento-tramite-form").attr("action", url);
            $("#requerimiento-tramite-label").html("Editar Requerimiento");
            $.get(url,function(data,status){
                data=JSON.parse(data);
                $('#nombre').val(data[0].nombre);

                $(".loader").addClass("hidden");
                $("#requerimiento-tramite-form").removeClass("hidden");
            });
            $("#requerimiento-tramite-modal").modal();

        }
    </script>
@endpush