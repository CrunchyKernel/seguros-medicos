
var verCotizacion = verCotizacion || {
  
};

;(function($, window, undefined){
    "use strict";

    $.extend(verCotizacion, {   
        init: function(){
            
        },
        enviarCotizacinEmail: function(idCotizacion, secret, sa, ded){
            if(idCotizacion > 0){
                $.ajax({
                        url: _root_+'cotizacion/enviarCotizacinEmail',
                        method: 'POST',
                        dataType: 'json',
                        data : { idCotizacion : idCotizacion, secret : secret, sa : sa, ded : ded },
                        cache : false,
                        beforeSend: function(){
                           $('.enviarCotizacinEmail').button("loading");
                        },
                        error: function()
                        {
                            $(".enviarCotizacinEmail").button('reset');
                            swal("Enviar cotización por e-mail", "Ocurrio un error al tratar de enviar la cotización.", "warning");
                        },
                        success: function(response)
                        {
                            if(response.status == 'success'){
                                
                            }
                            $(".enviarCotizacinEmail").button('reset');
                            swal({
                                title: response.titulo,
                                text: response.mensaje,
                                type: response.tipo,
                                html: true,
                            });
                        }
                    });
            }else{
                Adminsis.notificacion("Enivar cotización por e-mail", "El ID de la cotización es incorrecto", "stack_bar_bottom", "error");
            }
        },
    });
    $('body').delegate('.enviarCotizacinEmail', 'click', function(event){
        event.preventDefault();
        var idCotizacion = $(this).data('idcotizacion');
        var secret = $(this).data('secret');
        var sa = $(this).data('sa');
        var ded = $(this).data('ded');
        verCotizacion.enviarCotizacinEmail(idCotizacion, secret, sa, ded);
    });
})(jQuery, window);

jQuery(document).ready(function() {
    verCotizacion.init();
});
