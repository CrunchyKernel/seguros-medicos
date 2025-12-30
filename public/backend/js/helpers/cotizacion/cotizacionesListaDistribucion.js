
var cotizacionesListaDistribucion = cotizacionesListaDistribucion || {
  
};

;(function($, window, undefined){
    "use strict";

    var oTable = null;
    
    $.extend(cotizacionesListaDistribucion, {   
        init: function(){
            this.tabla();
        },
        actualizarTabla: function(){
            oTable.draw(true);
        },
        tabla: function(){
            oTable = $('#basicTable').DataTable({
                fnDrawCallback : function (oSettings) {
                    jQuery('.tooltips').tooltip({ container: 'body'});
                },
                responsive: true,
                //"aoColumnDefs": [{
                //    'bSortable': false,
                //    'aTargets': [4]
                //}],
                //"aaSorting": [[ 4, "asc" ]],
                "aoColumns": [
                            { "sWidth": "100px", sClass: "alignCenter" },
                            { "sWidth": "200px", sClass: "alignCenter" },
                            { "sWidth": "100px", sClass: "alignCenter" },
                            { "sWidth": "100px", sClass: "alignCenter" }
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
                "sAjaxSource": _root_+"cotizacion/getCotizacionesListaDistribucion",
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
        cotizacionesListaDistribucion.actualizarTabla();
    });
})(jQuery, window);

jQuery(document).ready(function() {
    cotizacionesListaDistribucion.init();
});