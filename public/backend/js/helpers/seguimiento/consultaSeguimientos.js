
var consultaSeguimientos = consultaSeguimientos || {
  
};

;(function($, window, undefined){
    "use strict";

    var oTable = null;
    
    $.extend(consultaSeguimientos, {   
        init: function(){
            this.crearTabla();
        },
        actualizarTabla: function(){
            oTable.draw(false);
        },
        crearTabla: function(){
            oTable = $('#listadoSeguimientos').DataTable({
                fnDrawCallback : function (oSettings) {
                    $(".campo").editable({
                            url: _root_+"seguimiento/actualizarSeguimientoCampos",
                            sourceCache: false,
                            params: function(params) {
                                params.campo = $(this).data('campo');
                                return params;
                            },
                            success: function(response, newValue) {
                                if(response == true){
                                    Adminsis.notificacion("Actualizacion", "Campo actualizado", "stack_bottom_left", "success");
                                }else{
                                    consultaSeguimientos.actualizarTabla();
                                    Adminsis.notificacion("Actualizacion", "Ocurrio un error al tratar el actualizar el campo", "stack_bottom_left", "error");
                                }
                            }
                        });
                    jQuery('.tooltips').tooltip({ container: 'body'});
                },
                responsive: true,
                "aoColumnDefs": [{
                    'bSortable': false,
                    'aTargets': [6]
                }],
                "aaSorting": [[ 0, "desc" ]],
                "aoColumns": [
                            { "sWidth": "30px", sClass: "alignCenter" },
                            null,
                            { "sWidth": "120px", sClass: "alignCenter"  },
                            null,
                            { "sWidth": "120px", sClass: "alignCenter" },
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
                "sAjaxSource": _root_+"seguimiento/getConsultaSeguimientos",
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
        eliminarSeguimiento: function(idSeguimiento, estatus){
            if(idSeguimiento > 0){
                swal({
                    title: "Eliminar seguimiento",
                    text: "¿Seguro que desea eliminar la seguimiento: <strong>"+idSeguimiento+"</strong>?",
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
                        url: _root_+'cotizacion/eliminarSeguimiento',
                        method: 'POST',
                        dataType: 'json',
                        data : { idSeguimiento : idSeguimiento },
                        cache : false,
                        error: function()
                        {
                            swal("Eliminar seguimiento", "Ocurrio un error al tratar de eliminar la seguimiento.", "warning");
                        },
                        success: function(response)
                        {
                            if(response.status == 'success'){
                                consultaSeguimientos.actualizarTabla();
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
                Adminsis.notificacion("Eliminar seguimiento", "El ID de la seguimiento es incorrecto", "stack_bar_bottom", "error");
            }
        },
    });
    $('body').delegate('.eliminarSeguimiento', 'click', function(event){
        event.preventDefault();
        var idSeguimiento = $(this).data('idcotizacion');
        var estatus = $(this).data('estatus');
        consultaSeguimientos.eliminarSeguimiento(idSeguimiento, estatus);
    });
    $('body').delegate('.actualizarTabla', 'click', function(event){
        event.preventDefault();
        consultaSeguimientos.actualizarTabla();
    });
})(jQuery, window);

jQuery(document).ready(function() {
    consultaSeguimientos.init();
});