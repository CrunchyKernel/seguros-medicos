
var publicacionOrden = publicacionOrden || {
  
};

;(function($, window, undefined){
    "use strict";

    var getPublicacionesCategoriasOrdenAjax = null;

    $.extend(publicacionOrden, {   
        init: function(){
            $('#publicacionesBlog').nestable({
                maxDepth: 1
            });
            jQuery('#id_blog_categoria').select2({
                minimumResultsForSearch: -1
            });
        },
        actualizarPublicacionesOrden: function(){
            $.ajax(_root_ + "publicacion/actualizarPublicacionesOrden",{
                                        data: { menu : $(".dd").nestable('serialize') },
                                        cache: false,
                                        timeout: 15000,
                                        method: 'POST',
                                        dataType: 'json',
                                        beforeSend: function(){
                                            $(".actualizarPublicacionesOrden").button('loading');
                                        },
                                        complete: function(){
                                            $(".actualizarPublicacionesOrden").button('reset');
                                        },
                                        success: function(respuesta){
                                            $(".actualizarPublicacionesOrden").button('reset');
                                            if(respuesta == '1'){
                                                Adminsis.notificacion("Publicaciones", "Publicaciones actualizadas correctamente", "stack_bar_bottom", "success");
                                            }else{
                                                Adminsis.notificacion("Publicaciones", "Ocurrio un error al tratar de actualizadar las categorías", "stack_bar_bottom", "danger");
                                            }
                                        },
                                        error: function(data){
                                            
                                        }
                                    });
        },
        getPublicacionesCategorias: function(idBlogCategoria){
            if(getPublicacionesCategoriasOrdenAjax){
                getPublicacionesCategoriasOrdenAjax.abort();
            }
            getPublicacionesCategoriasOrdenAjax = $.ajax(_root_ + "publicacion/getPublicacionesCategorias",{
                                                    data: { idBlogCategoria : idBlogCategoria },
                                                    cache: false,
                                                    timeout: 15000,
                                                    method: 'POST',
                                                    //dataType: 'json',
                                                    beforeSend: function(){
                                                        $('.dd-list').html("<li>Cargando publicaciones...</li>");
                                                    },
                                                    complete: function(){
                                                        
                                                    },
                                                    success: function(respuesta){
                                                        $('.dd-list').html(respuesta);
                                                        $('#publicacionesBlog').nestable('init');
                                                    },
                                                    error: function(data){
                                                        
                                                    }
                                                });
        },
    });
    $('body').delegate('.actualizarPublicacionesOrden', 'click', function(event){
        event.preventDefault();
        publicacionOrden.actualizarPublicacionesOrden();
    });
    $('body').delegate('#id_blog_categoria', 'change', function(event){
        event.preventDefault();
        var idBlogCategoria = $(this).val();
        publicacionOrden.getPublicacionesCategorias(idBlogCategoria)
    });
})(jQuery, window);

jQuery(document).ready(function() {
    publicacionOrden.init();
});