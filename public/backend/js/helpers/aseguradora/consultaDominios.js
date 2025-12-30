
var consultaDominios = consultaDominios || {
  
};

;(function($, window, undefined){
    "use strict";

    var form = $("#consultaDominios");
    var oTable = null;
    
    $.extend(consultaDominios, {   
        init: function(){
            this.tabla();
        },
        actualizarTabla: function(){
            oTable.draw(true);
        },
        tabla: function(){
            oTable = $('#basicTable').DataTable({
                fnDrawCallback : function (oSettings) {
                	$(".campo").editable({
						url: _root_+"aseguradora/actualizarDominio",
						sourceCache: false,
						params: function(params) {
							params.campo = $(this).data('campo');
							return params;
						},
						success: function(response, newValue) {
							if(response == true){
								Adminsis.notificacion("Actualizacion", "Campo actualizado", "stack_bottom_left", "success");
							}else{
								consultaAdministradores.actualizarTabla();
								Adminsis.notificacion("Actualizacion", "Ocurrio un error al tratar el actualizar el campo", "stack_bottom_left", "error");
							}
						}
					});
					$(".estatus").editable({
						url: _root_+"aseguradora/actualizarDominio",
						sourceCache: false,
						//mode: "inline",
						source: [ {value: 1, text: "Activo"}, {value: 2, text: "Inactivo"} ],
						params: function(params) {
							params.campo = "estatus";
							return params;
						},
						success: function(response, newValue) {
							if(response == true){
								Adminsis.notificacion("Actualizacion", "Estatus actualizado", "stack_bottom_left", "success");
							}else{
								consultaAdministradores.actualizarTabla();
								Adminsis.notificacion("Actualizacion", "Ocurrio un error al tratar de actualizar el estatus.", "stack_bottom_left", "error");
							}
						}
					});
                    jQuery('.tooltips').tooltip({ container: 'body'});
                },
                responsive: false,
                "aoColumnDefs": [{
                    'bSortable': false,
                    'aTargets': [1]
                }],
                "aaSorting": [[ 1, "asc" ]],
                "aoColumns": [
                            { "sWidth": "30px", sClass: "alignCenter" },
                            { "sWidth": "50px", sClass: "alignCenter" },
                            { "sWidth": "50px", sClass: "alignCenter" },
                            { "sWidth": "50px", sClass: "alignCenter" },
                            { "sWidth": "50px", sClass: "alignCenter" },
                            { "sWidth": "50px", sClass: "alignCenter" },
                            { "sWidth": "50px", sClass: "alignCenter" },
                            { "sWidth": "50px", sClass: "alignCenter" },
                            { "sWidth": "50px", sClass: "alignCenter" }
                        ],
                "iDisplayLength": 25,
                "aLengthMenu": [
                    [5, 10, 25, 50, -1],
                    [5, 10, 25, 50, "Todos"]
                ],
                "autoWidth": false,
                "oLanguage": {
                    "sLengthMenu": "Mostrando _MENU_ registros por pagina",
                    "sZeroRecords": "No se encontraron registros",
                    "sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    "sInfoEmpty": "Mostrando 0 a 0 de 0 registros",
                    "sInfoFiltered": "(filtrados de _MAX_ total registros)",
                    "sSearch": "Buscar",
                    "oPaginate": {
                        "sNext": "Siguiente",
                        "sPrevious": "Anterior",
                        "sLast": "Ultima",
                        "sFirst": "Primera"
                    }
                },
                "pagingType": "full_numbers",
                "sAjaxSource": _root_+"aseguradora/getConsultaDominios",
                "bServerSide": true,
                "fnServerData": function ( sSource, aoData, fnCallback, oSettings ) {
                    	oSettings.jqXHR = $.ajax( {
                            "dataType": 'json', 
                            "type": "POST", 
                            "url": sSource, 
                            "data": aoData,
                            "success": fnCallback
                        } );
                }
            });
        },
    });
    $('body').delegate('.actualizarTabla', 'click', function(event){
        event.preventDefault();
        consultaDominios.actualizarTabla();
    });
})(jQuery, window);

jQuery(document).ready(function() {
    consultaDominios.init();
});