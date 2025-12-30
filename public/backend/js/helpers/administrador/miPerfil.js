
var miPerfil = miPerfil || {
  
};

;(function($, window, undefined){
    "use strict";

    var form = $("#actualizarPasswordForm");
    
    $.extend(miPerfil, {   
        init: function(){
            this.formulario();
        },
        formulario: function(){
            form.validate({
                ignore: [],
                highlight: function(element) {
                    jQuery(element).closest('.form-group').removeClass('has-success').addClass('has-error');
                },
                success: function(element) {
                    jQuery(element).closest('.form-group').removeClass('has-error');
                },
                rules: {
                    passwordActual: {
                        required: true
                    },
                    passwordNuevo: {
                        required: true,
                    },
                },
                messages: {
                    passwordActual: {
                        required: "Escriba su contraseña actual"
                    },
                    passwordNuevo: {
                        required: "Escriba su contraseña nueva"
                    },
                }
            });
        },
        actualizarPassword: function(){
            if(form.valid() === true){
                $.ajax(_root_ + "administrador/actualizarPassword",{
                        data: $("#actualizarPasswordForm").serialize(),
                        cache: false,
                        timeout: 15000,
                        method: 'POST',
                        dataType: 'json',
                        beforeSend: function(){
                            $(".actualizarAdministradorPassword").button('loading');
                        },
                        complete: function(){
                            $(".actualizarAdministradorPassword").button('reset');
                        },
                        success: function(respuesta){
                            if(respuesta.status == 'success'){
                                $(form)[0].reset();
                            }
                            Adminsis.notificacion(respuesta.titulo, respuesta.mensaje, respuesta.posicion, respuesta.tipo);
                            $(".actualizarAdministradorPassword").button('reset');
                        },
                        error: function(data){
                            
                        }
                    });
            }
        },
    });
    $('body').delegate('.actualizarAdministradorPassword', 'click', function(event){
        event.preventDefault();
        miPerfil.actualizarPassword();
    });
})(jQuery, window);

jQuery(document).ready(function() {
    miPerfil.init();
});