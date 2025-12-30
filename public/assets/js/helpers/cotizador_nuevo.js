$("#nombre").on("change", function(){
	$("#nombres_1").val($("#nombre").val());
});
/*$("#nombres_1").on("change", function(){
	$("#nombre").val($("#nombres_1").val());
});*/
/*$("body").on("click", function(e){
	if($("#miembros").parent().hasClass("show")){
		if(!$("#miembros").is(e.target)
			&& $("#miembros").has(e.target).length === 0
			&& $(".show").has(e.target).length === 0
		){
			$("#miembros").parent().removeClass("show");
			var div = $("#miembros").next();
			$(div).removeClass("show miembros-open");
			$(div).removeAttr("x-placement");
		}
	}
});*/
/*$("#miembros").on("click", function(){
	$(this).parent().toggleClass("show");
	var div = $(this).next();
	$(div).toggleClass("show miembros-open");
	if($(div).hasClass("show"))
		$(div).attr("x-placement", "bottom_start");
	else
		$(div).removeAttr("x-placement");
});*/
var adultos = 1;
var menores = 0;
var c = 0;
function addMiembro(tipo){
	var _class, _nombre, _min, _max, _last, _opts;
	var div = '';
	_last = $("div.adulto").last();
	c++;
	if(tipo=="A"){
		_class = 'adulto';
		_nombre = 'Adulto';
		_opts = ['', 'Conyugue', 'Hijo(a)'];
		_min = 0;
		_max = 69;
	}
	else{
		_class = 'menor';
		_nombre = 'Menor';
		_opts = ['Hijo(a)'];
		_min = 0;
		_max = 17;
		if($("div.menor").last().length>0)
			_last = $("div.menor").last();
	}
	div += '<div class="form-row mb-2 ' + _class + '">';
		div += '<div class="col-md-2">';
			div += '<label for="titulos_' + c + '" class="form-label d-none">Titulo:</label>';
			div += '<select id="titulos_' + c + '" name="titulos[]" class="form-control" required>';
			for(x=0;x<_opts.length;x++)
				div += '<option value="' + _opts[x] + '">' + _opts[x] + '</option>';
			div += '</select>';
		div += '</div>';
		div +='<div class="col-md-6">';
			div += '<label for="nombres_' + c + '" class="form-label d-none">Nombre completo:</label>';
			div +='<input type="text" id="nombres_' + c + '" name="nombres[]" class="form-control" required>';
		div +='</div>';
		div +='<div class="col-md-2">';
			div += '<label for="sexos_' + c + '" class="form-label d-none">Sexo:</label>';
			div +='<select id="sexos_' + c + '" name="sexos[]" class="form-control" required>';
				div +='<option value="">Seleccionar...</option>';
				div +='<option value="m">Hombre</option>';
				div +='<option value="f">Mujer</option>';
			div +='</select>';
		div +='</div>';
		div +='<div class="col-md-2">';
			div += '<label for="edades_' + c + '" class="form-label d-none">Edad:</label>';
			div +='<input type="number" id="edades_' + c + '" name="edades[]" class="form-control" min="' + _min + '" max="' + _max + '" required>';
		div +='</div>';
	div +='</div>';
	
	$(div).insertAfter(_last);
}
$("#miembros").change(function(){
	var i = $("#miembros").val();
	var x;
	if(adultos < i){
		for(x=adultos;x<i;x++)
			addMiembro('A');
	}
	else if(adultos > i){
		for(x=i;x<adultos;x++)
			$("div.adulto").last().remove();
	}
	adultos = i;
});
/*$(".btn-miembros").on("click", function(e){
	e.preventDefault();
	if($(this).hasClass("adultos")){
		if($(this).hasClass("minus")){
			if(adultos>1){
				adultos--;
				$("tr.adulto").last().remove();
			}
		}
		else{
			adultos++;
			addMiembro('A');
		}
	}
	else{
		if($(this).hasClass("minus")){
			if(menores>0){
				menores--;
				$("tr.menor").last().remove();
			}
		}
		else{
			menores++;
			addMiembro('M');
		}
	}
	$("#total").val((adultos + menores));
	$("#adultos").val(adultos);
	$("#menores").val(menores);
	var t = adultos + ' Adulto(s)';
	if(menores>0)
		t += ', ' + menores + ' Menor(es)';
	$("#miembros").html(t);
});*/
$("#titulos_2, #nombres_2, #sexos_2, #edades_2").blur(function(e){
	if($("#titulos_2").val()!="" || $("#nombres_2").val()!="" || $("#sexos_2").val()!="" || $("#edades_2").val()!=""){
		$("#titulos_2, #nombres_2, #sexos_2, #edades_2").attr("required");
	}
	else{
		$("#titulos_2, #nombres_2, #sexos_2, #edades_2").removeAttr("required")
	}
});
$("#titulos_3, #nombres_3, #sexos_3, #edades_3").blur(function(e){
	if($("#titulos_3").val()!="" || $("#nombres_3").val()!="" || $("#sexos_3").val()!="" || $("#edades_3").val()!=""){
		$("#titulos_3, #nombres_3, #sexos_3, #edades_3").attr("required");
	}
	else{
		$("#titulos_3, #nombres_3, #sexos_3, #edades_3").removeAttr("required")
	}
});
$("#titulos_4, #nombres_4, #sexos_4, #edades_4").blur(function(e){
	if($("#titulos_4").val()!="" || $("#nombres_4").val()!="" || $("#sexos_4").val()!="" || $("#edades_4").val()!=""){
		$("#titulos_4, #nombres_4, #sexos_4, #edades_4").attr("required");
	}
	else{
		$("#titulos_4, #nombres_4, #sexos_4, #edades_4").removeAttr("required")
	}
});
$("#titulos_5, #nombres_5, #sexos_5, #edades_5").blur(function(e){
	if($("#titulos_5").val()!="" || $("#nombres_5").val()!="" || $("#sexos_5").val()!="" || $("#edades_5").val()!=""){
		$("#titulos_5, #nombres_5, #sexos_5, #edades_5").attr("required");
	}
	else{
		$("#titulos_5, #nombres_5, #sexos_5, #edades_5").removeAttr("required")
	}
});
$("#titulos_6, #nombres_6, #sexos_6, #edades_6").blur(function(e){
	if($("#titulos_6").val()!="" || $("#nombres_6").val()!="" || $("#sexos_6").val()!="" || $("#edades_6").val()!=""){
		$("#titulos_6, #nombres_6, #sexos_6, #edades_6").attr("required");
	}
	else{
		$("#titulos_6, #nombres_6, #sexos_6, #edades_6").removeAttr("required")
	}
});
$("#titulos_7, #nombre_7, #sexos_7, #edades_7").blur(function(e){
	if($("#titulos_7").val()!="" || $("#nombres_7").val()!="" || $("#sexos_7").val()!="" || $("#edades_7").val()!=""){
		$("#titulos_7, #nombres_7, #sexos_7, #edades_7").attr("required");
	}
	else{
		$("#titulos_7, #nombres_7, #sexos_7, #edades_7").removeAttr("required")
	}
});
var _submitted = 0;
$("#frmCotizacion").on("submit", function(e){
	e.preventDefault();
	$(this).removeClass("was-validated");
	if($(":invalid").filter(".form-control, .form-check-input").length>0){
		$(this).addClass('was-validated');
		$(":invalid").filter(".form-control, .form-check-input")[0].focus();
	}
	else{
	//if($("#chkTerminos").prop("checked")){
		if(validaIntegrantes()){
			var form = $(this);
			var data = $(form).serialize();
			console.log("Valida - " + _submitted);
			if(_submitted==0){
				var button = $(form).find(":submit");
				//$(form).data("submitted", 1);
				_submitted = 1;
				console.log("Actualiza - " + _submitted);
				$(button).attr("disabled", true);
				$(button).hide();
				$.ajax({
					url:'/nuevaCotizacionWS',
					data:data,
					method:'POST',
					type:'POST',
					dataType:'json',
					success:function(data, status, jqx){
						if(data.status=="success"){
							var idCotizacion = data.idCotizacion;
							var secret = data.secret;
							$.ajax({
								url:'/cotizarWS/' + data.idCotizacion + '/' + data.secret,
								method:'POST',
								dataType:'json',
								success:function(data, status, jqXHR){
									location.href='/cotizacion/' + idCotizacion + '/' + secret;
								},
								error:function(jqXHR, status, error){
									
								}
							});
							setTimeout(function(){
								location.href='/cotizacion/' + data.idCotizacion + '/' + data.secret;
							}, 15000);
						}
						else{
							if(data.status==440){
								$.notify({message: "Se produjo el siguiente error:<br>" + data.status + ' - ' + data.error}, {type: "danger", z_index: 2000});
								setTimeout(function(){location.href='xt-logout.php';}, 1500);
							}
							else
								$.notify({message: "Se produjo el siguiente error:<br>" + data.status + ' - ' + data.error}, {type: "danger", z_index: 2000});	
							//$(form).data("submitted", 0);
							_submitted = 0;
							$(button).removeAttr("disabled");
							$(button).show();
						}
					},
					error:function(jqx, status, error){
						//$(form).data("submitted", 0);
						_submitted = 0;
						$(button).removeAttr("disabled");
						$(button).show();
						console.log(error);	
					}
				});
			}
		}
	}
	//else{
	//	$.notify({message: "Debes de aceptar los términos, condiciones y aviso de privacidad"}, {type: "danger", z_index: 2000});
	//}
});
function doTabla(d, tipo, activo){
	var r;
	var _li = '';
	var _tab = '';
	var _paquete = 0;
	var _col, _colMd;
	
	switch(tipo){
		case "sa_db":
			r = d.tablaDatos.sa_db;
			break;
		case "sa_da":
			r = d.tablaDatos.sa_da;
			break;
		case "sb_db":
			r = d.tablaDatos.sb_db;
			break;
		case "sb_da":
			r = d.tablaDatos.sb_da;
			break;
	}
	
	_li += '<li class="nav-item"><a href="#tab' + tipo + '" id="tab-' + tipo + '" class="nav-link' + ((activo==true) ? ' active' : '') + '" data-toggle="tab" role="tab" aria-controls="tab' + tipo + '" aria-selected="true">' + r["titulo"] + '</a></li>';
	_tab += '<div class="tab-pane fade' + ((activo==true) ? ' show active' : '') + '" id="tab' + tipo + '" role="tabpanel" aria-labelledby="tab-' + tipo + '">';
	_tab += 	'<div class="container">';
	_tab += 		'<div class="row">';
	_tab += 			'<div class="col">';
	_tab += 				'<h2>' + r["nombre"] + '</h2>';
	_tab += 			'</div>';
	_tab += 		'</div>';
	_tab += 		'<div class="row">';
	
	_tab += 			'<div class="col-lg cotizador-tabla d-none d-md-block">';
	_tab += 				'<div class="row borde">';
	_tab += 					'<div class="col-12 text-center"><img src="/assets/images/aseguradoras/0.jpg" class="img-fluid"></div>';
	_tab += 				'</div>';
	_tab += 				'<div class="row borde pt-1 pb-1">';
	_tab += 					'<div class="col-12">&nbsp;</div>';
	_tab += 				'</div>';
	for(c=3;c<r.datos.tablas[0].length;c++){
		_tab += 			'<div class="row borde pt-1 pb-1">';
		_tab += 				'<div class="col col-12">';
		_tab += 					r.datos.tablas[0][c];
		_tab += 				'</div>';
		_tab += 			'</div>';
	}
	_tab += 				'<div class="row borde pt-1 pb-1">';
	_tab += 					'<div class="col col-6">';
	_tab += 						'Pago semestral';
	_tab += 					'</div>';
	_tab += 					'<div class="col col-6">';
	_tab += 						'Primer pago';
	_tab += 					'</div>';
	_tab += 				'</div>';
	_tab += 				'<div class="row borde pt-1 pb-1">';
	_tab += 					'<div class="col col-6">';
	_tab += 						'&nbsp;';
	_tab += 					'</div>';
	_tab += 					'<div class="col col-6">';
	_tab += 						'Posteriores';
	_tab += 					'</div>';
	_tab += 				'</div>';
	_tab += 				'<div class="row borde pt-1 pb-1">';
	_tab += 					'<div class="col col-6">';
	_tab += 						'Pago trimestral';
	_tab += 					'</div>';
	_tab += 					'<div class="col col-6">';
	_tab += 						'Primer pago';
	_tab += 					'</div>';
	_tab += 				'</div>';
	_tab += 				'<div class="row borde pt-1 pb-1">';
	_tab += 					'<div class="col col-6">';
	_tab += 						'&nbsp;';
	_tab += 					'</div>';
	_tab += 					'<div class="col col-6">';
	_tab += 						'Posteriores';
	_tab += 					'</div>';
	_tab += 				'</div>';
	_tab += 				'<div class="row borde pt-1 pb-1">';
	_tab += 					'<div class="col col-6">';
	_tab += 						'Pago mensual';
	_tab += 					'</div>';
	_tab += 					'<div class="col col-6">';
	_tab += 						'Primer pago';
	_tab += 					'</div>';
	_tab += 				'</div>';
	_tab += 				'<div class="row borde pt-1 pb-1">';
	_tab += 					'<div class="col col-6">';
	_tab += 						'&nbsp;';
	_tab += 					'</div>';
	_tab += 					'<div class="col col-6">';
	_tab += 						'Posteriores';
	_tab += 					'</div>';
	_tab += 				'</div>';
	_tab += 			'</div>';
	for(a=0;a<r.datos.aseguradoras.length;a++){
		_colMd = Math.floor(12 / parseInt(r.datos.aseguradoras[a].paquetes));
		_col = Math.floor(12 / (parseInt(r.datos.aseguradoras[a].paquetes) + 1));
		_tab += 		'<div class="col-lg cotizador-tabla">';
		_tab += 			'<div class="row borde">';
		_tab += 				'<div class="col-12 text-center">';
		_tab += 					'<img src="/assets/images/aseguradoras/' + r.datos.aseguradoras[a]["id"] + '.jpg" class="img-fluid">';
		_tab += 				'</div>';
		_tab += 			'</div>';
		for(c=2;c<r.datos.tablas[0].length;c++){
			_tab += 		'<div class="row borde pt-1 pb-1">';
			_tab += 			'<div class="col-5 d-block d-sm-none">';
			_tab += 				'<div class="row">';
			_tab += 					'<div class="col-12">';
			_tab += 						r.datos.tablas[0][c];
			_tab += 					'</div>';
			_tab += 				'</div>';
			_tab += 			'</div>';
			_tab += 			'<div class="col-7 col-lg-12">';
			_tab += 				'<div class="row">';
			for(p=1;p<=r.datos.aseguradoras[a].paquetes;p++){
				_tab += 				'<div class="col col-' + _colMd + ' col-md-' + _colMd + ' text-center">';
				_tab += 					r.datos.tablas[_paquete + p][c];
				_tab += 				'</div>';
			}
			_tab += 				'</div>';
			_tab += 			'</div>';
			_tab += 		'</div>';
		}
		_tab += 			'<div class="row borde pt-1 pb-1">';
		_tab += 				'<div class="col-5 d-block d-sm-none">';
		_tab += 					'<div class="row">';
		_tab += 						'<div class="col-6">';
		_tab += 							'Semestral';
		_tab += 						'</div>';
		_tab += 						'<div class="col-6">';
		_tab += 							'Primer pago';
		_tab += 						'</div>';
		_tab += 					'</div>';
		_tab += 				'</div>';
		_tab += 				'<div class="col-7 col-lg-12">';
		_tab += 					'<div class="row">';
		for(p=1;p<=r.datos.aseguradoras[a].paquetes;p++){
			_tab += 					'<div class="col col-' + _colMd + ' col-md-' + _colMd + ' text-center">';
			_tab += 						r.datos.pagos[_paquete + p][0];
			_tab += 					'</div>';
		}
		_tab += 					'</div>';
		_tab += 				'</div>';
		_tab += 			'</div>';
		_tab += 			'<div class="row borde pt-1 pb-1">';
		_tab += 				'<div class="col-5 d-block d-sm-none">';
		_tab += 					'<div class="row">';
		_tab += 						'<div class="col-6">';
		_tab += 							'&nbsp;';
		_tab += 						'</div>';
		_tab += 						'<div class="col-6">';
		_tab += 							'Posteriores';
		_tab += 						'</div>';
		_tab += 					'</div>';
		_tab += 				'</div>';
		_tab += 				'<div class="col-7 col-lg-12">';
		_tab += 					'<div class="row">';
		for(p=1;p<=r.datos.aseguradoras[a].paquetes;p++){
			_tab += 					'<div class="col col-' + _colMd + ' col-md-' + _colMd + ' text-center">';
			_tab += 						r.datos.pagos[_paquete + p][1];
			_tab += 					'</div>';
		}
		_tab += 					'</div>';
		_tab += 				'</div>';
		_tab += 			'</div>';
		
		_tab += 			'<div class="row borde pt-1 pb-1">';
		_tab += 				'<div class="col-5 d-block d-sm-none">';
		_tab += 					'<div class="row">';
		_tab += 						'<div class="col-6">';
		_tab += 							'Trimestral';
		_tab += 						'</div>';
		_tab += 						'<div class="col-6">';
		_tab += 							'Primer pago';
		_tab += 						'</div>';
		_tab += 					'</div>';
		_tab += 				'</div>';
		_tab += 				'<div class="col-7 col-lg-12">';
		_tab += 					'<div class="row">';
		for(p=1;p<=r.datos.aseguradoras[a].paquetes;p++){
			_tab += 					'<div class="col col-' + _colMd + ' col-md-' + _colMd + ' text-center">';
			_tab += 						r.datos.pagos[_paquete + p][2];
			_tab += 					'</div>';
		}
		_tab += 					'</div>';
		_tab += 				'</div>';
		_tab += 			'</div>';
		_tab += 			'<div class="row borde pt-1 pb-1">';
		_tab += 				'<div class="col-5 d-block d-sm-none">';
		_tab += 					'<div class="row">';
		_tab += 						'<div class="col-6">';
		_tab += 							'&nbsp;';
		_tab += 						'</div>';
		_tab += 						'<div class="col-6">';
		_tab += 							'Posteriores';
		_tab += 						'</div>';
		_tab += 					'</div>';
		_tab += 				'</div>';
		_tab += 				'<div class="col-7 col-lg-12">';
		_tab += 					'<div class="row">';
		for(p=1;p<=r.datos.aseguradoras[a].paquetes;p++){
			_tab += 					'<div class="col col-' + _colMd + ' col-md-' + _colMd + ' text-center">';
			_tab += 						r.datos.pagos[_paquete + p][3];
			_tab += 					'</div>';
		}
		_tab += 					'</div>';
		_tab += 				'</div>';
		_tab += 			'</div>';
		
		_tab += 			'<div class="row borde pt-1 pb-1">';
		_tab += 				'<div class="col-5 d-block d-sm-none">';
		_tab += 					'<div class="row">';
		_tab += 						'<div class="col-6">';
		_tab += 							'Mensual';
		_tab += 						'</div>';
		_tab += 						'<div class="col-6">';
		_tab += 							'Primer pago';
		_tab += 						'</div>';
		_tab += 					'</div>';
		_tab += 				'</div>';
		_tab += 				'<div class="col-7 col-lg-12">';
		_tab += 					'<div class="row">';
		for(p=1;p<=r.datos.aseguradoras[a].paquetes;p++){
			_tab += 					'<div class="col col-' + _colMd + ' col-md-' + _colMd + ' text-center">';
			_tab += 						r.datos.pagos[_paquete + p][4];
			_tab += 					'</div>';
		}
		_tab += 					'</div>';
		_tab += 				'</div>';
		_tab += 			'</div>';
		_tab += 			'<div class="row borde pt-1 pb-1">';
		_tab += 				'<div class="col-5 d-block d-sm-none">';
		_tab += 					'<div class="row">';
		_tab += 						'<div class="col-6">';
		_tab += 							'&nbsp;';
		_tab += 						'</div>';
		_tab += 						'<div class="col-6">';
		_tab += 							'Posteriores';
		_tab += 						'</div>';
		_tab += 					'</div>';
		_tab += 				'</div>';
		_tab += 				'<div class="col-7 col-lg-12">';
		_tab += 					'<div class="row">';
		for(p=1;p<=r.datos.aseguradoras[a].paquetes;p++){
			_tab += 					'<div class="col col-' + _colMd + ' col-md-' + _colMd + ' text-center">';
			_tab += 						r.datos.pagos[_paquete + p][5];
			_tab += 					'</div>';
		}
		_tab += 					'</div>';
		_tab += 				'</div>';
		_tab += 			'</div>';
		
		_tab += 		'</div>';
		_paquete += r.datos.aseguradoras[a].paquetes;
	}
	
	_tab += 		'</div>';
	_tab += 	'</div>';
	_tab += '</div>';
	
	return [_li, _tab]; 
}
function validaIntegrantes(){
	var _total = 1;
	for(x=2;x<=7;x++){
		if(
			$("#titulos_" + x).val()!=""
			|| $("#nombres_" + x).val()!=""
			|| $("#sexos_" + x).val()!=""
			|| $("#edades_" + x).val()!=""){
			if(
				$("#titulos_" + x).val()==""
				|| $("#nombres_" + x).val()==""
				|| $("#sexos_" + x).val()==""
				|| $("#edades_" + x).val()==""){
				$.notify({message: "Es necesario capturar todos los campos para cada integrante"}, {type: "danger"});
				return false;
			}
			_total++;
		}
	}
	$("#total").val(_total);
	return true;
}
function ctAutoHeight(){
	//$.each($(".cotizador-tabla").first().children(), function(i, div){
		//console.log(i);
		//console.log(div);
		//console.log($(div).height());
	//	$(".cotizador-tabla div:nth-child(" + (i + 1) + ")").css("min-height", $(div).height());
	//});
	console.log("Paso por ctAutoHeight");
	$.each($(".tab-pane"), function(t, tab){
		$.each($(tab).find(".cotizador-tabla").first().children(), function(i, div){
			for(x=1;x<$(tab).find(".cotizador-tabla").length;x++){
				$(tab).find(".cotizador-tabla").eq(x).children(":nth-child(" + (i+1) + ")").css("min-height", $(div).outerHeight(true));
			}
		});
		
	})
}


/** Aqui empieza la manipulacion del video, esta suspendido
var $window = $( window ); // 1. Window Object.
var $featuredMedia = $( "#featured-media" ); // 1. The Video Container.
var $featuredVideo = $( "#featured-video" ); // 2. The Youtube Video.

var player; // 3. Youtube player object.
var _top = $featuredMedia.offset().top; // 4. The video position from the top of the document;
var offset = Math.floor( _top + ( $featuredMedia.outerHeight() / 2 ) ); //5. offset.

window.onYouTubeIframeAPIReady = function() {
	player = new YT.Player( "featured-video", {
	   events: {
	     "onStateChange": onPlayerStateChange,
	     "onReady": onPlayerReady
	   }
	} );
};

function onPlayerStateChange( event ) {

   var isPlay  = 1 === event.data;
   var isPause = 2 === event.data;
   var isEnd   = 0 === event.data;

   if ( isPlay ) {
      $featuredVideo.removeClass( "is-paused" );
      $featuredVideo.toggleClass( "is-playing" );
   }

   if ( isPause ) {
      $featuredVideo.removeClass( "is-playing" );
      $featuredVideo.toggleClass( "is-paused" );
   }

   if ( isEnd ) {
      $featuredVideo.removeClass( "is-playing", "is-paused" );
   }
}

function onPlayerReady(event){
	event.target.playVideo();
}

$window
.on( "resize", function() {
   _top = $featuredMedia.offset().top;
   offset = Math.floor( _top + ( $featuredMedia.outerHeight() / 2 ) );
} )
.on( "scroll", function() {
   $featuredVideo.toggleClass( "is-sticky",
     $window.scrollTop() > offset && $featuredVideo.hasClass( "is-playing" )
   );
} );
* Aqui termina la manipulacion del video, esta suspendido
**/

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
var estados = [
	{"api": "Aguascalientes", "estado": "Aguascalientes"},
	{"api": "Baja California", "estado": "Baja California"},
	{"api": "Baja California Sur", "estado": "Baja California Sur"},
	{"api": "Campeche", "estado": "Campeche"},
	{"api": "Coahuila", "estado": "Coahuila"},
	{"api": "Colima", "estado": "Colima"},
	{"api": "Chiapas", "estado": "Chiapas"},
	{"api": "Chihuahua", "estado": "Chihuahua"},
	{"api": "Ciudad de México", "estado": "CDMX"},
	{"api": "Durango", "estado": "Durango"},
	{"api": "Guanajuato", "estado": "Guanajuato"},
	{"api": "Guerrero", "estado": "Guerrero"},
	{"api": "Hidalgo", "estado": "Hidalgo"},
	{"api": "Jalisco", "estado": "Jalisco"},
	{"api": "México", "estado": "México"},
	{"api": "Michoacán de Ocampo", "estado": "Michoacán"},
	{"api": "Morelos", "estado": "Morelos"},
	{"api": "Nayarit", "estado": "Nayarit"},
	{"api": "Nuevo León", "estado": "Nuevo León"},
	{"api": "Oaxaca", "estado": "Oaxaca"},
	{"api": "Puebla", "estado": "Puebla"},
	{"api": "Querétaro de Arteaga", "estado": "Querétaro"},
	{"api": "Quintana Roo", "estado": "Quintana Roo"},
	{"api": "San Luis Potosí", "estado": "San Luis Potosí"},
	{"api": "Sinaloa", "estado": "Sinaloa"},
	{"api": "Sonora", "estado": "Sonora"},
	{"api": "Tabasco", "estado": "Tabasco"},
	{"api": "Tamaulipas", "estado": "Tamaulipas"},
	{"api": "Tlaxcala", "estado": "Tlaxcala"},
	{"api": "Veracruz-Llave", "estado": "Veracruz"},
	{"api": "Yucatán", "estado": "Yucatán"},
	{"api": "Zacatecas", "estado": "Zacatecas"}
];
$.ajax({
	url:'https://ipapi.co/json/',
	method:'GET',
	dataType:'json',
	success:function(data, status, jqXHR){
		if(data.region){
			var estado = estados.find(estado => estado.api === data.region);
			if(estado)
				$("#estado option[value=" + estado.estado + "]").attr("selected", true);
		}
	},
	error:function(jqXHR, status, error){
		console.log("Error: " + error);
	}
});