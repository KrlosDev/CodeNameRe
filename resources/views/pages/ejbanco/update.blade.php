@push('JS')
<script>
    function editarEjbanco(url){
        $(".loader").removeClass("hidden");
        $("#ejbanco-form").addClass("hidden");
        $("[name=_method]").val("PUT");
        $("#ejbanco-form").attr("action", url)
        $("#ejbanco-label").html("Editar Ejecutivo de Banco");

        $("[id=password]").prop('required',false);
        $("[id=password_confirmation]").prop('required',false);

        $.get(url,function(data,status){
                data=JSON.parse(data);
                $('#name').val(data.user.name);
                $('#nombre').val(data.user.nombre);
                $('#email').val(data.user.email);
                $(".loader").addClass("hidden");
                $("#ejbanco-form").removeClass("hidden");
            });
        $("#ejbanco-modal").modal();
            
    }
</script>
@endpush