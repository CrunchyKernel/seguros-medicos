var Cotizador = Cotizador || {
	
};

;(function($, window, undefined)
{
	"use strict";
    var form = $("#integrantesForm");
    var integrantes = [];
    
    $.extend(Cotizador, {
        init: function(){
            this.formulario();
        },
        formulario: function(){
            form.validate({
                success: function(label) {
                    
                },
                rules: {
                    nombre: {
                        required: true,
                    },
                    e_mail: {
                        required: true,
                        email: true
                    },
                    telefono: {
                        required: true
                    },
                    ciudad: {
                        required: true
                    },
                },
                messages: {
                    nombre: {
                        required: "Escriba su nombre",
                    },
                    e_mail: {
                        required: "Escriba su correo electrónico",
                        email: "Escriba un correo electrónico válido"
                    },
                    telefono: {
                        required: "Escriba su teléfono"
                    },
                    ciudad: {
                        required: "Escriba su ciudad"
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
                        integrantes = [];
                        alert('Escriba el nombre del '+integranteTexto)
                        return false;
                    }
                    if($('#sexos_'+n).val() == -1){
                        integrantes = [];
                        alert('Seleccione el sexo del '+integranteTexto)
                        return false;
                    }
                    if($('#edades_'+n).val() < 0){
                        integrantes = [];
                        alert('Edad mínima es: 0')
                        return false;
                    }else if($('#edades_'+n).val().length == 0){
                        integrantes = [];
                        alert('Seleccione la edad del '+integranteTexto)
                        return false;
                    }
                    if(n == 1 && $('#edades_'+n).val() < 18){
                        integrantes = [];
                        alert('El titular debe ser mayor a 18 años')
                        return false;
                    }
                    if(n == 2 && $('#edades_'+n).val() < 18){
                        integrantes = [];
                        alert('El conyuge debe ser mayor a 18 años')
                        return false;
                    }
                    integrantes.push({integrante : n, nombre : $('#nombres_'+n).val(), sexo : $('#sexos_'+n).val(), edad : $('#edades_'+n).val()});
                });
                return integrantes;
            }
            return false;
        },
        cotizar: function(){
            var integrantes = Cotizador.validarIntegrantes();
            console.log(integrantes.length);
            if(integrantes.length > 0){
                if(form.valid() == true){
                	if($(form).data("submitted")=="0"){
	                	console.log("valida");
	                	$.ajax({
	                            url: _root_+'nuevaCotizacion',
	                            method: 'POST',
	                            dataType: 'json',
	                            data : $(form).serialize(),
	                            cache : false,
	                            processData: true,
	                            beforeSend: function()
	                            {
	                            	$(form).data("submitted", "1");
	                                $('.cotizarSeguro').button("loading");
	                            },
	                            error: function()
	                            {
	                            	$(form).data("submitted", "0");
	                                $('.cotizarSeguro').button("reset");
	                            },
	                            success: function(response)
	                            {
	                                if(response.status == 'success'){
	                                    $(form)[0].reset();
	                                    location.href = _root_+"verCotizacion/"+response.idCotizacion+"/"+response.secret;
	                                }else{
	                                	 swal("Error", response.mensaje, "error");
	                                    $('.cotizarSeguro').button("reset");
	                                }
	                            }
	                        });
					}
					else
						console.log('Ya enviada');
                }
            }else{
                console.log("-- forma no valida");
            }
		},
    });
    /*$('body').delegate('.cotizarSeguro', 'click', function(event){*/
    $('#integrantesForm').submit(function(e){
        e.preventDefault();
		console.log("-- Empieza");
        Cotizador.cotizar();
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
    Cotizador.init();
});
