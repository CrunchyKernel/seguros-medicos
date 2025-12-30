
var sumasAseguradas = sumasAseguradas || {
  
};

;(function($, window, undefined){
    "use strict";

    var form = $("#administradorForm");
    
    $.extend(sumasAseguradas, {   
        init: function(){
            this.editable();
        },
        editable: function(){
    		$(".campo").editable({
                        url: _root_+"aseguradora/actualizarSumaAsegurada",
                        sourceCache: false,
                        params: function(params) {
                            params.campo = $(this).data('campo');
                            params.idConcepto = $(this).data('idconcepto');
                            params.idPaquete = $(this).data('idpaquete');
                            return params;
                        },
                        success: function(response, newValue) {
                            if(response == true){
                                Adminsis.notificacion("Actualizacion", "Campo actualizado", "stack_bottom_left", "success");
                            }else{
                                Adminsis.notificacion("Actualizacion", "Ocurrio un error al tratar el actualizar el campo", "stack_bottom_left", "error");
                            }
                        }
                    });
            $(".derecho_poliza").editable({
                        url: _root_+"aseguradora/actualizarPaquete",
                        sourceCache: false,
                        params: function(params) {
                            params.campo = $(this).data('campo');
                            return params;
                        },
                        success: function(response, newValue) {
                            if(response == true){
                                Adminsis.notificacion("Actualizacion", "Campo actualizado", "stack_bottom_left", "success");
                            }else{
                                Adminsis.notificacion("Actualizacion", "Ocurrio un error al tratar el actualizar el campo", "stack_bottom_left", "error");
                            }
                        }
                    });
        },
    });
})(jQuery, window);

$(document).ready(function() {
    sumasAseguradas.init();
    $(".switchPlan").change(function(){
        var texto = $(this).prop('checked'); 
        var id_plan = $(this).attr('data-id');
        
        $.ajax({
            //Para la actualización requerimos el "id" del paquete q podemos obtener desde la variable textArea
            data : { opcion : texto, id_plan : id_plan },
            url  : "on-off-plan" , 
            method : 'POST',
            success: function(respuesta){
                switch(respuesta){
                    case "Plan activado":
                        swal(respuesta, "la página se recargara automáticamente", "success");
                    break;
                    
                    case "Plan desactivado":
                     swal(respuesta, "la página se recargara automáticamente", "warning");
                    break;
                    
                    default:
                        swal("Ocurrió un error", "la página se recargara automáticamente", "error");    
                }
                
                 window.location.reload();
            }
            
        });
        
    });
    
    $(".switchAseguradora").change(function(){
        var texto = $(this).prop('checked');
        
        
        $.ajax({
            data : { opcion : texto, id_aseguradora : $(this).attr('data-id') },
            url  : "on-off-aseguradora" , 
            method : 'POST',
            success: function(respuesta){
                switch(respuesta){
                    case "Aseguradora activada":
                        swal(respuesta, "la página se recargara automáticamente", "success");
                    break;
                    
                    case "Aseguradora desactivada":
                        swal(respuesta, "la página se recargara automáticamente", "warning");
                    break;
                    
                    default:
                        swal("Ocurrió un error", "la página se recargara automáticamente", "fail");    
                }
               
               window.location.reload();
            }
            
        });
        
    });
    
});