
var permisosAdministrador = permisosAdministrador || {
  
};

;(function($, window, undefined){
    "use strict";

    var form = $("#administradorForm");
    
    $.extend(permisosAdministrador, {   
        init: function(){
            jQuery('.permisoUsuario').select2({
                minimumResultsForSearch: -1
            });
        },
        actualizarModuloPermiso: function(idModulo, idUsuario, acceso){
            if(idModulo > 0 && idUsuario > 0){
                $.ajax(_root_ + "administrador/actualizarModuloPermiso",{
                                        data: { idModulo : idModulo, idUsuario : idUsuario, acceso : acceso },
                                        cache: false,
                                        timeout: 15000,
                                        method: 'POST',
                                        dataType: 'json',
                                        beforeSend: function(){
                                            
                                        },
                                        complete: function(){
                                            
                                        },
                                        success: function(respuesta){
                                            if(respuesta.status == 'success'){
                                                
                                            }else{
                                                
                                            }
                                            Adminsis.notificacion(respuesta.titulo, respuesta.mensaje, respuesta.posicion, respuesta.tipo);
                                        },
                                        error: function(data){
                                            
                                        }
                                    });
            }
        },
    });
    $('body').delegate('.permisoUsuario', 'change', function(event){
        event.preventDefault();
        var idModulo = $(this).data('idmodulo');
        var idUsuario = $(this).data('idusuario');
        var acceso = $(this).val();
        permisosAdministrador.actualizarModuloPermiso(idModulo, idUsuario, acceso);
    });
})(jQuery, window);

jQuery(document).ready(function() {
    permisosAdministrador.init();
});