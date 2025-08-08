@push('JS')
<script>
    function editarBanco(url){
        $(".loader").removeClass("hidden");
        $("#bancos-form").addClass("hidden");
        $("[name=_method]").val("PUT");
        $("#bancos-form").attr("action", url);
        $("#bancos-label").html("Editar Banco");
        $("[id=name]").prop('readonly', true);
        
        $.get(url,function(data,status){
            data=JSON.parse(data);
            $('#nombre').val(data.nombre);
            $(".loader").addClass("hidden");
            $("#bancos-form").removeClass("hidden");
        });
        $("#bancos-modal").modal();
    }
</script>
@endpush