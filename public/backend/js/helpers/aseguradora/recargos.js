/**
 * Created by desarrollo-protecto on 26/07/16.
 */

var recargos = recargos || {

    };

;(function($, window, undefined){
    "use strict";

    $.extend(recargos, {
        init: function(){
            this.editable();
        },
        editable: function(){
            $(".campo").editable({
                url: "recargos/actualizarInteres",
                sourceCache: false,
                send: "always",
                params: function(params) {
                    params.campo = $(this).attr('data-aseguradora');
                    params.ciclo = $(this).attr('data-ciclo');
                    params.aseguradora = $(this).data('idpaquete');
                    return params;
                },
                success: function(response, newValue, url) {
                    response = JSON.parse(response);
                    if(response.success == "true"){
                        //Adminsis.notificacion("Actualizacion", "Campo actualizado", "stack_bottom_left", "success");
                        alert("Actualizado");
                    }else{
                       // Adminsis.notificacion("Actualizacion", "Ocurrio un error al tratar el actualizar el campo", "stack_bottom_left", "error");
                        alert("Ocurrió un error al guardar." );
                    }
                },
                error: function (response) {
                    alert(response.data);
                }
            });
        },
    });
})(jQuery, window);

$(document).ready(function() {
    recargos.init();
});