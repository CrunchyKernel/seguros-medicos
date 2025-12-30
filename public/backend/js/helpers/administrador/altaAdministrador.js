
var altaAdministrador = altaAdministrador || {
  
};

;(function($, window, undefined){
    "use strict";

    var form = $("#administradorForm");
    
    $.extend(altaAdministrador, {   
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
                    nombre: {
                        required: true
                    },
                    e_mail: {
                        required: true,
                        email: true,
                    },
                },
                messages: {
                    nombre: {
                        required: "Escriba el nombre del administrador"
                    },
                    e_mail: {
                        required: "Escriba el e-mail del administrador",
                        email: "Escriba un e-mail válido",
                    },
                }
            });
        },
        agregarAdministrador: function(){
            if(form.valid() === true){
                $.ajax(_root_ + "administrador/agregarAdministrador",{
                                        data: $("#administradorForm").serialize(),
                                        cache: false,
                                        timeout: 15000,
                                        method: 'POST',
                                        dataType: 'json',
                                        beforeSend: function(){
                                            $(".registrarAdministrador").button('loading');
                                        },
                                        complete: function(){
                                            $(".registrarAdministrador").button('reset');
                                        },
                                        success: function(respuesta){
                                            if(respuesta.status == 'success'){
                                                $(form)[0].reset();
                                            }else{
                                                
                                            }
                                            Adminsis.notificacion(respuesta.titulo, respuesta.mensaje, respuesta.posicion, respuesta.tipo);
                                            $(".registrarAdministrador").button('reset');
                                        },
                                        error: function(data){
                                            
                                        }
                                    });
            }
        },
    });
    $('body').delegate('.registrarAdministrador', 'click', function(event){
        event.preventDefault();
        altaAdministrador.agregarAdministrador();
    });
})(jQuery, window);

jQuery(document).ready(function() {
    altaAdministrador.init();
});