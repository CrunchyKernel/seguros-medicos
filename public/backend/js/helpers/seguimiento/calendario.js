
var Calendario = Calendario || {
  
};

;(function($, window, undefined){
    "use strict";

    $.extend(Calendario, {   
        init: function(){
            this.calendario();
        },
        actualizarTabla: function(){
            
        },
        calendario: function(){
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                editable: true,
                locale: 'es',
                droppable: true,
                navLinks: true,
                eventLimit: true,
                selectable: true,
                selectHelper: true,
                defaultView: 'agendaDay',
                select: function(start, end) {
                    //Calendario.getEventForm(null,moment(start).format('YYYY-MM-DD HH:mm:ss'));
                },
                events: _root_+'seguimiento/getSeguimientosCalendario',
                eventDragStart: function( event, jsEvent, ui, view){
                    $(this).qtip().hide();
                },
                eventDrop: function(event, delta, revertFunc) {
                    var start = event.start.format('YYYY-MM-DD HH:mm:ss');
                    var end = start;
                    if(event.end){
                        end = event.end.format('YYYY-MM-DD HH:mm:ss');
                    }
                    $.ajax({
                        url: _root_+'seguimiento/actualizarSeguimientosCalendario',
                        type: 'POST',
                        data: {id_seguimiento:event.id_seguimiento,start:start,end:end},
                        dataType: "json",
                        success: function(response) {
                            console.log(response)
                            Adminsis.notificacion("Actualizacion", response.mensaje, "stack_bottom_left", response.tipo);
                        }
                    });
                },
                eventResize: function(event,dayDelta,minuteDelta,revertFunc) {
                    
                },
                eventRender: function(event, element) {
                	if (element && event.tooltip) {
						element.qtip({
							content: event.tooltip,
							hide: {
								fixed: true,
								delay: 300
							}
						});
					}
                },
                eventMouseover: function(event) {
					$(this).qtip();
				},
				eventMouseout: function(event) {
					$(this).qtip().hide();
				},
            });
        },
    });
})(jQuery, window);

jQuery(document).ready(function() {
    Calendario.init();
});