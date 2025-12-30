var Contratar = Contratar || {
	
};

;(function($, window, undefined)
{
	"use strict";
    var form = $("#contratarForm");
    
    $.extend(Contratar, {
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
                    fechaPoliza: {
                        required: function(element) {
                            if($("#contactoPoliza").is(":checked") == true){
                                return true
                            }
                            return false;
                        },
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
                    fechaPoliza: {
                        required: "Selecciona la fecha de vencimiento de su póliza"
                    },
                }
            });
        },
        cotizar: function(){
            if(form.valid() == true){
                $.ajax({
                        url: _root_+'cotizacionContratar',
                        method: 'POST',
                        dataType: 'json',
                        data : $(form).serialize(),
                        cache : false,
                        processData: true,
                        beforeSend: function()
                        {
                            //$(".altaFactura").button('loading');
                        },
                        error: function()
                        {
                            //$(".altaFactura").button('reset');
                        },
                        success: function(response)
                        {
                            if(response.status == 'success'){
                                location.href = _root_+"gracias/";
                            }else{

                            }
                            //$(".altaFactura").button('reset');
                        }
                    });
            }
		},
    });
    $('body').delegate('.cotizarSeguro', 'click', function(event){
        event.preventDefault();

        Contratar.cotizar();
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
    Contratar.init();
});