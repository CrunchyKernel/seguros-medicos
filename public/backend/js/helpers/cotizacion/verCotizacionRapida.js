
var verCotizacion = verCotizacion || {
  
};

;(function($, window, undefined){
    "use strict";

    $.extend(verCotizacion, {   
        init: function(){
            
        },
        enviarCotizacionEmail: function(dialog){
            if($('#idCotizacionEmail').val() > 0){
                $.ajax({
                        url: _root_+'cotizacion/enviarCotizacinEmail',
                        method: 'POST',
                        dataType: 'json',
                        //data : { idCotizacion : idCotizacion, secret : secret, sa : sa, ded : ded },
                        data : { idCotizacionEmail : $('#idCotizacionEmail').val(), para : $(".tm-input").tagsManager('tags'), sa : $('#sa').val(), ded : $('#ded').val(), mensaje : $(".summernote-quick").code() },
                        cache : false,
                        beforeSend: function(){
                            $(".enviarCotizacionEmailBtn").button('loading');
                        },
                        error: function()
                        {
                            $(".enviarCotizacionEmailBtn").button('reset');
                            swal("Enviar cotización por e-mail", "Ocurrio un error al tratar de enviar la cotización.", "warning");
                        },
                        success: function(response)
                        {
                            if(response.status == 'success'){
                                dialog.dockmodal("close");
                            }
                            $(".enviarCotizacionEmailBtn").button('reset');
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
    $('body').delegate('.enviarCotizacionEmail', 'click', function(event){
        event.preventDefault();
        var idCotizacion = $(this).data('idcotizacion');
        var secret = $(this).data('secret');
        var sa = $(this).data('sa');
        var ded = $(this).data('ded');
        var e_mail = $(this).data('email');
        //verCotizacion.enviarCotizacionEmail(idCotizacion, secret, sa, ded);

        $('#enviarCotizacionEmailDiv').dockmodal({
            title: 'Enviar cotización: '+idCotizacion,
            initialState: "modal",
            showPopout: false,
            showMinimize: false,
            dialogClass : 'cotizacionEmailForm',
            buttons: [{
                html: "Enviar",
                buttonClass: "btn btn-primary btn-sm enviarCotizacionEmailBtn",
                click: function(e, dialog) {
                    //CotizacionPaquetes.enviarCotizacionEmail(dialog);
                    verCotizacion.enviarCotizacionEmail(dialog);
                }
            }],
            open: function(event, dialog){
                $('.enviarCotizacionEmailBtn').attr('data-loading-text', 'Procesando... <img src="'+_root_+'../backend/images/loaders/loader31.gif">');
                $('#idCotizacionEmail').val(idCotizacion);
                $('#sa').val(sa);
                $('#ded').val(ded);
                $(".tm-input").tagsManager('empty');
                $(".tm-input").tagsManager('pushTag', e_mail);
                $('.summernote-quick').code('');
            },
            close: function(event, dialog){
                $('#idCotizacionEmail').val('');
                $('#sa').val('');
                $('#ded').val('');
            },
        });
        $('.summernote-quick').summernote({
            height: 275,
            focus: false,
            placeholder: 'Escriba su mensaje...',
            toolbar: [
                ['style', ['bold', 'italic', 'underline', ]],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
            ]
        });
    });
    $('body').delegate('#prioridadCotizacion', 'change', function(event){
        var idCotizacion = $(this).data('idcotizacion');
        var prioridad = $(this).is(":checked");
        
        $.ajax({
            url: _root_+'cotizacion/prioridadCotizacion',
            method: 'POST',
            dataType: 'json',
            data : { idCotizacion : idCotizacion, prioridad : prioridad },
            cache : false,
            beforeSend: function(){
                
            },
            error: function()
            {
                
            },
            success: function(response)
            {
                if(response.status == 'success'){
                    if(response.idCotizacionSiguiente > 0){
                        location.href = _root_+'cotizacion/verCotizacion/'+response.idCotizacionSiguiente;
                    }
                }
                Helper.notificacion(response.titulo, response.mensaje, response.posicion, response.tipo);
            }
        });
    });
})(jQuery, window);

jQuery(document).ready(function() {
    verCotizacion.init();
});
