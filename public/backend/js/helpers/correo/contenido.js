$(document).ready(function(){

    $('[name="textoEncabezado"], [name="textoPie"], [name="textoCuerpo"]').ckeditor({
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

    $("#guardaEncabezado").click( function(){
        var textoEncabezado = CKEDITOR.instances['textoEncabezado'].getData();
		var idDominio = $("#id_dominio").val();
        $.ajax({
            data : { textoEncabezado : textoEncabezado, id_dominio:idDominio },
            url  : "contenido/guardaEncabezado" ,
            method : 'POST',
            beforeSend: function(){
                $(".btn-info").button('loading');

            },
            complete: function(){
                $(".btn-info").button('reset');
            },
            success: function(respuesta){
                if(respuesta == 1){
                    swal("Texto de Encabezado", "guardado correctamente", "success");
                    document.getElementById('vistaPrevia').contentDocument.location.reload(true);
                }else{
                    swal("Error","No se pudo guardar el texto", "warning");
                }
            }
        });
    });

    $("#guardaCuerpo").click( function(){
        var textoCuerpo = CKEDITOR.instances['textoCuerpo'].getData();
		var idDominio = $("#id_dominio").val();
        $.ajax({
            data : { textoCuerpo : textoCuerpo, id_dominio:idDominio },
            url  : "contenido/guardaCuerpo" ,
            method : 'POST',
            beforeSend: function(){
                $(".btn-success").button('loading');

            },
            complete: function(){
                $(".btn-success").button('reset');
            },
            success: function(respuesta){
                if(respuesta == 1){
                    swal("Texto de cuerpo", "guardado correctamente", "success");
                    document.getElementById('vistaPrevia').contentDocument.location.reload(true);
                }else{
                    swal("Error","No se pudo guardar el texto", "warning");
                }
            }
        });
    });

    $("#guardaPie").click( function(){
        var textoPie = CKEDITOR.instances['textoPie'].getData();
		var idDominio = $("#id_dominio").val();
        $.ajax({
            data : { textoPie :  textoPie, id_dominio:idDominio },
            url  : "contenido/guardaPie" ,
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
                    document.getElementById('vistaPrevia').contentDocument.location.reload(true);
                }else{
                    swal("Error","No se pudo guardar el texto", "warning");
                }
            }
        });
    });
    
    $("#id_dominio").change(function(){
    	$.ajax({
            url  : "consultaContenidoCorreo/" + $("#id_dominio").val(),
            method : 'GET',
            dataType:'json',
            success: function(respuesta){
            	$("#vistaPrevia").attr('src', 'https://www.segurodegastosmedicosmayores.mx/Correo/previsualizar/' + $("#id_dominio").val());
                CKEDITOR.instances['textoEncabezado'].setData(respuesta.textoEncabezado);
                CKEDITOR.instances['textoCuerpo'].setData(respuesta.textoCuerpo);
                CKEDITOR.instances['textoPie'].setData(respuesta.textoPie);
            }

        });
    });
});