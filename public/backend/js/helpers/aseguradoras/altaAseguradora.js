
var altaAseguradora = altaAseguradora || {
  
};

;(function($, window, undefined){
    "use strict";

    var form = $("#aseguradoraForm");
    var formPlan = $("#planForm");
    var oTable = null;
    
    $.extend(altaAseguradora, {   
        init: function(){
            this.formulario();
            if($('#id_aseguradora').val()!='-1'){
            	this.tabla();
            	this.formularioPlan();
			}
        },
        formulario: function(){
            form.validate({
                ignore: [],
                highlight: function(element) {
                    jQuery(element).closest('.form-group').removeClass('has-success').addClass('has-error');
                },
                success: function(element) {
                    jQuery(element).closest('.form-group').removeClass('has-error');
                },
                rules: {
                    nombre: {
                        required: true
                    },
                    aseguradora: {
                        required: true
                    },
                    logo: {
                        required: true
                    },
                    interes_semestral: {
                        required: true
                    },
                    interes_trimestral: {
                        required: true
                    },
                    interes_mensual: {
                        required: true
                    },
                    orden: {
                        required: true
                    },
                },
                messages: {
                    nombre: {
                        required: 'Escriba el nombre de la aseguradora'
                    },
                    aseguradora: {
                        required: 'Escriba la clave de la aseguradora'
                    },
                    logo: {
                        required: 'Escriba el logo de la aseguradora'
                    },
                    interes_semestral: {
                        required: 'Escriba el interes semestral de la aseguradora'
                    },
                    interes_trimestral: {
                        required: 'Escriba el interes trimestral de la aseguradora'
                    },
                    interes_mensual: {
                        required: 'Escriba el interes mensual de la aseguradora'
                    },
                    orden: {
                        required: 'Escriba el orden de la aseguradora'
                    },
                }
            });
        },
        agregarAseguradora: function(){
            if(form.valid() === true){
            	var data = new FormData();
                
                $.each(form.serializeArray(), function(i, field){
                    data.append(field.name, field.value);
                });
                var inputFileImageCotizador = document.getElementById('imagen_cotizador');
                if(inputFileImageCotizador.files.length>0){
	                var file_cotizador = inputFileImageCotizador.files[0];
	                data.append('imagen_cotizador', file_cotizador);
						}
                var inputFileImagePdf = document.getElementById('imagen_pdf');
                if(inputFileImagePdf.files.length>0){
                	var file_pdf = inputFileImagePdf.files[0];
                	data.append('imagen_pdf', file_pdf);
				}
                $.ajax(_root_ + "aseguradoras/agregarAseguradora",{
                                        data: data,
                                        cache: false,
                                        contentType:false,
                                        processData: false,
                                        timeout: 15000,
                                        method: 'POST',
                                        dataType: 'json',
                                        beforeSend: function(){
                                            $(".registrarAseguradora").button('loading');
                                        },
                                        complete: function(){
                                            $(".registrarAseguradora").button('reset');
                                        },
                                        success: function(respuesta){
                                            if(respuesta.status == 'success'){
                                                if($('#id_aseguradora').val() == '-1'){
                                                    window.location.href = _root- + 'aseguradoras/altaAseguradora/' + respuesta.idAseguradora;
                                                }
                                                if($('#eliminarImagenCotizador').is(":checked") == true){
                                                    document.getElementById('preview_cotizador').src = _root_+"../backend/images/preview.png";
                                                    $('#eliminarImagenCotizador').prop("checked", false);
                                                }
                                                if($('#eliminarImagenPdf').is(":checked") == true){
                                                    document.getElementById('preview_pdf').src = _root_+"../backend/images/preview.png";
                                                    $('#eliminarImagenPdf').prop("checked", false);
                                                }
                                            }else{
                                                
                                            }
                                            Adminsis.notificacion(respuesta.titulo, respuesta.mensaje, respuesta.posicion, respuesta.tipo);
                                            $(".registrarAseguradora").button('reset');
                                        },
                                        error: function(data){
                                            
                                        }
                                    });
            }
        },
        actualizarTabla: function(){
            oTable.draw(true);
        },
        tabla: function(){
            oTable = $('#planesTable').DataTable({
                fnDrawCallback : function (oSettings) {
                	$(".campo").editable({
						url: _root_+"aseguradoras/actualizarAseguradora",
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
						url: _root_+"aseguradoras/actualizarAseguradora",
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
                "aaSorting": [[ 4, "asc" ]],
                "aoColumns": [
                            { "sWidth": "30px", sClass: "alignCenter" },
                            { "sWidth": "100px", sClass: "alignCenter" },
                            { "sWidth": "100px", sClass: "alignCenter" },
                            { "sWidth": "100px", sClass: "alignCenter" },
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
                "sAjaxSource": _root_+"aseguradoras/getConsultaAseguradoraPlanes/" + $('#id_aseguradora').val(),
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
        formularioPlan: function(){
        	formPlan.validate({
                ignore: [],
                highlight: function(element) {
                    jQuery(element).closest('.form-group').removeClass('has-success').addClass('has-error');
                },
                success: function(element) {
                    jQuery(element).closest('.form-group').removeClass('has-error');
                },
                rules: {
                    paquete: {
                        required: true
                    },
                    paquete_campo: {
                        required: true
                    },
                    derecho_poliza: {
                        required: true
                    },
                    orden: {
                        required: true
                    },
                },
                messages: {
                    paquete: {
                        required: 'Escriba el nombre del plan'
                    },
                    paquete_campo: {
                        required: 'Escriba la clave del plan'
                    },
                    derecho_poliza: {
                        required: 'Escriba el derecho de poliza del plan'
                    },
                    orden: {
                        required: 'Escriba el orden del plan'
					}
                }
            });
		},
        agregarPlan: function(){
			if(formPlan.valid() === true){
                $.ajax(_root_ + "aseguradoras/agregarPlan",{
                                        data: $("#planForm").serialize(),
                                        cache: false,
                                        timeout: 15000,
                                        method: 'POST',
                                        dataType: 'json',
                                        beforeSend: function(){
                                            $(".registrarPlan").button('loading');
                                        },
                                        complete: function(){
                                            $(".registrarPlan").button('reset');
                                        },
                                        success: function(respuesta){
                                            if(respuesta.status == 'success'){
                                                $(formPlan)[0].reset();
                                                altaAseguradora.actualizarTabla();
                                            }else{
                                                
                                            }
                                            Adminsis.notificacion(respuesta.titulo, respuesta.mensaje, respuesta.posicion, respuesta.tipo);
                                            $(".registrarPlan").button('reset');
                                        },
                                        error: function(data){
                                            
                                        }
                                    });
            }
		},
    });
    $('body').delegate('#imagen_cotizador', 'change', function(event){
        event.preventDefault();
        $(this).attr('src');
        var preview = document.getElementById('preview_cotizador');
        preview.src = URL.createObjectURL(event.target.files[0]);
        var newimg = preview.src;
        if(newimg.indexOf('/null') > -1) {
            preview.src = _root_+"backend/images/preview.png";
        }
    });
    $('body').delegate('#imagen_pdf', 'change', function(event){
        event.preventDefault();
        $(this).attr('src');
        var preview = document.getElementById('preview_pdf');
        preview.src = URL.createObjectURL(event.target.files[0]);
        var newimg = preview.src;
        if(newimg.indexOf('/null') > -1) {
            preview.src = _root_+"backend/images/preview.png";
        }
    });
    $('body').delegate('.registrarAseguradora', 'click', function(event){
        event.preventDefault();
        altaAseguradora.agregarAseguradora();
    });
    $('body').delegate('.registrarPlan', 'click', function(event){
        event.preventDefault();
        altaAseguradora.agregarPlan();
    });
})(jQuery, window);

jQuery(document).ready(function() {
    altaAseguradora.init();
    $('[name="textoWeb"], [name="textoMobile"], [name="textoPromo"]').ckeditor({
        height: 500,
        linkShowAdvancedTab: false,
        //scayt_autoStartup: false,
        //enterMode: Number(2),
        enterMode: CKEDITOR.ENTER_P,
        skin:'office2013',
        extraPlugins: 'scayt,justify',
        allowedContent: true,
        pasteFromWordRemoveStyles : false,
        contentsCss : ['https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css'],
        extraAllowedContent : '*{*}'

    //extraPlugins: 'base64image,pastebase64'
    });
    $("#guardaWeb").click( function(){
        var textoProtecto = CKEDITOR.instances['textoWeb'].getData(); 
        $.ajax({
            data : { textoPT : textoProtecto, id:$("#idAseguradora").val()},
            url  : "/admingm/aseguradoras/guardarWeb" , 
            method : 'POST',
            beforeSend: function(){
                $(".btn-info").button('loading');
                
            },
            complete: function(){
                $(".btn-info").button('reset');
            },
            success: function(respuesta){
                if(respuesta == 1){
                    swal("Texto Web", "guardado correctamente", "success");
                }else{
                    swal("Error","No se pudo guardar las notas", "warning");                       
                }
            }
        });
    });
    $("#guardaMobile").click( function(){
        var textoProtecto = CKEDITOR.instances['textoMobile'].getData(); 
        $.ajax({
            data : { textoPT : textoProtecto, id:$("#idAseguradora").val()},
            url  : "/admingm/aseguradoras/guardarMobile" , 
            method : 'POST',
            beforeSend: function(){
                $(".btn-info").button('loading');
                
            },
            complete: function(){
                $(".btn-info").button('reset');
            },
            success: function(respuesta){
                if(respuesta == 1){
                    swal("Texto Movil", "guardado correctamente", "success");
                }else{
                    swal("Error","No se pudo guardar las notas", "warning");                       
                }
            }
        });
    });
    $("#guardaPromo").click( function(){
        var textoProtecto = CKEDITOR.instances['textoPromo'].getData(); 
        $.ajax({
            data : { textoPT : textoProtecto, id:$("#idAseguradora").val()},
            url  : "/admingm/aseguradoras/guardarPromo" , 
            method : 'POST',
            beforeSend: function(){
                $(".btn-info").button('loading');
                
            },
            complete: function(){
                $(".btn-info").button('reset');
            },
            success: function(respuesta){
                if(respuesta == 1){
                    swal("Texto Promociones", "guardado correctamente", "success");
                }else{
                    swal("Error","No se pudo guardar las notas", "warning");                       
                }
            }
        });
    });
});