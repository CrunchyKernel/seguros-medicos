
var verCotizacionNuevo = verCotizacionNuevo || {
  
};

;(function($, window, undefined){
    "use strict";

    var form = $('#seguimientoForm');
    var integrantes = [];

    $.extend(verCotizacionNuevo, {   
        init: function(){
            jQuery('.sexos').select2({
                minimumResultsForSearch: -1
            });
            jQuery('.edades').spinner();
            $('#horaProgramada').timepicker({
                defaultTime: 'current',
                minuteStep: 5,
                showMeridian: false,
            });
            $('#fechaProgramada').datepicker({dateFormat: 'yy-mm-dd'});
            this.formulario();
            $(".tm-input").tagsManager({
                maxTags: 5,
                tagsContainer: '.tags',
                tagClass: 'tm-tag-info',
                validator: function (value) {
                    if(Adminsis.validarEmail(value) == false){
                        Adminsis.notificacion("Direcicón e-mail", "Escriba una dirección de correo válida", "stack_bar_bottom", "warning");
                        return false;
                    }
                    return true;
                },
            });
            this.editables();
            //this.recotizar($("#btnMapfreSADA"), 1);
        },
        editables: function(){
            $(".campo").editable({
                        url: _root_+"cotizacion/actualizarCotizacionCampos",
                        sourceCache: false,
                        params: function(params) {
                            params.campo = $(this).data('campo');
                            return params;
                        },
                        validate: function(value){
                            switch($(this).data('campo')){
                                case 'e_mail':
                                    if(Adminsis.validarEmail(value) == false){
                                        return "Escriba una dirección de correo válida";
                                    }
                                break;
                            }
                            return false;
                        },
                        success: function(response, newValue) {
                            if(response == true){
                                Adminsis.notificacion("Actualizacion", "Campo actualizado", "stack_bottom_left", "success");
                            }else{
                                consultaCotizaciones.actualizarTabla();
                                Adminsis.notificacion("Actualizacion", "Ocurrio un error al tratar el actualizar el campo", "stack_bottom_left", "error");
                            }
                        }
                    });
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
                    notas: {
                        required: true
                    },
                    fecha: {
                        required: true,
                    },
                    hora: {
                        required: true
                    },
                },
                messages: {
                    notas: {
                        required: "Escriba las notas del seguimiento"
                    },
                    fecha: {
                        required: "Selecciona la fecha del seguimiento",
                    },
                    hora: {
                        required: "Seleccione la hora del seguimiento"
                    },
                },
            });
        },
        //enviarCotizacionEmail: function(idCotizacion, secret, sa, ded){
        enviarCotizacionEmail: function(dialog){
            if($('#idCotizacionEmail').val() > 0){
            	var _p = '';
        		$.each($('#cotizacionEmailForm input[type=checkbox]:checked'), function(i, paquete){
        			if(_p!='')
        				_p += ',';
        			_p += $(paquete).data('idpaquete');
        		});
                $.ajax({
                        url: _root_+'cotizacion/enviarCotizacinEmailNuevo',
                        method: 'POST',
                        dataType: 'json',
                        //data : { idCotizacion : idCotizacion, secret : secret, sa : sa, ded : ded },
                        data : { idCotizacionEmail : $('#idCotizacionEmail').val(), para : $(".tm-input").tagsManager('tags'), sa : $('#sa').val(), ded : $('#ded').val(), mensaje : $(".summernote-quick").summernote('code'), paquetes:_p},
                        cache : false,
                        beforeSend: function(){
                            $(".enviarCotizacionEmailBtn").button('loading');
                        },
                        error: function()
                        {
                            $(".enviarCotizacionEmailBtn").button('reset');
                            swal("Enviar cotización por e-mail", "Ocurrio un error al tratar de enviar la cotización.", "warning");
                        },
                        success: function(response)
                        {
                            if(response.status == 'success'){
                                dialog.dockmodal("close");
                            }
                            $(".enviarCotizacionEmailBtn").button('reset');
                            swal({
                                title: response.titulo,
                                text: response.mensaje,
                                type: response.tipo,
                                html: true,
                            });
                        }
                    });
            }else{
                Adminsis.notificacion("Enivar cotización por e-mail", "El ID de la cotización es incorrecto", "stack_bar_bottom", "error");
            }
        },
        verPDF: function(dialog){
        	if($('#pdfidCotizacion').val() > 0){
        		var _p = '';
        		$.each($('#cotizacionPDFForm input[type=checkbox]:checked'), function(i, paquete){
        			if(_p!='')
        				_p += ',';
        			_p += $(paquete).data('idpaquete');
        		});
        		window.open('https://segurodegastosmedicosmayores.mx/verCotizacionPDF/' + $('#pdfidCotizacion').val() + '/' + $('#pdfsecret').val() + '/' + $('#pdfsa').val() + '/' + $('#pdfded').val() + '/' + _p);
        	}
        	else{
        		Adminsis.notificacion("Ver PDF", "El ID de la cotización es incorrecto", "stack_bar_bottom", "error");
        	}
        },
        agregarSeguimiento: function(idCotizacion, cotizacionEstatus, seguimiento, fechaProgramada){
            if(idCotizacion > 0){
                $.ajax({
                    url: _root_+'cotizacion/agregarSeguimiento',
                    method: 'POST',
                    dataType: 'json',
                    data : { idCotizacion : idCotizacion, cotizacionEstatus : cotizacionEstatus, notas : seguimiento, fechaProgramada : fechaProgramada },
                    cache : false,
                    processData: true,
                    beforeSend: function(){
                        
                    },
                    error: function()
                    {
                        
                    },
                    success: function(response)
                    {
                        if(response.status == "success"){
                            if(response.idCotizacionSiguiente > 0){
                                location.href = _root_+'cotizacion/verCotizacion/'+response.idCotizacionSiguiente;
                            }
                        }else{
                            Adminsis.notificacion(response.titulo, response.mensaje, response.posicion, response.tipo);
                        }
                        swal.close();
                    }
                });
            }
        },
        validarIntegrantes: function(){
            integrantes = [];
            if($('input[name^=integrantes]:checkbox:checked').length){
                $('input[name^=integrantes]:checkbox:checked').each(function () {
                    var n = $(this).val();
                    var integranteTexto = 'Hijo(a)';
                    switch(n){
                        case '1':
                            integranteTexto = 'Titular';
                        break;
                        case '2':
                            integranteTexto = 'Conyugue';
                        break;
                        default:
                            integranteTexto = 'Hijo(a): '+n;
                        break;
                    }
                    if($('#nombres_'+n).val().length == 0){
                        Adminsis.notificacion("", 'Escriba el nombre del '+integranteTexto, 'stack_bottom_left', 'error');
                        integrantes = null;
                        integrantes = [];
                        return false;
                    }
                    if($('#sexos_'+n).val() == -1){
                        Adminsis.notificacion("", 'Seleccione el sexo del '+integranteTexto, 'stack_bottom_left', 'error');
                        integrantes = null;
                        integrantes = [];
                        return false;
                    }
                    if($('#edades_'+n).val() < 0){
                        integrantes = null;
                        integrantes = [];
                        Adminsis.notificacion("", 'Edad mínima es: 0', 'stack_bottom_left', 'error');
                        return false;
                    }else if($('#edades_'+n).val().length == 0){
                        integrantes = null;
                        integrantes = [];
                        Adminsis.notificacion("", 'Seleccione la edad del '+integranteTexto, 'stack_bottom_left', 'error');
                        return false;
                    }
                    if(n == 1 && $('#edades_'+n).val() < 18){
                        Adminsis.notificacion("", 'El '+integranteTexto+' debe ser mayor a 18 años', 'stack_bottom_left', 'error');
                        integrantes = null;
                        integrantes = [];
                        return false;
                    }
                    if(n == 2 && $('#edades_'+n).val() < 18){
                        Adminsis.notificacion("", 'El '+integranteTexto+' debe ser mayor a 18 años', 'stack_bottom_left', 'error');
                        integrantes = null;
                        integrantes = [];
                        return false;
                    }
                    integrantes.push({integrante : n, nombre : $('#nombres_'+n).val(), sexo : $('#sexos_'+n).val(), edad : $('#edades_'+n).val()});
                });
                return integrantes;
            }
            return false;
        },
        recotizar: function(button, primera){
        	var idCotizacion = button.data('idcotizacion');
	        var sa = button.data('sa');
	        var ded = button.data('ded');
	        var loading = button.data('loading-text');
	        var text = button.data('text');
	        var t = 0;
	         
	        $.each($(".mapfrePaquete"), function(i, paquete){
	        	t++;
	        	var _h_ = $(paquete).data("hospitales");
	        	var url = _root_+'cotizacion/recotizar2023/' + idCotizacion + '/' + _h_;
		        $.ajax({
		            url: url,
		            method: 'POST',
		            dataType: 'json',
		            cache : false,
		            beforeSend: function(){
		                button.html(loading);
		            },
		            error: function()
		            {
		            	t--;
		            	if(t==0)
		                	button.html(text);
		            },
		            success: function(response)
		            {
		            	$.each(response["conceptos"], function(x, concepto){
		            		$("#" + _h_ + "-" + concepto["id"]).html(concepto["format"]);
		            	});
		            	
	                	$("#contado-sadb-" + _h_).html("<strong>" + response["contado"] + "</strong>");
						$("#semestral-sadb-" + _h_).html(response["semestral-1"]);
						$("#semestral-sadb-" + _h_).html(response["semestral-2"]);
						$("#trimestral-sadb-" + _h_).html(response["trimestral-1"]);
						$("#trimestral-sadb-" + _h_).html(response["trimestral-2"]);
						$("#mensual-sadb-" + _h_).html(response["mensual-1"]);
						$("#mensual-sadb-" + _h_).html(response["mensual-2"]);
						
						t--;
						if(t==0)
							button.html(text);
		            }
		        });
	        });
		},
    });
    $('body').delegate('.enviarCotizacionEmail', 'click', function(event){
        event.preventDefault();
        var idCotizacion = $(this).data('idcotizacion');
        var secret = $(this).data('secret');
        var sa = $(this).data('sa');
        var ded = $(this).data('ded');
        var e_mail = $(this).data('email');
        //verCotizacion.enviarCotizacionEmail(idCotizacion, secret, sa, ded);

        $('#enviarCotizacionEmailDiv').dockmodal({
            title: 'Enviar cotización: '+idCotizacion,
            initialState: "modal",
            showPopout: false,
            showMinimize: false,
            dialogClass : 'cotizacionEmailForm',
            buttons: [{
                html: "Enviar",
                buttonClass: "btn btn-primary btn-sm enviarCotizacionEmailBtn",
                click: function(e, dialog) {
                    //CotizacionPaquetes.enviarCotizacionEmail(dialog);
                    verCotizacionNuevo.enviarCotizacionEmail(dialog);
                }
            }],
            open: function(event, dialog){
                $('.enviarCotizacionEmailBtn').attr('data-loading-text', 'Procesando... <img src="'+_root_+'../backend/images/loaders/loader31.gif">');
                $('#idCotizacionEmail').val(idCotizacion);
                $('#sa').val(sa);
                $('#ded').val(ded);
                $(".tm-input").tagsManager('empty');
                $(".tm-input").tagsManager('pushTag', e_mail);
                $('.summernote-quick').summernote('code');
            },
            close: function(event, dialog){
                $('#idCotizacionEmail').val('');
                $('#sa').val('');
                $('#ded').val('');
            },
        });
        $('.summernote-quick').summernote({
            height: 275,
            focus: false,
            placeholder: 'Escriba su mensaje...',
            toolbar: [
                ['style', ['bold', 'italic', 'underline', ]],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
            ]
        });
    });
    $('body').delegate('.verCotizacionPDF', 'click', function(event){
        event.preventDefault();
        var idCotizacion = $(this).data('idcotizacion');
        var secret = $(this).data('secret');
        var sa = $(this).data('sa');
        var ded = $(this).data('ded');
        
        $('#verPDFDiv').dockmodal({
            title: 'Ver PDF: '+idCotizacion,
            initialState: "modal",
            showPopout: false,
            showMinimize: false,
            dialogClass : 'cotizacionPDFForm',
            buttons: [{
                html: "Ver",
                buttonClass: "btn btn-primary btn-sm verPDFBtn",
                click: function(e, dialog) {
                    //CotizacionPaquetes.enviarCotizacionEmail(dialog);
                    verCotizacionNuevo.verPDF(dialog);
                }
            }],
            open: function(event, dialog){
                $('.verPDFBtn').attr('data-loading-text', 'Procesando... <img src="'+_root_+'../backend/images/loaders/loader31.gif">');
                $('#pdfidCotizacion').val(idCotizacion);
                $('#pdfsecret').val(secret);
                $('#pdfsa').val(sa);
                $('#pdfded').val(ded);
            },
            close: function(event, dialog){
                $('#pdfidCotizacion').val('');
                $('#pdfsecret').val('');
                $('#pdfsa').val('');
                $('#pdfded').val('');
            },
        });
    });
    $('body').delegate('.agregarSeguimiento', 'click', function(event){
        event.preventDefault();
        var cotizacionEstatus = $(this).data('cotizacionestatus');
        var idCotizacion = $(this).data('idcotizacion');
        var seguimiento = $('#notas').val();
        var fechaProgramada = $("#fechaProgramada").val();
        var horaProgramada = $("#horaProgramada").val();
        
        var titulo = '';
        var texto = '';
        var botonTexto = 'Si, procede!';
        switch(cotizacionEstatus){
            case 7:
                // cerrar cotizacion
                titulo = "Cerrar cotización";
                texto = "¿Seguro que desea cerrar la cotización?<br>";
                botonTexto = "Sí, cerrar!";
            break;
            case 10:
                // telefono falso
                titulo = "Teléfono falso";
                texto = "El sistema agregará el teléfono a la base de datos y posteriormente las cotizaciones registradas tendrán una marca distintiva, adicionalmente el sistema enviará un correo al cliente avisando que deseamos contactarlo.<br>¿Desea continuar con el proceso?";
                botonTexto = "Sí, márcalo como falso!";
            break;
            case 4:
                //pasar a 2do
                titulo = "Segundo Intento";
                texto = "El sistema asignará el estatus a: <strong>Segundo Intento</strong><br>Con fecha programada: <strong>"+fechaProgramada+' '+horaProgramada+"</strong>.<br>¿Desea continuar?";
                botonTexto = "Sí, Segundo Intento!";
            break;
            case 5:
                //pasar a 3er
                titulo = "Tercer Intento";
                texto = "El sistema asignará el estatus a: <strong>Tercer Intento</strong><br>Con fecha programada: <strong>"+fechaProgramada+' '+horaProgramada+"</strong>.<br>¿Desea continuar?";
                botonTexto = "Sí, Tercer Intento!";
            break;
            case 6:
                // programar
                titulo = "Programar";
                texto = "El sistema asignará el estatus a: <strong>Programada</strong><br>Con fecha programada: <strong>"+fechaProgramada+' '+horaProgramada+"</strong>.<br>¿Desea continuar?";
                botonTexto = "Sí, Programarlo!";
            break;
        }
        swal({
            title: titulo,
            text: texto,
            type: "warning",
            showCancelButton: true,
            cancelButtonText: "Cancelar",
            confirmButtonColor: "#DD6B55",
            confirmButtonText: botonTexto,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
            html: true,
        }, function(){
            verCotizacionNuevo.agregarSeguimiento(idCotizacion, cotizacionEstatus, seguimiento, fechaProgramada+' '+horaProgramada);
        });
    });
    $('body').delegate('.actualziarSeguimientoEstatus', 'change', function(event){
        event.preventDefault();
        var idSeguimiento = $(this).val();
        var estatus = (($(this).is(":checked") == true) ? 1 : -1);
        $.ajax({
            url: _root_+'cotizacion/seguimientoRealizado',
            method: 'POST',
            dataType: 'json',
            data : { idSeguimiento : idSeguimiento, estatus : estatus },
            cache : false,
            beforeSend: function(){
                $(this).prop("disabled", true);
            },
            error: function()
            {
                $(this).prop("disabled", false);
            },
            success: function(response)
            {
                $(this).prop("disabled", false);
                $('.myCheckbox').attr('checked', ((response.realizado == -1) ? false : true));
                if(response.status == 'success'){
                }else{
                    Adminsis.notificacion(response.titulo, response.mensaje, response.posicion, response.tipo);
                }
            }
        });
    });
    $('body').delegate('.actualizarIntegrantes', 'click', function(event){
        event.preventDefault();
        var integrantes = verCotizacionNuevo.validarIntegrantes();
        if(integrantes.length > 0){
            var form = $("#actualizarIntegrantesForm");
            if(form.valid() === true){
                $.ajax(_root_ + "cotizacion/actualizarIntegrantes2023",{
                                        data : $(form).serialize(),
                                        cache: false,
                                        timeout: 0,
                                        method: 'POST',
                                        dataType: 'json',
                                        beforeSend: function(){
                                            $(".actualizarIntegrantes").button('loading');
                                        },
                                        complete: function(){
                                            //$(".actualizarIntegrantes").button('reset');
                                            console.log('Complete');
                                        },
                                        success: function(respuesta){
                                        	console.log(respuesta);
                                            if(respuesta.status == 'success'){
                                            	$("#tab-editar, #cotizacion_editar").removeClass("active");
                                            	$("#tab-cotizacion, #cotizacion_dasa").addClass("active");
                                                verCotizacionNuevo.recotizar($("#btnMapfreSADA"), 0);
                                            }else{
                                                $(form)[0].reset();
                                                $(".actualizarIntegrantes").button('reset');
                                            }
                                            Adminsis.notificacion(respuesta.titulo, respuesta.mensaje, respuesta.posicion, respuesta.tipo);
                                        },
                                        error: function(data){
                                            console.log('Error');
                                            console.log(data);
                                        }
                                    });
            }
        }
    });
    $('body').delegate('.nombres', 'focusout', function(event){
        event.preventDefault();
        if($(this).val().length > 0){
            var id = $(this).data('id');
            $('#integrantes_'+id).prop('checked', true)
        }
    });
    $('body').delegate('.nombres', 'keyup', function(event){
        event.preventDefault();
        if($(this).val().length > 0){
            var id = $(this).data('id');
            $('#integrantes_'+id).prop('checked', true)
        }
    });
    $('body').delegate('.sexos', 'change', function(event){
        event.preventDefault();
        var id = $(this).data('id');
        $('#integrantes_'+id).prop('checked', true)
    });
    $('body').delegate('.edades', 'focusout', function(event){
        event.preventDefault();
        var id = $(this).data('id');
        $('#integrantes_'+id).prop('checked', true)
    });
    $('body').delegate('#prioridadCotizacion', 'change', function(event){
        var idCotizacion = $(this).data('idcotizacion');
        var prioridad = $(this).is(":checked");
        
        $.ajax({
            url: _root_+'cotizacion/prioridadCotizacion',
            method: 'POST',
            dataType: 'json',
            data : { idCotizacion : idCotizacion, prioridad : prioridad },
            cache : false,
            beforeSend: function(){
                
            },
            error: function()
            {
                
            },
            success: function(response)
            {
                if(response.status == 'success'){

                }
                Adminsis.notificacion(response.titulo, response.mensaje, response.posicion, response.tipo);
            }
        });
    });
    $('body').delegate('#textoRespuestaCorreo','change',function(event){
        event.preventDefault();
        //$('.summernote-quick').summernote('editor.insertText', $(this).val());
        $('.summernote-quick').summernote('editor.pasteHTML', $(this).val().replace(/(?:\r\n|\r|\n)/g, '<br>') );
    });
    $('body').delegate('.recotizarMapfre', 'click', function(event){
    	event.preventDefault();
        verCotizacionNuevo.recotizar($(this), 0);
    });
    $('#cmdWhatsapp').click(function(e){
    	e.preventDefault();
    	var idCotizacion = $(this).data('idcotizacion');
        
        $.ajax({
            url: _root_+'cotizacion/cotizacionToListaDistribucion',
            method: 'POST',
            dataType: 'json',
            data : { idCotizacion : idCotizacion},
            cache : false,
            beforeSend: function(){
                
            },
            error: function()
            {
                
            },
            success: function(response)
            {
                if(response.status == 'success'){

                }
                Adminsis.notificacion(response.titulo, response.mensaje, response.posicion, response.tipo);
            }
        });
    });
})(jQuery, window);

jQuery(document).ready(function() {
    verCotizacionNuevo.init();
});
