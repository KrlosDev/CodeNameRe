@push('JS')
<script>
    function editarConstructora(url){
        $(".loader").removeClass("hidden");
        $("#constructoras-form").addClass("hidden");
        $("[name=_method]").val("PUT");
        $("#constructoras-form").attr("action", url)
        $("#constructoras-label").html("Editar Constructora");
        $("[id=name]").prop('readonly', true);

        $("[id=password]").prop('required',false);
        $("[id=password_confirmation]").prop('required',false);

        $.get(url,function(data,status){
                data=JSON.parse(data);
                $('#name').val(data.user.name);
                $('#nombre').val(data.user.nombre);
                $('#email').val(data.user.email);
                $('#max_brokers').val(data.max_brokers);
                $('#max_ejecutivos_ventas').val(data.max_ejecutivos_ventas);
                $('#max_ejecutivos_bancos').val(data.max_ejecutivos_bancos);
                $('#fecha_vencimiento').val(data.licencia.fecha_vencimiento);
                $('#fecha_suspension').val(data.licencia.fecha_suspension);
                $(".loader").addClass("hidden");
                $("#constructoras-form").removeClass("hidden");
            });
        $("#constructoras-modal").modal();
            
    }
</script>
@endpush