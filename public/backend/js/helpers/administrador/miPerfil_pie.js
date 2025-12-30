$(document).ready(function(){
    $('[name="textoPie"]').ckeditor({
        height: 500,
        linkShowAdvancedTab: false,
        enterMode: CKEDITOR.ENTER_P,
        skin:'office2013',
        extraPlugins: 'scayt,justify',
        allowedContent: true,
        pasteFromWordRemoveStyles : false,
        contentsCss : ['https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css'],
        extraAllowedContent : '*{*}'
    });
    $("#guardaPie").click( function(){
        var textoPie = CKEDITOR.instances['textoPie'].getData();
		var idDominio = $("#id_dominio").val();
        $.ajax({
            data : { textoPie :  textoPie, id_dominio:idDominio },
            url  : "miPerfil/guardaPie" ,
            method : 'POST',
            beforeSend: function(){
                $(".btn-success").button('loading');

            },
            complete: function(){
                $(".btn-success").button('reset');
            },
            success: function(respuesta){
                if(respuesta == 1){
                    swal("Texto de Pie", "guardado correctamente", "success");
                }else{
                    swal("Error","No se pudo guardar el texto", "warning");
                }
            }
        });
    });
    
    $("#id_dominio").change(function(){
    	$.ajax({
            url  : "consultaPie/" + $("#id_dominio").val(),
            method : 'GET',
            dataType:'json',
            success: function(respuesta){
            	CKEDITOR.instances['textoPie'].setData(respuesta.textoPie);
            }

        });
    });
});