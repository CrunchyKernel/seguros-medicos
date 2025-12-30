@section('contenido')
	<div clas="container">
		<div class="row">
			<div class="col-sm-6">
				<img class="img-responsive pull-left" src="{{asset('protectodiez/logos/gastosmedicosmayores180.jpg')}}" >
			</div>
			<div class="col-sm-6">
				<img class="img-responsive pull-right" src="{{asset('protectodiez/logos/PROTECTODIEZ-LOGO-500-253-.jpg')}}" > 	
			</div>
		</div>
	</div>
	<div class="container-fluid">
			{{$tablaClienteDatos}}
			{{$tablaIntegrantes}}
	</div>

	@foreach($textos_plan_activo as $texto_plan_activo)
		<div class="container-fluid">
			<div class="text-justify">
				{{$texto_plan_activo->descripcion_backend}}
			</div>
		</div>
	@endforeach

	<div class="container-fluid" style="page-break-before:always;">
		<h3>
			<p class="text-center">
				Tabla de Costos por Aseguradora y Planes
			</p>
		</h3>
		{{$tablaDatos}}
		<p class="text-left">
			<strong>Para más opciones de paquetes, precios y formas de pago da click <a href="http://www.segurodegastosmedicosmayores.mx/verCotizacion/{{$cotizacionDatos->id_cotizacion}}/{{$cotizacionDatos->secret}}"> aquí </a></strong>
		</p>
		{{$textoProtecto}}
	</div>
		{{HTML::style('backend/css/bootstrap.min.css')}}
		{{HTML::style('backend/js/bootstrap.min.js')}}
		<script>
			$.noConflict();
		</script>
@stop

