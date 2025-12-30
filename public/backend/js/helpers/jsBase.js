
var altaPublicacion = altaPublicacion || {
  
};

;(function($, window, undefined)
{
  "use strict";
    
    $.extend(altaPublicacion, {   
        init: function(){
            
        },
    });
    /*
    $('body').delegate('.cotizarPaquete', 'click', function(event){
        event.preventDefault();
        var paquete = $("#paquete").val();
        altaPublicacion.cotizarPaqueteFormlario(aseguradora, paquete);
    });
	*/
})(jQuery, window);

jQuery(document).ready(function() {
    altaPublicacion.init();
});