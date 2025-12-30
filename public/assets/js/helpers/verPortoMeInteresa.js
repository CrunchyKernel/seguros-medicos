$("#frmComentarios").submit(function(e){
	e.preventDefault();
	var data = $(this).serialize();
	$.ajax({
		url: '/me-interesa/contactanos',
		data: data,
		method: 'POST',
		dataType: 'html',
		success: function(data, status, jqXhr){
			$("#modContactanos").modal("show");
			$("#frmComentarios")[0].reset();
		},
		error: function(jqXhr, status, error){
			$("#modContactanos").modal("show");
			$("#frmComentarios")[0].reset();
		}
	});
});