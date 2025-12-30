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
function recotizarWS(tipo, hospitales){
	$.ajax({
		url:'/test/recotizarWS/' + idCotizacion + '/' + secret,
		data:'tipo=' + tipo.replace(/_/g, "") + '&hospitales=' + hospitales,
		method:'POST',
		dataType:'json',
		global:false,
		success:function(data, status, jqXHR){
			if(data.status==200){
				$("#div-" + tipo + "-" + hospitales + "-contado").html("<b>$ " + data.contado + "</b>");
				$("#div-" + tipo + "-" + hospitales + "-semestral-1").html("$ " + data.semestral_1);
				$("#div-" + tipo + "-" + hospitales + "-semestral-2").html("$ " + data.semestral_2);
				$("#div-" + tipo + "-" + hospitales + "-trimestral-1").html("$ " + data.trimestral_1);
				$("#div-" + tipo + "-" + hospitales + "-trimestral-2").html("$ " + data.trimestral_2);
				$("#div-" + tipo + "-" + hospitales + "-mensual-1").html("$ " + data.mensual_1);
				$("#div-" + tipo + "-" + hospitales + "-mensual-2").html("$ " + data.mensual_2);
			}
		},
		error:function(jqXHR, status, error){
			console.log(error);
		}
	});
}
$(document).ready(function(){
	toRecotizar.forEach(function(e, i, array){
		recotizarWS(e["tipo"], e["hospitales"]);
	});
});