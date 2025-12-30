			(function($){
				"use strict";
				/*$("body").on("blur", ".input2", function(){
					$(this).on('blur',function(){
						if($(this).val().trim()!=""){
							$(this).addClass('has-val');
						}
						else{
							$(this).removeClass('has-val');
						}
					});
				});*/
				var cotizar = -1;
				var hijos = -1;
				var age18 = false;
				var submit = false;
				var total = 0;
				$("#_url").val(window.location.href);
				/*var name=$('.validate-input input[name="name"]');
				var email=$('.validate-input input[name="email"]');
				var message=$('.validate-input textarea[name="message"]');*/
				/*$('.validate-form').on('submit',function(){
					var check=true;
					if($(name).val().trim()==''){
						showValidate(name);
						check=false;
					}
					if($(email).val().trim().match(/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{1,5}|[0-9]{1,3})(\]?)$/)==null){
						showValidate(email);
						check=false;
					}
					if($(message).val().trim()==''){
						showValidate(message);
						check=false;
					}
					return check;
				});*/
				/*$("body").on("focus", ".validate-form .input2", function(){
					$(this).focus(function(){
						hideValidate(this);
					});
				});*/
				/*function showValidate(input){
					var thisAlert=$(input).parent();
					$(thisAlert).addClass('alert-validate');
				}*/
				/*function hideValidate(input){
					var thisAlert=$(input).parent();
					$(thisAlert).removeClass('alert-validate');
				}*/
				$("#edad, #edad-2").mask("09");
				$("#hijos").mask("0");
				$("#telefono").mask("00-0000-0000");
				$("#ciudad").change(function(){
					$("#d-cotizar").removeClass("d-none");
				});
				$("#cotizar").change(function(){
					submit = false;
					cotizar = $("#cotizar").val();
					$("#sexo, #edad, #sexo-2, #edad-2, #hijos, #email, #telefono, #observaciones").val("");
					$("#hijos-container").empty();
					$("#poliza, #maternidad, #multiregion, #privacidad").prop("checked", false);
					$("#emergencia_extranjero, #dental").prop("checked", true);
					$("#d-sexo, .sexo-conyuge, .hijos, .d-opciones, .d-contacto").addClass("d-none");
					switch(cotizar){
						case "1":
							total = 1;
						case "2":
							total = 2;
						case "3":
							total = 2;
							$("#sexo, #edad").attr("required", true);
							$("#d-sexo, #sexo, #edad, .sexo").removeClass("d-none");
						case "5":
							total = 1;
							$("#sexo, #edad").attr("required", true);
							$("#d-sexo, #sexo, #edad, .sexo").removeClass("d-none");
							$("#edad-2").removeAttr("min");
							break;
						case "4":
							total = 1;
							$("#sexo, #edad").attr("required", false);
							$(".sexo").addClass("d-none");
							//$("#lblConyuge").html("Mi conyuge es&nbsp;");
							$("#d-sexo, .sexo-conyuge").removeClass("d-none");
							$("#edad-2").attr("min", "18");
							break;
					}
				});
				$("#edad").on("keydown", function(){
					switch(cotizar){
						case "1":
							submit = true;
							$("#sexo-2, #edad-2").attr("required", false);
							$(".d-opciones, .d-contacto").removeClass("d-none");
							break;
						case "2":
						case "3":
							submit = false;
							$("#sexo-2, #edad-2").attr("required", true);
							$(".sexo-conyuge").removeClass("d-none");
							break;
						case "5":
							submit = false;
							$(".hijos").removeClass("d-none");
							break;
					}
				});
				$("#edad-2").on("keydown", function(){
					switch(cotizar){
						case "2":
							submit = true;
							$("#hijos").attr("required", false);
							$(".d-opciones, .d-contacto").removeClass("d-none");
							break;
						case "3":
						case "4":
							submit = false;
							$("#hijos").attr("required", true);
							$(".hijos").removeClass("d-none");
							break;
					}
				});
				$("#hijos").change(function(){
					submit = false;
					hijos = $("#hijos").val();
					total += hijos;
					showHijos();
					//$(".edades-hijos").removeClass("d-none");
				});
				$("#sexo-1-2").change(function(){
					submit = true;
					$(".d-opciones, .d-contacto").removeClass("d-none");
				});
				function showHijos(){
					$("#hijos-container").empty().append('<label">&nbsp;que tiene(n)&nbsp;</label>');
					if(hijos>1){
						for(var x=1;x<=hijos;x++){
							$("#hijos-container").append('<input type="text" name="edad-1-' + x + '" id="edad-1-' + x + '" class="form-control cotizador-nuevo" required max="70">');
							$("#edad-1-" + x).mask("09");
							if(x<hijos){
								if(hijos-x==1)
									$("#hijos-container").append('<label>&nbsp;y&nbsp;</label>');
								else
									$("#hijos-container").append('<label>,&nbsp;</label>');
							}
						}
						$("#hijos-container").append('<label>&nbsp;años y son&nbsp;</label>');
						for(var x=1;x<=hijos;x++){
							$("#hijos-container").append('<select class="form-control" name="sexo-1-' + x + '" id="sexo-1-' + x + '" required><option value=""></option><option value="H">Hombre</option><option value="M">Mujer</option></select>');
							if(x<hijos){
								if(hijos-x==1)
									$("#hijos-container").append('<label>&nbsp;y&nbsp;</label>');
								else
									$("#hijos-container").append('<label>,&nbsp;</label>');
							}
							else{
								$("#sexo-1-" + x).change(function(){
									submit = true;
									$(".d-opciones, .d-contacto").removeClass("d-none");
								});
							}
						}
						$("#hijos-container").append('<label>&nbsp;respectivamente.</label>');
					}
					else{
						$("#hijos-container").append('<input type="text" name="edad-1-1" id="edad-1-1" class="form-control cotizador-nuevo" required max="70">');
						$("#hijos-container").append('<label>&nbsp;años y es&nbsp;</label>');
						$("#hijos-container").append('<select class="form-control" name="sexo-1-1" id="sexo-1-1" required><option value=""></option><option value="H">Hombre</option><option value="M">Mujer</option></select>');
						$("#edad-1-1").mask("09");
						$("#sexo-1-1").change(function(){
							submit = true;
							$(".d-opciones, .d-contacto").removeClass("d-none");
						});
					}
				}
				$("#frmCotizador").validate({
					messages:{
						privacidad: 'Es necesario Aceptar términos, condiciones y aviso de privacidad'
					}
				});
				$("#frmCotizador").submit(function(e){
					e.preventDefault();
					if(submit){
						if($(this).valid()){
							$("#total").val(total);
							var _data = $(this).serialize();
							$.ajax({
								url:'/testCotizacionWS2023',
								method:'POST',
								dataType:'json',
								data:_data,
								success:function(data, status, jqXHR){
									if(data.status=="success"){
										var idCotizacion = data.idCotizacion;
										var secret = data.secret;
										$.ajax({
											url:'/cotizarWS2023/' + idCotizacion + '/' + secret,
											method:'POST',
											dataType:'json',
											success:function(data, status, jqXHR){
												location.href='/cotizacion-nuevo/' + idCotizacion + '/' + secret;
											},
											error:function(jqXHR, status, error){
												
											}
										});
									}
									else{
										$.notify({message:data.mensaje}, {type:'danger'});
									}
								},
								error:function(jqXHR, status, error){
									console.log("Error: " + error);
								}
							});
						}
					}
				});
				
				
				
				/*var total = 0;
				var current = $("#card1");
				$(".step").click(function(e){
					e.preventDefault();
					var id = $(this).data("id");
					if(current.attr("id")!=id){
						current.fadeOut(400, function(){
							$("#" + id).fadeIn();
							current = $("#" + id);
						});
					}
				});
				$("#nombre1, #nombre2-1, #nombre3-1, #nombre5-1").change(function(){
					$("#nombre").val($(this).val());
					if($("#nombre").val()!="")
						$("#nombre").addClass("has-val");
					else
						$("#nombre").removeClass("has-val");
				});
				$("#privacidad").change(function(){
					if($("#privacidad").prop("checked"))
						$("#next4").attr("disabled", false);
					else
						$("#next4").attr("disabled", true);
				});*/
				var estados = [
					{"api": "Aguascalientes", "estado": "AGUASCALIENTES"},
					{"api": "Baja California", "estado": "BAJA CALIFORNIA"},
					{"api": "Baja California Sur", "estado": "BAJA CALIFORNIA SUR"},
					{"api": "Campeche", "estado": "CAMPECHE"},
					{"api": "Coahuila", "estado": "COAHUILA"},
					{"api": "Colima", "estado": "COLIMA"},
					{"api": "Chiapas", "estado": "CHIAPAS"},
					{"api": "Chihuahua", "estado": "CHIHUAHUA"},
					{"api": "Ciudad de Mexico", "estado": "CDMX"},
					{"api": "Durango", "estado": "DURANGO"},
					{"api": "Guanajuato", "estado": "GUANAJUATO"},
					{"api": "Guerrero", "estado": "GUERRERO"},
					{"api": "Hidalgo", "estado": "HIDALGO"},
					{"api": "Jalisco", "estado": "JALISCO"},
					{"api": "Mexico", "estado": "MÉXICO"},
					{"api": "Michoacan de Ocampo", "estado": "MICHOACÁN"},
					{"api": "Morelos", "estado": "MORELOS"},
					{"api": "Nayarit", "estado": "NAYARIT"},
					{"api": "Nuevo Leon", "estado": "NUEVO LEÓN"},
					{"api": "Oaxaca", "estado": "OAXACA"},
					{"api": "Puebla", "estado": "PUEBLA"},
					{"api": "Queretaro de Arteaga", "estado": "QUERÉTARO"},
					{"api": "Quintana Roo", "estado": "QUINTANA ROO"},
					{"api": "San Luis Potosi", "estado": "SAN LUIS POTOSÍ"},
					{"api": "Sinaloa", "estado": "SINALOA"},
					{"api": "Sonora", "estado": "SONORA"},
					{"api": "Tabasco", "estado": "TABASCO"},
					{"api": "Tamaulipas", "estado": "TAMAULIPAS"},
					{"api": "Tlaxcala", "estado": "TLAXCALA"},
					{"api": "Veracruz-Llave", "estado": "VERACRUZ"},
					{"api": "Yucatan", "estado": "YUCATÁN"},
					{"api": "Zacatecas", "estado": "ZACATECAS"}
				];
				$.ajax({
					//url:'https://ipapi.co/json/',
					url:'/ip-location',
					method:'GET',
					dataType:'json',
					success:function(data, status, jqXHR){
						console.log(data);
						if(data.region_name){
							var estado = estados.find(estado => estado.api === data.region_name);
							if(estado){
								$("#estado option:contains(" + estado.estado + ")").attr("selected", true);
								if($("#estado").val()!=""){
									$("#estado").addClass("has-val").trigger("change");
								}
							}
						}
					},
					error:function(jqXHR, status, error){
						console.log("Error: " + error);
					}
				});
				$("#estado").change(function(){
					$("#_estado").val($("#estado option:selected").text());
					$.ajax({
						url:'/poblaciones/' + $("#estado").val(),
						method:'GET',
						dataType:'json',
						success:function(data, status, jqXHR){
							$("#ciudad").empty().append('<option value=""></option>');
							$.each(data, function(i, poblacion){
								$("#ciudad").append('<option value="' + poblacion.id + '">' + poblacion.poblacion + '</option>');
							});
						},
						error:function(jqXHR, status, error){
							console.log("Error: " + error);
						}
					});
				});
				$("#ciudad").change(function(){
					$("#_ciudad").val($("#ciudad option:selected").text());
				});
				/*function card1Changes(showAlert){
					var check = true;
					age18 = false;
					if($("#estado").val()==""){
						if(showAlert)
							showValidate($("#estado"));
						check = false;
					}
					if($("#ciudad").val()=="" || $("#ciudad").val()==null){
						if(showAlert)
							showValidate($("#ciudad"));
						check = false;
					}
					if($("#cotizar").val()==""){
						if(showAlert)
							showValidate($("#cotizar"));
						check = false;
					}
					var r = '';
					switch($("#cotizar").val()){
						case "1":
							if($("#sexo1").val()==""){
								if(showAlert)
									showValidate($("#sexo1"));
								check = false;
							}
							if($("#nombre1").val()==""){
								if(showAlert)
									showValidate($("#nombre1"));
								check = false;
							}
							if($("#edad1").val()==""){
								if(showAlert)
									showValidate($("#edad1"));
								check = false;
							}
							else{
								if(parseInt($("#edad1").val())>=18)
									age18 = true;
							}
							r += '<p class="text-muted">1 persona</p>';
							r += '<ul><li class="text-muted">' + $("#nombre1").val() + ' (' + $("#sexo1").val() + ') - ' + $("#edad1").val() + '</li></ul>';
							break;
						case "2":
							if($("#sexo2-1").val()==""){
								if(showAlert)
									showValidate($("#sexo2-1"));
								check = false;
							}
							if($("#nombre2-1").val()==""){
								if(showAlert)
									showValidate($("#nombre2-1"));
								check = false;
							}
							if($("#edad2-1").val()==""){
								if(showAlert)
									showValidate($("#edad2-1"));
								check = false;
							}
							else{
								if(parseInt($("#edad2-1").val())>=18)
									age18 = true;
							}
							if($("#sexo2-2").val()==""){
								if(showAlert)
									showValidate($("#sexo2-2"));
								check = false;
							}
							if($("#nombre2-2").val()==""){
								if(showAlert)
									showValidate($("#nombre2-2"));
								check = false;
							}
							if($("#edad2-2").val()==""){
								if(showAlert)
									showValidate($("#edad2-2"));
								check = false;
							}
							else{
								if(parseInt($("#edad2-2").val())>=18)
									age18 = true;
							}
							r += '<p class="text-muted">Mi pareja y yo</p>';
							r += '<ul>';
							r += '<li class="text-muted">' + $("#nombre2-1").val() + ' (' + $("#sexo2-1").val() + ') - ' + $("#edad2-1").val() + '</li>';
							r += '<li class="text-muted">' + $("#nombre2-2").val() + ' (' + $("#sexo2-2").val() + ') - ' + $("#edad2-2").val() + '</li>';
							r += '</ul>';
							break;
						case "3":
							if($("#sexo3-1").val()==""){
								if(showAlert)
									showValidate($("#sexo3-1"));
								check = false;
							}
							if($("#nombre3-1").val()==""){
								if(showAlert)
									showValidate($("#nombre3-1"));
								check = false;
							}
							if($("#edad3-1").val()==""){
								if(showAlert)
									showValidate($("#edad3-1"));
								check = false;
							}
							else{
								if(parseInt($("#edad3-1").val())>=18)
									age18 = true;
							}
							if($("#sexo3-2").val()==""){
								if(showAlert)
									showValidate($("#sexo3-2"));
								check = false;
							}
							if($("#nombre3-2").val()==""){
								if(showAlert)
									showValidate($("#nombre3-2"));
								check = false;
							}
							if($("#edad3-2").val()==""){
								if(showAlert)
									showValidate($("#edad3-2"));
								check = false;
							}
							else{
								if(parseInt($("#edad3-2").val())>=18)
									age18 = true;
							}
							if($("#hijos1").val()==""){
								if(showAlert)
									showValidate($("#hijos1"));
								check = false;
							}
							r += '<p class="text-muted">Mi pareja, yo y mis hijos</p>';
							r += '<ul>';
							r += '<li class="text-muted">' + $("#nombre3-1").val() + ' (' + $("#sexo3-1").val() + ') - ' + $("#edad3-1").val() + '</li>';
							r += '<li class="text-muted">' + $("#nombre3-2").val() + ' (' + $("#sexo3-2").val() + ') - ' + $("#edad3-2").val() + '</li>';
							for(var x = 1; x<=$("#hijos1").val();x++){
								if($("#sexoHijos-" + x).val()==""){
									if(showAlert){
										showValidate($("#sexoHijos-" + x));
									}
									check = false;
								}
								if($("#nombreHijos-" + x).val()==""){
									if(showAlert){
										showValidate($("#nombreHijos-" + x));
									}
									check = false;
								}
								if($("#edadHijos-" + x).val()==""){
									if(showAlert){
										showValidate($("#edadHijos-" + x));
									}
									check = false;
								}
								else{
									if(parseInt($("#edadHijos-" + x).val())>=18)
										age18 = true;
								}
								r += '<li class="text-muted">' + $("#nombreHijos-" + x).val() + ' (' + $("#sexoHijos-" + x).val() + ') - ' + $("#edadHijos-" + x).val() + '</li>';
							}
							r += '</ul>';
							break;
						case "4":
							if($("#sexo4-2").val()==""){
								if(showAlert)
									showValidate($("#sexo4-2"));
								check = false;
							}
							if($("#nombre4-2").val()==""){
								if(showAlert)
									showValidate($("#nombre4-2"));
								check = false;
							}
							if($("#edad4-2").val()==""){
								if(showAlert)
									showValidate($("#edad4-2"));
								check = false;
							}
							else{
								if(parseInt($("#edad4-2").val())>=18)
									age18 = true;
							}
							if($("#hijos2").val()==""){
								if(showAlert)
									showValidate($("#hijos2"));
								check = false;
							}
							r += '<p class="text-muted">Mi pareja y mis hijos</p>';
							r += '<ul>';
							r += '<li class="text-muted">' + $("#nombre4-2").val() + ' (' + $("#sexo4-2").val() + ') - ' + $("#edad4-2").val() + '</li>';
							for(var x = 1; x<=$("#hijos2").val();x++){
								if($("#sexoHijos-" + x).val()==""){
									if(showAlert)
										showValidate($("#sexoHijos-" + x));
									check = false;
								}
								if($("#nombreHijos-" + x).val()==""){
									if(showAlert)
										showValidate($("#nombreHijos-" + x));
									check = false;
								}
								if($("#edadHijos-" + x).val()==""){
									if(showAlert)
										showValidate($("#edadHijos-" + x));
									check = false;
								}
								else{
									if(parseInt($("#edadHijos-" + x).val())>=18)
										age18 = true;
								}
								r += '<li class="text-muted">' + $("#nombreHijos-" + x).val() + ' (' + $("#sexoHijos-" + x).val() + ') - ' + $("#edadHijos-" + x).val() + '</li>';
							}
							r += '</ul>';
							break;
						case "5":
							if($("#sexo5-1").val()==""){
								if(showAlert)
									showValidate($("#sexo5-1"));
								check = false;
							}
							if($("#nombre5-1").val()==""){
								if(showAlert)
									showValidate($("#nombre5-1"));
								check = false;
							}
							if($("#edad5-1").val()==""){
								if(showAlert)
									showValidate($("#edad5-1"));
								check = false;
							}
							else{
								if(parseInt($("#edad5-1").val())>=18)
									age18 = true;
							}
							if($("#hijos3").val()==""){
								if(showAlert)
									showValidate($("#hijos3"));
								check = false;
							}
							r += '<p class="text-muted">Yo y mis hijos</p>';
							r += '<ul>';
							r += '<li class="text-muted">' + $("#nombre5-1").val() + ' (' + $("#sexo5-1").val() + ') - ' + $("#edad5-1").val() + '</li>';
							for(var x = 1; x<=$("#hijos3").val();x++){
								if($("#sexoHijos-" + x).val()==""){
									if(showAlert)
										showValidate($("#sexoHijos-" + x));
									check = false;
								}
								if($("#nombreHijos-" + x).val()==""){
									if(showAlert)
										showValidate($("#nombreHijos-" + x));
									check = false;
								}
								if($("#edadHijos-" + x).val()==""){
									if(showAlert)
										showValidate($("#edadHijos-" + x));
									check = false;
								}
								else{
									if(parseInt($("#edadHijos-" + x).val())>=18)
										age18 = true;
								}
								r += '<li class="text-muted">' + $("#nombreHijos-" + x).val() + ' (' + $("#sexoHijos-" + x).val() + ') - ' + $("#edadHijos-" + x).val() + '</li>';
							}
							r += '</ul>';
							break;
						case "6":
							if($("#hijos4").val()==""){
								if(showAlert)
									showValidate($("#hijos4"));
								check = false;
							}
							r += '<p class="text-muted">Mis hijos</p>';
							r += '<ul>';
							for(var x = 1; x<=$("#hijos4").val();x++){
								if($("#sexoHijos-" + x).val()==""){
									if(showAlert)
										showValidate($("#sexoHijos-" + x));
									check = false;
								}
								if($("#nombreHijos-" + x).val()==""){
									if(showAlert)
										showValidate($("#nombreHijos-" + x));
									check = false;
								}
								if($("#edadHijos-" + x).val()==""){
									if(showAlert)
										showValidate($("#edadHijos-" + x));
									check = false;
								}
								else{
									if(parseInt($("#edadHijos-x").val())>=18)
										age18 = true;
								}
								r += '<li class="text-muted">' + $("#nombreHijos-" + x).val() + ' (' + $("#sexoHijos-" + x).val() + ') - ' + $("#edadHijos-" + x).val() + '</li>';
							}
							r += '</ul>';
							break;
					}
					if(check)
						$("#step1").find(".rounded-circle").removeClass("bg-gray").addClass("bg-primary");
					else
						$("#step1").find(".rounded-circle").removeClass("bg-primary").addClass("bg-gray");
					$("#step1").find(".results").html(
						$("#ciudad option:selected").text() + ", " + 
						$("#estado option:selected").text() + 
						'<label class="text-azul d-bock mt-2">Protección para</label>' + r
					);
					if(showAlert){
						if(age18)
							$("#lblError").addClass("d-none");
						else{
							$("#lblError").removeClass("d-none");
							check = false;
						}
					}
					return check;
				}*/
				/*$("body").on("change", "#card1 .input2", function(){
					card1Changes(false);
				});*/
				//$("#card1 .input2").change(function(){
				//	card1Changes(false);
				//});
				/*$("#next1").click(function(e){
					e.preventDefault();
					var check = card1Changes(true);
					if(check){
						$("#step1").find(".rounded-circle").removeClass("bg-gray").addClass("bg-primary");
						//$("#step1").find(".results").html($("#ciudad option:selected").text() + ", " + $("#estado option:selected").text());
						$("#card1").fadeOut(400, function(){
							$("#card3").fadeIn();
							card3Changes();
							current = $("#card3");
						});
					}
				});*/
				/*$("#cotizar").change(function(){
					switch($("#cotizar").val()){
						case "1":
							$("#cotizar-2").fadeOut();
							$("#cotizar-3").fadeOut();
							$("#cotizar-4").fadeOut();
							$("#cotizar-5").fadeOut();
							$("#cotizar-6").fadeOut();
							$("#cotizar-1").fadeIn();
							$("#total").val(1);
							$(".opcional").removeClass("validar");
							$("#sexo1, #nombre1, #edad1").addClass("validar");
							break;
						case "2":
							$("#cotizar-1").fadeOut();
							$("#cotizar-3").fadeOut();
							$("#cotizar-4").fadeOut();
							$("#cotizar-5").fadeOut();
							$("#cotizar-6").fadeOut();
							$("#cotizar-2").fadeIn();
							$("#total").val(2);
							$(".opcional").removeClass("validar");
							$("#sexo2-1, #nombre2-1, #edad2-1, #sexo2-2, #nombre2-2, #edad2-2").addClass("validar");
							break;
						case "3":
							$("#cotizar-1").fadeOut();
							$("#cotizar-2").fadeOut();
							$("#cotizar-4").fadeOut();
							$("#cotizar-5").fadeOut();
							$("#cotizar-6").fadeOut();
							$("#cotizar-3").fadeIn();
							total = 2;
							$(".opcional").removeClass("validar");
							$("#sexo3-1, #nombre3-1, #edad3-1, #sexo3-2, #nombre3-2, #edad3-2, #hijos1").addClass("validar");
							break;
						case "4":
							$("#cotizar-1").fadeOut();
							$("#cotizar-2").fadeOut();
							$("#cotizar-3").fadeOut();
							$("#cotizar-5").fadeOut();
							$("#cotizar-6").fadeOut();
							$("#cotizar-4").fadeIn();
							total = 1;
							$(".opcional").removeClass("validar");
							$("#sexo4-2, #nombre4-2, #edad4-2, #hijos2").addClass("validar");
							break;
						case "5":
							$("#cotizar-1").fadeOut();
							$("#cotizar-2").fadeOut();
							$("#cotizar-3").fadeOut();
							$("#cotizar-4").fadeOut();
							$("#cotizar-6").fadeOut();
							$("#cotizar-5").fadeIn();
							total = 1;
							$(".opcional").removeClass("validar");
							$("#sexo5-1, #nombre5-1, #edad5-1, #hijos3").addClass("validar");
							break;
						case "6":
							$("#cotizar-1").fadeOut();
							$("#cotizar-2").fadeOut();
							$("#cotizar-3").fadeOut();
							$("#cotizar-4").fadeOut();
							$("#cotizar-5").fadeOut();
							$("#cotizar-6").fadeIn();
							$(".opcional").removeClass("validar");
							$("#hijos4").addClass("validar");
							break;
					}
				});*/
				/*$("#hijos1").change(function(){
					showHijos($("#container-hijos1"), $("#hijos1").val());
				});
				$("#hijos2").change(function(){
					showHijos($("#container-hijos2"), $("#hijos2").val());
				});
				$("#hijos3").change(function(){
					showHijos($("#container-hijos3"), $("#hijos3").val());
				});
				$("#hijos4").change(function(){
					showHijos($("#container-hijos4"), $("#hijos4").val());
				});*/
				/*function card2Changes(){
					var check = true;
					if($("#cotizar").val()==""){
						showValidate($("#cotizar"));
						check = false;
					}
					var r = '';
					switch($("#cotizar").val()){
						case "1":
							if($("#sexo1").val()==""){
								showValidate($("#sexo1"));
								check = false;
							}
							if($("#nombre1").val()==""){
								showValidate($("#nombre1"));
								check = false;
							}
							if($("#edad1").val()==""){
								showValidate($("#edad1"));
								check = false;
							}
							r += '<p>1 persona</p>';
							r += '<ul><li>' + $("#nombre1").val() + ' (' + $("#sexo1").val() + ') - ' + $("#edad1").val() + '</li></ul>';
							break;
						case "2":
							if($("#sexo2-1").val()==""){
								showValidate($("#sexo2-1"));
								check = false;
							}
							if($("#nombre2-1").val()==""){
								showValidate($("#nombre2-1"));
								check = false;
							}
							if($("#edad2-1").val()==""){
								showValidate($("#edad2-1"));
								check = false;
							}
							if($("#sexo2-2").val()==""){
								showValidate($("#sexo2-2"));
								check = false;
							}
							if($("#nombre2-2").val()==""){
								showValidate($("#nombre2-2"));
								check = false;
							}
							if($("#edad2-2").val()==""){
								showValidate($("#edad2-2"));
								check = false;
							}
							r += '<p>Mi pareja y yo</p>';
							r += '<ul>';
							r += '<li>' + $("#nombre2-1").val() + ' (' + $("#sexo2-1").val() + ') - ' + $("#edad2-1").val() + '</li>';
							r += '<li>' + $("#nombre2-2").val() + ' (' + $("#sexo2-2").val() + ') - ' + $("#edad2-2").val() + '</li>';
							r += '</ul>';
							break;
						case "3":
							if($("#sexo3-1").val()==""){
								showValidate($("#sexo3-1"));
								check = false;
							}
							if($("#nombre3-1").val()==""){
								showValidate($("#nombre3-1"));
								check = false;
							}
							if($("#edad3-1").val()==""){
								showValidate($("#edad3-1"));
								check = false;
							}
							if($("#sexo3-2").val()==""){
								showValidate($("#sexo3-2"));
								check = false;
							}
							if($("#nombre3-2").val()==""){
								showValidate($("#nombre3-2"));
								check = false;
							}
							if($("#edad3-2").val()==""){
								showValidate($("#edad3-2"));
								check = false;
							}
							if($("#hijos1").val()==""){
								showValidate($("#hijos1"));
								check = false;
							}
							r += '<p>Mi pareja, yo y mis hijos</p>';
							r += '<ul>';
							r += '<li>' + $("#nombre3-1").val() + ' (' + $("#sexo3-1").val() + ') - ' + $("#edad3-1").val() + '</li>';
							r += '<li>' + $("#nombre3-2").val() + ' (' + $("#sexo3-2").val() + ') - ' + $("#edad3-2").val() + '</li>';
							for(var x = 1; x<=$("#hijos1").val();x++){
								if($("#sexoHijos-" + x).val()==""){
									showValidate($("#sexoHijos-" + x));
									check = false;
								}
								if($("#nombreHijos-" + x).val()==""){
									showValidate($("#nombreHijos-" + x));
									check = false;
								}
								if($("#edadHijos-" + x).val()==""){
									showValidate($("#edadHijos-" + x));
									check = false;
								}
								r += '<li>' + $("#nombreHijos-" + x).val() + ' (' + $("#sexoHijos-" + x).val() + ') - ' + $("#edadHijos-" + x).val() + '</li>';
							}
							r += '</ul>';
							break;
						case "4":
							if($("#sexo4-2").val()==""){
								showValidate($("#sexo4-2"));
								check = false;
							}
							if($("#nombre4-2").val()==""){
								showValidate($("#nombre4-2"));
								check = false;
							}
							if($("#edad4-2").val()==""){
								showValidate($("#edad4-2"));
								check = false;
							}
							if($("#hijos2").val()==""){
								showValidate($("#hijos2"));
								check = false;
							}
							r += '<p>Mi pareja y mis hijos</p>';
							r += '<ul>';
							r += '<li>' + $("#nombre4-2").val() + ' (' + $("#sexo4-2").val() + ') - ' + $("#edad4-2").val() + '</li>';
							for(var x = 1; x<=$("#hijos2").val();x++){
								if($("#sexoHijos-" + x).val()==""){
									showValidate($("#sexoHijos-" + x));
									check = false;
								}
								if($("#nombreHijos-" + x).val()==""){
									showValidate($("#nombreHijos-" + x));
									check = false;
								}
								if($("#edadHijos-" + x).val()==""){
									showValidate($("#edadHijos-" + x));
									check = false;
								}
								r += '<li>' + $("#nombreHijos-" + x).val() + ' (' + $("#sexoHijos-" + x).val() + ') - ' + $("#edadHijos-" + x).val() + '</li>';
							}
							r += '</ul>';
							break;
						case "5":
							if($("#sexo5-1").val()==""){
								showValidate($("#sexo5-1"));
								check = false;
							}
							if($("#nombre5-1").val()==""){
								showValidate($("#nombre5-1"));
								check = false;
							}
							if($("#edad5-1").val()==""){
								showValidate($("#edad5-1"));
								check = false;
							}
							if($("#hijos3").val()==""){
								showValidate($("#hijos3"));
								check = false;
							}
							r += '<p>Yo y mis hijos</p>';
							r += '<ul>';
							r += '<li>' + $("#nombre5-1").val() + ' (' + $("#sexo5-1").val() + ') - ' + $("#edad5-1").val() + '</li>';
							for(var x = 1; x<=$("#hijos3").val();x++){
								if($("#sexoHijos-" + x).val()==""){
									showValidate($("#sexoHijos-" + x));
									check = false;
								}
								if($("#nombreHijos-" + x).val()==""){
									showValidate($("#nombreHijos-" + x));
									check = false;
								}
								if($("#edadHijos-" + x).val()==""){
									showValidate($("#edadHijos-" + x));
									check = false;
								}
								r += '<li>' + $("#nombreHijos-" + x).val() + ' (' + $("#sexoHijos-" + x).val() + ') - ' + $("#edadHijos-" + x).val() + '</li>';
							}
							r += '</ul>';
							break;
						case "6":
							if($("#hijos4").val()==""){
								showValidate($("#hijos4"));
								check = false;
							}
							r += '<p>Mis hijos</p>';
							r += '<ul>';
							for(var x = 1; x<=$("#hijos4").val();x++){
								if($("#sexoHijos-" + x).val()==""){
									showValidate($("#sexoHijos-" + x));
									check = false;
								}
								if($("#nombreHijos-" + x).val()==""){
									showValidate($("#nombreHijos-" + x));
									check = false;
								}
								if($("#edadHijos-" + x).val()==""){
									showValidate($("#edadHijos-" + x));
									check = false;
								}
								r += '<li>' + $("#nombreHijos-" + x).val() + ' (' + $("#sexoHijos-" + x).val() + ') - ' + $("#edadHijos-" + x).val() + '</li>';
							}
							r += '</ul>';
							break;
					}
					if(check)
						$("#step2").find(".rounded-circle").removeClass("bg-gray").addClass("bg-primary");
					else
						$("#step2").find(".rounded-circle").removeClass("bg-primary").addClass("bg-gray");
					$("#step2").find(".results").html(r);
					return check;
				}*/
				//$("#card2 .input2").change(function(){
				//	card2Changes();
				//});
				//$("body").on("change", ".card2", function(){
				//	card2Changes();
				//});
				/*$("#next2-old").click(function(e){
					e.preventDefault();
					var check = card2Changes();
					if(check){
						var r = '';
						switch($("#cotizar").val()){
							case "1":
								r += '<p>1 persona</p>';
								r += '<ul><li>' + $("#nombre1").val() + ' (' + $("#sexo1").val() + ') - ' + $("#edad1").val() + '</li></ul>';
								break;
							case "2":
								r += '<p>Mi pareja y yo</p>';
								r += '<ul>';
								r += '<li>' + $("#nombre2-1").val() + ' (' + $("#sexo2-1").val() + ') - ' + $("#edad2-1").val() + '</li>';
								r += '<li>' + $("#nombre2-2").val() + ' (' + $("#sexo2-2").val() + ') - ' + $("#edad2-2").val() + '</li>';
								r += '</ul>';
								break;
							case "3":
								r += '<p>Mi pareja, yo y mis hijos</p>';
								r += '<ul>';
								r += '<li>' + $("#nombre3-1").val() + ' (' + $("#sexo3-1").val() + ') - ' + $("#edad3-1").val() + '</li>';
								r += '<li>' + $("#nombre3-2").val() + ' (' + $("#sexo3-2").val() + ') - ' + $("#edad3-2").val() + '</li>';
								for(var x = 1; x<=$("#hijos1").val();x++){
									r += '<li>' + $("#nombreHijos-" + x).val() + ' (' + $("#sexoHijos-" + x).val() + ') - ' + $("#edadHijos-" + x).val() + '</li>';
								}
								r += '</ul>';
								break;
							case "4":
								r += '<p>Mi pareja y mis hijos</p>';
								r += '<ul>';
								r += '<li>' + $("#nombre4-2").val() + ' (' + $("#sexo4-2").val() + ') - ' + $("#edad4-2").val() + '</li>';
								for(var x = 1; x<=$("#hijos2").val();x++){
									r += '<li>' + $("#nombreHijos-" + x).val() + ' (' + $("#sexoHijos-" + x).val() + ') - ' + $("#edadHijos-" + x).val() + '</li>';
								}
								r += '</ul>';
								break;
							case "5":
								r += '<p>Yo y mis hijos</p>';
								r += '<ul>';
								r += '<li>' + $("#nombre5-1").val() + ' (' + $("#sexo5-1").val() + ') - ' + $("#edad5-1").val() + '</li>';
								for(var x = 1; x<=$("#hijos3").val();x++){
									r += '<li>' + $("#nombreHijos-" + x).val() + ' (' + $("#sexoHijos-" + x).val() + ') - ' + $("#edadHijos-" + x).val() + '</li>';
								}
								r += '</ul>';
								break;
							case "6":
								r += '<p>Mis hijos</p>';
								r += '<ul>';
								for(var x = 1; x<=$("#hijos4").val();x++){
									r += '<li>' + $("#nombreHijos-" + x).val() + ' (' + $("#sexoHijos-" + x).val() + ') - ' + $("#edadHijos-" + x).val() + '</li>';
								}
								r += '</ul>';
								break;
						}
						$("#step2").find(".rounded-circle").removeClass("bg-gray").addClass("bg-primary");
						$("#step2").find(".results").html(r);
						$("#card2").fadeOut(400, function(){
							$("#card3").fadeIn();
							current = $("#card3");
						});
					}
				});*/
				/*function showHijos(container, no){
					var field = '';
					container.empty();
					for(var x=1;x<=no;x++){
						field = '<div class="row">';
						field += '<div class="col-md-2">';
						field += '			<div class="wrap-input2 validate-input" data-validate="Mi hijo(a) es requerido">';
						field += '				<select class="input2 validar opcional hijos" name="sexoHijos-' + x + '" id="sexoHijos-' + x + '">';
						field += '					<option value=""></option>';
						field += '					<option value="H">Hombre</option>';
						field += '					<option value="M">Mujer</option>';
						field += '				</select>';
						field += '				<span class="focus-input2" data-placeholder="Mi hijo(a) es"></span>';
						field += '			</div>';
						field += '		</div>';
						field += '		<div class="col-md-8">';
						field += '			<div class="wrap-input2 validate-input" data-validate="Nombre es requerido">';
						field += '				<input type="text" class="input2 validar opcional hijos" name="nombreHijos-' + x + '" id="nombreHijos-' + x + '">';
						field += '				<span class="focus-input2" data-placeholder="Su nombre"></span>';
						field += '			</div>';
						field += '		</div>';
						field += '		<div class="col-md-2">';
						field += '			<div class="wrap-input2 validate-input" data-validate="Su edad es requerido">';
						field += '				<input type="number" class="input2 validar opcional hijos" name="edadHijos-' + x + '" id="edadHijos-' + x + '" step="1" min="0" max="99">';
						field += '				<span class="focus-input2" data-placeholder="Su edad"></span>';
						field += '			</div>';
						field += '		</div>';
						field += '	</div>';
						container.append(field);
					}
					total += parseInt(no);
					$("#total").val(total);
				}*/
				/*function card3Changes(showAlert){
					var check = true;
					if($("#nombre").val()==""){
						if(showAlert)
							showValidate($("#nombre"));
						check = false;
					}
					if($("#email").val()=="" && $("#phone").val()==""){
						if(showAlert){
							showValidate($("#email"));
							showValidate($("#phone"));
						}
						check = false;
					}
					var r = '<ul>';
					if($("#poliza").prop("checked"))
						r += '<li class="text-muted">Tengo póliza actualmente</li>';
					if($("#maternidad").prop("checked"))
						r += '<li class="text-muted">Me interesa cobertura en Maternidad</li>';
					if($("#emergencia_extranjero").prop("checked"))
						r += '<li class="text-muted">A veces viajo al extranjero y necesito cobertura</li>';
					if($("#dental").prop("checked"))
						r += '<li class="text-muted">Deseo cobertura Dental Básica</li>';
					if($("#multiregion").prop("checked"))
						r += '<li class="text-muted">Quisiera cobertura en Estados de México más costosos</li>';
					r += '</ul><label class="text-azul d-block mt-2">Contacto</label>';
					if($("#nombre").val()!="")
						r += $("#nombre").val() +  '<br>';
					if($("#email").val()!="")
						r += $("#email").val() +  '<br>';
					if($("#phone").val()!="")
						r += $("#phone").val() +  '<br>';
					$("#step3").find(".results").html(r);
					if(check)
						$("#step3").find(".rounded-circle").removeClass("bg-gray").addClass("bg-primary");
					else
						$("#step3").find(".rounded-circle").removeClass("bg-primary").addClass("bg-gray");
					return check;
				}*/
				/*$("#card3 .custom-control-input").change(function(){
					card3Changes(false);
				});*/
				/*$("#card3 .input2").change(function(){
					card3Changes(false);
				});*/
				/*$("#next3").click(function(e){
					e.preventDefault();
					var r = '';
					if($("#poliza").prop("checked"))
						r += '<li class="text-muted">Tengo una póliza actual</li>';
					if($("#maternidad").prop("checked"))
						r += '<li class="text-muted">Me interesa la maternidad</li>';
					if($("#emergencia_extranjero").prop("checked"))
						r += '<li class="text-muted">Viajaré al extranjero y necesito cobertura</li>';
					if($("#dental").prop("checked"))
						r += '<li class="text-muted">Me interesa tener cobertura dental básica</li>';
					if($("#multiregion").prop("checked"))
						r += '<li class="text-muted">Me interesa tener cobertura en Estados de Mexico más costosos</li>';
					$("#step3").find(".rounded-circle").removeClass("bg-gray").addClass("bg-primary");
					if(r!='')
						$("#step3").find(".results").html('<ul>' + r + '</ul>');
					$("#card3").fadeOut(400, function(){
						$("#card4").fadeIn();
						current = $("#card4");
					});
				});*/
				/*function card4Changes(){
					var check = true;
					if($("#nombre").val()==""){
						showValidate($("#nombre"));
						check = false;
					}
					if($("#email").val()=="" && $("#phone").val()==""){
						showValidate($("#email"));
						showValidate($("#phone"));
						check = false;
					}
					var r = '';
					if($("#nombre").val()!="")
						r += $("#nombre").val() +  '<br>';
					if($("#email").val()!="")
						r += $("#email").val() +  '<br>';
					if($("#phone").val()!="")
						r += $("#phone").val() +  '<br>';
					$("#step4").find(".results").html(r);
					if(check)
						$("#step4").find(".rounded-circle").removeClass("bg-gray").addClass("bg-primary");
					else
						$("#step4").find(".rounded-circle").removeClass("bg-primary").addClass("bg-gray");
					return check;
				}*/
				/*$("#card4 .input2").change(function(){
					card4Changes();
				});*/
				/*$("#frmCotizador").submit(function(e){
					e.preventDefault();
					var valido = 1;
					if(!card3Changes(true))
						valido = 0;
					$.each($(".validar"), function(i, el){
						if($(el).val()==""){
							$(".card").fadeOut();
							var card = $(el).closest(".card");
							card.fadeIn();
							current = card;
							valido = 0;
							return false;
						}
					});
					if(!age18){
						$(".card").fadeOut();
						var card = $("#card1");
						card.fadeIn();
						current = card;
						valido = 0;
						return false;
					}
					if(!$("#privacidad").prop("checked")){
						$.notify({message:'Debes de aceptar los términos, condiciones y aviso de privacidad'},{type:'warning'});
						valido = 0;
						return false;
					}
					if(valido==1){
						var _data = $(this).serialize();
						$.ajax({
							url:'/nuevaCotizacionWS2023',
							method:'POST',
							dataType:'json',
							data:_data,
							success:function(data, status, jqXHR){
								if(data.status=="success"){
									var idCotizacion = data.idCotizacion;
									var secret = data.secret;
									$.ajax({
										url:'/cotizarWS2023/' + idCotizacion + '/' + secret,
										method:'POST',
										dataType:'json',
										success:function(data, status, jqXHR){
											location.href='/cotizacion-nuevo/' + idCotizacion + '/' + secret;
										},
										error:function(jqXHR, status, error){
											
										}
									});
								}
								else{
									console.log("Error: " + data);
								}
							},
							error:function(jqXHR, status, error){
								console.log("Error: " + error);
							}
						});
					}
				});*/
				function generateUUID() {
				    var d = new Date().getTime();
				    var uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
				        var r = (d + Math.random()*16)%16 | 0;
				        d = Math.floor(d/16);
				        return (c=='x' ? r : (r&0x3|0x8)).toString(16);
				    });
				    return uuid;
				};
				$("#link_cotizacion").val(generateUUID());
			})(jQuery);