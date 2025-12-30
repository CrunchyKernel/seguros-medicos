$("#btnSitemap").on("click", function(e){
	e.preventDefault();
	$.ajax({
		url:'/admingm/publicacion/doSitemap',
		method:'POST',
		dataType:'json',
		success: function(data, status, jqXHR){
			Adminsis.notificacion("Sitemap", "Se genero correctamente el archivo sitemap.xml", "stack_bottom_left", "success");
		},
		error: function(jqXHR, status, error){
			console.log(error);
		}
	});
});