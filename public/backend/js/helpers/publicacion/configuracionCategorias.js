
var categoriaPublicacion = categoriaPublicacion || {
  
};

;(function($, window, undefined){
    "use strict";

    $.extend(categoriaPublicacion, {   
        init: function(){
            $('#categoriasBlog').nestable({
                maxDepth: 3
            });
            this.getCategoriasNestableMenu();
        },
        editable: function(){
            $(".campo").editable({
                        url: _root_+"publicacion/actualizarCategoria",
                        sourceCache: false,
                        display: function(value) {
                            switch($(this).data('campo')){
                                case 'metakey':
                                case 'metadesc':
                                    $(this).text('Editar');
                                break;
                                default:
                                    $(this).text(value);
                                break;
                            }
                        },
                        validate: function(val){
                            switch($(this).data('campo')){
                                case 'metakey':
                                case 'metadesc':
                                    if(val.length > 155){
                                        return "Caracteres máximos 155"
                                    }
                                break;
                            }
                        },
                        params: function(params) {
                            params.campo = $(this).data('campo');
                            return params;
                        },
                        success: function(response, newValue) {
                            if(response == true){
                                Adminsis.notificacion("Actualizacion", "Categoría actualizada", "stack_bottom_left", "success");
                            }else{
                                categoriaPublicacion.getCategoriasNestableMenu();
                                Adminsis.notificacion("Actualizacion", "Ocurrio un error al tratar de actualizar la categoría", "stack_bottom_left", "error");
                            }
                        }
                    });
        },
        getCategoriasNestableMenu: function(){
            $.ajax(_root_ + "publicacion/getCategoriasNestableMenu",{
                                        cache: false,
                                        timeout: 15000,
                                        method: 'POST',
                                        //dataType: 'json',
                                        beforeSend: function(){
                                            $('.dd-list').html("<li>Cargando categorías...</li>");
                                        },
                                        complete: function(){
                                            
                                        },
                                        success: function(respuesta){
                                            $('.dd-list').html(respuesta);
                                            jQuery('.tooltips').tooltip({ container: 'body'});
                                            categoriaPublicacion.editable();
                                            $('#categoriasBlog').nestable('init');
                                            //$('.dd').nestable('collapseAll');
                                        },
                                        error: function(data){
                                            
                                        }
                                    });
        },
        actualizarCategoriasOrden: function(){
            $.ajax(_root_ + "publicacion/actualizarCategoriasOrden",{
                                        data: { menu : $(".dd").nestable('serialize') },
                                        cache: false,
                                        //contentType:false,
                                        //processData: true,
                                        timeout: 15000,
                                        method: 'POST',
                                        dataType: 'json',
                                        beforeSend: function(){
                                            $(".actualizarCategoriasOrden").button('loading');
                                        },
                                        complete: function(){
                                            $(".actualizarCategoriasOrden").button('reset');
                                        },
                                        success: function(respuesta){
                                            $(".actualizarCategoriasOrden").button('reset');
                                            if(respuesta == '1'){
                                                Adminsis.notificacion("Categorías", "Categorías actualizadas correctamente", "stack_bar_bottom", "success");
                                            }else{
                                                Adminsis.notificacion("Categorías", "Ocurrio un error al tratar de actualizadar las categorías", "stack_bar_bottom", "danger");
                                            }
                                        },
                                        error: function(data){
                                            
                                        }
                                    });
        },
        agregarHijo: function(idPadre, nombre){
            swal({
                title: nombre,
                text: "Escrina el nombre para el hijo",
                type: "input",
                showCancelButton: true,
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
                inputPlaceholder: "Nombre del hijo"
            }, function(nodoHijo){
                if (nodoHijo === false) return false;
                if (nodoHijo === "") {
                    swal.showInputError("Escriba el nombre del hijo");
                    return false
                }
                $.ajax(_root_ + "publicacion/agregarCategoriaHijo",{
                                        data: { idPadre : idPadre, nodoHijo : nodoHijo },
                                        cache: false,
                                        timeout: 15000,
                                        method: 'POST',
                                        dataType: 'json',
                                        beforeSend: function(){
                                            //$(".actualizarCategoriasOrden").button('loading');
                                        },
                                        complete: function(){
                                            //$(".actualizarCategoriasOrden").button('reset');
                                        },
                                        success: function(respuesta){
                                            if(respuesta.status == 'success'){
                                                categoriaPublicacion.getCategoriasNestableMenu();
                                            }
                                            swal({
                                                title: respuesta.titulo,
                                                text: respuesta.mensaje,
                                                showCancelButton: false,
                                                closeOnConfirm: false,
                                                type: respuesta.tipo
                                            });
                                        },
                                        error: function(data){
                                            
                                        }
                                    });
            });
        },
        eliminarHijo: function(idBlogCategoria, nombre){
            swal({
                title: nombre,
                text: "¿Seguro que desea eliminar la categoría?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Sí, elimninar!",
                showLoaderOnConfirm: true,
                closeOnConfirm: false
            }, function(){
                $.ajax(_root_ + "publicacion/eliminarCategoria",{
                                        data: { idBlogCategoria : idBlogCategoria },
                                        cache: false,
                                        timeout: 15000,
                                        method: 'POST',
                                        dataType: 'json',
                                        beforeSend: function(){
                                            //$(".actualizarCategoriasOrden").button('loading');
                                        },
                                        complete: function(){
                                            //$(".actualizarCategoriasOrden").button('reset');
                                        },
                                        success: function(respuesta){
                                            if(respuesta.status == 'success'){
                                                categoriaPublicacion.getCategoriasNestableMenu();
                                            }
                                            swal({
                                                title: respuesta.titulo,
                                                text: respuesta.mensaje,
                                                showCancelButton: false,
                                                closeOnConfirm: false,
                                                type: respuesta.tipo
                                            });
                                        },
                                        error: function(data){
                                            
                                        }
                                    });
            });
        },
    });
    $('body').delegate('.actualizarCategoriasOrden', 'click', function(event){
        event.preventDefault();
        categoriaPublicacion.actualizarCategoriasOrden();
    });
    $('body').delegate('.agregarHijo', 'click', function(event){
        event.preventDefault();
        var idBlogCategoria = $(this).data('idblogcategoria');
        var nombre = $(this).data('nombre');
        categoriaPublicacion.agregarHijo(idBlogCategoria, nombre);
    });
    $('body').delegate('.eliminarHijo', 'click', function(event){
        event.preventDefault();
        var idBlogCategoria = $(this).data('idblogcategoria');
        var nombre = $(this).data('nombre');
        categoriaPublicacion.eliminarHijo(idBlogCategoria, nombre);
    });
})(jQuery, window);

jQuery(document).ready(function() {
    categoriaPublicacion.init();
});