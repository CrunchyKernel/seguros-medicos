
var altaArchivo = altaArchivo || {
  
};

;(function($, window, undefined){
    "use strict";

    var form = $("#archivoForm");
    var oTable = null;
    
    $.extend(altaArchivo, {   
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
                	archivo: {
                        required: true
                    },
                	alias: {
                        required: true
                    },
                    descarga: {
                        required: true
                    }
                },
                messages: {
                	archivo: {
                        required: 'Seleccione el archivo'
                    },
                	alias: {
                        required: 'Escriba el alias'
                    },
                    descarga: {
                        required: 'Escriba el descargable'
                    }
                }
            });
        },
        agregarArchivo: function(){
            if(form.valid() === true){
            	var data = new FormData($(form)[0]);
            	 $.ajax(_root_ + "publicacion/agregarArchivo",{
                                        //data: $("#archivoForm").serialize(),
                                        data: data,
                                        cache: false,
                                        timeout: 15000,
                                        method: 'POST',
                                        type: 'POST',
                                        contentType: false,
										 processData: false,
                                        dataType: 'json',
                                        beforeSend: function(){
                                            $(".registrarArchivo").button('loading');
                                        },
                                        complete: function(){
                                            $(".registrarArchivo").button('reset');
                                        },
                                        success: function(respuesta){
                                            if(respuesta.status == 'success'){
                                                $(form)[0].reset();
                                            }else{
                                                
                                            }
                                            Adminsis.notificacion(respuesta.titulo, respuesta.mensaje, respuesta.posicion, respuesta.tipo);
                                            $(".registrarArchivo").button('reset');
                                        },
                                        error: function(data){
                                            
                                        }
                                    });
            }
        },
    });
    $('body').delegate('.registrarArchivo', 'click', function(event){
        event.preventDefault();
        altaArchivo.agregarArchivo();
    });
})(jQuery, window);

jQuery(document).ready(function() {
    altaArchivo.init();
});