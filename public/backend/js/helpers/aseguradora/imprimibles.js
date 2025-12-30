$(document).ready(function(){

    $('[name="textoProtecto"], [name="textoSaludo"], [name="textoCEncabezado"], [name="textoCAbajode"], [name="textoCPie"]').ckeditor({
        height: 500,
        linkShowAdvancedTab: false,
        //scayt_autoStartup: false,
        //enterMode: Number(2),
        enterMode: CKEDITOR.ENTER_P,
        skin:'office2013',
        extraPlugins: 'scayt,justify',
        allowedContent: true,
        pasteFromWordRemoveStyles : false,
        contentsCss : ['https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css'],
        extraAllowedContent : '*{*}'

    //extraPlugins: 'base64image,pastebase64'
    });

    $("#guardaTP").click( function(){
        var textoProtecto = CKEDITOR.instances['textoProtecto'].getData(); 
        var idDominio = $("#id_dominio").val();
        $.ajax({
            data : { textoPT : textoProtecto , id_dominio:idDominio},
            url  : "imprimibles/guardarTextoProtecto" , 
            method : 'POST',
            beforeSend: function(){
                $(".btn-info").button('loading');
                
            },
            complete: function(){
                $(".btn-info").button('reset');
            },
            success: function(respuesta){
                if(respuesta == 1){
                    swal("Texto ProtectoDiez", "guardado correctamente", "success");
                }else{
                    swal("Error","No se pudo guardar la nota", "warning");                       
                }
            }
        });
    });

    $("#guardaSB").click( function(){
        var textoProtecto = CKEDITOR.instances['textoSaludo'].getData();
		var idDominio = $("#id_dominio").val();
        $.ajax({
            data : { textoPT : textoProtecto , id_dominio:idDominio},
            url  : "imprimibles/guardarTextoSaludo" ,
            method : 'POST',
            beforeSend: function(){
                $(".btn-success").button('loading');

            },
            complete: function(){
                $(".btn-success").button('reset');
            },
            success: function(respuesta){
                if(respuesta == 1){
                    swal("Texto Saludo-bienvenida", "guardado correctamente", "success");
                }else{
                    swal("Error","No se pudo guardar el texto", "warning");
                }
            }

        });
    });
    
    $("#guardaCEncabezado").click( function(){
        var textoProtecto = CKEDITOR.instances['textoCEncabezado'].getData();
		var idDominio = $("#id_dominio").val();
        $.ajax({
            data : { textoPT : textoProtecto , id_dominio:idDominio},
            url  : "imprimibles/guardarCotizacionEncabezado" ,
            method : 'POST',
            beforeSend: function(){
                $(".btn-success").button('loading');

            },
            complete: function(){
                $(".btn-success").button('reset');
            },
            success: function(respuesta){
                if(respuesta == 1){
                    swal("Texto Cotizacion encabezado", "guardado correctamente", "success");
                }else{
                    swal("Error","No se pudo guardar el texto", "warning");
                }
            }

        });
    });
    
    $("#guardaCAbajode").click( function(){
        var textoProtecto = CKEDITOR.instances['textoCAbajode'].getData();
		var idDominio = $("#id_dominio").val();
        $.ajax({
            data : { textoPT : textoProtecto , id_dominio:idDominio},
            url  : "imprimibles/guardarCotizacionAbajode" ,
            method : 'POST',
            beforeSend: function(){
                $(".btn-success").button('loading');

            },
            complete: function(){
                $(".btn-success").button('reset');
            },
            success: function(respuesta){
                if(respuesta == 1){
                    swal("Texto Cotizacion abajo de cotizador", "guardado correctamente", "success");
                }else{
                    swal("Error","No se pudo guardar el texto", "warning");
                }
            }

        });
    });
    
    $("#guardaCPie").click( function(){
        var textoProtecto = CKEDITOR.instances['textoCPie'].getData();
		var idDominio = $("#id_dominio").val();
        $.ajax({
            data : { textoPT : textoProtecto , id_dominio:idDominio},
            url  : "imprimibles/guardarCotizacionPie" ,
            method : 'POST',
            beforeSend: function(){
                $(".btn-success").button('loading');

            },
            complete: function(){
                $(".btn-success").button('reset');
            },
            success: function(respuesta){
                if(respuesta == 1){
                    swal("Texto Cotizacion pie de pagina", "guardado correctamente", "success");
                }else{
                    swal("Error","No se pudo guardar el texto", "warning");
                }
            }

        });
    });
    
    $("#id_dominio").change(function(){
    	$.ajax({
            url  : "consultaImprimibles/" + $("#id_dominio").val(),
            method : 'GET',
            dataType:'json',
            success: function(respuesta){
                CKEDITOR.instances['textoProtecto'].setData(respuesta.textoProtecto);
                CKEDITOR.instances['textoSaludo'].setData(respuesta.textoSaludo);
                CKEDITOR.instances['textoCEncabezado'].setData(respuesta.textoCEncabezado);
                CKEDITOR.instances['textoCPie'].setData(respuesta.textoCPie);
            }

        });
    });
});