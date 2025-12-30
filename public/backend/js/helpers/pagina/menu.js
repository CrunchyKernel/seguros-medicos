
var menu = menu || {
  
};

;(function($, window, undefined){
    "use strict";

    $.extend(menu, {   
        init: function(){
            $('#menus').nestable({
                maxDepth: 3
            });
            this.getMenuNestableMenu();
        },
        editable: function(){
            $(".campo").editable({
                            url: _root_+"pagina/actualizarMenu",
                            sourceCache: false,
                            params: function(params) {
                                params.campo = $(this).data('campo');
                                return params;
                            },
                            success: function(response, newValue) {
                                if(response == true){
                                    Adminsis.notificacion("Actualizacion", "Categoría actualizada", "stack_bottom_left", "success");
                                }else{
                                    menu.getMenuNestableMenu();
                                    Adminsis.notificacion("Actualizacion", "Ocurrio un error al tratar de actualizar la categoría", "stack_bottom_left", "error");
                                }
                            }
                        });
        },
        getMenuNestableMenu: function(){
            $.ajax(_root_ + "pagina/getMenuNestableMenu",{
                                        cache: false,
                                        timeout: 15000,
                                        method: 'POST',
                                        //dataType: 'json',
                                        beforeSend: function(){
                                            $('.dd-list').html("<li>Cargando menús...</li>");
                                        },
                                        complete: function(){
                                            
                                        },
                                        success: function(respuesta){
                                            //$('#menus').nestable('destroy');
                                            $('.dd-list').html(respuesta);
                                            $('#menus').nestable('init');
                                            //$('#menus').nestable('collapseAll');
                                            jQuery('.tooltips').tooltip({ container: 'body'});
                                            menu.editable();
                                        },
                                        error: function(data){
                                            
                                        }
                                    });
        },
        actualizarMenusOrden: function(){
            $.ajax(_root_ + "pagina/actualizarMenusOrden",{
                                        data: { menu : $(".dd").nestable('serialize') },
                                        cache: false,
                                        timeout: 15000,
                                        method: 'POST',
                                        dataType: 'json',
                                        beforeSend: function(){
                                            $(".actualizarMenusOrden").button('loading');
                                        },
                                        complete: function(){
                                            $(".actualizarMenusOrden").button('reset');
                                        },
                                        success: function(respuesta){
                                            $(".actualizarMenusOrden").button('reset');
                                            if(respuesta == '1'){
                                                Adminsis.notificacion("Menús", "Menús actualizadas correctamente", "stack_bar_bottom", "success");
                                            }else{
                                                Adminsis.notificacion("Menús", "Ocurrio un error al tratar de actualizadar los menús", "stack_bar_bottom", "danger");
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
                inputPlaceholder: "Nombre del hijo"
            }, function(nodoHijo){
                if (nodoHijo === false) return false;
                if (nodoHijo === "") {
                    swal.showInputError("Escriba el nombre del hijo");
                    return false
                }
                $.ajax(_root_ + "pagina/agregarMenuHijo",{
                                        data: { idPadre : idPadre, nodoHijo : nodoHijo },
                                        cache: false,
                                        timeout: 15000,
                                        method: 'POST',
                                        dataType: 'json',
                                        beforeSend: function(){
                                            //$(".actualizarMenusOrden").button('loading');
                                        },
                                        complete: function(){
                                            //$(".actualizarMenusOrden").button('reset');
                                        },
                                        success: function(respuesta){
                                            if(respuesta.status == 'success'){
                                                menu.getMenuNestableMenu();
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
        eliminarHijo: function(idSitioMenu, nombre){
            swal({
                title: nombre,
                text: "¿Seguro que desea eliminar la categoría?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Sí, elimninar!",
                closeOnConfirm: false
            }, function(){
                $.ajax(_root_ + "pagina/eliminarMenu",{
                                        data: { idSitioMenu : idSitioMenu },
                                        cache: false,
                                        timeout: 15000,
                                        method: 'POST',
                                        dataType: 'json',
                                        beforeSend: function(){
                                            //$(".actualizarMenusOrden").button('loading');
                                        },
                                        complete: function(){
                                            //$(".actualizarMenusOrden").button('reset');
                                        },
                                        success: function(respuesta){
                                            if(respuesta.status == 'success'){
                                                menu.getMenuNestableMenu();
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
    $('body').delegate('.actualizarMenusOrden', 'click', function(event){
        event.preventDefault();
        menu.actualizarMenusOrden();
    });
    $('body').delegate('.agregarHijo', 'click', function(event){
        event.preventDefault();
        var idSitioMenu = $(this).data('idsitiomenu');
        var nombre = $(this).data('nombre');
        menu.agregarHijo(idSitioMenu, nombre);
    });
    $('body').delegate('.eliminarHijo', 'click', function(event){
        event.preventDefault();
        var idSitioMenu = $(this).data('idsitiomenu');
        var nombre = $(this).data('nombre');
        menu.eliminarHijo(idSitioMenu, nombre);
    });
})(jQuery, window);

jQuery(document).ready(function() {
    menu.init();
});