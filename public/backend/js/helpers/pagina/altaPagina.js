
var altaPagina = altaPagina || {
  
};

;(function($, window, undefined){
    "use strict";

    $.extend(altaPagina, {   
        init: function(){
            
        },
        
    });
    $(".seccion").hover(
        function() {
            $(this).append('<div class="seccion_insert_hover_add"><i class="fa fa-plus"></i>&nbsp;Agregar</div>');
            $('.seccion_insert_hover_add').click(function() {
                //$("#newsletter-builder-area-center-frame-content").prepend($("#newsletter-preloaded-rows .sim-row[data-id='"+$(this).parent().attr("data-id")+"']").clone());
                $("#frameWrapper").prepend($("#secciones_html .seccion-row[data-id='"+$(this).parent().attr("data-id")+"']").clone());
                //hover_edit();
                //perform_delete();
                //$("#newsletter-builder-area-center-frame-buttons-dropdown").fadeOut(200);
            });
        }, function() {
            $(this).children(".seccion_insert_hover_add").remove();
        }
    ); 
})(jQuery, window);

jQuery(document).ready(function() {
    altaPagina.init();
});
