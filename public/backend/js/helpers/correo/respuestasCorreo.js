
var consultaCorreoRespuesta = consultaCorreoRespuesta || {
  
};

;(function($, window, undefined){
    "use strict";

    var oTable = null;
    
    $.extend(consultaCorreoRespuesta, {   
        init: function(){
            this.crearTabla();
        },
        actualizarTabla: function(){
            oTable.draw(false);
        },
        crearTabla: function(){
            oTable = $('#listadoCorreoRespuestas').DataTable({
                fnDrawCallback : function (oSettings) {
                    $(".campo").editable({
                            url: _root_+"Correo/actualizarCorreoRespuestaCampos",
                            sourceCache: false,
                            params: function(params) {
                                params.campo = $(this).data('campo');
                                params._method = 'PUT';
                                return params;
                            },
                            success: function(response, newValue) {
                                if(response == true){
                                    Adminsis.notificacion("Actualizacion", "Campo actualizado", "stack_bottom_left", "success");
                                }else{
                                    consultaCorreoRespuesta.actualizarTabla();
                                    Adminsis.notificacion("Actualizacion", "Ocurrio un error al tratar el actualizar el campo", "stack_bottom_left", "error");
                                }
                            }
                        });
                    jQuery('.tooltips').tooltip({ container: 'body'});
                },
                responsive: true,
                "aoColumnDefs": [{
                    'bSortable': false,
                    'aTargets': [3]
                }],
                "aaSorting": [[ 0, "desc" ]],
                "aoColumns": [
                            { "sWidth": "30px", sClass: "alignCenter" },
                            { "sWidth": "120px", sClass: "alignCenter"  },
                            { "sWidth": "120px", sClass: "alignCenter"  },
                            null,
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
                "sAjaxSource": _root_+"Correo/getConsultaCorreoRespuesta",
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
        eliminarCorreoRespuesta: function(id, titulo){
            if(id > 0){
                swal({
                    title: "Eliminar respuesta de correo",
                    text: "¿Seguro que desea eliminar la respuesta de correo: <strong>"+titulo+"</strong>?",
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
                        url: _root_+'Correo/respuestasCorreo/'+id,
                        method: 'DELETE',
                        dataType: 'json',
                        cache : false,
                        error: function()
                        {
                            swal("Eliminar correo respuesta", "Ocurrio un error al tratar de eliminar la respuesta de correo.", "warning");
                        },
                        success: function(response)
                        {
                            if(response.status == 'success'){
                                consultaCorreoRespuesta.actualizarTabla();
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
                Adminsis.notificacion("Eliminar correo respuesta", "El ID es incorrecto", "stack_bar_bottom", "error");
            }
        },
    });
    $('body').delegate('.eliminarCorreoRespuesta', 'click', function(event){
        event.preventDefault();
        var id = $(this).data('id');
        var titulo = $(this).data('titulo');
        consultaCorreoRespuesta.eliminarCorreoRespuesta(id, titulo);
    });
    $('body').delegate('.actualizarTabla', 'click', function(event){
        event.preventDefault();
        consultaCorreoRespuesta.actualizarTabla();
    });
    $('body').delegate('.agregarCorreoRespuesta','click',function(event){
        event.preventDefault();
        $.ajax({
            url: _root_+'Correo/respuestasCorreo',
            method: 'POST',
            dataType: 'json',
            data: $('#correoRespuestaForm').serialize(),
            cache : false,
            error: function()
            {
                swal("Agregar correo respuesta", "Ocurrio un error al tratar de agregar la respuesta de correo.", "warning");
            },
            error: function()
            {
                swal("Eliminar correo respuesta", "Ocurrio un error al tratar de eliminar la respuesta de correo.", "warning");
            },
            beforeSend: function(){
                $(".agregarCorreoRespuesta").button('loading');
            },
            complete: function(){
                $(".agregarCorreoRespuesta").button('reset');
            },
            success: function(response)
            {
                $(".agregarCotizacion").button('reset');
                if(response.status == 'success'){
                    $('#correoRespuestaForm')[0].reset();
                    consultaCorreoRespuesta.actualizarTabla();
                }
                Adminsis.notificacion(response.titulo, response.mensaje, "stack_bar_bottom", response.tipo);
                /*
                swal({
                    title: response.titulo,
                    text: response.mensaje,
                    type: response.tipo,
                    html: true,
                });
                */
            }
        });
    });
})(jQuery, window);

jQuery(document).ready(function() {
    consultaCorreoRespuesta.init();
});