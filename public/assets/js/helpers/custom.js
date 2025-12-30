var Custom = Custom || {
	
};

;(function($, window, undefined)
{
	"use strict";
    var formularioContacto = null;
    var integrantes = [];
    
    $.extend(Custom, {
        init: function(){
            this.formularioContacto();
        },
        formularioContacto: function(){
            formularioContacto = $("#contactoForm");
            if(formularioContacto.length == 1){
                $("#contactoForm").validate({
                    success: function(label) {
                        
                    },
                    errorClass: "error",
                    validClass: "success",
                    errorPlacement: function(error, element) {
                        /*
                        if (element.is(":radio") || element.is(":checkbox")) {
                            element.closest('.option-group').after(error);
                        } else {
                            error.insertAfter(element.parent());
                        }
                        */
                    },
                    rules: {
                        nombre: {
                            required: true,
                        },
                        e_mail: {
                            required: true,
                            email: true,
                        },
                        mensaje: {
                            required: true,
                        },
                    },
                    messages: {
                        nombre: {
                            required: "Escriba su nombre",
                        },
                        e_mail: {
                            required: "Escriba su e-mail",
                            email: "Escriba un e-mail válido",
                        },
                        mensaje: {
                            required: "Escriba su mensaje",
                        },
                    }
                });
            }
        },
        enviarContacto: function(){
            if(formularioContacto.valid() === true){
                $.ajax({
                        url: _root_+'enviarContacto',
                        data: $("#contactoForm").serialize(),
                        method: 'POST',
                        dataType: 'json',
                        cache : false,
                        processData: true,
                        beforeSend: function()
                        {
                            $(".enviarContacto").val('Enviando...');
                            $(".enviarContacto").prop( "disabled", true );
                        },
                        error: function()
                        {
                            $(".enviarContacto").val('ENVIAR');
                            $(".enviarContacto").prop( "disabled", false );
                        },
                        success: function(response)
                        {
                            $(".enviarContacto").val('ENVIAR');
                            $(".enviarContacto").prop( "disabled", false );
                            if(response.status == 'success'){
                                alert(response.mensaje)
                            }
                        }
                    });
            }
        },
        enviarCotizacionEmail: function(idCotizacion, secret){
            if(idCotizacion > 0 && secret.length > 5){
                $.ajax({
                        url: _root_+'enviarCotizacionEmail',
                        data: { idCotizacion : idCotizacion, secret : secret },
                        method: 'POST',
                        dataType: 'json',
                        cache : false,
                        processData: true,
                        beforeSend: function()
                        {
                            //$(".enviarCotizacionEmail").button('loading');
                            $(".enviarCotizacionEmail").html('Enviando cotización...');
    						$(".enviarCotizacionEmail").prop( "disabled", true );
                        },
                        error: function()
                        {
                            //$(".enviarCotizacionEmail").button('reset');
                            $(".enviarCotizacionEmail").html('<i class="fa fa-envelope-o fa-lg"></i>  Envíar cotización por e-mail');
    						$(".enviarCotizacionEmail").prop( "disabled", false );
                        },
                        success: function(response)
                        {
                            $(".enviarCotizacionEmail").html('<i class="fa fa-envelope-o fa-lg"></i>  Envíar cotización por e-mail');
    						$(".enviarCotizacionEmail").prop( "disabled", false );
                            if(response.status == 'success'){
                                alert(response.mensaje)
                            }
                            //$(".enviarCotizacionEmail").button('reset');
                        }
                    });
            }else{
                alert('Datos incorrectos')
            }
		},
    });
    $('body').delegate('.enviarCotizacionEmail', 'click', function(event){
        event.preventDefault();
        var idCotizacion = $(this).data('idcotizacion');
        var secret = $(this).data('secret');
        Custom.enviarCotizacionEmail(idCotizacion, secret);
    });
    $('body').delegate('.enviarContacto', 'click', function(event){
        event.preventDefault();
        Custom.enviarContacto();
    });
    $('body').delegate('.mostrarPreciosMensualidades', 'click', function(event){
        event.preventDefault();
        if($('.tabla-precios').hasClass('hide') == true){
            $('.tabla-precios').removeClass('hide');
            $(this).find('strong').html('Ocultar pagos en mensualidades');
        }else{
            $('.tabla-precios').addClass('hide');
            $(this).find('strong').html('Mostrar pagos en mensualidades');
        }
    });
})(jQuery, window);

jQuery(document).ready(function() {
    Custom.init();
});
