'use strict';

var Stacks = {
    stack_top_right: {
        "dir1": "down",
        "dir2": "left",
        "push": "top",
        "spacing1": 10,
        "spacing2": 10
    },
    stack_top_left: {
        "dir1": "down",
        "dir2": "right",
        "push": "top",
        "spacing1": 10,
        "spacing2": 10
    },
    stack_bottom_left: {
        "dir1": "right",
        "dir2": "up",
        "push": "top",
        "spacing1": 10,
        "spacing2": 10
    },
    stack_bottom_right: {
        "dir1": "left",
        "dir2": "up",
        "push": "top",
        "spacing1": 10,
        "spacing2": 10
    },
    stack_bar_top: {
        "dir1": "down",
        "dir2": "right",
        "push": "top",
        "spacing1": 0,
        "spacing2": 0
    },
    stack_bar_bottom: {
        "dir1": "up",
        "dir2": "right",
        "spacing1": 0,
        "spacing2": 0
    },
    stack_context: {
        "dir1": "down",
        "dir2": "left",
        "context": $("#stack-context")
    },
}

var Adminsis = Adminsis || {
	
};

;(function($, window, undefined)
{
	"use strict";
    
    $.extend(Adminsis, {	
		
		init: function(){
            
        },
        siguienteCotizacion: function(){
            $.ajax({
                url: _root_+'cotizacion/siguienteCotizacion',
                method: 'POST',
                dataType: 'json',
                //data : {  },
                cache : false,
                processData: true,
                beforeSend: function(){
                    $('.siguienteCotizacion').button('loading');
                },
                error: function()
                {
                    $('.siguienteCotizacion').button('reset');
                },
                success: function(response)
                {
                    if(response.idCotizacionSiguiente){
                        location.href = _root_+'cotizacion/verCotizacion/'+response.idCotizacionSiguiente;
                    }else{
                        $('.siguienteCotizacion').button('reset');
                        Adminsis.notificacion(response.titulo, response.mensaje, response.posicion, response.tipo);
                        location.href = _root_;
                    }
                }
            });
        },
        validarEmail: function(email){
            var expr = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            if (!expr.test(email)){
                return false;
            }
            return true;
        },
        crearMascaras : function(){
            // Input Mask
            if($.isFunction($.fn.inputmask)){
                $("[data-mask]").each(function(i, el){
                    var $this = $(el),
                        mask = $this.data('mask').toString(),
                        opts = {
                            numericInput: attrDefault($this, 'numeric', false),
                            radixPoint: attrDefault($this, 'radixPoint', ''),
                            rightAlignNumerics: attrDefault($this, 'numericAlign', 'left') == 'right'
                        },
                        placeholder = attrDefault($this, 'placeholder', ''),
                        is_regex = attrDefault($this, 'isRegex', '');
                    
                        
                    if(placeholder.length){
                        opts[placeholder] = placeholder;
                    }
                    
                    switch(mask.toLowerCase()){
                        case "telefono_celular":
                            mask = "99-99-99-99-99";
                        break;
                        case "telefono_particular":
                            mask = "(99) 99-99-99-99";
                        break;
                        case "cp":
                            mask = "99999";
                        break;
                        case "currency":
                        case "rcurrency":
                        
                            var sign = attrDefault($this, 'sign', '$');;
                            
                            mask = "999,999,999.99";
                            
                            if($this.data('mask').toLowerCase() == 'rcurrency'){
                                mask += ' ' + sign;
                            }else{
                                mask = sign + ' ' + mask;
                            }
                            
                            opts.numericInput = true;
                            opts.rightAlignNumerics = false;
                            opts.radixPoint = '.';
                            break;
                            
                        case "email":
                            mask = 'Regex';
                            opts.regex = "[a-zA-Z0-9._%-]+@[a-zA-Z0-9-]+\\.[a-zA-Z]{2,4}.[a-zA-Z]{2,4}";
                        break;
                        
                        case "fdecimal":
                            mask = 'decimal';
                            $.extend(opts, {
                                autoGroup       : true,
                                groupSize       : 3,
                                radixPoint      : attrDefault($this, 'rad', '.'),
                                groupSeparator  : attrDefault($this, 'dec', ',')
                            });
                    }
                    
                    if(is_regex)
                    {
                        opts.regex = mask;
                        mask = 'Regex';
                    }
                    $this.inputmask(mask, opts);
                });
            }
        },
        notificacion: function(titulo, texto, noteStack, noteStyle, ocultar, animation, noteShadow, noteOpacity){
        	if(noteStyle == undefined){
				noteStyle = "warning";
			}
			if(noteShadow == undefined){
				noteShadow = false;
			}
			if(noteOpacity == undefined){
				noteOpacity = 1;
			}
			if(noteStack == undefined){
				noteStack = "stack_bar_top";
			}
			if(titulo == undefined){
				titulo = "Seguro de gastos médicos";
			}
			if(texto == undefined){
				texto = "Look at my beautiful styling! ^_^";
			}
            if(animation == undefined){
                animation = "slide";
            }
            if(ocultar == undefined){
                ocultar = true;
            }
			// PNotify Plugin Event Init
            new PNotify({
	            title: titulo,
	            text: texto,
	            shadow: noteShadow,
	            opacity: noteOpacity,
	            addclass: noteStack,
	            type: noteStyle, //"notice", "info", "success", or "error"
	            stack: Stacks[noteStack],
	            width: findWidth(noteStack),
	            delay: 1400,
                hide: ocultar,
                animation: animation, // "none", "show", "fade", and "slide"
                desktop: {
                    desktop: false, // Display the notification as a desktop notification.
                    icon: null, // The URL of the icon to display. If false, no icon will show. If null, a default icon will show.
                    tag: null, // Using a tag lets you update an existing notice, or keep from duplicating notices between tabs. If you leave tag null, one will be generated, facilitating the "update" function.
                },
                buttons: {
                    closer: true, // Provide a button for the user to manually close the notice.
                    closer_hover: false, // Only show the closer button on hover.
                    sticker: false, // Provide a button for the user to manually stick the notice.
                    sticker_hover: false, // Only show the sticker button on hover.
                },
	        });
		},
        
    });
    $('body').delegate('.siguienteCotizacion', 'click', function(event){
        event.preventDefault();
        Adminsis.siguienteCotizacion();
    })
})(jQuery, window);

jQuery(document).ready(function() {
    Adminsis.init();
});

function findWidth(noteStack) {
    if (noteStack == "stack_bar_top") {
        return "100%";
    }
    if (noteStack == "stack_bar_bottom") {
        return "70%";
    } else {
        return "290px";
    }
}

function attrDefault($el, data_var, default_val)
{
    if(typeof $el.data(data_var) != 'undefined')
    {
        return $el.data(data_var);
    }
    
    return default_val;
}