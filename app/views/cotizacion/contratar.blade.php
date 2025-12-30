
@section('contenido')
	<div class="page_title">
		<div class="container">
			<div class="title"><h1>Contratar paquete</h1></div>
	        <!--<div class="pagenation">&nbsp;<a href="index.html">Home</a> <i>/</i> <a href="#">Features</a> <i>/</i> Pricing Tables</div>-->
		</div>
	</div>
	<div class="container">
		<div class="content_fullwidth">
			<h2>Formulario <strong>para contratar</strong></h2>
			<blockquote>Complete el formulario llenando todos los campos solocitados</blockquote>
		</div>
		<form id="contratarForm" name="contratarForm" method="post">
			<table width="100%">
				<tr>
					<td style="width: 33%;">
						<label for="nombre" class="blocklabel">Nombre completo</label>
						<input placeholder="Mi nombre" id="nombre" name="nombre" type="text" class="input_bg" style="width: 95%;" value="{{$cotizacionDatos->nombre}}">
					</td>
					<td style="width: 33%;">
						<label for="e_mail" class="blocklabel">Correo electrónico</label>
						<input placeholder="micorreo@dominio.com" id="e_mail" name="e_mail" type="email" class="input_bg" style="width: 95%;" value="{{$cotizacionDatos->e_mail}}">
					</td>
					<td style="width: 33%;"></td>
				</tr>
				<tr>
					<td style="width: 33%;">
						<label for="telefono" class="blocklabel">Teléfono</label>
						<input placeholder="Teléfono" id="telefono" name="telefono" type="text" class="input_bg" style="width: 95%;" value="{{$cotizacionDatos->telefono}}">
					</td>
					<td style="width: 33%;">
						<label for="Estado" class="blocklabel">Estado</label>
						<input placeholder="Jalisco" id="estado" name="estado" type="text" class="input_bg" style="width: 200px;"  value="{{$cotizacionDatos->estado}}" readonly="">
					</td>
					<td style="width: 33%;">
						<label for="ciudad" class="blocklabel">Ciudad</label>
						<input placeholder="Ciudad" id="ciudad" name="ciudad" type="text" class="input_bg" style="width: 95%;" value="{{$cotizacionDatos->ciudad}}">
					</td>
				</tr>
				<tr>
					<td colspan="3">
						<label for="nombre" class="blocklabel">Forma de contacto</label>
					</td>
				</tr>
				<tr>
					<td colspan="3">
						<label class="radio"><input type="radio" name="contacto" id="contactoUrgente" checked="" value="1"><i></i>Llámenme deseo contratar lo más pronto posible</label> <br>
						<label class="radio"><input type="radio" name="contacto" id="contactoPoliza" value="2"><i></i>Llámenme cuando mi póliza actual vence</label> <br>
					</td>
				</tr>
				<tr>
					<td style="width: 33%;">
						<label for="nombre" class="blocklabel">Fecha de vencimiento</label>
						<input type="date" min="{{date('Y-m-d')}}" class="datepicker input_bg" id="fechaPoliza" name="fechaPoliza" style="cursor: pointer; width: 95%;" value="{{date('Y-m-d')}}">
					</td>
					<td colspan="2" style="width: 33%;"></td>
				</tr>
				<tr>
					<td colspan="3">
						<label for="nombre" class="blocklabel">Comentario para el agente</label>
					</td>
				</tr>
				<tr>
					<td colspan="3">
						<textarea rows="4" name="comentarios" id="comentarios" class="textarea_bg" style="width: 100%;"></textarea>
					</td>
				</tr>
			</table>
			<center><input type="button" value="CONTRATAR" class="comment_submit cotizarSeguro" id="send"></center>
			<br>
			<div class="table-style">
				{{$tablaIntegrantes}}
			</div>
			<br>
			<input type="hidden" name="idCotizacion" value="{{$cotizacionDatos->id_cotizacion}}">
			<input type="hidden" name="secret" value="{{$cotizacionDatos->secret}}">
			<input type="hidden" name="paquete" value="{{$paquete}}">
			<input type="hidden" name="sa" value="{{$sa}}">
			<input type="hidden" name="ded" value="{{$ded}}">
		</form> 
		<div class="clearfix divider_line2"></div>
	</div>
	{{HTML::script('assets/js/universal/jquery.js')}}
    {{HTML::script('assets/js/jquery.validate.min.js')}}
	{{HTML::script('assets/js/helpers/contratar.js')}}
@stop