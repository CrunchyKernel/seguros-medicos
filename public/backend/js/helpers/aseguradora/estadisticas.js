$(document).ready(function(){
	$("#frmReporte").submit(function(e){
		e.preventDefault();
		
		$.ajax({
            data : $("#frmReporte").serialize(),
            url  : "estadisticas" , 
            method : 'POST',
            beforeSend: function(){
                $("#btnOk").button('loading');
                
            },
            complete: function(){
                $("#btnOk").button('reset');
            },
            success: function(data){
                console.log(data);
                $("#tblDia").find("tbody").empty();
                $.each(data.dia, function(i, row){
                	$("#tblDia").find("tbody").append('<tr><td>' + row.dia + '</td><td>' + row.total + '</td><td>' + row.cotizaciones + '</td><td>' + row.porcentaje + '</td></tr>');
                });
                $("#tblDispositivo").find("tbody").empty();
                $.each(data.dispositivo, function(i, row){
                	$("#tblDispositivo").find("tbody").append('<tr><td>' + row.dispositivo + '</td><td>' + row.total + '</td><td>' + row.cotizaciones + '</td><td>' + row.porcentaje + '</td></tr>');
                });
                $("#tblRutaPC").find("tbody").empty();
                $.each(data.rutaPC, function(i, row){
                	$("#tblRutaPC").find("tbody").append('<tr><td>' + row.alias + '</td><td>' + row.total + '</td><td>' + row.cotizaciones + '</td><td>' + row.porcentaje + '</td></tr>');
                });
                $("#tblRutaMovil").find("tbody").empty();
                $.each(data.rutaMovil, function(i, row){
                	$("#tblRutaMovil").find("tbody").append('<tr><td>' + row.alias + '</td><td>' + row.total + '</td><td>' + row.cotizaciones + '</td><td>' + row.porcentaje + '</td></tr>');
                });
                $(".resultados").removeClass("hidden");
            }
        });
	});
});