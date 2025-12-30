jQuery(document).ready(function() {
    
    $("#agregaDominio").click(function(){
        var datosDominio = $("#datosDominio").serialize();
        $.post('dominios', datosDominio, function (data) {
            alert(data);
            window.location.reload();
        });
    });

    $(".administra").click(function(){//cualquier elemento con la clase administra
        var  url = $(this).attr("name");

        $.ajax({
            data: { 'dominio': url },
            url: "dominios/cambiaDB",
            type: 'POST',
            async: true
        }).success( function( data ){
            data = JSON.parse( data );
            sweetAlert(data);
        });
    });//fin para la acción del boton administrar <a>

    $("#infoModalDominio").click(function(){
        var dominio = $(this).attr("name");

        $.ajax({
            url: "dominios/" + dominio,
        }).success(function(data){
            $("#dominioF").val(data.dominio);
            $("#hostF").val(data.host);
            $("#descripcionF").val(data.descripcion);
            $("#passwordF").val(data.password);
            $("#usernameF").val(data.username);
            $("#databaseF").val(data.database);

        });
    });//fin de info modal

    $("#modificaDominio").click(function(){
        var datosDominio = $("#infoDominio").serialize();

        $.ajax({
            method: 'PUT',
            data: datosDominio,
            url: 'dominios/1'
        }). success(function(data){
            if(data == "Se ha modificado correctamente"){
                sweetAlert({
                 title: "Ejecutado",
                 text : data,
                 type : "success"
                });
                window.location.reload();
            }else{
                sweetAlert({
                    title: "Ejecutado",
                     text : data,
                     type : "error"
                });
            }
        });
    });//fin de moifica dominio
});

/*Método para accesar a un dominio o sitio diferente al que se está utilizando
    queda obsoleto por el momento, podría ser de utilidad después, ya que se hace uso de CORS
 $("a").click(function(){//cualquier elemento del tipo <a>
 var  url = $(this).attr("name");//en name tiene como valor la url para el logeo
 //del usuario actualmente logueado en el admingm
 var credenciales = { "password": $("#passwordS").attr("value"), "username": $("#usuario").attr("value") };
 // Es necesario establecer encabezados del lado del servidor, ya que se está usando "CORS"
 // (Cross origin Request Sharing)
 $.ajax({
 data: credenciales,
 url: url + "/admingm/login/iniciarSesion",
 type: 'POST',
 async: true,
 crossDomain:true,
 xhrFields:{
 withCredentials: true
 }
 }).success( function( data ){
 data = JSON.parse( data );

 if( data.status == "success" ){
 window.open( url + "/admingm/publicacion/consultaPublicaciones", '_blank' );
 }
 });
 });
*/