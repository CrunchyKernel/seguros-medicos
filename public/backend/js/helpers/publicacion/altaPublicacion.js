
var altaPublicacion = altaPublicacion || {
  
};

;(function($, window, undefined){
    "use strict";

    var form = $("#altaPublicacionForm");
    var html;
    
    $.extend(altaPublicacion, {   
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
            jQuery('#fecha_publicacion').datepicker({
                dateFormat: "yy-mm-dd"
            });
            //CKEDITOR.config.extraPlugins = "base64image,pastebase64";
            jQuery('#metakey').tagsInput({width:'auto'});
            jQuery("#id_blog_categoria").select2();
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
                    titulo: {
                        required: true
                    },
                },
                messages: {
                    titulo: {
                        required: "Escriba el título de la publicación"
                    },
                }
            });
        },
        agregarPublicacion: function(){
            if(form.valid() === true){
                var data = new FormData();
                
                $.each(form.serializeArray(), function(i, field){
                    data.append(field.name, field.value);
                });
                var inputFileImage = document.getElementById('imagen');
                var file = inputFileImage.files[0];
                data.append('imagen', file);
                $.ajax(_root_ + "publicacion/agregarPublicacion",{
                                        data: data,
                                        cache: false,
                                        contentType:false,
                                        processData: false,
                                        timeout: 15000,
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
                                                if($('#id_blog').val() == '-1'){
                                                    $(form)[0].reset();
                                                    CKEDITOR.instances.contenido.setData("");
                                                    $('#metakey').importTags('');
                                                    document.getElementById('preview').src = _root_+"../backend/images/preview.png";
                                                }
                                                if($('#eliminarImagen').is(":checked") == true){
                                                    document.getElementById('preview').src = _root_+"../backend/images/preview.png";
                                                    $('#eliminarImagen').prop("checked", false);
                                                }
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
    $('body').delegate('#imagen', 'change', function(event){
        event.preventDefault();
        $(this).attr('src');
        var preview = document.getElementById('preview');
        preview.src = URL.createObjectURL(event.target.files[0]);
        var newimg = preview.src;
        if(newimg.indexOf('/null') > -1) {
            preview.src = _root_+"backend/images/preview.png";
        }
    });
    $('body').delegate('.publicar', 'click', function(event){
        event.preventDefault();
        altaPublicacion.agregarPublicacion();
    });
})(jQuery, window);

jQuery(document).ready(function() {
    altaPublicacion.init();
});