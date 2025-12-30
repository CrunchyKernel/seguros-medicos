
var altaDominio = altaDominio || {
  
};

;(function($, window, undefined){
    "use strict";

    var form = $("#dominioForm");
    var formPlan = $("#planForm");
    var oTable = null;
    
    $.extend(altaDominio, {   
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
                    dominio: {
                        required: true
                    },
                    email: {
                        required: true
                    },
                    sender: {
                        required: true
                    },
                    logo: {
                        required: true
                    },
                    ver_cotizacion: {
                        required: true
                    }
                },
                messages: {
                	nombre: {
                        required: 'Escriba el nombre'
                    },
                    dominio: {
                        required: 'Escriba el dominio'
                    },
                    email: {
                        required: 'Escriba el email'
                    },
                    sender: {
                        required: 'Escriba el remitente'
                    },
                    logo: {
                        required: 'Escriba la ruta para el logo'
                    },
                    ver_cotizacion: {
                        required: 'Escriba la ruta para ver cotizacion'
                    }
                }
            });
        },
        agregarDominio: function(){
            if(form.valid() === true){
            	 $.ajax(_root_ + "aseguradora/agregarDominio",{
                                        data: $("#dominioForm").serialize(),
                                        cache: false,
                                        timeout: 15000,
                                        method: 'POST',
                                        dataType: 'json',
                                        beforeSend: function(){
                                            $(".registrarDominio").button('loading');
                                        },
                                        complete: function(){
                                            $(".registrarDominio").button('reset');
                                        },
                                        success: function(respuesta){
                                            if(respuesta.status == 'success'){
                                                $(form)[0].reset();
                                            }else{
                                                
                                            }
                                            Adminsis.notificacion(respuesta.titulo, respuesta.mensaje, respuesta.posicion, respuesta.tipo);
                                            $(".registrarDominio").button('reset');
                                        },
                                        error: function(data){
                                            
                                        }
                                    });
            }
        },
    });
    $('body').delegate('.registrarDominio', 'click', function(event){
        event.preventDefault();
        altaDominio.agregarDominio();
    });
})(jQuery, window);

jQuery(document).ready(function() {
    altaDominio.init();
});