<script type="text/javascript">
    $(document).ready(function(){
    	$(document).delegate('.minimizar', 'click', function(event){
			event.preventDefault();
			if($('.bodyHide').is(":visible")){
				$('.chat .minimizar .tools li i').removeClass('fa-minus-square');
				$('.chat .minimizar .tools li i').addClass('fa-plus-square');
			}else{
				$('.chat .minimizar .tools li i').removeClass('fa-plus-square');
				$('.chat .minimizar .tools li i').addClass('fa-minus-square');
			}
 			$('.bodyHide').stop().animate({height: "toggle", opacity: "toggle"}, 300);
		});
	});
</script>

<div class='chat'>
	<header class="minimizar" style="cursor: pointer;">
		<h2 class='title'>Chat en línea</h2>
		<ul class='tools'>
			<li><i class='fa fa-minus-square'></i></li>
		</ul>
	</header>
	<div class='bodyHide' style="/*display: none;*/">
		@if(!Session::has('id'))
			<form id="chatForm" name="chatForm" method="post" novalidate="novalidate" style="padding: 10px;">
				<fieldset>
					<table width="100%">
						<tr>
							<td>
								<label for="nombre_chat" class="blocklabel">Nombre completo</label>
								<input placeholder="Mi nombre" id="nombre_chat" name="nombre_chat" type="text" class="input_bg validate" style="width: 95%;" value="Javier Santiago">
							</td>
						</tr>
						<tr>
							<td>
								<label for="e_mail_chat" class="blocklabel">Correo electrónico</label>
								<input placeholder="micorreo@dominio.com" id="e_mail_chat" name="e_mail_chat" type="email" class="input_bg validate" style="width: 95%;" value="javiers@protectodiez.mx">
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>
								<input type="button" value="Iniciar Chat" class="comment_submit iniciarChat">
							</td>
						</tr>
					</table>
				</fieldset>
			</form>
		@else
			<div class='body'>
				<ul>
					@if(count(json_decode(Session::get('mensajes'))) > 0)
						@foreach(json_decode(Session::get('mensajes')) AS $mensaje)
							<li class="{{$mensaje->owner}}">
                                <div class="content">
                                   <h3>{{$mensaje->nombre}} <span class="meta">{{$mensaje->fecha}}</span></h3>
                                    <span class="preview">{{$mensaje->mensaje}}</span>
                                </div>
                            </li>
						@endforeach
					@endif
				</ul>
			</div>
			<footer>
				<input type="text" class="enviarMensaje" placeholder="Escriba su mensaje [ Enter para enviar ]">
			</footer>
			<script type="text/javascript">
				jQuery(document).ready(function() {
				    chatOnline.getMensajes();
				});
			</script>
		@endif
	</div>
</div>

<script type="text/javascript">

var chatOnline = chatOnline || {
	
};

;(function($, window, undefined)
{
	"use strict";

    var formChat = $("#chatForm");
    var getMensajesChatOnline = null;
    
    $.extend(chatOnline, {
        init: function(){
            this.formularioChat();
        },
        formularioChat: function(){
            formChat.validate({
                errorClass: "error",
                validClass: "success",
                rules: {
                    nombre_chat: {
                        required: true,
                    },
                    e_mail_chat: {
                        required: true,
                        email: true
                    },
                },
                messages: {
                    nombre_chat: {
                        required: "Escriba su nombre",
                    },
                    e_mail_chat: {
                        required: "Escriba su Correo electrónico",
                        email: "Escriba un Correo electrónico válido"
                    },
                }
            });
        },
        getMensajes: function(){
        	if(getMensajesChatOnline){
        		getMensajesChatOnline.abort();
        	}
        	getMensajesChatOnline = $.ajax({
					                    url: _root_+'getMensajesChatOnline',
					                    method: 'POST',
					                    dataType: 'json',
					                    cache : false,
					                    processData: true,
					                    beforeSend: function()
					                    {
					                        
					                    },
					                    error: function()
					                    {
					                        
					                    },
					                    success: function(response)
					                    {
					                    	if(response.mensajes){
					                            $.each(response.mensajes, function(i){
					                            	$('.bodyHide .body ul').append('<li class="'+response.mensajes[i].owner+'">'+
				                                                        '<div class="content">'+
				                                                            '<h3>'+response.mensajes[i].nombre+' <span class="meta">'+response.mensajes[i].fecha+'</span></h3>'+
				                                                            '<span class="preview">'+response.mensajes[i].mensaje+'</span>'+
				                                                        '</div>'+
				                                                    '</li>');
					                            });
					                        }else{
					                        	/*
					                            $('.bodyHide .body ul').append('<li class="customer">'+
				                                                        '<div class="content">'+
				                                                            '<h3>Gastos Médicos</h3>'+
				                                                            '<span class="preview">En un momento uno de nuestros asesores lo atenderá, espere unos momentos por favor...</span>'+
				                                                        '</div>'+
				                                                    '</li>');
				                                */
					                        }
					                    }
					                });
        },
    });
    $(document).delegate('.iniciarChat', 'click', function(event){
        event.preventDefault();
        
        if(formChat.valid() === true){
            $.ajax({
                    url: _root_+'iniciarSesionChat',
                    data: formChat.serialize(),
                    method: 'POST',
                    dataType: 'json',
                    cache : false,
                    processData: true,
                    beforeSend: function()
                    {
                        $(".iniciarChat").val('Procesando...');
                        $(".iniciarChat").prop( "disabled", true );
                    },
                    error: function()
                    {
                        $(".iniciarChat").val('INICIAR CHAT');
                        $(".iniciarChat").prop( "disabled", false );
                    },
                    success: function(response)
                    {
                        if(response.status == 'success'){
                            $('.bodyHide').html('<div class="body">'+
                                                    '<ul>'+
                                                    '<li class="customer">'+
                                                        '<div class="content">'+
                                                            '<h3>Gastos Médicos</h3>'+
                                                            '<span class="preview">En un momento uno de nuestros asesores lo atenderá, espere unos momentos por favor...</span>'+
                                                        '</div>'+
                                                    '</li>'+
                                                '</ul>'+
                                            '</div>'+
                                            '<footer>'+
                                                '<input type="text" class="enviarMensaje" placeholder="Escriba su mensaje [ Enter para enviar ]">'+
                                            '</footer>');
                        }else{
                            $(".iniciarChat").val('INICIAR CHAT');
                            $(".iniciarChat").prop( "disabled", false );
                        }
                    }
                });
        }
    });
})(jQuery, window);

jQuery(document).ready(function() {
    chatOnline.init();
});
</script>

<style type="text/css">
.chat {
  border: 1px solid #0896b8;
  background: #ffffff;
  width: 300px;
  /*margin: 0 auto;*/
  position: fixed;
  right: 0 !important;
  bottom: 0;
  z-index: 999999999;
}
.chat header {
  background: #0896b8;
  padding: 10px 15px;
  color: #ffffff;
  font-size: 14px;
}
.chat header:before, .chat header:after {
  display: block;
  content: '';
  clear: both;
}
.chat header h2, .chat .body ul li .content h3 {
  margin: 0;
  padding: 0;
  font-size: 13px;
  float: left;
  color: #ffffff;
}
.chat header .tools {
  list-style: none;
  margin: 0;
  padding: 0;
  float: right;
}
.chat header .tools li {
  display: inline-block;
  margin-right: 6px;
}
.chat header .tools li:last-child {
  margin: 0;
}
.chat header .tools li a {
  color: #ffffff;
  text-decoration: none;
  -webkit-transition: all 0.3s linear 0s;
  -moz-transition: all 0.3s linear 0s;
  -ms-transition: all 0.3s linear 0s;
  -o-transition: all 0.3s linear 0s;
  transition: all 0.3s linear 0s;
}
.chat .body {
  position: relative;
  min-height: 250px;
  max-height: 250px;
  overflow-y: scroll;
}
.chat .body .search {
  display: none;
  width: 100%;
}
.chat .body .search.opened {
  display: block;
}
.chat .body .search input {
  width: 85%;
  margin: 0;
  padding: 10px 15px;
  border: none;
  -webkti-box-size: border-box;
  -moz-box-size: border-box;
  box-size: border-box;
}
.chat .body ul {
  list-style: none;
  padding: 0;
  margin: 0;
  border-top: 1px solid #f2f2f2;
}
.chat .body ul li.me {
  position: relative;
  background: #ffffff;
  display: block;
  width: 100%;
  padding: 5px;
  box-sizing: border-box;
}
.chat .body ul li:before, .chat .body ul li:after {
  display: block;
  content: '';
  clear: both;
}
.chat .body ul li.customer {
	background: #f2f2f2;
	position: relative;
    display: block;
    width: 100%;
    padding: 5px;
    box-sizing: border-box;
}
.chat .body ul li .content {
  display: inline-block;
  margin-left: 6px;
  vertical-align: top;
  line-height: 1;
}
.chat .body ul li .content h3 {
  display: block;
  width: 100%;
  /*margin-bottom: 5px;*/
  color: #808080;
}
.chat .body ul li .content .preview {
  display: block;
  width: 100%;
  /*max-width: 200px;*/
  /*margin-bottom: 5px;*/
  color: #000;
  font-size: 12px;
}
.chat .body ul li .content .meta {
  color: #b3b3b3;
  font-size: 12px;
}
.chat .body ul li .content .meta a {
  color: #999999;
  text-decoration: none;
}
.chat .body ul li .content .meta a:hover {
  text-decoration: underline;
}
.chat .body ul li .message {
  display: none;
  position: absolute;
  top: 0;
  left: 0;
  overflow: hidden;
  height: 100%;
  width: 100%;
  padding: 10px;
  box-sizing: border-box;
}
.chat footer {
    border-top: 1px solid #0896b8;
	height: 40px;
	padding: 5px;
}
.chat footer input {
	width: 95%;
	height: 90%;
	border: none;
    padding-left: 10px;
}

</style>