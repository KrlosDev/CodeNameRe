@push('JS')
<script>
    function editarBroker(url){
        $(".loader").removeClass("hidden");
        $("#brokers-form").addClass("hidden");
        $("[name=_method]").val("PUT");
        $("#brokers-form").attr("action", url)
        $("#brokers-label").html("Editar Broker");
        $("[id=name]").prop('readonly', true);
        $.get(url,function(data,status){
                data=JSON.parse(data);
                $('#nombre').val(data.user.nombre);
                $('#name').val(data.user.name);
                $('#email').val(data.user.email);
                $('#max_ej_ventas').val(data.max_ej_ventas);
                $('#max_ej_bancos').val(data.max_ej_bancos);
                $(".loader").addClass("hidden");
                $("#brokers-form").removeClass("hidden");
            });
        $("#brokers-modal").modal();

        $("[id=password]").prop('required',false);
        $("[id=password_confirmation]").prop('required',false);

                 
    }
</script>
@endpush