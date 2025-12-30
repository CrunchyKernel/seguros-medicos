
var consultaPublicacion = consultaPublicacion || {
  
};

;(function($, window, undefined){
    "use strict";

    var form = $("#consultaPublicacionForm");
    var oTable = null;
    
    $.extend(consultaPublicacion, {   
        init: function(){
            this.tabla();
            jQuery("#id_blog_categoria").select2();
            jQuery('#tipo').select2({
                minimumResultsForSearch: -1
            });
            jQuery('#estatus').select2({
                minimumResultsForSearch: -1
            });
        },
        actualizarTabla: function(){
            oTable.draw(true);
        },
        tabla: function(){
            oTable = $('#basicTable').DataTable({
                fnDrawCallback : function (oSettings) {
                    $(".campo").editable({
                            url: _root_+"publicacion/actualizarPublicacion",
                            sourceCache: false,
                            params: function(params) {
                                params.campo = $(this).data('campo');
                                return params;
                            },
                            success: function(response, newValue) {
                                if(response == true){
                                    Adminsis.notificacion("Actualizacion", "Campo actualizado", "stack_bottom_left", "success");
                                }else{
                                    consultaPublicacion.actualizarTabla();
                                    Adminsis.notificacion("Actualizacion", "Ocurrio un error al tratar el actualizar el campo", "stack_bottom_left", "error");
                                }
                            }
                        });
                    
                    $(".tipo").editable({
                            url: _root_+"publicacion/actualizarPublicacion",
                            sourceCache: false,
                            source: [ {value: 1, text: "Blog"}, {value: 2, text: "Página"} ],
                            params: function(params) {
                                params.campo = "tipo";
                                return params;
                            },
                            success: function(response, newValue) {
                                if(response == true){
                                    Adminsis.notificacion("Actualizacion", "Tipo actualizado", "stack_bottom_left", "success");
                                }else{
                                    consultaPublicacion.actualizarTabla();
                                    Adminsis.notificacion("Actualizacion", "Ocurrio un error al tratar de actualizar el tipo.", "stack_bottom_left", "error");
                                }
                            }
                        });
                    
                    $(".estatus").editable({
                            url: _root_+"publicacion/actualizarPublicacion",
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
                                    consultaPublicacion.actualizarTabla();
                                    Adminsis.notificacion("Actualizacion", "Ocurrio un error al tratar de actualizar el estatus.", "stack_bottom_left", "error");
                                }
                            }
                        });
                    jQuery('.tooltips').tooltip({ container: 'body'});
                },
                responsive: true,
                "aoColumnDefs": [{
                    'bSortable': false,
                    'aTargets': [5]
                }],
                "aaSorting": [[ 1, "asc" ]],
                "aoColumns": [
                            { "sWidth": "30px", sClass: "alignCenter" },
                            { "sWidth": "450px", sClass: "alignCenter" },
                            { "sWidth": "150px", sClass: "alignCenter" },
                            { "sWidth": "120px", sClass: "alignRight" },
                            { "sWidth": "60px", sClass: "alignCenter" },
                            { "sWidth": "60px", sClass: "alignCenter" },
                            { "sWidth": "60px", sClass: "alignCenter" },
                            { "sWidth": "30px", sClass: "alignCenter" }
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
                "sAjaxSource": _root_+"publicacion/getConsultaPublicaciones",
                "bServerSide": true,
                "fnServerData": function ( sSource, aoData, fnCallback, oSettings ) {
                        aoData.push( { "name": "id_blog_categoria", "value": $("#id_blog_categoria").val() } );
                        aoData.push( { "name": "buscar", "value": $("#buscar").val() } );
                        aoData.push( { "name": "tipo", "value": $("#tipo").val() } );
                        aoData.push( { "name": "estatus", "value": $("#estatus").val() } );
                        
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
        eliminarPublicacion: function(idBlog){
            if(idBlog > 0){
                swal({
                    title: "Eliminar publicación",
                    text: "¿Seguro que desea eliminar la publicación: <strong>"+idBlog+"</strong>?",
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
                        url: _root_+'publicacion/eliminarPublicacion',
                        method: 'POST',
                        dataType: 'json',
                        data : { idBlog : idBlog },
                        cache : false,
                        error: function()
                        {
                            swal("Eliminar publicación", "Ocurrio un error al tratar de eliminar la publicación.", "warning");
                        },
                        success: function(response)
                        {
                            if(response.status == 'success'){
                                consultaPublicacion.actualizarTabla();
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
                Adminsis.notificacion("Eliminar publicación", "El ID de la publicación es incorrecto", "stack_bar_bottom", "error");
            }
        },
    });
    $('body').delegate('.eliminarPublicacion', 'click', function(event){
        event.preventDefault();
        var idBlog = $(this).data('idblog');
        consultaPublicacion.eliminarPublicacion(idBlog);
    });
    $('body').delegate('.actualizarTabla', 'click', function(event){
        event.preventDefault();
        consultaPublicacion.actualizarTabla();
    });
    $('body').delegate('#buscar', 'keypress', function(event){
        if (event.which == 13 || event.keyCode == 13) {
            consultaPublicacion.actualizarTabla();
        }
    });
})(jQuery, window);

jQuery(document).ready(function() {
    consultaPublicacion.init();
});