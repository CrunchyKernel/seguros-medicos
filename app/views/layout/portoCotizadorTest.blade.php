<section id="cotizador" class="section-padding">
	<div class="container">
		<div class="row">
			<form action="" class="contact2-form validate-form" id="frmCotizador" novalidate>
			<input type="hidden" name="total" id="total" value="">
			<input type="hidden" name="link_cotizacion" id="link_cotizacion" value="">
			<input type="hidden" name="_estado" id="_estado" value="">
			<input type="hidden" name="_ciudad" id="_ciudad" value="">
			<input type="hidden" name="_url" id="_url" value="">
				<div class="col">
					<div class="form-group row">
						<h3 class="text-6">Mis datos personales</h3>
					</div>
					<div class="form-group row">
						<label for="nombre" class="col-md-2 col-form-label">Nombre</label>
						<div class="col-md-10">
							<input type="text" name="nombre" id="nombre" class="form-control" required>
						</div>
					</div>
					<div class="form-group row">
						<label for="estado" class="col-md-2 col-form-label">Estado</label>
						<div class="col-md-10">
							<select class="form-control" name="estado" id="estado" required>
								<option value=""></option>
								<option value="1">AGUASCALIENTES</option>
								<option value="2">BAJA CALIFORNIA</option>
								<option value="3">BAJA CALIFORNIA SUR</option>
								<option value="4">CAMPECHE</option>
								<option value="5">COAHUILA</option>
								<option value="6">COLIMA</option>
								<option value="7">CHIAPAS</option>
								<option value="8">CHIHUAHUA</option>
								<option value="9">CDMX</option>
								<option value="10">DURANGO</option>
								<option value="11">GUANAJUATO</option>
								<option value="12">GUERRERO</option>
								<option value="13">HIDALGO</option>
								<option value="14">JALISCO</option>
								<option value="15">MÉXICO</option>
								<option value="16">MICHOACÁN</option>
								<option value="17">MORELOS</option>
								<option value="18">NAYARIT</option>
								<option value="19">NUEVO LEÓN</option>
								<option value="20">OAXACA</option>
								<option value="21">PUEBLA</option>
								<option value="22">QUERÉTARO</option>
								<option value="23">QUINTANA ROO</option>
								<option value="24">SAN LUIS POTOSÍ</option>
								<option value="25">SINALOA</option>
								<option value="26">SONORA</option>
								<option value="27">TABASCO</option>
								<option value="28">TAMAULIPAS</option>
								<option value="29">TLAXCALA</option>
								<option value="30">VERACRUZ</option>
								<option value="31">YUCATÁN</option>
								<option value="32">ZACATECAS</option>
							</select>
						</div>
					</div>
					<div class="form-group row">
						<label for="ciudad" class="col-md-2 col-form-label">Ciudad</label>
						<div class="col-md-10">
							<select class="form-control" name="ciudad" id="ciudad" required>
							</select>
						</div>
					</div>
					<div class="form-group row d-none" id="d-cotizar">
						<label for="cotizar" class="col-md-2 col-form-label">Quiero un seguro para</label>
						<div class="col-md-10">
							<select class="form-control" name="cotizar" id="cotizar" required>
								<option value=""></option>
								<option value="1">1 persona</option>
								<option value="2">Mi pareja y yo</option>
								<option value="3">Mi pareja, yo y mis hijos</option>
								<option value="4">Mi pareja e hijos</option>
								<option value="5">Yo y mis hijos</option>
								<!--option value="6">Mis hijos</option-->
							</select>
						</div>
					</div>
					<div class="form-inline row mb-3 d-none" id="d-sexo">
						<label for="sexo" class="sexo pl-3">Soy&nbsp;</label>
						<select class="form-control sexo" name="sexo" id="sexo">
							<option value=""></option>
							<option value="H">Hombre</option>
							<option value="M">Mujer</option>
						</select>
						<label for="edad" class="sexo">&nbsp;y tengo&nbsp;</label>
						<input type="text" name="edad" id="edad" class="form-control cotizador-nuevo sexo" min="18" max="70" data-error="Aqui el mensaje">
						<label for="" class="sexo">&nbsp;años.</label>
						
						<label for="sexo-2" class="d-none sexo-conyuge" id="lblConyuge">&nbsp;Mi cónyuge es&nbsp;</label>
						<select class="form-control d-none sexo-conyuge" name="sexo-2" id="sexo-2">
							<option value=""></option>
							<option value="H">Hombre</option>
							<option value="M">Mujer</option>
						</select>
						<label for="edad-2" class="d-none sexo-conyuge">&nbsp;y tiene&nbsp;</label>
						<input type="text" name="edad-2" id="edad-2" class="form-control cotizador-nuevo d-none sexo-conyuge" max="70">
						<label for="edad-2" class="d-none sexo-conyuge">&nbsp;años.</label>
					</div>
					<div class="form-inline row mb-3 d-none hijos">
						<label for="hijos" class="d-none hijos pl-3">Tengo&nbsp;</label>
						<input type="text" name="hijos" id="hijos" class="form-control cotizador-nuevo d-none hijos">
						<label for="" class="d-none hijos">&nbsp;hijo(s), </label>
						
						<div id="hijos-container" class="form-inline"></div>
						<!--label for="edad-1-1" class="d-none edades-hijos">&nbsp;, que tienen&nbsp;</label>
						<input type="number" name="edad-1-1" id="edad-1-1" class="form-control d-none edades-hijos" step="1">
						<label for="" class="d-none edades-hijos">&nbsp;y&nbsp;</label>
						<input type="number" name="edad-1-2" id="edad-1-2" class="form-control d-none edades-hijos" step="1">
						<label for="sexo-1-1" class="d-none edades-hijos">&nbsp;años y son&nbsp;</label>
						<select class="form-control d-none edades-hijos" name="sexo-1-1" id="sexo-1-1">
							<option value=""></option>
							<option value="H">Hombre</option>
							<option value="M">Mujer</option>
						</select>
						<label for="" class="d-none edades-hijos">&nbsp;y&nbsp;</label>
						<select class="form-control d-none edades-hijos" name="sexo-1-2" id="sexo-1-2">
							<option value=""></option>
							<option value="H">Hombre</option>
							<option value="M">Mujer</option>
						</select>
						<label for="" class="d-none edades-hijos">&nbsp;respectivamente</label!-->
					</div>
					<div class="form-group row d-none d-opciones">
						<h3 class="text-6">Sobre mi seguro</h3>
					</div>
					<div class="form-group row d-none d-opciones">
						<div class="col-md-10 offset-2">
							<div class="form-check">
								<input class="form-check-input" type="checkbox" id="poliza" name="poliza">
								<label class="form-check-label" for="poliza">Tengo póliza actualmente</label>
							</div>
						</div>
					</div>
					<div class="form-group row d-none d-opciones">
						<div class="col-md-10 offset-2">
							<div class="form-check">
								<input class="form-check-input" type="checkbox" id="maternidad" name="maternidad">
								<label class="form-check-label" for="maternidad">Me interesa cobertura en Maternidad</label>
							</div>
						</div>
					</div>
					<div class="form-group row d-none d-opciones">
						<div class="col-md-10 offset-2">
							<div class="form-check">
								<input class="form-check-input" type="checkbox" id="emergencia_extranjero" name="emergencia_extranjero" checked>
								<label class="form-check-label" for="emergencia_extranjero">A veces viajo al extranjero y necesito cobertura</label>
							</div>
						</div>
					</div>
					<div class="form-group row d-none d-opciones">
						<div class="col-md-10 offset-2">
							<div class="form-check">
								<input class="form-check-input" type="checkbox" id="dental" name="dental" checked>
								<label class="form-check-label" for="dental">Deseo cobertura Dental Básica</label>
							</div>
						</div>
					</div>
					<div class="form-group row d-none d-opciones">
						<div class="col-md-10 offset-2">
							<div class="form-check">
								<input class="form-check-input" type="checkbox" id="multiregion" name="multiregion">
								<label class="form-check-label" for="multiregion">Quisiera cobertura en Estados de México más costosos (<a href="#" data-toggle="modal" data-target="#estados-modal">más información</a>)</label>
							</div>
						</div>
					</div>
					<div class="form-group row d-none d-contacto">
						<h3 class="text-6">Por último, mis datos de contacto</h3>
					</div>
					<div class="form-group row d-none d-contacto">
						<label for="email" class="col-md-2 col-form-label">Email</label>
						<div class="col-md-10">
							<input type="email" name="email" id="email" class="form-control" required>
						</div>
					</div>
					<div class="form-group row d-none d-contacto">
						<label for="telefono" class="col-md-2 col-form-label">Teléfono</label>
						<div class="col-md-10">
							<input type="text" name="telefono" id="telefono" class="form-control" pattern="^[0-9]{2}-[0-9]{4}-[0-9]{4}$" minlength="12" maxlength="12" title="XX-XXXX-XXXX" required>
						</div>
					</div>
					<div class="form-group row d-none d-contacto">
						<label for="observaciones" class="col-md-2 col-form-label">Observaciones</label>
						<div class="col-md-10">
							<textarea name="observaciones" id="observaciones" class="form-control"></textarea>
						</div>
					</div>
					<div class="form-group row d-none d-contacto">
						<div class="col-md-10 offset-md-2">
							<label class="font-weight-bold">Te contactaremos solamente en caso de ser necesario.</label>
						</div>
					</div>
					<div class="form-group row d-none d-contacto">
						<div class="col-md-10 offset-md-2">
							<div class="form-check">
								<input class="form-check-input" type="checkbox" id="privacidad" name="privacidad" title="Es necesario Aceptar términos, condiciones y aviso de privacidad" required>
								<label class="form-check-label" for="privacidad">Acepto los términos, condiciones y aviso de privacidad</label>
							</div>
						</div>
					</div>
					<div class="form-group row d-none d-contacto">
						<div class="col text-center">
							<button type="submit" class="btn btn-outline btn-primary font-weight-bold custom-btn-style-1 text-2" id="next4">Muestrame la cotización</button>
						</div>
					</div>
					<div class="modal fade" id="estados-modal" tabindex="-1" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title">Estados</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							        	<span aria-hidden="true">&times;</span>
							        </button>
								</div>
								<div class="modal-body">
									<ul>
										<li><b>Grupo 1</b>: CDMX y zona conurbada</li>
										<li><b>Grupo 2</b>: Nuevo León y Jalisco</li>
										<li><b>Grupo 3</b>: Estado de México (excepto zona conurbada)</li>
										<li><b>Grupo 4</b>: Hidalgo, Querétaro, Morelos y Guerrero</li>
										<li><b>Grupo 5</b>: Coahuia, Durango, Guanajuato, Michoacán, Nayarit y Tamaulipas</li>
										<li><b>Grupo 6</b>: Aguscalientes, Baja California Norte, Baja California Sur, Colima, Puebla, San Luis Potosí y Sonora</li>
										<li><b>Grupo 7</b>: Campeche, Chiapas, Chihuahua, Oaxaca, Quintana Roo, Sinaloa, Tabasco, Tlaxcala, Veracruz, Yucatán y Zacatecas</li>
									</ul>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</section>