
var altaRedireccion = altaRedireccion || {
  
};

;(function($, window, undefined){
    "use strict";

    var form = $("#redireccionForm");
    var oTable = null;
    
    $.extend(altaRedireccion, {   
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
                	alias: {
                        required: true
                    },
                    redirect_to: {
                        required: true
                    },
                    tipo: {
                        required: true
                    }
                },
                messages: {
                	alias: {
                        required: 'Escriba el alias'
                    },
                    redirect_to: {
                        required: 'Escriba el redireccionar a'
                    },
                    tipo: {
                        required: 'Escriba el tipo'
                    }
                }
            });
        },
        agregarRedireccion: function(){
            if(form.valid() === true){
            	 $.ajax(_root_ + "publicacion/agregarRedireccion",{
                                        data: $("#redireccionForm").serialize(),
                                        cache: false,
                                        timeout: 15000,
                                        method: 'POST',
                                        dataType: 'json',
                                        beforeSend: function(){
                                            $(".registrarRedireccion").button('loading');
                                        },
                                        complete: function(){
                                            $(".registrarRedireccion").button('reset');
                                        },
                                        success: function(respuesta){
                                            if(respuesta.status == 'success'){
                                                $(form)[0].reset();
                                            }else{
                                                
                                            }
                                            Adminsis.notificacion(respuesta.titulo, respuesta.mensaje, respuesta.posicion, respuesta.tipo);
                                            $(".registrarRedireccion").button('reset');
                                        },
                                        error: function(data){
                                            
                                        }
                                    });
            }
        },
    });
    $('body').delegate('.registrarRedireccion', 'click', function(event){
        event.preventDefault();
        altaRedireccion.agregarRedireccion();
    });
})(jQuery, window);

jQuery(document).ready(function() {
    altaRedireccion.init();
});