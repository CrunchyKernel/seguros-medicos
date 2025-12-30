var emailWhiteList = emailWhiteList || {
	
};

;(function($, window, undefined)
{
	"use strict";

    var oTable = null;
    var form = $("#altaEmailWhiteForm");
    
    $.extend(emailWhiteList, {   
        
        init: function(){
            this.crearTabla();
            this.formulario();
            //$('#cotizacionesTotales').spinner({
            //    min: 0,
            //    step: 1,
            //    //start: 1000,
            //});
        },
        formulario: function(){
            form.validate({
                ignore: [],
                errorClass: "state-error",
                validClass: "state-success",
                errorElement: "em",
                highlight: function(element, errorClass, validClass) {
                    $(element).closest('.field').addClass(errorClass).removeClass(validClass);
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).closest('.field').removeClass(errorClass).addClass(validClass);
                },
                errorPlacement: function(error, element) {
                    if (element.is(":radio") || element.is(":checkbox")) {
                        element.closest('.option-group').after(error);
                    } else {
                        error.insertAfter(element.parent());
                    }
                },
                invalidHandler: function(e, validator){
                    var errors = validator.numberOfInvalids();
                    if (errors) {
                        validator.errorList[0].element.focus();
                    }
                },
                submitHandler: function(){
                    $.ajax({
                        url: _root_+'cotizacion/guardarEmailWhiteList',
                        method: 'POST',
                        dataType: 'json',
                        data : form.serialize(),
                        cache : false,
                        beforeSend: function(){
                            $('.agregarEmailWhite').button('loading');
                        },
                        error: function()
                        {
                            $('.agregarEmailWhite').button('reset');
                        },
                        success: function(response)
                        {
                            $('.agregarEmailWhite').button('reset');
                            if(response.status == 'success'){
                                form[0].reset();
                                emailWhiteList.actualizarTabla();
                            }
                            Adminsis.notificacion(response.titulo, response.mensaje, response.posicion, response.tipo);
                        }
                    });
                    return false;
                },
                rules: {
                    e_mail: {
                        required: true,
                        email: true
                    },
                    cotizacionesTotales: {
                        number: true
                    },
                },
                messages: {
                    e_mail: {
                        required: "Escriba una dirección de correo",
                        email: "Escriba una dirección de correo válida"
                    },
                    cotizacionesTotales: {
                        number: "Solo números"
                    },
                }
            });
        },
        actualizarTabla: function(estatus){
            oTable.draw(true);
        },
        crearTabla: function(estatus){
            oTable = $('#listadoCorreos').DataTable({
                "aoColumnDefs": [{
                    'bSortable': false,
                    'aTargets': [3]
                }],
                fnDrawCallback : function (oSettings) {
                    //Adminsis.toolPop();
                    
                    $(".campo").editable({
                            url: _root_+"cotizacion/actualizarEmailWhiteListCampo",
                            sourceCache: false,
                            //mode: "inline",
                            validate: function(value) {
                                if ($.trim(value) == ''){
                                    return 'Escriba un número';
                                }
                                if($(this).data('campo') == 'cotizacionesTotales'){
                                    if(isNaN(parseInt(value))){
                                        return 'Escriba un número';
                                    }
                                }
                            },
                            params: function(params) {
                                params.campo = $(this).data('campo');
                                return params;
                            },
                            success: function(response, newValue) {
                                if(response == true){
                                    //Adminsis.notificacion("Actualizacion", "Campo "+$(this).data('nombre')+" actualizado", "stack_bottom_left", "success");
                                }else{
                                    emailWhiteList.actualizarTabla();
                                    Adminsis.notificacion("Actualizacion", "Ocurrio un error al tratar de actualizar el campo ", "stack_bottom_left", "error");
                                }
                            }
                        });
                },
                "oLanguage": {
                    "sLengthMenu": "Mostrando _MENU_ registros por pagina",
                    "sZeroRecords": "No se encontraron registros",
                    "sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    "sInfoEmpty": "Mostrando 0 a 0 de 0 registros",
                    "sInfoFiltered": "(filtrados de _MAX_ total registros)",
                    "sSearch" : "Buscar",
                    "oPaginate": {
                        "sNext": "Siguiente",
                        "sPrevious": "Anterior",
                        "sLast": "Ultima",
                        "sFirst": "Primera"
                    },
                },
                "aoColumns": [
                    { "sWidth": "30px", sClass: "alignCenter" },
                    null,
                    { "sWidth": "50px", sClass: "alignCenter" },
                    { "sWidth": "90px", sClass: "alignCenter"  }
                ],
                "iDisplayLength": 25,
                "aLengthMenu": [
                    [5, 10, 25, 50, -1],
                    [5, 10, 25, 50, "Todos"]
                ],
                "aaSorting": [[ 1, "asc" ]],
                "sDom": '<"dt-panelmenu clearfix"lfr>t<"dt-panelfooter clearfix"ip>',
                "bServerSide": true,
                "sAjaxSource": _root_+"cotizacion/getEmailWhiteList",
                "fnServerData": function ( sSource, aoData, fnCallback, oSettings ) {
                    aoData.push( { "name": "estatus", "value": estatus } );
                    oSettings.jqXHR = $.ajax( {
                        "dataType": 'json', 
                        "type": "POST", 
                        "url": sSource, 
                        "data": aoData,
                        "success": fnCallback
                    });
                }
            });
        },
    });
    $('body').delegate('.eliminarEmailWhite', 'click', function(event){
        event.preventDefault();
        var idEmailWhitelist = $(this).data('idemailwhitelist');

        $.ajax({
                url: _root_+'cotizacion/eliminarEmailWhiteList',
                method: 'POST',
                dataType: 'json',
                data : { idEmailWhitelist : idEmailWhitelist },
                cache : false,
                beforeSend: function(){
                    $('#eliminarEmailWhite'+idEmailWhitelist).button('loading');
                },
                error: function()
                {
                    $('#eliminarEmailWhite'+idEmailWhitelist).button('reset');
                },
                success: function(response)
                {
                    if(response.status == 'success'){
                        emailWhiteList.actualizarTabla();
                    }else{
                        $('#eliminarEmailWhite'+idEmailWhitelist).button('reset');
                    }
                    Adminsis.notificacion(response.titulo, response.mensaje, response.posicion, response.tipo);
                }
            });
    });
})(jQuery, window);

jQuery(document).ready(function() {
    emailWhiteList.init();
});