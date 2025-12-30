$(document).ready(function(){
	
	$("#btnOk").click(function(e){
		e.preventDefault();
		
		$.ajax({
            data : $("#frmBase").serialize(),
            url  : "baseMapfre" , 
            method : 'POST',
            beforeSend: function(){
                $("#btnOk").button('loading');
                
            },
            complete: function(){
                $("#btnOk").button('reset');
            },
            success: function(respuesta){
                if(respuesta == 1){
                    swal("Configuración Mapfre", "guardada correctamente", "success");
                }else{
                    swal("Error","No se pudo guardar la configuración", "warning");                       
                }
            }
        });
	});
	
});