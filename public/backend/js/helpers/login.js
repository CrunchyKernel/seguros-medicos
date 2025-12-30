
var iniciarSesion = iniciarSesion || {
	
};

;(function($, window, undefined)
{
	"use strict";
    var form = $("#iniciarSesionForm");
    var iniciarSesionAjax = null;
    
    $.extend(iniciarSesion, {   
        init: function(){
            this.formulario();
        },
        formulario: function(){
            form.validate({
                success: function(label) {
                    
                },
                errorClass: "has-error",
                validClass: "has-success",
                errorElement: "em",
                highlight: function(element, errorClass, validClass) {
                    jQuery(element).closest('.form-group').removeClass(validClass).addClass(errorClass);
                },
                unhighlight: function(element, errorClass, validClass) {
                    jQuery(element).closest('.form-group').removeClass(errorClass).addClass(validClass);
                },
                errorPlacement: function(error, element) {
                    if (element.is(":radio") || element.is(":checkbox")) {
                        element.closest('.option-group').after(error);
                    } else {
                        error.insertAfter(element.parent());
                    }
                },
                rules: {
                    username: {
                        required: true,
                        email: true
                    },
                    password: {
                        required: true,
                    },
                },
                messages: {
                    username: {
                        required: "Escriba una cuenta válida",
                        email: "Escriba una cuenta válida"
                    },
                    password: {
                        required: "Escriba su contraseña",
                    },
                }
            });
        },
        iniciarSesion: function(){
            if(form.valid() === true){
                if(iniciarSesionAjax){
                    iniciarSesionAjax.abort();
                }
                iniciarSesionAjax = $.ajax({
                                        url: 'login/iniciarSesion',
                                        method: 'POST',
                                        dataType: 'json',
                                        data : $("form#iniciarSesionForm").serialize(),
                                        cache : false,
                                        processData: true,
                                        beforeSend: function()
                                        {
                                            $("input#account").prop("disabled", true);
                                            $("input#username").prop("disabled", true);
                                            $(".iniciarSesion").button('loading');
                                        },
                                        error: function()
                                        {
                                            $("input#account").prop("disabled", false);
                                            $("input#username").prop("disabled", false);
                                            $(".iniciarSesion").button('reset');
                                        },
                                        success: function(response)
                                        {
                                            iniciarSesionAjax = null;
                                            if(response.status == 'success'){
                                                $("form#iniciarSesionForm")[0].reset();
                                                location.href=_root_+"main";
                                            }else{
                                        	   $(".iniciarSesion").button('reset');
                                                $("input#account").prop("disabled", false);
                                                $("input#username").prop("disabled", false);
	                                            $(".iniciarSesion").prop("disabled", false);
	                                            if(response.blocked==1)
	                                            	$("#divError").removeClass("hidden");
                                                notificacion("Iniciar sesión", response.mensaje, response.tipo);
                                            }
                                        }
                                    });
            }
		},
    });
    $('body').delegate('.iniciarSesion', 'click', function(event){
        event.preventDefault();
        iniciarSesion.iniciarSesion();
    });
})(jQuery, window);

jQuery(document).ready(function() {
    iniciarSesion.init();
});

function notificacion(titulo, mensaje, tipo){
	if(tipo == undefined){
		tipo = 'warning';
	}
	jQuery.gritter.add({
	    title: titulo,
	    text: mensaje,
		class_name: 'growl-'+tipo,
		image: _root_+'../backend/images/icons/login.png',
		sticky: false,
		time: ''
	});
}