<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <!-- If you delete this tag, the sky will fall on your head -->
        <meta name="viewport" content="width=device-width" />

        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>{{$nombre}} - Cotización - Seguro de Gastos Médicos Mayores</title>
    </head>
    <body>
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <img class="img-responsive pull-left" src="http://segurodegastosmedicosmayores.mx/protectodiez/logos/gastosmedicosmayores180.jpg">
            </div>
            <div class="col-sm-6">
                <img class="img-responsive pull-right" src="http://segurodegastosmedicosmayores.mx/protectodiez/logos/PROTECTODIEZ-LOGO-500-253-.jpg" >
            </div>
        </div>
    </div>
    <div class="container">
        <h3>Hola {{$nombre}}:</h3>
        <p class="lead"><small>{{sistemaFunciones::fechaLetras(date('Y-m-d'))}}</small></p>
        {{$encabezado}}
    </div>
    <div class="container">
        <div style="border: 1px solid rgb(204, 204, 204); padding: 5px 10px; text-align: center; background: rgb(238, 238, 238);">
            <a class="btn soc-btn fb" href="{{URL::to('verCotizacion/'.$id_cotizacion.'/'.$secret)}}" target="_blank">
                Cotizaci&oacute;n en l&iacute;nea
            </a>
        </div>
        <br>
        {{$cuerpo}}
    </div>
    <div class="container">
        {{$pie}}
        <p>
            © 2005 - {{date('Y')}} protectodiez.com. Todos los derechos reservados. |
            <a href="#"  target="_blank">Aviso de privacidad</a>
            {{HTML::style('backend/css/bootstrap.min.css')}}
            {{HTML::style('backend/js/bootstrap.min.js')}}
        </p>
    </div>
    </body>
</html>
