
@section('contenido')
	<div class="page_title">
		<div class="container">
			<div class="title"><h1>{{((isset($metaTitulo)) ? $metaTitulo : '')}}</h1></div>
	        <!--<div class="pagenation">&nbsp;<a href="index.html">Home</a> <i>/</i> <a href="#">Features</a> <i>/</i> Pricing Tables</div>-->
		</div>
	</div>
	
	<div class="container">
		<div id="ppdiv_2147163" class="content_fullwidth embed-responsive embed-responsive-16by9"></div>
	</div>
	
	<div class="container">
		<div class="content_fullwidth">
			<p>&nbsp;</p>
			<h2>Cotiza ahora <strong>tu Seguro.</strong></h2>
			<!--blockquote>Complete el formulario llenando todos los campos para poder ver su cotización</blockquote-->
			<p>&nbsp;</p>
		</div>
		<form id="integrantesForm" name="integrantesForm" method="post" data-submitted="0">
			<table width="100%">
				<tr>
					<td style="width: 33%;">
						<label for="nombre" class="blocklabel">Nombre completo</label>
						<input placeholder="Mi nombre" id="nombre" name="nombre" type="text" class="input_bg" style="width: 95%;">
					</td>
					<td style="width: 33%;">
						<label for="e_mail" class="blocklabel">Correo electrónico</label>
						<input placeholder="micorreo@dominio.com" id="e_mail" name="e_mail" type="email" class="input_bg" style="width: 95%;">
					</td>
					<td style="width: 33%;">
						<label for="telefono" class="blocklabel">Teléfono</label>
						<input placeholder="Teléfono" id="telefono" name="telefono" type="text" class="input_bg" style="width: 95%;">
					</td>
				</tr>
				<tr>
					<td style="width: 33%;">
						<label for="Estado" class="blocklabel">Estado</label>
						<input placeholder="Jalisco" id="estado" name="estado" type="text" class="input_bg" style="width: 200px;" value="Jalisco" readonly="">
					</td>
					<td style="width: 33%;">
						<label for="ciudad" class="blocklabel">Ciudad</label>
						<input placeholder="Ciudad" id="ciudad" name="ciudad" type="text" class="input_bg" style="width: 95%;">
					</td>
					<td style="width: 33%; vertical-align: top;">
						<label for="poliza_actual" class="blocklabel">¿Cuenta con alguna póliza actual?</label>
						<select id="poliza_actual" name="poliza_actual" class="input_bg">
							<option value="no">No</option>
							<option value="si">Si</option>
						</select>
					</td>
				</tr>
			</table>
			<br>
			<div class="table-style">
                <table class="table-list">
                	<thead>
                		<tr>
                			<th style="width: 30px; text-align: center;"></th>
                			<th style="width: 30px; text-align: center;">Titulo</th>
	                        <th style="width: 30px; text-align: center;">Incluir</th>
	                        <th style="text-align: center;">Nombre del integrante</th>
	                        <th style="width: 120px; text-align: center;">Sexo</th>
	                        <th style="width: 100px; text-align: center;">Edad</th>
	                    </tr>
                	</thead>
                    <tbody>
                    	@for($i=1;$i<=7;$i++)
                    		<tr>
                    			<td class="alignCenter alignVerticalMiddle">{{$i}}</td>
	                        	<td class="alignVerticalMiddle">
	                        		@if($i == 1)
	                        			Titular
	                        		@elseif($i == 2)
	                        			Cónyugue
	                        		@else
	                        			Hijo(a)
	                        		@endif
	                        	</td>
	                        	<td class="alignVerticalMiddle"> <input type="checkbox" id="integrantes_{{$i}}" name="integrantes[]" value="{{$i}}" {{(($i == 1) ? 'checked="" disabled="" ' : '')}}> </td>
	                        	<td class="alignVerticalMiddle"> <input class="input_bg nombres" placeholder="Nombre del integrante" type="text" data-id="{{$i}}"  id="nombres_{{$i}}" name="nombres[]" style="width: 98%;"> </td>
	                        	<td class="alignVerticalMiddle">
	                        		<select class="input_bg sexos" data-id="{{$i}}" style="width: 98%;" id="sexos_{{$i}}" name="sexos[]">
	                        			<option value="-1">Sexo</option>
	                        			<option value="m">Hombre</option>
	                        			<option value="f">Mujer</option>
	                        		</select>
	                        	</td>
	                        	<td class="alignVerticalMiddle"> <input class="input_bg alignCenter edades" placeholder="Edad" type="number" data-id="{{$i}}" id="edades_{{$i}}" name="edades[]" step="1" min="{{(($i == 1 || $i == 2) ? 18 : 0 )}}" max="69" digits="true" data-msg-digits="Solo números" data-msg-min="Edad mínima {{(($i == 1 || $i == 2) ? 18 : 0 )}} años" data-msg-max="Edad máxima 69 años"> </td>
	                    	</tr>
                    	@endfor
                	</tbody>
                </table>
                <table width="100%">
                	<tr>
						<td>
							<label for="comentarios" class="blocklabel">Comentarios</label>
							<textarea placeholder="Comentarios" id="comentarios" name="comentarios" class="input_bg" cols="20" rows="10" style="width: 100%; height: 100px;"></textarea>
						</td>
					</tr>
                </table>
                <hr>
			</div>
			<br>
			<center><input type="submit" value="COTIZAR SEGURO" class="comment_submit cotizarSeguro" id="send" data-loading-text='Procesando...'></center>
		</form>
		<!--<h3 style="margin: 5px;">Revisa nuestro aviso de privacidad en <a href="http://www.segurosautos.com.mx/aviso-privacidad" target="_blank">aquí</a></h3>-->
		<div class="clearfix divider_line2" style="heigth: 0px; margin: 5px 0px 5px 0px;"></div>
		@if(isset($cotizadorPagina)  && count($cotizadorPagina) > 0)
			@foreach($cotizadorPagina AS $html)
				{{$html}}
			@endforeach
		@endif
	</div>
	{{HTML::script('assets/js/helpers/cotizador1.js?v20200518')}}
	<script type="text/javascript" src="https://s3.amazonaws.com/press-play-v2/2138168/2147163/outer.js"></script>
@stop