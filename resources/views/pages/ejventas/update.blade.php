@push('JS')
<script>
    function editarEjventas(url){
        $(".loader").removeClass("hidden");
        $("#ejventas-form").addClass("hidden");
        $("[name=_method]").val("PUT");
        $("#ejventas-form").attr("action", url)
        $("#ejvntas-label").html("Editar Ejecutivo de Ventas");

        $("[id=password]").prop('required',false);
        $("[id=password_confirmation]").prop('required',false);

        $.get(url,function(data,status){
                data=JSON.parse(data);
                $('#nombre').val(data.user.nombre);
                $('#name').val(data.user.name);
                $('#email').val(data.user.email);
                $(".loader").addClass("hidden");
                $("#ejventas-form").removeClass("hidden");
            });
        $("#ejventas-modal").modal();
            
    }
</script>
@endpush