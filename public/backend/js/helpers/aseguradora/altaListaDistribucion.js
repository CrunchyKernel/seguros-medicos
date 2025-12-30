
var altaLista = altaLista || {
  
};

;(function($, window, undefined){
    "use strict";

    var form = $("#listaForm");
    var formPlantilla = $("#plantillaForm");
    var oTable = null;
    
    $.extend(altaLista, {   
        init: function(){
            this.formulario();
            if($('#id_lista').val()!='-1'){
            	this.tabla();
            	this.formularioPlantilla();
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
                    }
                },
                messages: {
                    nombre: {
                        required: 'Escriba el nombre de la lista'
                    }
                }
            });
        },
        agregarLista: function(){
            if(form.valid() === true){
            	var data = new FormData();
                
                $.each(form.serializeArray(), function(i, field){
                    data.append(field.name, field.value);
                });
                $.ajax(_root_ + "aseguradora/guardarListasDistribucion",{
                                        data: data,
                                        cache: false,
                                        contentType:false,
                                        processData: false,
                                        timeout: 15000,
                                        method: 'POST',
                                        dataType: 'json',
                                        beforeSend: function(){
                                            $(".registrarLista").button('loading');
                                        },
                                        complete: function(){
                                            $(".registrarLista").button('reset');
                                        },
                                        success: function(respuesta){
                                            if(respuesta.status == 'success'){
                                                if($('#id_lista').val() == '-1'){
                                                    window.location.href = _root_ + 'aseguradora/altaListaDistribucion/' + respuesta.idLista;
                                                }
                                            }else{
                                                
                                            }
                                            Adminsis.notificacion(respuesta.titulo, respuesta.mensaje, respuesta.posicion, respuesta.tipo);
                                            $(".registrarLista").button('reset');
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
            oTable = $('#plantillasTable').DataTable({
                fnDrawCallback : function (oSettings) {
                	$(".delete").click(function(e){
                		Adminsis.notificacion("Boton Eliminar", $(this).data("nombre"), "stack_bottom_left", "success");
                		/*url: _root_+"aseguradora/actualizarAseguradora",
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
						}*/
					});
					jQuery('.tooltips').tooltip({ container: 'body'});
                },
                responsive: true,
                //"aoColumnDefs": [{
                //    'bSortable': false,
                //    'aTargets': [4]
                //}],
                "aaSorting": [[ 1, "asc" ]],
                "aoColumns": [
                            { "sWidth": "300px", sClass: "" },
                            { "sWidth": "50px", sClass: "alignCenter" }
                            //{ "sWidth": "50px", sClass: "alignCenter" }
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
                "sAjaxSource": _root_+"aseguradora/getConsultaListaDistribucionesPlantillas/" + $('#id_lista').val(),
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
        formularioPlantilla: function(){
        	formPlantilla.validate({
                ignore: [],
                highlight: function(element) {
                    jQuery(element).closest('.form-group').removeClass('has-success').addClass('has-error');
                },
                success: function(element) {
                    jQuery(element).closest('.form-group').removeClass('has-error');
                },
                rules: {
                    plantilla: {
                        required: true
                    },
                    orden: {
                        required: true
                    }
                },
                messages: {
                    plantilla: {
                        required: 'Escriba el nombre de la plantilla'
                    },
                    orden: {
                        required: 'Escriba el orden de la plantilla'
                    }
                }
            });
		},
        agregarPlantilla: function(){
			if(formPlantilla.valid() === true){
                $.ajax(_root_ + "aseguradora/agregarPlantilla",{
                                        data: $("#plantillaForm").serialize(),
                                        cache: false,
                                        timeout: 15000,
                                        method: 'POST',
                                        dataType: 'json',
                                        beforeSend: function(){
                                            $(".registrarPlantilla").button('loading');
                                        },
                                        complete: function(){
                                            $(".registrarPlantilla").button('reset');
                                        },
                                        success: function(respuesta){
                                            if(respuesta.status == 'success'){
                                                $(formPlantilla)[0].reset();
                                                altaLista.actualizarTabla();
                                            }else{
                                                
                                            }
                                            Adminsis.notificacion(respuesta.titulo, respuesta.mensaje, respuesta.posicion, respuesta.tipo);
                                            $(".registrarPlantilla").button('reset');
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
    $('body').delegate('.registrarLista', 'click', function(event){
        event.preventDefault();
        altaLista.agregarLista();
    });
    $('body').delegate('.registrarPlantilla', 'click', function(event){
        event.preventDefault();
        altaLista.agregarPlantilla();
    });
})(jQuery, window);

jQuery(document).ready(function() {
    altaLista.init();
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
    $("#tipo").change(function(){
    	switch($("#tipo").val()){
    		case "":
    			$(".tipo-1, .tipo-2").addClass("hidden");
    			break;
    		case "1":
    			$(".tipo-1").removeClass("hidden");
    			$(".tipo-2").addClass("hidden");
    			break;
    		case "2":
    			$(".tipo-2").removeClass("hidden");
    			$(".tipo-1").addClass("hidden");
    			break;
    	}
    });
});