
var piePagina = piePagina || {
  
};

;(function($, window, undefined){
    "use strict";

    var form = $("#piePaginaForm");
    var html;
    
    $.extend(piePagina, {   
        init: function(){
        	CKEDITOR.dtd.h1.ul = 1;
            var editor = jQuery('#contenido').ckeditor({
                height: 500,
                linkShowAdvancedTab: false,
                //scayt_autoStartup: false,
                //enterMode: Number(2),
                enterMode: CKEDITOR.ENTER_DIV,
                skin:'office2013',
                extraPlugins: 'scayt,justify',
                allowedContent: true
                //extraPlugins: 'base64image,pastebase64'
            }).editor;
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
                    contenido: {
                        required: true
                    },
                },
                messages: {
                    contenido: {
                        required: "Escriba el contenido del pie de pagina"
                    },
                }
            });
        },
        actualizar: function(){
            if(form.valid() === true){
                var data = $(form).serialize();
                $.ajax(_root_ + "publicacion/doPiePagina",{
                                        data: data,
                                        method: 'POST',
                                        dataType: 'json',
                                        beforeSend: function(){
                                            $(".publicar").button('loading');
                                        },
                                        complete: function(){
                                            $(".publicar").button('reset');
                                        },
                                        success: function(respuesta){
                                            if(respuesta.status == 'success'){
                                                Adminsis.notificacion("Publicación", respuesta.mensaje, respuesta.posicion, respuesta.tipo);
                                            }else{
                                                
                                            }
                                            $(".publicar").button('reset');
                                        },
                                        error: function(data){
                                            
                                        }
                                    });
            }
        },
    });
    $('body').delegate('.publicar', 'click', function(event){
        event.preventDefault();
        piePagina.actualizar();
    });
})(jQuery, window);

jQuery(document).ready(function() {
    piePagina.init();
});