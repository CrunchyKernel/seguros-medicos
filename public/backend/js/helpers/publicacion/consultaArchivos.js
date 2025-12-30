
var consultaArchivos = consultaArchivos || {
  
};

;(function($, window, undefined){
    "use strict";

    var oTable = null;
    
    $.extend(consultaArchivos, {   
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
						url: _root_+"publicacion/actualizarArchivo",
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
					jQuery('.tooltips').tooltip({ container: 'body'});
                },
                responsive: false,
                "aoColumnDefs": [{
                    'bSortable': false,
                    'aTargets': [1]
                }],
                "aaSorting": [[ 1, "asc" ]],
                "aoColumns": [
                            { "sWidth": "50px", sClass: "alignCenter" },
                            { "sWidth": "50px", sClass: "alignCenter" },
                            { "sWidth": "50px", sClass: "alignCenter" },
                            { "sWidth": "50px", sClass: "alignCenter" },
                            { "sWidth": "50px", sClass: "alignCenter" },
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
                "sAjaxSource": _root_+"publicacion/getConsultaArchivos",
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
        consultaArchivos.actualizarTabla();
    });
    $('body').on('click', '.delete', function(e){
    	e.preventDefault();
    	var id = $(this).data("id");
    	$.ajax({
    		url: _root_ + 'publicacion/eliminarArchivo/' + id,
    		method: 'POST',
    		dataType: 'json',
    		success: function(data, status, jqXHR){
				consultaArchivos.actualizarTabla();
			},
			error: function(jqXHR, status, error){
				console.log(error);
			}
    	});
    });
})(jQuery, window);

jQuery(document).ready(function() {
    consultaArchivos.init();
});