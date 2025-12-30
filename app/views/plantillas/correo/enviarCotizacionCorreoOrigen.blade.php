<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <!-- If you delete this tag, the sky will fall on your head -->
        <meta name="viewport" content="width=device-width" />

        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>{{ucwords(strtolower($cotizacionDatos->nombre))}} - Cotización - Seguro de Gastos Médicos Mayores</title>
        <style type="text/css">
        	body{
        		font-family: 'calibri' !important;
    			font-size: 16px !important;
        	}
        </style>
    </head>
    <body>
    <table width="100%">
		<tr>
			<td width="25%"><img src="{{asset('protectodiez/logos/logo-saludcompara-180.jpg')}}" width="250px"></td>
			<td width="50%"></td>
			<td width="25%"><img src="{{asset('protectodiez/logos/PROTECTODIEZ-LOGO-500-253-.jpg')}}" width="250px"></td>
		</tr>
	</table>
	<br>
	<table width="100%">
		<tr>
			<td width="50%"></td>
			<td width="50%" style="text-align: right;">Guadalajara, Jalisco, {{sistemaFunciones::fechaLetras(date('Y-m-d'))}}</td>
		</tr>
	</table>
    <div class="container">
        <h3>Hola {{ucwords(strtolower($cotizacionDatos->nombre))}}:</h3>
        @if(strlen($mensaje) > 0)
            <p>{{$mensaje}}</p>
        @endif
        {{$encabezado}}
    </div>
    <div class="container">
        <div style="border: 1px solid rgb(204, 204, 204); padding: 5px 10px; text-align: center; background: rgb(238, 238, 238);">
            <a class="btn soc-btn fb" href="{{URL::to('verCotizacion/'.$id_cotizacion.'/'.$secret)}}" target="_blank">
                Cotizaci&oacute;n en l&iacute;nea
            </a>
        </div>
        {{$cuerpo}}
        {{$signature}}
    </div>
    <div class="container">
        {{$pie}}
        <p>
            © 2005 - {{date('Y')}} protectodiez.com. Todos los derechos reservados. |
            <a href="#"  target="_blank">Aviso de privacidad</a>
        </p>
    </div>
    </body>
</html>
