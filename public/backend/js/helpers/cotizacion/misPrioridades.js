
var cotizacionesPrioridad = cotizacionesPrioridad || {
  
};

;(function($, window, undefined){
    "use strict";

    var oTable = null;
    
    $.extend(cotizacionesPrioridad, {   
        init: function(){
            jQuery('#estatus').select2({
                minimumResultsForSearch: -1
            });
            jQuery('#id_agente').select2({
                minimumResultsForSearch: -1
            });
            this.crearTabla();
        },
        actualizarTabla: function(){
            oTable.draw(false);
        },
        crearTabla: function(estatus){
            oTable = $('#listadoCotizaciones').DataTable({
                fnDrawCallback : function (oSettings) {
                    $(".campo").editable({
                            url: _root_+"cotizacion/actualizarCotizacionCampos",
                            sourceCache: false,
                            params: function(params) {
                                params.campo = $(this).data('campo');
                                return params;
                            },
                            success: function(response, newValue) {
                                if(response == true){
                                    Adminsis.notificacion("Actualizacion", "Campo actualizado", "stack_bottom_left", "success");
                                }else{
                                    cotizacionesPrioridad.actualizarTabla();
                                    Adminsis.notificacion("Actualizacion", "Ocurrio un error al tratar el actualizar el campo", "stack_bottom_left", "error");
                                }
                            }
                        });
                    jQuery('.tooltips').tooltip({ container: 'body'});
                },
                responsive: true,
                "aoColumnDefs": [{
                    'bSortable': false,
                    'aTargets': [7]
                }],
                "aaSorting": [[ 0, "desc" ]],
                "aoColumns": [
                            { "sWidth": "30px", sClass: "alignCenter" },
                            null,
                            { "sWidth": "30px", sClass: "alignCenter" },
                            { "sWidth": "200px" },
                            { "sWidth": "200px" },
                            { "sWidth": "60px", sClass: "alignCenter" },
                            { "sWidth": "80px" },
                            { "sWidth": "80px", sClass: "alignCenter" },
                            { "sWidth": "40px", sClass: "alignCenter" }
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
                "sAjaxSource": _root_+"cotizacion/getConsultaCotizacionesPrioridad",
                "bServerSide": true,
                "fnServerData": function ( sSource, aoData, fnCallback, oSettings ) {
                    aoData.push( { "name": "estatus", "value": $('#estatus').val() } );
                    aoData.push( { "name": "id_agente", "value": $('#id_agente').val() } );
                    
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
        eliminarCotizacion: function(idCotizacion, estatus){
            if(idCotizacion > 0){
                swal({
                    title: "Eliminar cotización",
                    text: "¿Seguro que desea eliminar la cotización: <strong>"+idCotizacion+"</strong>?",
                    type: "warning",
                    showCancelButton: true,
                    cancelButtonText: "Cancelar",
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Sí, eliminarla!",
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true,
                    html: true,
                }, function(){
                    $.ajax({
                        url: _root_+'cotizacion/eliminarCotizacion',
                        method: 'POST',
                        dataType: 'json',
                        data : { idCotizacion : idCotizacion },
                        cache : false,
                        error: function()
                        {
                            swal("Eliminar cotización", "Ocurrio un error al tratar de eliminar la cotización.", "warning");
                        },
                        success: function(response)
                        {
                            if(response.status == 'success'){
                                cotizacionesPrioridad.actualizarTabla(estatus);
                            }
                            swal({
                                title: response.titulo,
                                text: response.mensaje,
                                type: response.tipo,
                                html: true,
                            });
                        }
                    });
                });
            }else{
                Adminsis.notificacion("Eliminar cotización", "El ID de la cotización es incorrecto", "stack_bar_bottom", "error");
            }
        },
    });
    $('body').delegate('.eliminarCotizacion', 'click', function(event){
        event.preventDefault();
        var idCotizacion = $(this).data('idcotizacion');
        var estatus = $(this).data('estatus');
        cotizacionesPrioridad.eliminarCotizacion(idCotizacion, estatus);
    });
    $('body').delegate('.actualizarTabla', 'click', function(event){
        event.preventDefault();
        cotizacionesPrioridad.actualizarTabla();
    });
})(jQuery, window);

jQuery(document).ready(function() {
    cotizacionesPrioridad.init();
});