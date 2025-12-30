<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <!--Seguro de Gastos Mesdicos Mayores-->
    <title>{{ucwords(strtolower($cotizacionDatos->nombre))}} - Cotizaci&oacute;n - {{$cotizacionDatos->dominio()->first()->nombre}}</title>
    
    <style type="text/css">
    	body{
    		font-family: 'calibri';
    		font-size: 12px !important;
    	}
    	ul li{
    		text-align: justify;
    	}
    	p{
    		text-align: justify;
    	}
    	.text-justify{
    		text-align: justify;
    	}
    	.alignCenter {
    		text-align: center;
    	}
    	table {page-break-inside:auto }
		tr    { page-break-inside:avoid; page-break-after:auto }
		thead { display:table-header-group }

    </style>
</head>
<body>
	<table width="100%">
		<tr>
			<!--//asset('protectodiez/logos/gastosmedicosmayores180.jpg')-->
			<td width="25%"><img src="{{asset($cotizacionDatos->dominio()->first()->logo)}}" width="250px"></td>
			<td width="50%"></td>
			<td width="25%"><!--img src="asset('protectodiez/logos/PROTECTODIEZ-LOGO-500-253-.jpg')" width="250px"--></td>
		</tr>
	</table>
	<br>
	<table width="100%">
		<tr>
			<td width="50%"></td>
			<td width="50%" style="text-align: right;">{{sistemaFunciones::fechaLetras(date('Y-m-d'))}}</td>
		</tr>
	</table>
    <div class="container">
        <h3>Hola {{ucwords(strtolower(utf8_decode($cotizacionDatos->nombre)))}}</h3>
    </div>
	{{$bienvenida}}
	{{($cotizacion::tablaIntegrantes(false, true))}}
    
    @if(count($paquetes)>0)
    	<?php $a = -1;?>
    	@foreach($aAseguradoras as $aseguradora)
    		@if($a!=$aseguradora["id"])
    			<bookmark content="{{$aseguradora->nombre}}" level="0" />
    			@if(file_exists('images_post/images/'.$aseguradora["id"].'.jpg'))
					<br><br><br><br>
					<table width="100%">
						<tr>
							<td><center>{{HTML::image('images_post/images/'.$aseguradora["id"].'.jpg', '', array('width' => '200px'))}}</center></td>
						</tr>
					</table>
				@endif
				<?php $a = $aseguradora["id"];?>
    		@endif
    		@foreach($aseguradora["paquetes"] as $paquete)
    			<bookmark content="{{$paquete['paquete']}}" level="1" />
    			{{utf8_decode($paquete["descripcion_backend"])}}
    		@endforeach
    	@endforeach
    @else
	    @foreach($aseguradoras AS $aseguradora)
			@if($aseguradora->Paquetes()->where('activo', '=', 1)->get()->count() > 0)
				<bookmark content="{{$aseguradora->nombre}}" level="0" />
				@if(file_exists('images_post/images/'.$aseguradora->id_aseguradora.'.jpg'))
					<br><br><br><br>
					<table width="100%">
						<tr>
							<td><center>{{HTML::image('images_post/images/'.$aseguradora->id_aseguradora.'.jpg', '', array('width' => '200px'))}}</center></td>
						</tr>
					</table>
				@endif
				@foreach($aseguradora->Paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete)
					<bookmark content="{{$paquete->paquete}}" level="1" />
					{{utf8_decode($paquete->descripcion_backend)}}
				@endforeach
			@endif
		@endforeach
	@endif
	@if(\Auth::check() && file_exists('backend/images/signature'.\Auth::user()->id.'jpg'))
		{{HTML::image('backend/images/signature'.\Auth::user()->id.'jpg')}}
	@endif
	<pagebreak orientation="L" margin-top="30" />
	<table width="100%">
		<tr>
			<td><center><h1>Tabla de Costos por Aseguradora y Planes</h1></center></td>
		</tr>
	</table>
	@if(count($paquetes)>0)
		{{($cotizacion::tablaDatosWS2023Paquetes(false, false, true, $paquetes))}}
	@else
		{{($cotizacion::tablaDatosWS2023(false, false, true))}}
	@endif
    <!--//URL::to('verCotizacion/'.$cotizacionDatos->id_cotizacion.'/'.$cotizacionDatos->secret)-->
    <p>Para m&aacute;s opciones de paquetes, precios y formas de pago da click <a href="{{$cotizacionDatos->dominio()->first()->dominio . $cotizacionDatos->dominio()->first()->ver_cotizacion_nuevo}}/{{$cotizacionDatos->id_cotizacion}}/{{$cotizacionDatos->secret}}" target="_blank">aqu&iacute;</a></p>
    <p>Los costos aqu&iacute; mostrados son c&aacute;lculos que pueden tener variaciones de acuerdo a la aseguradora, cambios repentinos, errores, o la salud del asegurado. No constituyen un compromiso para la aseguradora. Consulte con su Asesor de Seguros. Aplican restricciones.</p>
    <pagebreak orientation="P" />
    {{$beneficios}}
</body>
</html>
