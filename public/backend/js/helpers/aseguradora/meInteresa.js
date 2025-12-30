$(document).ready(function(){
    
    //$('[name="editor1"], [name="editor2"], [name="editor3"],' +
    //    '[name="editor4"], [name="editor5"], [name="editor6"], [name="editor7"], [name="editor8"]').ckeditor({
    $('.ckeEditor').ckeditor({
        height: 500,
        linkShowAdvancedTab: false,
        //scayt_autoStartup: false,
        //enterMode: Number(2),
        enterMode: CKEDITOR.ENTER_P,
        skin:'office2013',
        extraPlugins: 'scayt,justify',
        allowedContent: true,
        pasteFromWordRemoveStyles : false,
        contentsCss : ['https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css'],
        extraAllowedContent : '*{*}'
        //extraPlugins: 'base64image,pastebase64'
    });

    //Complicandome la vida :
    $(".btn-success").click( function(){
        //Cuando se presiona un boton se consigue su "id"
        var textArea =  $(this).attr('id');
        //Que comparte el nombre con el editor que le corresponde + una G
        textArea = textArea.split("G")[0];
        //Le quitamos la G y ahora podemos traer el contenido del CKEDITOR
        var descripcion_backend = CKEDITOR.instances[textArea].getData(); 
         //Hacemos la petición para actualizar la nota del plan 
        var idPaquete = $(this).data("id");
        $.ajax({
            //Para la actualización requerimos el "id" del paquete q podemos obtener desde la variable textArea
            data : { descripcion : descripcion_backend, id : idPaquete},
            url  : "guardarMeInteresa" , 
            method : 'POST',
            beforeSend: function(){
                $(".btn-success").button('loading');
            },
            complete: function(){
                $(".btn-success").button('reset');
            },
            success: function(respuesta){
                if(respuesta == 1){
                    swal("Me interesa", "guardada correctamente", "success");
                }else{
                    swal("Error","No se pudo guardar la descripción", "warning");                        
                }
               // window.location.reload();
            }
            
        });
    });
});