jQuery(document).ready(function() {
	$("#btnSend").click(function(e){
		e.preventDefault();
		$.ajax({
            data : { idCotizacion: $("#idCotizacion").val()},
            url  : _root_ + "cotizacion/whatsappMessage" , 
            method : 'POST',
            //beforeSend: function(){
            //    $(".btn-info").button('loading');
            //    
            //},
            //complete: function(){
            //    $(".btn-info").button('reset');
            //},
            success: function(respuesta){
                Adminsis.notificacion(respuesta.titulo, respuesta.mensaje, respuesta.posicion, respuesta.tipo);
            }
        });
	});
});