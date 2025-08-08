function loadSearchAuto(id,url,length) {
    $(id).autocomplete({
        source: url,
        minLength: length
    });
}
        
function cargarPropiedades(url,identidad,id) {
    if(typeof id === 'unsigned')
        id = "#id_propiedad";
    var identidad_array = identidad.split('/');
    if(identidad_array.length < 1 || identidad_array.length > 2)
        return;
    var identidad_decoded = identidad_array[0];
    var html = "";
    $.get(url+"/"+identidad_decoded,function(data,status){
        data=JSON.parse(data);
        
        $.each(data.data,function(index,value) {
            html += "<option value='"+value.id+"'>"+value.codigo+"</option>";
        });
        $(id).html(html);
    });
}