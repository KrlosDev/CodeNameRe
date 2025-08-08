@push('JS')
    <script>
        function editarCasaEstado(url){

            $("#casa").prop('checked', false);
            $(".loader").removeClass("hidden");
            $("#casa-estado-form").addClass("hidden");
            $("[name=_method]").val("PUT");
            $("#casa-estado-form").attr("action", url);
            $("#casa-estado-label").html("Editar Estado");
            $.get(url,function(data,status){
                data=JSON.parse(data);
                $('#nombre').val(data.nombre);
                $('#slug').val(data.slug);

                $(".loader").addClass("hidden");
                $("#casa-estado-form").removeClass("hidden");
            });
            $("#casa-estado-modal").modal();

        }
    </script>
@endpush