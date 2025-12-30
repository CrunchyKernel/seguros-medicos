
var consultaAdministradores = consultaAdministradores || {
  
};

;(function($, window, undefined){
    "use strict";

    var form = $("#consultaAdministradores");
    var oTable = null;
    
    $.extend(consultaAdministradores, {   
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
                            url: _root_+"administrador/actualizarAdministrador",
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
                            url: _root_+"administrador/actualizarAdministrador",
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
                responsive: true,
                "aoColumnDefs": [{
                    'bSortable': false,
                    'aTargets': [4]
                }],
                "aaSorting": [[ 1, "asc" ]],
                "aoColumns": [
                            { "sWidth": "30px", sClass: "alignCenter" },
                            null,
                            { "sWidth": "100px", sClass: "alignCenter" },
                            { "sWidth": "100px", sClass: "alignCenter" },
                            { "sWidth": "250px" },
                            { "sWidth": "100px" },
                            { "sWidth": "60px", sClass: "alignCenter" },
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
                "sAjaxSource": _root_+"administrador/getConsultaAdministradores",
                "bServerSide": true,
                "fnServerData": function ( sSource, aoData, fnCallback, oSettings ) {
                        //aoData.push( { "name": "id_blog_categoria", "value": $("#id_blog_categoria").val() } );
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
        eliminarAdministrador: function(idAdministrador){
            if(idAdministrador > 0){
                swal({
                    title: "Eliminar administrador",
                    text: "¿Seguro que desea eliminar al administrador: <strong>"+idAdministrador+"</strong>?",
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
                        url: _root_+'administrador/eliminarAdministrador',
                        method: 'POST',
                        dataType: 'json',
                        data : { idAdministrador : idAdministrador },
                        cache : false,
                        error: function()
                        {
                            swal("Eliminar administrador", "Ocurrio un error al tratar de eliminar al administrador.", "warning");
                        },
                        success: function(response)
                        {
                            if(response.status == 'success'){
                                consultaAdministradores.actualizarTabla();
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
                Adminsis.notificacion("Eliminar administrador", "El ID del administrador es incorrecto", "stack_bar_bottom", "error");
            }
        },
        enviarAccesoAdministrador: function(idAdministrador){
            if(idAdministrador > 0){
                swal({
                    title: "Acceso administrador",
                    text: "¿Seguro que desea enviar un acceso nuevo de adminsitrador al: <strong>"+idAdministrador+"</strong>?",
                    type: "warning",
                    showCancelButton: true,
                    cancelButtonText: "Cancelar",
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Sí, envíala!",
                    closeOnConfirm: false,
                    html: true,
                }, function(){
                    $.ajax({
                        url: _root_+'administrador/enviarAccesoAdministrador',
                        method: 'POST',
                        dataType: 'json',
                        data : { idAdministrador : idAdministrador },
                        cache : false,
                        error: function()
                        {
                            swal("Enviar acceso de administrador", "Ocurrio un error al tratar de eliminar al administrador.", "warning");
                        },
                        success: function(response)
                        {
                            if(response.status == 'success'){
                                
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
                Adminsis.notificacion("Enivar acceso administrador", "El ID del administrador es incorrecto", "stack_bar_bottom", "error");
            }
        },
    });
    $('body').delegate('.eliminarAdministrador', 'click', function(event){
        event.preventDefault();
        var idAdministrador = $(this).data('idadministrador');
        consultaAdministradores.eliminarAdministrador(idAdministrador);
    });
    $('body').delegate('.enviarAccesoAdministrador', 'click', function(event){
        event.preventDefault();
        var idAdministrador = $(this).data('idadministrador');
        consultaAdministradores.enviarAccesoAdministrador(idAdministrador);
    });
    $('body').delegate('.actualizarTabla', 'click', function(event){
        event.preventDefault();
        consultaAdministradores.actualizarTabla();
    });
})(jQuery, window);

jQuery(document).ready(function() {
    consultaAdministradores.init();
});