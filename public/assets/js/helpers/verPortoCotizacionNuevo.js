var cotizacion;
var valores;
var paquetes;
var DATA;
var _pctMapfre = 1;
$(document).ajaxStart(function(){
	$(".loading").show();
});
$(document).ajaxStop(function(){
	$(".loading").hide();
});
$("#steps").on("show.bs.collapse", function(){
	$("#cotizacion-col").removeClass("col-md-12").addClass("col-md-9");
	$(".steps-toggler").removeClass("steps-open").addClass("steps-close");
});$("#steps").on("hidden.bs.collapse", function(){
	$("#cotizacion-col").removeClass("col-md-9").addClass("col-md-12");
	$(".steps-toggler").removeClass("steps-close").addClass("steps-open");
});
$(".btnEditar").click(function(e){
	e.preventDefault();
	//location.href = 'editar-cotizacion.php?idCotizacion=' + idCotizacion + '&secret=' + secret;
	$("#cotizacion-body").collapse("hide");
	$("#card3").fadeIn();
	$("#steps").collapse("show");
	$(".btnEditar").addClass("d-none");
	cardData();
	$([document.documentElement, document.body]).animate({
        scrollTop: $("#card3").offset().top
    }, 2000);
});
function isOdd (number) {
	return (number & 1) === 1;
}
function formatCurrency(n){
	return '$' + parseFloat(n, 10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString();
}
function mapfreValores(valores, tipo, index){
	$.each(valores, function(i, v){
		switch(tipo){
			case "H":
				$('td[data-id-concepto="' + v.id + '"][data-hospitales="' + index + '"]').html(v.format);
				$('div[data-id-concepto="' + v.id + '"][data-hospitales="' + index + '"]').html(v.format);
				$('.cmdEditar[data-hospitales="' + index + '"]').removeClass("d-none");
				$('.cmdMeInteresa[data-hospitales="' + index + '"]').removeClass("d-none");
				break;
		}
	});
}
function getPrecio(tipo, hospitales, pre){
	var data = 'tipo=' + tipo + '&hospitales=' + hospitales;
	var precio = pre.split("-");
	var x = precio[1];
	var _paquetes = 0;
	var _pct = 1;
	$.each(DATA.tablaDatos.sa_db.datos.aseguradoras, function(_z, _a){
		_paquetes += _a.paquetes;
		if(x<=_paquetes){
			_pct = 1 + (_a.inflar/100);
			return false;
		}
	});
	$.ajax({
		url:'/recotizarWS2023/' + idCotizacion + '/' + secret,
		method:'POST',
		data:data,
		dataType:'json',
		global:false,
		success:function(data, status, jqXHR){
			console.log(data);
			/*$.each(data.conceptos, function(i, concepto){
				$("#concepto-" + concepto.id + "-" + x).html(concepto.concepto);
			});*/
			mapfreValores(data.conceptos, "H", hospitales)
			//console.log("Si pasa por aqui");
			$("#precio-top-" + x).html('<h4 class="mb-0"><strike>$' + new Intl.NumberFormat('es-MX', {maximumFractionDigits:0}).format((parseFloat(data.contado.replace(",", "").replace("$", "")) * parseFloat(_pct))) + '</strike></h4><h3 class="text-azul mt-0 mb-0"><b>' + data.contado + '</b></h3>');
			//console.log("Si pasa por aqui 2");
			$("#" + pre + ", #m-" + pre).html('<b>' + data.contado + '</b>');
			//console.log("Si pasa por aqui 3");
			$("#" + pre + "-s1, #m-" + pre + "-s1").html(data.semestral_1);
			$("#" + pre + "-s2, #m-" + pre + "-s2").html(data.semestral_2);
			$("#" + pre + "-t1, #m-" + pre + "-t1").html(data.trimestral_1);
			$("#" + pre + "-t2, #m-" + pre + "-t2").html(data.trimestral_2);
			$("#" + pre + "-m1, #m-" + pre + "-m1").html(data.mensual_1);
			$("#" + pre + "-m2, #m-" + pre + "-m2").html(data.mensual_2);
		},
		error:function(jqXHR, status, error){
			
		}
	});
}
function doTabla(data){
	// Tabla de cotizacion
	paquetes = 0;
	var el = '';
	el = '<div class="table-responsive"><table id="tblCotizacion" class="table table-hover"><thead><tr data-tipo="logos"><th>&nbsp;</th>';
	
	var _e = '';
	var _mapfrePaquetes = 0;
	// Cotizar nivel superior
	if(data.cotizacionDatos.nivel_amplio==0){
		var _p = 0;
		$.each(data.tablaDatos.sa_db.datos.aseguradoras, function(i, a){
			_p += a.paquetes;
		});
		el += '<th colspan="' + _p + '" class="text-right"><a href="#" class="btn btn-outline btn-rounded btn-primary text-1 font-weight-bold text-uppercase nivel-amplio">COTIZAR NIVEL SUPERIOR <i class="fa fa-arrow-right"></i></a></th></tr><tr><th>&nbsp;</th>';
	}
	// Logotipos
	$.each(data.tablaDatos.sa_db.datos.aseguradoras, function(i, a){
		if(a.id==2){
			if(data.cotizacionDatos.nivel_amplio==0){
				//_e = '<a href="#" class="btn btn-outline btn-rounded btn-primary text-1 font-weight-bold text-uppercase nivel-amplio">COTIZAR NIVEL SUPERIOR <i class="fa fa-arrow-right"></i></a>';
				_mapfrePaquetes = a.paquetes;
			}
		}
		el += '<th colspan="' + a.paquetes + '" class="text-center" data-id="' + a.id + '"><img src="https://www.segurodegastosmedicosmayores.mx/assets/images/aseguradoras/' + a.id + '.jpg" class="img-fluid"></th>';
		paquetes += a.paquetes;
	});
	el += '</tr></thead><tbody>';
	
	// Coberturas
	var coberturas = data.tablaDatos.sa_db.datos.tablas[0].length;
	var _tipo, _id;
	var _cobertura, _tooltip;
	var _class = '';
	$.each(data.tablaDatos.sa_db.datos.tablas[0], function(i, c){
		if(i>1){
			_tipo = 'coberturas';
			_id = '';
			if(i==2)
				_tipo = 'hospitales';
			if(coberturas==(i+1)){
				_tipo = 'contado';
				_class = 'bg-pago';
			}
			if(data.tablaDatos.sa_db.datos.tablas[1][i].toString().indexOf('|')>=0){
				var _t = data.tablaDatos.sa_db.datos.tablas[1][i].toString().split('|');
				_id = _t[0];
			}
			_cobertura = c;
			_tooltip = '';
			if(c.indexOf('|')>=0){
				var _t = c.split('|');
				_cobertura = _t[0];
				_tooltip = _t[1];
			}
			if(_tipo=='hospitales'){
				// Agrega una fila con los precios
				el += '<tr data-tipo="contado-top" data-id-concepto="' + _id + '" class=""><th role="col" class="text-azul">' + _cobertura + '</th>';
				for(x=1;x<=paquetes;x++){
					var h = data.tablaDatos.sa_db.datos.tablas[x][1];
					el += '<--precioTop-' + h + '-->';
				}
				el += '</tr>';
				
				el += '<tr data-tipo="' + _tipo + '" data-id-concepto="' + _id + '"><th role="col" class="text-azul">' + _cobertura + '</th>';
			}
			else{
				if(_tooltip!='')
					el += '<tr data-tipo="' + _tipo + '" data-id-concepto="' + _id + '" class="' + _class + '"><th role="col" class="text-azul">' + _cobertura + '&nbsp;<img src="/images_post/images/ic-info.png" class="img-fluid" data-toggle="tooltip" title="' + _tooltip + '"></th>';
				else
					el += '<tr data-tipo="' + _tipo + '" data-id-concepto="' + _id + '" class="' + _class + '"><th role="col" class="text-azul">' + _cobertura + '</th>';
			}
			for(x=1;x<=paquetes;x++){
				var v = data.tablaDatos.sa_db.datos.tablas[x][i];
				var h = data.tablaDatos.sa_db.datos.tablas[x][1];
				if(coberturas > (i+1)){
					if(v.indexOf("|")>=0){
						var concepto = v.split("|");
						el += '<td class="text-center" id="concepto-' + concepto[0] + '-' + x + '" data-id-concepto="' + concepto[0] + '" data-hospitales="' + h + '">' + concepto[1] + '</td>';
					}
					else{
						el += '<td class="text-center"><h4 class="text-azul"><b>' + v + '</b></h4></td>';
					}
				}
				else{
					var _pct = 1;
					var _paquetes = 0;
					$.each(data.tablaDatos.sa_db.datos.aseguradoras, function(_z, _a){
						_paquetes += _a.paquetes;
						if(x<=_paquetes){
							_pct = 1 + (_a.inflar/100);
							return false;
						}
					});
					if(v!=-1){
						el += '<td class="text-center text-azul" id="precio-' + x + '"><b>' + v + '</b></td>';
						el = el.replace('<--precioTop-' + h + '-->', '<th class="text-center text-azul" id="precio-top-' + x + '"><h4 class="mb-0"><strike>$' + new Intl.NumberFormat('es-MX', {maximumFractionDigits:0}).format((parseFloat(v.replace(",", "").replace("$", "")) * parseFloat(_pct))) + '</strike></h4><h3 class="text-azul mt-0 mb-0"><b>' + v + '</b></h3></th>');
					}
					else{
						el += '<td class="text-center text-azul precio" id="precio-' + x + '" data-tipo="sadb" data-hospitales="' + data.tablaDatos.sa_db.datos.tablas[x][1] + '"><div class="spinner-border spinner-border-sm text-azul" role="status"><span class="sr-only">Procesando...</span></div></td>';
						el = el.replace('<--precioTop-' + h + '-->', '<th class="text-center text-azul" id="precio-top-' + x + '" data-tipo="sadb" data-hospitales="' + data.tablaDatos.sa_db.datos.tablas[x][1] + '"><div class="spinner-border spinner-border-sm text-azul" role="status"><span class="sr-only">Procesando...</span></div></th>');
					}
				}
			}
			el += '</tr>';
			//if(_tipo=='hospitales' && _e!=""){
			//	el += '<tr><td class="text-center" colspan="' + _mapfrePaquetes + '">' + _e + '</td></tr>';
			//}
		}
	});
	
	// Pagos
	el += '<tr data-tipo="cabecera-diferidos" class="cabecera-diferidos hand"><th role="col" colspan="' + (paquetes + 1) + '" class="text-azul text-center">Mostar pagos diferidos</th></tr>';
	var v;
	var _tipo, _pago;
	$.each(data.tablaDatos.sa_db.datos.pagos[0], function(i, c){
		_tipo = '';
		_pago = '';
		_class = ' tab-left';
		switch(i){
			case 0:
				_tipo = 'cabecera-';
				_class = ' bg-pago';
				break;
			case 1:
				_pago = 's1';
				break
			case 2:
				_pago = 's2';
				break
			case 3:
				_tipo = 'cabecera-';
				_class = ' bg-pago';
				break;
			case 4:
				_pago = 't1';
				break
			case 5:
				_pago = 't2';
				break
			case 6:
				_tipo = 'cabecera-';
				_class = ' bg-pago';
				break;
			case 7:
				_pago = 'm1';
				break
			case 8:
				_pago = 'm2';
				break
		}
		el += '<tr data-tipo="' + _tipo + 'pagos" data-pago="' + _pago + '" class="pagos-diferidos d-none' + _class + '"><th role="col" class="text-azul">' + c + '</th>';
		for(x=1;x<=paquetes;x++){
			switch(i){
				case 1:
					v = data.tablaDatos.sa_db.datos.pagos[x][0];
					if(v!=-1)
						el += '<td class="text-center" id="precio-' + x + '-s1">' + v + '</td>';
					else
						el += '<td class="text-center" id="precio-' + x + '-s1"><div class="spinner-border spinner-border-sm text-azul" role="status"><span class="sr-only">Proesando...</span></div></td>';
					break;
				case 2:
					v = data.tablaDatos.sa_db.datos.pagos[x][1];
					if(v!=-1)
						el += '<td class="text-center" id="precio-' + x + '-s2">' + v + '</td>';
					else
						el += '<td class="text-center" id="precio-' + x + '-s2"><div class="spinner-border spinner-border-sm text-azul" role="status"><span class="sr-only">Proesando...</span></div></td>';
					break;
				case 4:
					v = data.tablaDatos.sa_db.datos.pagos[x][2];
					if(v!=-1)
						el += '<td class="text-center" id="precio-' + x + '-t1">' + v + '</td>';
					else
						el += '<td class="text-center" id="precio-' + x + '-t1"><div class="spinner-border spinner-border-sm text-azul" role="status"><span class="sr-only">Proesando...</span></div></td>';
					break;
				case 5:
					v = data.tablaDatos.sa_db.datos.pagos[x][3];
					if(v!=-1)
						el += '<td class="text-center" id="precio-' + x + '-t2">' + v + '</td>';
					else
						el += '<td class="text-center" id="precio-' + x + '-t2"><div class="spinner-border spinner-border-sm text-azul" role="status"><span class="sr-only">Proesando...</span></div></td>';
					break;
				case 7:
					v = data.tablaDatos.sa_db.datos.pagos[x][4];
					if(v!=-1)
						el += '<td class="text-center" id="precio-' + x + '-m1">' + v + '</td>';
					else
						el += '<td class="text-center" id="precio-' + x + '-m1"><div class="spinner-border spinner-border-sm text-azul" role="status"><span class="sr-only">Proesando...</span></div></td>';
					break;
				case 8:
					v = data.tablaDatos.sa_db.datos.pagos[x][5];
					if(v!=-1)
						el += '<td class="text-center" id="precio-' + x + '-m2">' + v + '</td>';
					else
						el += '<td class="text-center" id="precio-' + x + '-m2"><div class="spinner-border spinner-border-sm text-azul" role="status"><span class="sr-only">Proesando...</span></div></td>';
					break;
				default:
					el += '<td>&nbsp;</td>';
					break;
			}
		}
		el += '</tr>';
	});
	// Botones de Editar
	el += '<tr data-tipo="botones"><td>&nbsp;</td>';
	x = 1;
	$.each(cotizacion.tablaDatos.sa_db.datos.aseguradoras, function(i, aseguradora){
		for(z=1;z<=aseguradora.paquetes;z++){
			if(aseguradora.id==2)
				el += '<td align="center">' +
							'<button type="button" class="btn btn-outline btn-primary font-weight-bold custom-btn-style-1 cmdEditar d-none" data-col="' + x + '" data-hospitales="' + cotizacion.tablaDatos.sa_db.datos.tablas[x][1] + '">Editar</button>' + 
							'<button type="button" class="btn btn-outline btn-gray btn-sm font-weight-bold custom-btn-style-1 cmdCancelar d-none" data-col="' + x + '" data-hospitales="' + cotizacion.tablaDatos.sa_db.datos.tablas[x][1] + '">Cancelar</button>' + 
							'<button type="button" class="btn btn-outline btn-primary btn-sm font-weight-bold custom-btn-style-1 cmdRecotizar d-none" data-col="' + x + '" data-hospitales="' + cotizacion.tablaDatos.sa_db.datos.tablas[x][1] + '">Recotizar</button>' + 
						'</td>';
						
			else
				el += '<td>&nbsp;</td>';
			x++;
		}
	});
	el += '</tr>';
	// Botones Me Interesa
	el += '<tr data-tipo="botones-me-interesa"><td>&nbsp;</td>';
	x = 1;
	$.each(cotizacion.tablaDatos.sa_db.datos.aseguradoras, function(i, aseguradora){
		var _d;
		for(z=1;z<=aseguradora.paquetes;z++){
			_d = '';
			if(aseguradora.id==2)
				_d = 'd-none';
			el += '<td align="center">' +
						'<a href="/me-interesa/' + idCotizacion + '/' + secret + '/' + cotizacion.tablaDatos.sa_db.datos.tablas[x][1] + '" class="btn btn-outline btn-primary font-weight-bold custom-btn-style-1 w-100 cmdMeInteresa ' + _d + '" data-hospitales="' + cotizacion.tablaDatos.sa_db.datos.tablas[x][1] + '">Me Interesa</button>' + 
					'</td>';
						
			x++;
		}
	});
	el += '</tr>';
	
	
	el += '</tbody></table></div>';
	$("#card-tabla").find(".card-body").append(el);
	$.each(cotizacion.tablaDatos.sa_db.recotizaciones, function(i, r){
		mapfreValores(r.valores, "H", r.hospitales);
	});
	$.each($(".precio"), function(i, el){
		getPrecio($(this).data("tipo"), $(this).data("hospitales"), $(this).attr("id"));
	});
}
function doMTabla(data){
	var el = '<div class="container">';
	var P = 1;
	var p = 1;
	var _line;
	var _class;
	var _cobetura;
	var _cob, _tool;
	
	$.each(data.tablaDatos.sa_db.datos.aseguradoras, function(i, a){
		// Logotipo
		el += '<div class="row"><div class="col text-center"><img src="https://www.segurodegastosmedicosmayores.mx/assets/images/aseguradoras/' + a.id + '.jpg" class="img-fluid"></div></div>';
		
		// Paquetes
		el += '<div class="row">';
		for(x=1;x<=a.paquetes;x++){
			el += '<div class="col text-right text-primary"><strong>' + data.tablaDatos.sa_db.datos.tablas[p][2] + '</strong></div>';
			p++;
		}
		el += '</div>';
		
		// Coberturas
		$.each(data.tablaDatos.sa_db.datos.tablas[0], function(i, c){
			if(i >= 3){
				_class = "";
				if(isOdd(i))
					_class = " bg-gris-claro";
				p = P;
				_cob = c;
				_tool = '';
				if(c.indexOf('|')>=0){
					var _t = c.split('|');
					_cob = _t[0];
					_tool = _t[1];
				}
				if(_tool!='')
					el += '<div class="row pt-2' + _class + '"><div class="col text-azul"><strong>' + _cob + '</strong>&nbsp;<img src="/images_post/images/ic-info.png" class="img-fluid" data-toggle="tooltip" title="' + _tool + '"></div></div>';
				else
					el += '<div class="row pt-2' + _class + '"><div class="col text-azul"><strong>' + _cob + '</strong></div></div>';
				el += '<div class="row pb-2' + _class + '">';
				for(x=1;x<=a.paquetes;x++){
					if(typeof data.tablaDatos.sa_db.datos.tablas[p][i]=='string'){
						if(data.tablaDatos.sa_db.datos.tablas[p][i].indexOf('|')>=0){
							_cobertura = data.tablaDatos.sa_db.datos.tablas[p][i].split('|');
							el += '<div class="col text-right" data-id-concepto="' + _cobertura[0] + '" data-hospitales="' + data.tablaDatos.sa_db.datos.tablas[p][1] + '">' + _cobertura[1] + '</div>';
						}
						else{
							if(data.tablaDatos.sa_db.datos.tablas[p][i]==-1)
								el += '<div class="col text-right" id="m-precio-' + p + '"><div class="spinner-border spinner-border-sm text-azul" role="status"><span class="sr-only">Procesando...</span></div></div>';
							else
								el += '<div class="col text-right" id="m-precio-' + p + '">' + data.tablaDatos.sa_db.datos.tablas[p][i] + '</div>';
						}
					}
					else{
						if(data.tablaDatos.sa_db.datos.tablas[p][i]==-1)
							el += '<div class="col text-right" id="m-precio-' + p + '"><div class="spinner-border spinner-border-sm text-azul" role="status"><span class="sr-only">Procesando...</span></div></div>';
						else
							el += '<div class="col text-right" id="m-precio-' + p + '">' + data.tablaDatos.sa_db.datos.tablas[p][i] + '</div>';
					}
					p++;
				}
				el += '</div>';
			}
			_line = i;
		});
		
		// Pago semestral
		p = P;
		_line++;
		_class = "";
		if(isOdd(_line))
			_class = " bg-gris-claro";
		el += '<div class="row pt-2' + _class + '"><div class="col text-azul"><strong>SEMESTRAL</strong></div></div>';
		el += '<div class="row' + _class + '"><div class="col text-azul">Primer pago</div></div>';
		el += '<div class="row' + _class + '">';
		for(x=1;x<=a.paquetes;x++){
			if(data.tablaDatos.sa_db.datos.pagos[p][0]==-1)
				el += '<div class="col text-right" id="m-precio-' + p + '-s1"><div class="spinner-border spinner-border-sm text-azul" role="status"><span class="sr-only">Procesando...</span></div></div>';
			else
				el += '<div class="col text-right" id="m-precio-' + p + '-s1">' + data.tablaDatos.sa_db.datos.pagos[p][0] + '</div>';
			p++;
		}
		el += '</div>';
		p = P;
		el += '<div class="row' + _class + '"><div class="col text-azul">Posterior</div></div>';
		el += '<div class="row pb-2' + _class + '">';
		for(x=1;x<=a.paquetes;x++){
			if(data.tablaDatos.sa_db.datos.pagos[p][1]==-1)
				el += '<div class="col text-right" id="m-precio-' + p + '-s2"><div class="spinner-border spinner-border-sm text-azul" role="status"><span class="sr-only">Procesando...</span></div></div>';
			else
				el += '<div class="col text-right" id="m-precio-' + p + '-s2">' + data.tablaDatos.sa_db.datos.pagos[p][1] + '</div>';
			p++;
		}
		el += '</div>';
		
		// Pago trimestral
		p = P;
		_line++;
		_class = "";
		if(isOdd(_line))
			_class = " bg-gris-claro";
		el += '<div class="row pt-2' + _class + '"><div class="col text-azul"><strong>TRIMESTRAL</strong></div></div>';
		el += '<div class="row' + _class + '"><div class="col text-azul">Primer pago</div></div>';
		el += '<div class="row' + _class + '">';
		for(x=1;x<=a.paquetes;x++){
			if(data.tablaDatos.sa_db.datos.pagos[p][2]==-1)
				el += '<div class="col text-right" id="m-precio-' + p + '-t1"><div class="spinner-border spinner-border-sm text-azul" role="status"><span class="sr-only">Procesando...</span></div></div>';
			else
				el += '<div class="col text-right" id="m-precio-' + p + '-t1">' + data.tablaDatos.sa_db.datos.pagos[p][2] + '</div>';
			p++;
		}
		el += '</div>';
		p = P;
		el += '<div class="row' + _class + '"><div class="col text-azul">Posterior</div></div>';
		el += '<div class="row pb-2' + _class + '">';
		for(x=1;x<=a.paquetes;x++){
			if(data.tablaDatos.sa_db.datos.pagos[p][3]==-1)
				el += '<div class="col text-right" id="m-precio-' + p + '-t2"><div class="spinner-border spinner-border-sm text-azul" role="status"><span class="sr-only">Procesando...</span></div></div>';
			else
				el += '<div class="col text-right" id="m-precio-' + p + '-t2">' + data.tablaDatos.sa_db.datos.pagos[p][3] + '</div>';
			p++;
		}
		el += '</div>';
		
		// Pago mensual
		p = P;
		_line++;
		_class = "";
		if(isOdd(_line))
			_class = " bg-gris-claro";
		el += '<div class="row pt-2' + _class + '"><div class="col text-azul"><strong>MENSUAL</strong></div></div>';
		el += '<div class="row' + _class + '"><div class="col text-azul">Primer pago</div></div>';
		el += '<div class="row' + _class + '">';
		for(x=1;x<=a.paquetes;x++){
			if(data.tablaDatos.sa_db.datos.pagos[p][4]==-1)
				el += '<div class="col text-right" id="m-precio-' + p + '-m1"><div class="spinner-border spinner-border-sm text-azul" role="status"><span class="sr-only">Procesando...</span></div></div>';
			else
				el += '<div class="col text-right" id="m-precio-' + p + '-m1">' + data.tablaDatos.sa_db.datos.pagos[p][4] + '</div>';
			p++;
		}
		el += '</div>';
		p = P;
		el += '<div class="row' + _class + '"><div class="col text-azul">Posterior</div></div>';
		el += '<div class="row pb-2' + _class + '">';
		for(x=1;x<=a.paquetes;x++){
			if(data.tablaDatos.sa_db.datos.pagos[p][5]==-1)
				el += '<div class="col text-right" id="m-precio-' + p + '-m2"><div class="spinner-border spinner-border-sm text-azul" role="status"><span class="sr-only">Procesando...</span></div></div>';
			else
				el += '<div class="col text-right" id="m-precio-' + p + '-m2">' + data.tablaDatos.sa_db.datos.pagos[p][5] + '</div>';
			p++;
		}
		el += '</div>';
		
		// Boton para imprimir
		el += '<div class="row mb-5 mt-5"><div class="col text-center">';
		el += '<a href="#" class="btn btn-xl btn-outline btn-rounded btn-primary text-1 ml-3 font-weight-bold text-uppercase btnPrint" target="_blank"><i class="fa fa-print"></i> IMPRIMIR</a>';
		el += '</div></div>';
		
		// Notas
		if(a.movil!=null){
			el += '<div class="div class="row mt-5 mb-5">' + a.movil + '</div>';
		}
		
		P = p;
	});
	
	el += '</div>';
	$("#m-tabla").html(el);
	
	$('[data-appear-animation]').each(function() {
		var $this = $(this),
			opts;

		var pluginOptions = theme.fn.getOptions($this.data('plugin-options'));
		if (pluginOptions)
			opts = pluginOptions;

		$this.themePluginAnimate(opts);
	});
}
$.ajax({
	url:'/cotizacion2023/' + idCotizacion + '/' + secret,
	method:'GET',
	dataType:'json',
	success:function(data, status, jqXHR){
		cotizacion = data;
		console.log(data);
		secret = cotizacion.cotizacionDatos.secret;
		var _r = '';
		
		// Card 1
		_r = cotizacion.cotizacionDatos.ciudad.toUpperCase() + ", " + cotizacion.cotizacionDatos.estado.toUpperCase();
		_r += '<label class="text-azul d-bock mt-2">Protección para</label>';
		switch(cotizacion.cotizacionDatos.cotizar_para){
			case 1:
				_r += '<p class="text-muted">1 persona</p><ul>';
				break;
			case 2:
				_r += '<p class="text-muted">Mi pareja y yo</p></ul>';
				break;
			case 3:
				_r += '<p class="text-muted">Mi pareja, yo y mis hijos</p><ul>';
				break;
			case 4:
				_r += '<p class="text-muted">Mi pareja y mis hijos</p><ul>';
				break;
			case 5:
				_r += '<p class="text-muted">Yo y mis hijos</p><ul>';
				break;
			case 6:
				_r += '<p class="text-muted">Mis hijos</p><ul>';
				break;
		}
		$.each(cotizacion.cotizacionDatos.integrantes, function(x, i){
			_r += '<li class="text-muted">' + i.nombre + ' (' + ((i.sexo=='m') ? 'H' : 'F') + ') - ' + i.edad + '</li>';
		});
		_r += '</ul>';
		$("#step1, #m-step1").find(".results").html(_r);
		
		/*switch(data.cotizacionDatos.cotizar_para){
			case 1:
				_h = '1 persona';
				_h += '<ul>';
				$.each(data.cotizacionDatos.integrantes, function(i, p){
					_h += '<li>' + p.nombre + ' (' + ((p.sexo=='m') ? 'H' : 'M') + ') - ' + p.edad + '</li>';
				});
				_h += '</ul>';
				$("#step2").find(".results").html(_h);
				break;
			case 2:
				_h = 'Mi pareja y yo';
				_h += '<ul>';
				$.each(data.cotizacionDatos.integrantes, function(i, p){
					_h += '<li>' + p.nombre + ' (' + ((p.sexo=='m') ? 'H' : 'M') + ') - ' + p.edad + '</li>';
				});
				_h += '</ul>';
				$("#step2").find(".results").html(_h);
				break;
			case 3:
				_h = 'Mi pareja, yo y mis hijos';
				_h += '<ul>';
				$.each(data.cotizacionDatos.integrantes, function(i, p){
					_h += '<li>' + p.nombre + ' (' + ((p.sexo=='m') ? 'H' : 'M') + ') - ' + p.edad + '</li>';
				});
				_h += '</ul>';
				$("#step2").find(".results").html(_h);
				break;
			case 4:
				_h = 'Mi pareja y mis hijos';
				_h += '<ul>';
				$.each(data.cotizacionDatos.integrantes, function(i, p){
					_h += '<li>' + p.nombre + ' (' + ((p.sexo=='m') ? 'H' : 'M') + ') - ' + p.edad + '</li>';
				});
				_h += '</ul>';
				$("#step2").find(".results").html(_h);
				break;
			case 5:
				_h = 'Yo y mis hijos';
				_h += '<ul>';
				$.each(data.cotizacionDatos.integrantes, function(i, p){
					_h += '<li>' + p.nombre + ' (' + ((p.sexo=='m') ? 'H' : 'M') + ') - ' + p.edad + '</li>';
				});
				_h += '</ul>';
				$("#step2").find(".results").html(_h);
				break;
			case 6:
				_h = 'Mis hijos';
				_h += '<ul>';
				$.each(data.cotizacionDatos.integrantes, function(i, p){
					_h += '<li>' + p.nombre + ' (' + ((p.sexo=='m') ? 'H' : 'M') + ') - ' + p.edad + '</li>';
				});
				_h += '</ul>';
				$("#step2").find(".results").html(_h);
				break;
		}*/
		
		// Card 3
		_r = '<ul>';
		if(data.cotizacionDatos.poliza_actual=='si')
			_r += '<li class="text-muted">Tengo póliza actualmente</li>';
		if(data.cotizacionDatos.maternidad==1)
			_r += '<li class="text-muted">Me interesa cobertura en Maternidad - ' + formatCurrency(data.cotizacionDatos.sa_maternidad) + '</li>';
		if(data.cotizacionDatos.emergencia_extranjero==1)
			_r += '<li class="text-muted">A veces viajo al extranjero y necesito cobertura</li>';
		if(data.cotizacionDatos.dental==1)
			_r += '<li class="text-muted">Deseo cobertura Dental Básica</li>';
		if(data.cotizacionDatos.multiregion==1)
			_r += '<li class="text-muted">Quisiera cobertura en Estados de México más costosos</li>';
		_r += '</ul><label class="text-azul d-block mt-2">Contacto</label>';
		_r += data.cotizacionDatos.nombre;
		if(data.cotizacionDatos.e_mail!="")
			_r += '<br>' + data.cotizacionDatos.e_mail;
		if(data.cotizacionDatos.telefono!="")
			_r += '<br>' + data.cotizacionDatos.telefono;
		$("#step3, #m-step3").find(".results").html(_r);
		
		//$("#no-cotizacion").find("span").html(data.idCotizacion);
		$("#card-tabla").find(".card-header").html(data.cotizacionDatos.nombre + ' - No. Cotización: ' + data.idCotizacion + ' - ' + data.cotizacionDatos.mapfre_numero);
		$("#m-cotizacion").find(".results").html(data.idCotizacion + ' - ' + data.cotizacionDatos.mapfre_numero);
		
		$("body").on("click", ".cabecera-diferidos", function(){
			$(".cabecera-diferidos").addClass("d-none");
			$(".pagos-diferidos").removeClass("d-none");
		});
		
		DATA = data;
		$.each(DATA.tablaDatos.sa_db.datos.aseguradoras, function(_z, _a){
			if(_a.id==2){
				_pctMapfre = 1 + (_a.inflar/100);
				return false;
			}
		});
		doMTabla(data);
		doTabla(data);
		$('[data-toggle="tooltip"]').tooltip()
		$(".btnPrint").attr("href", "https://segurodegastosmedicosmayores.mx/verCotizacionPDF/" + idCotizacion + "/" + secret + "/sa/da");
		//$(".btnEditar").removeClass("d-none");
		
		// Formulario de contacto
		$("#ccId").val(cotizacion.idCotizacion);
		$("#ccSecret").val(cotizacion.secret);
		var z = 1;
		var html;
		$.each(cotizacion.tablaDatos.sa_db.datos.aseguradoras, function(i, aseguradora){
			for(x=1;x<=aseguradora.paquetes;x++){
				html = '<div class="form-check pr-2">';
				html += '<input class="form-check-input" type="checkbox" name="planes[]" value="' + aseguradora.nombre + ' - ' + cotizacion.tablaDatos.sa_db.datos.tablas[z][2] + '" id="cc-' + z + '">';
				html += '<label class="form-check-label" for="cc-' + z + '">' + aseguradora.nombre + ' - ' + cotizacion.tablaDatos.sa_db.datos.tablas[z][2] + '</label>';
				html += '</div>';
				$("#ccPaquetes").append(html);
				z++;
			}
		});
	},
	error:function(jqXHR, status, error){
		
	}
});

// Metodos para edicion
var current = $("#card1");
$('[data-toggle="tooltip"]').tooltip();
function showValidate(input){
	var thisAlert=$(input).parent();
	$(thisAlert).addClass('alert-validate');
}
function hideValidate(input){
	var thisAlert=$(input).parent();
	$(thisAlert).removeClass('alert-validate');
}
$("body").on("blur", ".input2", function(){
	$(this).on('blur',function(){
		if($(this).val().trim()!=""){
			$(this).addClass('has-val');
		}
		else{
			$(this).removeClass('has-val');
		}
	});
});
$("body").on("focus", ".validate-form .input2", function(){
	$(this).focus(function(){
		hideValidate(this);
	});
});
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
$("#maternidad").change(function(){
	if($("#maternidad").prop("checked"))
		$("#opciones-maternidad").removeClass("d-none");
	else
		$("#opciones-maternidad").addClass("d-none");
});
$("#dental").change(function(){
	if($("#dental").prop("checked"))
		$("#opciones-dental").removeClass("d-none");
	else
		$("#opciones-dental").addClass("d-none");
});
$("#tabulador").change(function(){
	if($("#tabulador").prop("checked"))
		$("#opciones-tabulador").removeClass("d-none");
	else
		$("#opciones-tabulador").addClass("d-none");
});
$("#sa").change(function(){
	if($("#sa").prop("checked"))
		$("#opciones-sa").removeClass("d-none");
	else
		$("#opciones-sa").addClass("d-none");
});
/*function cardData(){
	var _r;
	// Card 1
	if($("#estado").val()==""){
		$("#estado option:contains(" + cotizacion.cotizacionDatos.estado.toUpperCase() + ")").attr("selected", true);
		if($("#estado").val()!=""){
			$("#estado").addClass("has-val").trigger("change");
		}
	}
	// Card 2
	$("#cotizar").val(cotizacion.cotizacionDatos.cotizar_para).addClass("has-val").trigger("change");
	// Card 3
	if(cotizacion.cotizacionDatos.poliza_actual!="no")
		$("#poliza").prop("checked", true);
	if(cotizacion.cotizacionDatos.maternidad==1){
		$("#maternidad").prop("checked", true).trigger("change");
		$("#sa-maternidad").val(cotizacion.cotizacionDatos.sa_maternidad);
	}
	if(cotizacion.cotizacionDatos.viajes==1)
		$("#viajes").prop("checked", true);
	if(cotizacion.cotizacionDatos.dental==1){
		$("#dental").prop("checked", true).trigger("change");
		$("#sa-dental").val(cotizacion.cotizacionDatos.sa_dental);
	}
	if(cotizacion.cotizacionDatos.otros_estados==1)
		$("#cambio-estado").prop("checked", true);
	if(cotizacion.cotizacionDatos.reduccion_deducible==1)
		$("#reduccion").prop("checked", true);
	if(cotizacion.cotizacionDatos.tabulador!=null){
		$("#tabulador").prop("checked", true).trigger("change");
		$("#sa-tabulador").val(cotizacion.cotizacionDatos.tabulador);
	}
	if(cotizacion.cotizacionDatos.suma_asegurada!=null){
		$("#sa").prop("checked", true).trigger("change");
		$("#sa-suma").val(cotizacion.cotizacionDatos.suma_asegurada);
		$("#sa-deducible").val(cotizacion.cotizacionDatos.deducible);
	}
	if(cotizacion.cotizacionDatos.nivel_amplio==1)
		$("#amplio").prop("checked", true);
	current = $("#card3");
	// Card 4
	if($("#nombre").val()==""){
		$("#nombre").val(cotizacion.cotizacionDatos.nombre).addClass("has-val").trigger("change");
		$("#email").val(cotizacion.cotizacionDatos.e_mail).addClass("has-val").trigger("change");
		$("#phone").val(cotizacion.cotizacionDatos.telefono);
		if($("#phone").val()!="")
			$("#phone").addClass("has-val").trigger("change");
	}
}*/
/*$("#estado").change(function(){
	$.ajax({
		url:'https://www.segurodegastosmedicosmayores.mx/poblaciones/' + $("#estado").val(),
		method:'GET',
		dataType:'json',
		success:function(data, status, jqXHR){
			$("#ciudad").empty().append('<option value=""></option>');
			$.each(data, function(i, poblacion){
				$("#ciudad").append('<option value="' + poblacion.id + '">' + poblacion.poblacion + '</option>');
			});
			if($("#ciudad").val()==""){
				$("#ciudad option:contains(" + cotizacion.cotizacionDatos.ciudad.toUpperCase() + ")").attr("selected", true);
				if($("#ciudad").val()!=""){
					$("#ciudad").addClass("has-val").trigger("change");
					hideValidate($("#ciudad"));
				}
			}
		},
		error:function(jqXHR, status, error){
			console.log("Error: " + error);
		}
	});
});*/
/*function card1Changes(){
	var check = true;
	if($("#estado").val()==""){
		showValidate($("#estado"));
		check = false;
	}
	if($("#ciudad").val()=="" || $("#ciudad").val()==null){
		showValidate($("#ciudad"));
		check = false;
	}
	if(check)
		$("#step1").find(".rounded-circle").removeClass("bg-gray").addClass("bg-primary");
	else
		$("#step1").find(".rounded-circle").removeClass("bg-primary").addClass("bg-gray");
	$("#step1").find(".results").html($("#ciudad option:selected").text() + ", " + $("#estado option:selected").text());
	return check;
}*/
$("#card1 .input2").change(function(){
	card1Changes();
});
$("#next1").click(function(e){
	e.preventDefault();
	var check = card1Changes();
	if(check){
		$("#step1").find(".rounded-circle").removeClass("bg-gray").addClass("bg-primary");
		$("#step1").find(".results").html($("#ciudad option:selected").text() + ", " + $("#estado option:selected").text());
		$("#card1").fadeOut(400, function(){
			$("#card2").fadeIn();
			current = $("#card2");
			
			$("#cotizar").val(cotizacion.cotizacionDatos.cotizar_para).addClass("has-val").trigger("change");
		});
	}
});
$("#cotizar").change(function(){
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
			
			if($("#sexo1").val()==""){
				if(cotizacion.cotizacionDatos.integrantes[0]){
					var i = cotizacion.cotizacionDatos.integrantes[0];
					$("#sexo1").val((i.sexo=="m" ? "H" : "M")).addClass("has-val");
					$("#nombre1").val(i.nombre).addClass("has-val");
					$("#edad1").val(i.edad).addClass("has-val");
				}
			}
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
			
			if($("#sexo2-1").val()==""){
				if(cotizacion.cotizacionDatos.integrantes[0]){
					var i = cotizacion.cotizacionDatos.integrantes[0];
					$("#sexo2-1").val((i.sexo=="m" ? "H" : "M")).addClass("has-val");
					$("#nombre2-1").val(i.nombre).addClass("has-val");
					$("#edad2-1").val(i.edad).addClass("has-val");
				}
			}
			if($("#sexo2-2").val()==""){
				if(cotizacion.cotizacionDatos.integrantes[1]){
					var i = cotizacion.cotizacionDatos.integrantes[1];
					$("#sexo2-2").val((i.sexo=="m" ? "H" : "M")).addClass("has-val");
					$("#nombre2-2").val(i.nombre).addClass("has-val");
					$("#edad2-2").val(i.edad).addClass("has-val");
				}
			}
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
			
			if($("#hijos1").val()==""){
				$("#hijos1").val(cotizacion.cotizacionDatos.no_hijos).addClass("has-val").trigger("change");
			}
			if($("#sexo3-1").val()==""){
				if(cotizacion.cotizacionDatos.integrantes[0]){
					var i = cotizacion.cotizacionDatos.integrantes[0];
					$("#sexo3-1").val((i.sexo=="m" ? "H" : "M")).addClass("has-val");
					$("#nombre3-1").val(i.nombre).addClass("has-val");
					$("#edad3-1").val(i.edad).addClass("has-val");
				}
			}
			if($("#sexo3-2").val()==""){
				if(cotizacion.cotizacionDatos.integrantes[1]){
					var i = cotizacion.cotizacionDatos.integrantes[1];
					$("#sexo3-2").val((i.sexo=="m" ? "H" : "M")).addClass("has-val");
					$("#nombre3-2").val(i.nombre).addClass("has-val");
					$("#edad3-2").val(i.edad).addClass("has-val");
				}
			}
			for(x=1;x<=cotizacion.cotizacionDatos.no_hijos;x++){
				var i = cotizacion.cotizacionDatos.integrantes[x+1];
				$("#sexoHijos-" + x).val((i.sexo=="m" ? "H" : "M")).addClass("has-val");
				$("#nombreHijos-" + x).val(i.nombre).addClass("has-val");
				$("#edadHijos-" + x).val(i.edad).addClass("has-val");
			}
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
			
			if($("#hijos2").val()==""){
				$("#hijos2").val(cotizacion.cotizacionDatos.no_hijos).addClass("has-val").trigger("change");
			}
			if($("#sexo4-2").val()==""){
				if(cotizacion.cotizacionDatos.integrantes[0]){
					var i = cotizacion.cotizacionDatos.integrantes[0];
					$("#sexo4-2").val((i.sexo=="m" ? "H" : "M")).addClass("has-val");
					$("#nombre4-2").val(i.nombre).addClass("has-val");
					$("#edad4-2").val(i.edad).addClass("has-val");
				}
			}
			for(x=1;x<=cotizacion.cotizacionDatos.no_hijos;x++){
				var i = cotizacion.cotizacionDatos.integrantes[x];
				$("#sexoHijos-" + x).val((i.sexo=="m" ? "H" : "M")).addClass("has-val");
				$("#nombreHijos-" + x).val(i.nombre).addClass("has-val");
				$("#edadHijos-" + x).val(i.edad).addClass("has-val");
			}
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
			
			if($("#hijos3").val()==""){
				$("#hijos3").val(cotizacion.cotizacionDatos.no_hijos).addClass("has-val").trigger("change");
			}
			if($("#sexo5-1").val()==""){
				if(cotizacion.cotizacionDatos.integrantes[0]){
					var i = cotizacion.cotizacionDatos.integrantes[0];
					$("#sexo5-1").val((i.sexo=="m" ? "H" : "M")).addClass("has-val");
					$("#nombre5-1").val(i.nombre).addClass("has-val");
					$("#edad5-1").val(i.edad).addClass("has-val");
				}
			}
			for(x=1;x<=cotizacion.cotizacionDatos.no_hijos;x++){
				var i = cotizacion.cotizacionDatos.integrantes[x];
				$("#sexoHijos-" + x).val((i.sexo=="m" ? "H" : "M")).addClass("has-val");
				$("#nombreHijos-" + x).val(i.nombre).addClass("has-val");
				$("#edadHijos-" + x).val(i.edad).addClass("has-val");
			}
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
			
			if($("#hijos4").val()==""){
				$("#hijos4").val(cotizacion.cotizacionDatos.no_hijos).addClass("has-val").trigger("change");
			}
			for(x=1;x<=cotizacion.cotizacionDatos.no_hijos;x++){
				var i = cotizacion.cotizacionDatos.integrantes[x-1];
				$("#sexoHijos-" + x).val((i.sexo=="m" ? "H" : "M")).addClass("has-val");
				$("#nombreHijos-" + x).val(i.nombre).addClass("has-val");
				$("#edadHijos-" + x).val(i.edad).addClass("has-val");
			}
			break;
	}
});
$("#hijos1").change(function(){
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
});
function card2Changes(){
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
}
$("#card2 .input2").change(function(){
	card2Changes();
});
$("body").on("change", ".card2", function(){
	card2Changes();
});
$("#next2").click(function(e){
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
			
			if(cotizacion.cotizacionDatos.poliza_actual!="no")
				$("#poliza").prop("checked", true);
			if(cotizacion.cotizacionDatos.maternidad==1)
				$("#maternidad").prop("checked", true).trigger("change");
			if(cotizacion.cotizacionDatos.viajes==1)
				$("#viajes").prop("checked", true);
			if(cotizacion.cotizacionDatos.dental==1)
				$("#dental").prop("checked", true).trigger("change");
			if(cotizacion.cotizacionDatos.otros_estados==1)
				$("#cambio-estado").prop("checked", true);
		});
	}
});
function showHijos(container, no){
	var field = '';
	for(var x=1;x<=no;x++){
		field = '<div class="row">';
		field += '<div class="col-md-2">';
		field += '			<div class="wrap-input2 validate-input" data-validate="Mi hijo(a) es requerido">';
		field += '				<select class="input2 card2 validar opcional hijos" name="sexoHijos-' + x + '" id="sexoHijos-' + x + '">';
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
		field += '				<input type="number" class="input2 card2 validar opcional hijos" name="edadHijos-' + x + '" id="edadHijos-' + x + '" step="1">';
		field += '				<span class="focus-input2" data-placeholder="Su edad"></span>';
		field += '			</div>';
		field += '		</div>';
		field += '	</div>';
		container.append(field);
	}
	total += parseInt(no);
	$("#total").val(total);
}
function card3Changes(){
	var r = '';
	if($("#poliza").prop("checked"))
		r += '<li>Tengo póliza actualmente</li>';
	if($("#maternidad").prop("checked"))
		r += '<li>Me interesa cobertura en Maternidad - ' + $("#sa-maternidad :selected").text() + '</li>';
	if($("#viajes").prop("checked"))
		r += '<li>A veces viajo al extranjero y necesito cobertura</li>';
	if($("#dental").prop("checked"))
		r += '<li>Deseo cobertura Dental ' + $("#sa-dental :selected").text() + '</li>';
	if($("#cambio-estado").prop("checked"))
		r += '<li>Quisiera cobertura en Estados de México más costosos</li>';
	if($("#reduccion").prop("checked"))
		r += '<li>Reducción de deducible por accidente</li>';
	if($("#tabulador").prop("checked"))
		r += '<li>Tabulador ' + $("#sa-tabulador :selected").text() + '</li>';
	if($("#sa").prop("checked")){
		r += '<li>Suma segurada ' + $("#sa-suma :selected").text() + '</li>';
		r += '<li>Deducible ' + $("#sa-deducible :selected").text() + '</li>';
	}
	if($("#amplio").prop("checked"))
		r += '<li>Quiero hospitales de categoría superior (nivel Amplio)</li>';
	$("#step3").find(".rounded-circle").removeClass("bg-gray").addClass("bg-primary");
	if(r!='')
		$("#step3").find(".results").html('<ul>' + r + '</ul>');
	$("#step3").find(".rounded-circle").removeClass("bg-gray").addClass("bg-primary");
}
$("#card3 .custom-control-input, #card3 .input2").change(function(){
	card3Changes();
});
$("#next3").click(function(e){
	e.preventDefault();
	var r = '';
	if($("#poliza").prop("checked"))
		r += '<li>Tengo una póliza actual</li>';
	if($("#maternidad").prop("checked"))
		r += '<li>Me interesa la maternidad</li>';
	if($("#viajes").prop("checked"))
		r += '<li>Viajaré al extranjero y necesito cobertura</li>';
	if($("#dental").prop("checked"))
		r += '<li>Me interesa tener cobertura dental básica</li>';
	if($("#cambio-estado").prop("checked"))
		r += '<li>Me interesa tener cobertura en Estados de Mexico más costosos</li>';
	$("#step3").find(".rounded-circle").removeClass("bg-gray").addClass("bg-primary");
	if(r!='')
		$("#step3").find(".results").html('<ul>' + r + '</ul>');
	$("#card3").fadeOut(400, function(){
		$("#card4").fadeIn();
		current = $("#card4");
		
		if($("#nombre").val()==""){
			$("#nombre").val(cotizacion.cotizacionDatos.nombre).addClass("has-val");
			$("#email").val(cotizacion.cotizacionDatos.e_mail).addClass("has-val");
			$("#phone").val(cotizacion.cotizacionDatos.telefono);
			if($("#phone").val()!="")
				$("#phone").addClass("has-val");
		}
	});
});
function card4Changes(){
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
}
$("#card4 .input2").change(function(){
	card4Changes();
});
$("#frmCotizador").submit(function(e){
	e.preventDefault();
	var valido = 1;
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
	if(valido==1){
		var _data = $(this).serialize();
		$.ajax({
			url:'/actualizaCotizacionWS2023/' + idCotizacion + '/' + secret,
			method:'POST',
			dataType:'json',
			data:_data,
			success:function(data, status, jqXHR){
				if(data.status=="success"){
					console.log(data);
					$("#card1, #card2, #card3, #card3").fadeOut();
					$(".btnEditar").addClass("d-none");
					$("#cotizacion-body").collapse("show");
					$("#cotizacion-body").empty();
					$([document.documentElement, document.body]).animate({
				        scrollTop: $("#cotizacion-body").offset().top
				    }, 2000);
				    doTabla(data);
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
});
function showInput(td){
	var _input = '';
	var _v, _checkbox = false;
	var _id = $(td).data("id-concepto");
	switch(_id){
		case 1:
			_v = parseInt($(td).html().replace('$', '').replaceAll(',', ''));
			_input = '<select class="form-control" name="sa-suma" id="sa-suma">' +
							'<option value="5000000" ' + ((_v==5000000) ? 'selected' : '') + '>$5,000,000</option>' +
							'<option value="10000000" ' + ((_v==10000000) ? 'selected' : '') + '>$10,000,000</option>' +
							'<option value="15000000" ' + ((_v==15000000) ? 'selected' : '') + '>$15,000,000</option>' +
							'<option value="20000000" ' + ((_v==20000000) ? 'selected' : '') + '>$20,000,000</option>' +
							'<option value="25000000" ' + ((_v==25000000) ? 'selected' : '') + '>$25,000,000</option>' +
							'<option value="40000000" ' + ((_v==40000000) ? 'selected' : '') + '>$40,000,000</option>' +
							'<option value="100000000" ' + ((_v==100000000) ? 'selected' : '') + '>$100,000,000</option>' +
							'<option value="130000000" ' + ((_v==130000000) ? 'selected' : '') + '>$130,000,000</option>' +
						'</select>';
			break;
		case 2:
			_v = parseInt($(td).html().replace('$', '').replaceAll(',', ''));
			_input = '<select class="form-control" name="sa-deducible" id="sa-deducible">' +
							'<option value="10000" ' + ((_v==10000) ? 'selected' : '') + '>$10,000</option>' +
							'<option value="11000" ' + ((_v==11000) ? 'selected' : '') + '>$11,000</option>' +
							'<option value="12000" ' + ((_v==12000) ? 'selected' : '') + '>$12,000</option>' +
							'<option value="13000" ' + ((_v==13000) ? 'selected' : '') + '>$13,000</option>' +
							'<option value="14000" ' + ((_v==14000) ? 'selected' : '') + '>$14,000</option>' +
							'<option value="15000" ' + ((_v==15000) ? 'selected' : '') + '>$15,000</option>' +
							'<option value="16000" ' + ((_v==16000) ? 'selected' : '') + '>$16,000</option>' +
							'<option value="17000" ' + ((_v==17000) ? 'selected' : '') + '>$17,000</option>' +
							'<option value="18000" ' + ((_v==18000) ? 'selected' : '') + '>$18,000</option>' +
							'<option value="19000" ' + ((_v==19000) ? 'selected' : '') + '>$19,000</option>' +
							'<option value="20000" ' + ((_v==20000) ? 'selected' : '') + '>$20,000</option>' +
							'<option value="25000" ' + ((_v==25000) ? 'selected' : '') + '>$25,000</option>' +
							'<option value="30000" ' + ((_v==30000) ? 'selected' : '') + '>$30,000</option>' +
							'<option value="40000" ' + ((_v==40000) ? 'selected' : '') + '>$40,000</option>' +
							'<option value="50000" ' + ((_v==50000) ? 'selected' : '') + '>$50,000</option>' +
						'</select>';
			break;
		case 7:
			_v = parseInt($(td).html().replace('$', '').replaceAll(',', ''));
			_input = '<select class="form-control" name="sa-maternidad" id="sa-maternidad">' +
							'<option value="0" ' + ((_v==0) ? 'selected' : '') + '>$0</option>' +
							'<option value="20000" ' + ((_v==20000) ? 'selected' : '') + '>$20,000</option>' +
							'<option value="25000" ' + ((_v==25000) ? 'selected' : '') + '>$25,000</option>' +
							'<option value="30000" ' + ((_v==30000) ? 'selected' : '') + '>$30,000</option>' +
							'<option value="35000" ' + ((_v==35000) ? 'selected' : '') + '>$35,000</option>' +
							'<option value="40000" ' + ((_v==40000) ? 'selected' : '') + '>$40,000</option>' +
							'<option value="45000" ' + ((_v==45000) ? 'selected' : '') + '>$45,000</option>' +
							'<option value="50000" ' + ((_v==50000) ? 'selected' : '') + '>$50,000</option>' +
						'</select>';
			break;
		case 9:
			_v = parseInt($(td).html().replace('$', '').replaceAll(',', ''));
			_input = '<div class="custom-control custom-switch custom-switch-md">' +
							'<input type="checkbox" class="custom-control-input" id="sa-extranjero" name="sa-extranjero" ' + ((_v==100000) ? 'checked' : '') + '>' +
							'<label class="custom-control-label" for="sa-extranjero">&nbsp;</label>' +
						'</div>';
			break;
		case 16:
			_v = $(td).html();
			_input = '<select class="form-control" name="sa-tabulador" id="sa-tabulador">' +
							'<option value="C" ' + ((_v=='Básico') ? 'selected' : '') + '>Básico</option>' +
							'<option value="D" ' + ((_v=='Normal') ? 'selected' : '') + '>Normal</option>' +
							'<option value="E" ' + ((_v=='Medio') ? 'selected' : '') + '>Medio</option>' +
							'<option value="F" ' + ((_v=='Alto') ? 'selected' : '') + '>Alto</option>' +
						'</select>';
			break;
		case 17:
			_v = $(td).html();
			_input = '<div class="custom-control custom-switch custom-switch-md">' +
							'<input type="checkbox" class="custom-control-input" id="sa-asistencia-viaje" name="sa-asistencia-viaje" ' + ((_v=='Sí') ? 'checked' : '') + ' disabled>' +
							'<label class="custom-control-label" for="sa-asistencia-viaje">&nbsp;</label>' +
						'</div>';
			break;
		case 18:
			_v = $(td).html();
			_input = '<div class="custom-control custom-switch custom-switch-md">' +
							'<input type="checkbox" class="custom-control-input" id="sa-reduccion" name="sa-reduccion" ' + ((_v=='Sí') ? 'checked' : '') + '>' +
							'<label class="custom-control-label" for="sa-reduccion">&nbsp;</label>' +
						'</div>';
			break;
		case 19:
			_v = $(td).html();
			_input = '<select class="form-control" name="sa-dental" id="sa-dental">' + 
							'<option value="">Ninguno</option>' + 
							'<option value="plata" ' + ((_v=='Plata') ? 'selected' : '') + '>Plata</option>' + 
							'<option value="oro" ' + ((_v=='Oro') ? 'selected' : '') + '>Oro</option>' + 
						'</select>';
			break;
		case 20:
			_v = $(td).html();
			_input = '<div class="custom-control custom-switch custom-switch-md">' +
							'<input type="checkbox" class="custom-control-input" id="sa-complicaciones" name="sa-complicaciones" ' + ((_v=='Sí') ? 'checked' : '') + '>' +
							'<label class="custom-control-label" for="sa-complicaciones">&nbsp;</label>' +
						'</div>';
			break;
		case 21:
			_v = $(td).html();
			_input = '<div class="custom-control custom-switch custom-switch-md">' +
							'<input type="checkbox" class="custom-control-input" id="sa-vanguardia" name="sa-vanguardia" ' + ((_v=='Sí') ? 'checked' : '') + '>' +
							'<label class="custom-control-label" for="sa-vanguardia">&nbsp;</label>' +
						'</div>';
			break;
		case 22:
			_v = $(td).html();
			_input = '<div class="custom-control custom-switch custom-switch-md">' +
							'<input type="checkbox" class="custom-control-input" id="sa-multiregion" name="sa-multiregion" ' + ((_v=='Sí') ? 'checked' : '') + '>' +
							'<label class="custom-control-label" for="sa-multiregion">&nbsp;</label>' +
						'</div>';
			break;
		case 23:
			_v = $(td).html();
			_input = '<div class="custom-control custom-switch custom-switch-md">' +
							'<input type="checkbox" class="custom-control-input" id="sa-preexistentes" name="sa-preexistentes" ' + ((_v=='Sí') ? 'checked' : '') + '>' +
							'<label class="custom-control-label" for="sa-preexistentes">&nbsp;</label>' +
						'</div>';
			break;
		case 24:
			_v = $(td).html();
			_input = '<div class="custom-control custom-switch custom-switch-md">' +
							'<input type="checkbox" class="custom-control-input" id="sa-catastroficas" name="sa-catastroficas" ' + ((_v=='Sí') ? 'checked' : '') + '>' +
							'<label class="custom-control-label" for="sa-catastroficas">&nbsp;</label>' +
						'</div>';
			break;
		case 25:
			_v = $(td).html();
			_input = '<div class="custom-control custom-switch custom-switch-md">' +
							'<input type="checkbox" class="custom-control-input" id="sa-funeraria" name="sa-funeraria" ' + ((_v=='Sí') ? 'checked' : '') + '>' +
							'<label class="custom-control-label" for="sa-funeraria">&nbsp;</label>' +
						'</div>';
			break;
	}
	if(_input!='')
		$(td).html('').append(_input);
}
$("body").on("click", ".cmdEditar", function(e){
	e.preventDefault();
	var _col = $(this).data("col");
	var _plan = $(this).data("plan");
	var rows = $('td[id^="concepto"][id$="-' + _col + '"]');
	valores = [];
	$.each(rows, function(i, row){
		valores.push({'idConcepto':$(row).data("id-concepto"), 'valor':$(row).html()});
		showInput(row);
	});
	$(".cmdEditar").addClass("d-none");
	$('.cmdCancelar[data-col="' + _col + '"], .cmdRecotizar[data-col="' + _col + '"]').removeClass("d-none");
});
$("body").on("click", ".cmdCancelar", function(e){
	e.preventDefault();
	var _col = $(this).data("col");
	$.each(valores, function(i, v){
		$("#concepto-" + v.idConcepto + "-" + _col).html(v.valor);
	});
	$(".cmdCancelar, .cmdRecotizar").addClass("d-none");
	$(".cmdEditar").removeClass("d-none");
});
$("body").on("click", ".cmdRecotizar", function(e){
	e.preventDefault();
	var h = $(this).data("hospitales");
	var pre = "precio-" + $(this).data("col");
	var data = 'tipo=sadb&hospitales=' + h + '&update=1'
	var col = $(this).data("col");
	var _v;
	
	data += '&sa=' + $('td[data-id-concepto="1"][data-hospitales="' + h + '"]').find('#sa-suma').val();
	data += '&deducible=' + $('td[data-id-concepto="2"][data-hospitales="' + h + '"]').find('#sa-deducible').val();
	data += '&tabulador=' + $('td[data-id-concepto="16"][data-hospitales="' + h + '"]').find('#sa-tabulador').val();
	if($('td[data-id-concepto="7"][data-hospitales="' + h + '"]').find('#sa-maternidad').length>0)
		data += '&sa_maternidad=' + $('td[data-id-concepto="7"][data-hospitales="' + h + '"]').find('#sa-maternidad').val();
	_v = 0;
	if($('td[data-id-concepto="9"][data-hospitales="' + h + '"]').find('#sa-extranjero').prop("checked"))
		_v = 1;
	data += '&emergencia_extranjero=' + _v;
	_v = 0;
	if($('td[data-id-concepto="18"][data-hospitales="' + h + '"]').find('#sa-reduccion').prop("checked"))
		_v = 1;
	data += '&reduccion_deducible=' + _v;
	data += '&dental=' + $('td[data-id-concepto="19"][data-hospitales="' + h + '"]').find('#sa-dental').val();
	_v = 0;
	if($('td[data-id-concepto="20"][data-hospitales="' + h + '"]').find('#sa-complicaciones').prop("checked"))
		_v = 1;
	data += '&complicaciones=' + _v;
	_v = 0;
	if($('td[data-id-concepto="21"][data-hospitales="' + h + '"]').find('#sa-vanguardia').prop("checked"))
		_v = 1;
	data += '&vanguardia=' + _v;
	_v = 0;
	if($('td[data-id-concepto="22"][data-hospitales="' + h + '"]').find('#sa-multiregion').prop("checked"))
		_v = 1;
	data += '&multiregion=' + _v;
	_v = 0;
	if($('td[data-id-concepto="23"][data-hospitales="' + h + '"]').find('#sa-preexistentes').prop("checked"))
		_v = 1;
	data += '&preexistentes=' + _v;
	_v = 0;
	if($('td[data-id-concepto="24"][data-hospitales="' + h + '"]').find('#sa-catastroficas').prop("checked"))
		_v = 1;
	data += '&catastroficas=' + _v;
	_v = 0;
	if($('td[data-id-concepto="25"][data-hospitales="' + h + '"]').find('#sa-funeraria').prop("checked"))
		_v = 1;
	data += '&funeraria=' + _v;
	
	$.ajax({
		url:'/recotizarWS2023/' + idCotizacion + '/' + secret,
		method:'POST',
		data:data,
		dataType:'json',
		success:function(data, status, jqXHR){
			console.log(data);
			if(data.refresh==1)
				window.location.reload();
			mapfreValores(data.conceptos, 'H', h);
			$("#precio-top-" + col).html('<h4 class="mb-0"><strike>$' + new Intl.NumberFormat('es-MX', {maximumFractionDigits:0}).format((parseFloat(data.contado.replace(",", "").replace("$", "")) * parseFloat(_pctMapfre))) + '</strike></h4><h3 class="text-azul mt-0 mb-0"><b>' + data.contado + '</b></h3>');
			$("#" + pre).html('<b>' + data.contado + '</b>');
			$("#" + pre + "-s1").html(data.semestral_1);
			$("#" + pre + "-s2").html(data.semestral_2);
			$("#" + pre + "-t1").html(data.trimestral_1);
			$("#" + pre + "-t2").html(data.trimestral_2);
			$("#" + pre + "-m1").html(data.mensual_1);
			$("#" + pre + "-m2").html(data.mensual_2);
			$(".cmdCancelar, .cmdRecotizar").addClass("d-none");
			$(".cmdEditar").removeClass("d-none");
		},
		error:function(jqXHR, status, error){
			
		}
	});
});
$("body").on("click", "a.nivel-amplio", function(e){
	e.preventDefault();
	var _pct = 1;
	$.each(DATA.tablaDatos.sa_db.datos.aseguradoras, function(_z, _a){
		if(_a.id==2){
			_pct = 1 + (_a.inflar/100);
			return false;
		}
	});
	var data = 'tipo=sadb&hospitales=amplia&nivel_amplio=1'
	$.ajax({
		url:'/recotizarWS2023/' + idCotizacion + '/' + secret,
		method:'POST',
		data:data,
		dataType:'json',
		success:function(data, status, jqXHR){
			console.log(data);
			if(data.refresh==1)
				window.location.reload();
			$.each($("#tblCotizacion tr"), function(i, tr){
				switch($(tr).data("tipo")){
					case "logos":
						var _v = parseInt($(tr).find('th[data-id="2"]').attr("colspan"));
						$(tr).find('th[data-id="2"]').attr("colspan", _v + 1);
						$(tr).find('th[data-id="2"]').find('span .nivel-amplio').addClass('d-none');
						break;
					case "contado-top":
						$(tr).append('<th class="text-center text-azul" id="precio-top-' + (paquetes + 1) + '" data-tipo="sadb" data-hospitales="amplia"><h4 class="mb-0"><strike>$' + new Intl.NumberFormat('es-MX', {maximumFractionDigits:0}).format((parseFloat(data.contado.replace(",", "").replace("$", "")) * parseFloat(_pct))) + '</strike></h4><h3 class="text-azul mt-0 mb-0"><b>' + data.contado + '</b></h3></th>');
						break;
					case "hospitales":
						$(tr).append('<th rol="col" class="text-center"><h4 class="text-azul"><b>Amplia</b></h4></th>');
						break;
					case "coberturas":
						var _id = parseInt($(tr).data("id-concepto"));
						var _v = '';
						$.each(data.conceptos, function(i, concepto){
							if(concepto.id==_id){
								_v = concepto.format;
								return false;
							}
						});
						$(tr).append('<td class="text-center" id="concepto-' + _id + '-' + (paquetes + 1) + '" data-id-concepto="' + _id + '" data-hospitales="amplia">' + _v + '</td>');
						break;
					case "contado":
						$(tr).append('<td class="text-center text-azul" id="precio-' + (paquetes + 1) + '"><b>' + data.contado + '</b></td>');
						break;
					case "cabecera-diferidos":
						var _v = parseInt($(tr).find('th').attr('colspan'));
						$(tr).find('th').attr('colspan', _v + 1);
						break;
					case "cabecera-pagos":
						$(tr).append('<td>&nbsp;</td>');
						break;
					case "pagos":
						var _importe = 0;
						var _v = $(tr).data("pago");
						switch(_v){
							case "s1":
								_importe = data.semestral_1;
								break;
							case "s2":
								_importe = data.semestral_2;
								break;
							case "t1":
								_importe = data.trimestral_1;
								break;
							case "t2":
								_importe = data.trimestral_2;
								break;
							case "m1":
								_importe = data.mensual_1;
								break;
							case "m2":
								_importe = data.mensual_2;
								break;
						}
						$(tr).append('<td class="text-center" id="precio-' + (paquetes + 1) + '-' + _v + '">' + _importe + '</td>');
						break;
					case "botones":
						var _c = '<button type="button" class="btn btn-outline btn-primary font-weight-bold custom-btn-style-1 cmdEditar" data-col="' + (paquetes + 1) + '" data-hospitales="amplia">Editar</button>';
						_c += '<button type="button" class="btn btn-outline btn-gray btn-sm font-weight-bold custom-btn-style-1 cmdCancelar d-none" data-col="' + (paquetes + 1) + '" data-hospitales="amplia">Cancelar</button>';
						_c += '<button type="button" class="btn btn-outline btn-primary btn-sm font-weight-bold custom-btn-style-1 cmdRecotizar d-none" data-col="' + (paquetes + 1) + '" data-hospitales="amplia">Recotizar</button>';
						$(tr).append('<td class="text-center">' + _c + '</td>');
						break;
					case "botones-me-interesa":
						$(tr).append('<td class="text-center"><a href="/me-interesa/' + idCotizacion + '/' + secret + '/amplia" class="btn btn-outline btn-primary font-weight-bold custom-btn-style-1 w-100 cmdMeInteresa" data-hospitales="amplia">Me Interesa</a></td>');
						break;
				}
			});
			$(".cmdCancelar, .cmdRecotizar").addClass("d-none");
			$(".cmdEditar").removeClass("d-none");
		},
		error:function(jqXHR, status, error){
			
		}
	});
});
$("#frmCotizacionContacto").submit(function(e){
	e.preventDefault();
	var data = $(this).serialize();
	$.ajax({
		url: '/cotizacion/cuestionario',
		data: data,
		method: 'POST',
		dataType: 'html',
		success: function(data, status, jqXhr){
			$("#modCuestionario").modal("show");
			$("#frmCotizacionContacto")[0].reset();
		},
		error: function(jqXhr, status, error){
			$("#modCuestionario").modal("show");
			$("#frmCotizacionContacto")[0].reset();
		}
	});
});
$("#modCuestionario").on("hidden.bs.modal", function(e){
	location.href = "#cotizacion";
});