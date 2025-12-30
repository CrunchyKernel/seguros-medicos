			<div class="content_fullwidth">
				<h2>Cotiza ahora <strong>tu Seguro</strong></h2>
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
	                		<tr>
	                			<td class="alignCenter alignVerticalMiddle">1</td>
	                        	<td class="alignVerticalMiddle">
	                        		Titular
	                        	</td>
	                        	<td class="alignVerticalMiddle"> <input type="checkbox" id="integrantes_1" name="integrantes[]" value="1" checked disabled> </td>
	                        	<td class="alignVerticalMiddle"> <input class="input_bg nombres" placeholder="Nombre del integrante" type="text" data-id="1"  id="nombres_1" name="nombres[]" style="width: 98%;"> </td>
	                        	<td class="alignVerticalMiddle">
	                        		<select class="input_bg sexos" data-id="1" style="width: 98%;" id="sexos_1" name="sexos[]">
	                        			<option value="-1">Sexo</option>
	                        			<option value="m">Hombre</option>
	                        			<option value="f">Mujer</option>
	                        		</select>
	                        	</td>
	                        	<td class="alignVerticalMiddle"> <input class="input_bg alignCenter edades" placeholder="Edad" type="number" data-id="1" id="edades_1" name="edades[]" step="1" min="18" max="69" digits="true" data-msg-digits="Solo números" data-msg-min="Edad mínima 18 años" data-msg-max="Edad máxima 69 años"> </td>
	                    	</tr>
	                    	<tr>
	                			<td class="alignCenter alignVerticalMiddle">2</td>
	                        	<td class="alignVerticalMiddle">
	                        		Cónyuge
	                        	</td>
	                        	<td class="alignVerticalMiddle"> <input type="checkbox" id="integrantes_2" name="integrantes[]" value="2"> </td>
	                        	<td class="alignVerticalMiddle"> <input class="input_bg nombres" placeholder="Nombre del integrante" type="text" data-id="2"  id="nombres_2" name="nombres[]" style="width: 98%;"> </td>
	                        	<td class="alignVerticalMiddle">
	                        		<select class="input_bg sexos" data-id="2" style="width: 98%;" id="sexos_2" name="sexos[]">
	                        			<option value="-1">Sexo</option>
	                        			<option value="m">Hombre</option>
	                        			<option value="f">Mujer</option>
	                        		</select>
	                        	</td>
	                        	<td class="alignVerticalMiddle"> <input class="input_bg alignCenter edades" placeholder="Edad" type="number" data-id="2" id="edades_2" name="edades[]" step="1" min="18" max="69" digits="true" data-msg-digits="Solo números" data-msg-min="Edad mínima 18 años" data-msg-max="Edad máxima 69 años"> </td>
	                    	</tr>
	                    	<tr>
	                			<td class="alignCenter alignVerticalMiddle">3</td>
	                        	<td class="alignVerticalMiddle">
	                        		Hijo(a)
	                        	</td>
	                        	<td class="alignVerticalMiddle"> <input type="checkbox" id="integrantes_3" name="integrantes[]" value="3"> </td>
	                        	<td class="alignVerticalMiddle"> <input class="input_bg nombres" placeholder="Nombre del integrante" type="text" data-id="3"  id="nombres_3" name="nombres[]" style="width: 98%;"> </td>
	                        	<td class="alignVerticalMiddle">
	                        		<select class="input_bg sexos" data-id="3" style="width: 98%;" id="sexos_3" name="sexos[]">
	                        			<option value="-1">Sexo</option>
	                        			<option value="m">Hombre</option>
	                        			<option value="f">Mujer</option>
	                        		</select>
	                        	</td>
	                        	<td class="alignVerticalMiddle"> <input class="input_bg alignCenter edades" placeholder="Edad" type="number" data-id="3" id="edades_3" name="edades[]" step="1" min="0" max="69" digits="true" data-msg-digits="Solo números" data-msg-min="Edad mínima 0 años" data-msg-max="Edad máxima 69 años"> </td>
	                    	</tr>
	                    	<tr>
	                			<td class="alignCenter alignVerticalMiddle">4</td>
	                        	<td class="alignVerticalMiddle">
	                        		Hijo(a)
	                        	</td>
	                        	<td class="alignVerticalMiddle"> <input type="checkbox" id="integrantes_4" name="integrantes[]" value="4"> </td>
	                        	<td class="alignVerticalMiddle"> <input class="input_bg nombres" placeholder="Nombre del integrante" type="text" data-id="4"  id="nombres_4" name="nombres[]" style="width: 98%;"> </td>
	                        	<td class="alignVerticalMiddle">
	                        		<select class="input_bg sexos" data-id="4" style="width: 98%;" id="sexos_4" name="sexos[]">
	                        			<option value="-1">Sexo</option>
	                        			<option value="m">Hombre</option>
	                        			<option value="f">Mujer</option>
	                        		</select>
	                        	</td>
	                        	<td class="alignVerticalMiddle"> <input class="input_bg alignCenter edades" placeholder="Edad" type="number" data-id="4" id="edades_4" name="edades[]" step="1" min="0" max="69" digits="true" data-msg-digits="Solo números" data-msg-min="Edad mínima 0 años" data-msg-max="Edad máxima 69 años"> </td>
	                    	</tr>
	                    	<tr>
	                			<td class="alignCenter alignVerticalMiddle">5</td>
	                        	<td class="alignVerticalMiddle">
	                        		Hijo(a)
	                        	</td>
	                        	<td class="alignVerticalMiddle"> <input type="checkbox" id="integrantes_5" name="integrantes[]" value="5"> </td>
	                        	<td class="alignVerticalMiddle"> <input class="input_bg nombres" placeholder="Nombre del integrante" type="text" data-id="5"  id="nombres_5" name="nombres[]" style="width: 98%;"> </td>
	                        	<td class="alignVerticalMiddle">
	                        		<select class="input_bg sexos" data-id="5" style="width: 98%;" id="sexos_5" name="sexos[]">
	                        			<option value="-1">Sexo</option>
	                        			<option value="m">Hombre</option>
	                        			<option value="f">Mujer</option>
	                        		</select>
	                        	</td>
	                        	<td class="alignVerticalMiddle"> <input class="input_bg alignCenter edades" placeholder="Edad" type="number" data-id="5" id="edades_5" name="edades[]" step="1" min="0" max="69" digits="true" data-msg-digits="Solo números" data-msg-min="Edad mínima 0 años" data-msg-max="Edad máxima 69 años"> </td>
	                    	</tr>
	                    	<tr>
	                			<td class="alignCenter alignVerticalMiddle">6</td>
	                        	<td class="alignVerticalMiddle">
	                        		Hijo(a)
	                        	</td>
	                        	<td class="alignVerticalMiddle"> <input type="checkbox" id="integrantes_6" name="integrantes[]" value="6"> </td>
	                        	<td class="alignVerticalMiddle"> <input class="input_bg nombres" placeholder="Nombre del integrante" type="text" data-id="6"  id="nombres_6" name="nombres[]" style="width: 98%;"> </td>
	                        	<td class="alignVerticalMiddle">
	                        		<select class="input_bg sexos" data-id="6" style="width: 98%;" id="sexos_6" name="sexos[]">
	                        			<option value="-1">Sexo</option>
	                        			<option value="m">Hombre</option>
	                        			<option value="f">Mujer</option>
	                        		</select>
	                        	</td>
	                        	<td class="alignVerticalMiddle"> <input class="input_bg alignCenter edades" placeholder="Edad" type="number" data-id="6" id="edades_6" name="edades[]" step="1" min="0" max="69" digits="true" data-msg-digits="Solo números" data-msg-min="Edad mínima 0 años" data-msg-max="Edad máxima 69 años"> </td>
	                    	</tr>
	                    	<tr>
	                			<td class="alignCenter alignVerticalMiddle">7</td>
	                        	<td class="alignVerticalMiddle">
	                        		Hijo(a)
	                        	</td>
	                        	<td class="alignVerticalMiddle"> <input type="checkbox" id="integrantes_7" name="integrantes[]" value="7"> </td>
	                        	<td class="alignVerticalMiddle"> <input class="input_bg nombres" placeholder="Nombre del integrante" type="text" data-id="7"  id="nombres_7" name="nombres[]" style="width: 98%;"> </td>
	                        	<td class="alignVerticalMiddle">
	                        		<select class="input_bg sexos" data-id="7" style="width: 98%;" id="sexos_7" name="sexos[]">
	                        			<option value="-1">Sexo</option>
	                        			<option value="m">Hombre</option>
	                        			<option value="f">Mujer</option>
	                        		</select>
	                        	</td>
	                        	<td class="alignVerticalMiddle"> <input class="input_bg alignCenter edades" placeholder="Edad" type="number" data-id="7" id="edades_7" name="edades[]" step="1" min="0" max="69" digits="true" data-msg-digits="Solo números" data-msg-min="Edad mínima 0 años" data-msg-max="Edad máxima 69 años"> </td>
	                    	</tr>
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