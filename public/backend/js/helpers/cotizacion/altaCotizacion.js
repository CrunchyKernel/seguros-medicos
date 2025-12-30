
var altaCotizacion = altaCotizacion || {
  
};

;(function($, window, undefined){
    "use strict";

    var form = $("#cotizacionForm");
    var integrantes = [];
    
    $.extend(altaCotizacion, {   
        init: function(){
        	jQuery('.sexos').select2({
                minimumResultsForSearch: -1
            });
            jQuery('.edades').spinner();
            this.formulario();
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
                    e_mail: {
                        required: true,
                        email: true,
                    },
                    ciudad: {
                        required: true
                    },
                },
                messages: {
                    nombre: {
                        required: "Escriba el nombre del cliente"
                    },
                    e_mail: {
                        required: "Escriba el e-mail del cliente",
                        email: "Escriba un e-mail válido",
                    },
                    ciudad: {
                        required: "Escriba la ciudad"
                    },
                }
            });
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
        agregarCotizacion: function(){
        	var integrantes = altaCotizacion.validarIntegrantes();
        	console.log(integrantes);
        	if(integrantes.length > 0){
	            if(form.valid() === true){
	                $.ajax(_root_ + "cotizacion/agregarCotizacion",{
	                                        data : $(form).serialize(),
	                                        cache: false,
	                                        timeout: 15000,
	                                        method: 'POST',
	                                        dataType: 'json',
	                                        beforeSend: function(){
	                                            $(".agregarCotizacion").button('loading');
	                                        },
	                                        complete: function(){
	                                            $(".agregarCotizacion").button('reset');
	                                        },
	                                        success: function(respuesta){
	                                            if(respuesta.status == 'success'){
	                                                $(form)[0].reset();
	                                                location.href = _root_+"cotizacion/verCotizacion/"+respuesta.idCotizacion+"/"+respuesta.secret;
	                                            }else{
	                                            	$(".agregarCotizacion").button('reset');
	                                            }
	                                            Adminsis.notificacion(respuesta.titulo, respuesta.mensaje, respuesta.posicion, respuesta.tipo);
	                                        },
	                                        error: function(data){
	                                            
	                                        }
	                                    });
	            }
        	}
        },
    });
    $('body').delegate('.agregarCotizacion', 'click', function(event){
        event.preventDefault();
        altaCotizacion.agregarCotizacion();
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
})(jQuery, window);

jQuery(document).ready(function() {
    altaCotizacion.init();
});