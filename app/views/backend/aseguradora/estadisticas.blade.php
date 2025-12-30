
@section('contenido')
	<form id="frmReporte" class="form-horizontal">
		<div class="row">
			<div class="col-md-3">
				<h4 class="md-title mb-5">DESDE</h4>
				<input type="date" id="desde" name="desde" style="width: 100%;">
			</div>
			<div class="col-md-3">
				<h4 class="md-title mb-5">HASTA</h4>
				<input type="date" id="hasta" name="hasta" style="width: 100%;">
			</div>
			<div class="col-md-2">
				<div style="margin-bottom: 45px;"></div>
				<button class="btn btn-primary btn-block" type="submit" id="btnOk">Aceptar</button>
			</div>
		</div>
	</form>
	<div class="mb20"></div>
	<div class="row mb-5 resultados hidden">
		<div class="col-md-6">
			<h4 class="text-center">
				POR DIA DE LA SEMANA
			</h4>
			<table class="table table-striped" id="tblDia">
				<thead>
					<th>DIA</th>
					<th>IMPRESIONES</th>
					<th>COTIZACIONES</th>
					<th>PORCENTAJE</th>
				</thead>
				<tbody></tbody>
			</table>
		</div>
		<div class="col-md-6">
			<h4 class="text-center">
				POR DISPOSITIVO
			</h4>
			<table class="table table-striped" id="tblDispositivo">
				<thead>
					<th>DISPOSITIVO</th>
					<th>IMPRESIONES</th>
					<th>COTIZACIONES</th>
					<th>PORCENTAJE</th>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>
	<div class="row mb-5 resultados hidden">
		<div class="col-md-6">
			<h4 class="text-center">
				POR RUTA EN PC
			</h4>
			<table class="table table-striped" id="tblRutaPC">
				<thead>
					<th>RUTA</th>
					<th>IMPRESIONES</th>
					<th>COTIZACIONES</th>
					<th>PORCENTAJE</th>
				</thead>
				<tbody></tbody>
			</table>
		</div>
		<div class="col-md-6">
			<h4 class="text-center">
				POR RUTA EN MOVIL
			</h4>
			<table class="table table-striped" id="tblRutaMovil">
				<thead>
					<th>RUTA</th>
					<th>IMPRESIONES</th>
					<th>COTIZACIONES</th>
					<th>PORCENTAJE</th>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>
@stop