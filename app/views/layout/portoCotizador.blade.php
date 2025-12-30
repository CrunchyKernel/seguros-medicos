<section id="cotizador" class="section-padding">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<h2 class="custom-primary-font appear-animation" data-appear-animation="fadeInUpShorter" data-appear-animation-delay="200">Cotiza ahora tu seguro</h2>
			</div>
		</div>
		<form id="frmCotizacion" data-submitted="0" novalidate="">
			<input type="hidden" name="id_origen" value="1">
			<input type="hidden" id="total" name="total" value="1">
			<input type="hidden" id="link_cotizacion" name="link_cotizacion" value="04f73171-d545-40b2-8dc6-05cc410739be">
			<div class="row">
				<div class="col-md-12">
					<div class="form-row">
						<div class="more-features-box-text-icon cotizador-pasos mb-3 mt-3">1</div>
					</div>
					<div class="form-row">
						<div class="col-md-4">
							<label for="nombre">Nombre completo</label>
							<input type="text" id="nombre" name="nombre" class="form-control" value="" required="">
							<div class="invalid-feedback">
								Favor de proporcionar Nombre completo
							</div>
						</div>
						<div class="col-md-4">
							<label for="e_mail">Correo electrónico</label>
							<input type="email" id="email" name="email" class="form-control" value="" required="">
							<div class="invalid-feedback">
								Favor de proporcionar Correo electrónico
							</div>
						</div>
						<div class="col-md-4">
							<label for="telefono">Teléfono</label>
							<input type="text" id="telefono" name="telefono" class="form-control" value="" required="">
							<div class="invalid-feedback">
								Favor de proporcionar Teléfono
							</div>
						</div>
					</div>
					<div class="form-row">
						<div class="col-md-4">
							<label for="estado">Estado</label>
							<select id="estado" name="estado" class="form-control" required="">
								<option value=""></option>
								<option value="Aguascalientes">Aguascalientes</option>
								<option value="Baja California">Baja California</option>
								<option value="Baja California Sur">Baja California Sur</option>
								<option value="Campeche">Campeche</option>
								<option value="Coahuila">Coahuila</option>
								<option value="Colima">Colima</option>
								<option value="Chiapas">Chiapas</option>
								<option value="Chihuahua">Chihuahua</option>
								<option value="CDMX">CDMX</option>
								<option value="Durango">Durango</option>
								<option value="Guanajuato">Guanajuato</option>
								<option value="Guerrero">Guerrero</option>
								<option value="Hidalgo">Hidalgo</option>
								<option value="Jalisco" selected="selected">Jalisco</option>
								<option value="México">México</option>
								<option value="Michoacán">Michoacán</option>
								<option value="Morelos">Morelos</option>
								<option value="Nayarit">Nayarit</option>
								<option value="Nuevo León">Nuevo León</option>
								<option value="Oaxaca">Oaxaca</option>
								<option value="Puebla">Puebla</option>
								<option value="Querétaro">Querétaro</option>
								<option value="Quintana Roo">Quintana Roo</option>
								<option value="San Luis Potosí">San Luis Potosí</option>
								<option value="Sinaloa">Sinaloa</option>
								<option value="Sonora">Sonora</option>
								<option value="Tabasco">Tabasco</option>
								<option value="Tamaulipas">Tamaulipas</option>
								<option value="Tlaxcala">Tlaxcala</option>
								<option value="Veracruz">Veracruz</option>
								<option value="Yucatán">Yucatán</option>
								<option value="Zacatecas">Zacatecas</option>
							</select>
						</div>
						<div class="col-md-4">
							<label for="ciudad">Ciudad</label>
							<input type="text" id="ciudad" name="ciudad" class="form-control" value="" required="">
							<div class="invalid-feedback">
								Favor de proporcionar Ciudad
							</div>
						</div>
						<div class="col-md-4">
							<label for="telefono">¿Cuenta con alguna póliza actual?</label>
							<select id="poliza_actual" name="poliza_actual" class="form-control" required="">
								<option value="no">No</option>
								<option value="si">Si</option>
							</select>
						</div>
					</div>
				</div>
				<!--div class="col-md-3">
					<div class="form-row">
						<div class="more-features-box-text-icon cotizador-pasos mb-3 mt-3">2</div>
					</div>
					<div class="form-row">
						<label for="miembros">Integrantes</label>
						<select id="miembros" class="form-control">
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
							<option value="6">6</option>
							<option value="7">7</option>
							<option value="8">8</option>
						</select>
					</div>
				</div-->
			</div>
			
			<div class="row">
				<div class="col">
					<div class="form-row">
						<div class="more-features-box-text-icon cotizador-pasos mb-3 mt-3">2</div>
					</div>
				</div>
			</div>
			<div id="tblMiembros">
				<div class="form-row miembros-header pt-3 pb-3 mb-2">
					<div class="col-md-2 text-center"><b>PARENTESCO</b></div>
					<div class="col-md-6 text-center"><b>NOMBRE COMPLETO</b></div>
					<div class="col-md-2 text-center"><b>SEXO</b></div>
					<div class="col-md-2 text-center"><b>EDAD</b></div>
				</div>
				<div class="form-row miembros-header-sm pt-3 pb-3 mb-2  d-block d-lg-none">
					<div class="col text-center"><b>MIEMBROS</b></div>
				</div>
				<div class="form-row mb-2 adulto">
					<div class="col-md-2">
						<label for="titulos_1" class="form-label  d-block d-lg-none">Parentesco:</label>
						<select id="titulos_1" name="titulos[]" class="form-control" required="">
							<option value="titular">Titular</option>
						</select>
					</div>
					<div class="col-md-6">
						<label for="nombres_1" class="form-label  d-block d-lg-none">Nombre:</label>
						<input type="text" id="nombres_1" name="nombres[]" class="form-control" value="" required="">
						<div class="invalid-feedback">
							Favor de proporcionar Nombre completo
						</div>
					</div>
					<div class="col-md-2">
						<label for="sexos_1" class="form-label  d-block d-lg-none">Sexo:</label>
						<select id="sexos_1" name="sexos[]" class="form-control" required="">
							<option value="">Seleccionar...</option>
							<option value="m">Hombre</option>
							<option value="f">Mujer</option>
						</select>
						<div class="invalid-feedback">
							Favor de seleccionar Sexo
						</div>
					</div>
					<div class="col-md-2">
						<label for="edades_1" class="form-label  d-block d-lg-none">Edad:</label>
						<input type="number" id="edades_1" name="edades[]" class="form-control" min="18" max="69" value="" required="">
						<div class="invalid-feedback">
							Favor de proporcionar Edad
						</div>
					</div>
				</div>
				<hr class="d-block d-lg-none">
				<div class="form-row mb-2">
					<div class="col-md-2">
						<label for="titulos_2" class="form-label  d-block d-lg-none">Parentesco:</label>
						<select id="titulos_2" name="titulos[]" class="form-control">
							<option value="">Seleccionar...</option>
							<option value="conyugue">Conyugue</option>
							<option value="hijo(a)">Hijo(a)</option>
						</select>
						<div class="invalid-feedback">
							Favor de proporcionar Parentesco
						</div>
					</div>
					<div class="col-md-6">
						<label for="nombres_2" class="form-label  d-block d-lg-none">Nombre:</label>
						<input type="text" id="nombres_2" name="nombres[]" class="form-control" value="">
						<div class="invalid-feedback">
							Favor de proporcionar Nombre completo
						</div>
					</div>
					<div class="col-md-2">
						<label for="sexos_2" class="form-label  d-block d-lg-none">Sexo:</label>
						<select id="sexos_2" name="sexos[]" class="form-control">
							<option value="">Seleccionar...</option>
							<option value="m">Hombre</option>
							<option value="f">Mujer</option>
						</select>
						<div class="invalid-feedback">
							Favor de proporcionar Sexo
						</div>
					</div>
					<div class="col-md-2">
						<label for="edades_2" class="form-label  d-block d-lg-none">Edad:</label>
						<input type="number" id="edades_2" name="edades[]" class="form-control" min="0" max="69" value="">
						<div class="invalid-feedback">
							Favor de proporcionar Edad
						</div>
					</div>
				</div>
				<hr class="d-block d-lg-none">
				<div class="form-row mb-2">
					<div class="col-md-2">
						<label for="titulos_3" class="form-label  d-block d-lg-none">Parentesco:</label>
						<select id="titulos_3" name="titulos[]" class="form-control">
							<option value="">Seleccionar...</option>
							<option value="hijo(a)">Hijo(a)</option>
						</select>
						<div class="invalid-feedback">
							Favor de proporcionar Parentesco
						</div>
					</div>
					<div class="col-md-6">
						<label for="nombres_3" class="form-label  d-block d-lg-none">Nombre:</label>
						<input type="text" id="nombres_3" name="nombres[]" class="form-control" value="">
						<div class="invalid-feedback">
							Favor de proporcionar Nombre completo
						</div>
					</div>
					<div class="col-md-2">
						<label for="sexos_3" class="form-label  d-block d-lg-none">Sexo:</label>
						<select id="sexos_3" name="sexos[]" class="form-control">
							<option value="">Seleccionar...</option>
							<option value="m">Hombre</option>
							<option value="f">Mujer</option>
						</select>
						<div class="invalid-feedback">
							Favor de proporcionar Sexo
						</div>
					</div>
					<div class="col-md-2">
						<label for="edades_3" class="form-label  d-block d-lg-none">Edad:</label>
						<input type="number" id="edades_3" name="edades[]" class="form-control" min="0" max="69" value="">
						<div class="invalid-feedback">
							Favor de proporcionar Edad
						</div>
					</div>
				</div>
				<hr class="d-block d-lg-none">
				<div class="form-row mb-2">
					<div class="col-md-2">
						<label for="titulos_4" class="form-label d-block d-lg-none">Parentesco:</label>
						<select id="titulos_4" name="titulos[]" class="form-control">
							<option value="">Seleccionar...</option>
							<option value="hijo(a)">Hijo(a)</option>
						</select>
						<div class="invalid-feedback">
							Favor de proporcionar Parentesco
						</div>
					</div>
					<div class="col-md-6">
						<label for="nombres_4" class="form-label  d-block d-lg-none">Nombre:</label>
						<input type="text" id="nombres_4" name="nombres[]" class="form-control" value="">
						<div class="invalid-feedback">
							Favor de proporcionar Nombre completo
						</div>
					</div>
					<div class="col-md-2">
						<label for="sexos_4" class="form-label  d-block d-lg-none">Sexo:</label>
						<select id="sexos_4" name="sexos[]" class="form-control">
							<option value="">Seleccionar...</option>
							<option value="m">Hombre</option>
							<option value="f">Mujer</option>
						</select>
						<div class="invalid-feedback">
							Favor de proporcionar Sexo
						</div>
					</div>
					<div class="col-md-2">
						<label for="edades_4" class="form-label  d-block d-lg-none">Edad:</label>
						<input type="number" id="edades_4" name="edades[]" class="form-control" min="0" max="69" value="">
						<div class="invalid-feedback">
							Favor de proporcionar Edad
						</div>
					</div>
				</div>
				<hr class="d-block d-lg-none">
				<div class="form-row mb-2">
					<div class="col-md-2">
						<label for="titulos_5" class="form-label  d-block d-lg-none">Parentesco:</label>
						<select id="titulos_5" name="titulos[]" class="form-control">
							<option value="">Seleccionar...</option>
							<option value="hijo(a)">Hijo(a)</option>
						</select>
						<div class="invalid-feedback">
							Favor de proporcionar PArentesco
						</div>
					</div>
					<div class="col-md-6">
						<label for="nombres_5" class="form-label  d-block d-lg-none">Nombre:</label>
						<input type="text" id="nombres_5" name="nombres[]" class="form-control" value="">
						<div class="invalid-feedback">
							Favor de proporcionar Nombre completo
						</div>
					</div>
					<div class="col-md-2">
						<label for="sexos_5" class="form-label  d-block d-lg-none">Sexo:</label>
						<select id="sexos_5" name="sexos[]" class="form-control">
							<option value="">Seleccionar...</option>
							<option value="m">Hombre</option>
							<option value="f">Mujer</option>
						</select>
						<div class="invalid-feedback">
							Favor de proporcionar Sexo
						</div>
					</div>
					<div class="col-md-2">
						<label for="edades_5" class="form-label  d-block d-lg-none">Edad:</label>
						<input type="number" id="edades_5" name="edades[]" class="form-control" min="0" max="69" value="">
						<div class="invalid-feedback">
							Favor de proporcionar Edad
						</div>
					</div>
				</div>
				<hr class="d-block d-lg-none">
				<div class="form-row mb-2">
					<div class="col-md-2">
						<label for="titulos_6" class="form-label  d-block d-lg-none">Parentesco:</label>
						<select id="titulos_6" name="titulos[]" class="form-control">
							<option value="">Seleccionar...</option>
							<option value="hijo(a)">Hijo(a)</option>
						</select>
						<div class="invalid-feedback">
							Favor de proporcionar PArentesco
						</div>
					</div>
					<div class="col-md-6">
						<label for="nombres_6" class="form-label d-block d-lg-none">Nombre:</label>
						<input type="text" id="nombres_6" name="nombres[]" class="form-control" value="">
						<div class="invalid-feedback">
							Favor de proporcionar Nombre completo
						</div>
					</div>
					<div class="col-md-2">
						<label for="sexos_6" class="form-label  d-block d-lg-none">Sexo:</label>
						<select id="sexos_6" name="sexos[]" class="form-control">
							<option value="">Seleccionar...</option>
							<option value="m">Hombre</option>
							<option value="f">Mujer</option>
						</select>
						<div class="invalid-feedback">
							Favor de proporcionar Sexo
						</div>
					</div>
					<div class="col-md-2">
						<label for="edades_6" class="form-label  d-block d-lg-none">Edad:</label>
						<input type="number" id="edades_6" name="edades[]" class="form-control" min="0" max="69" value="">
						<div class="invalid-feedback">
							Favor de proporcionar Edad
						</div>
					</div>
				</div>
				<hr class="d-block d-lg-none">
				<div class="form-row mb-2">
					<div class="col-md-2">
						<label for="titulos_7" class="form-label  d-block d-lg-none">Parentesco:</label>
						<select id="titulos_7" name="titulos[]" class="form-control">
							<option value="">Seleccionar...</option>
							<option value="hijo(a)">Hijo(a)</option>
						</select>
						<div class="invalid-feedback">
							Favor de proporcionar Parentesco
						</div>
					</div>
					<div class="col-md-6">
						<label for="nombres_7" class="form-label  d-block d-lg-none">Nombre:</label>
						<input type="text" id="nombres_7" name="nombres[]" class="form-control" value="">
						<div class="invalid-feedback">
							Favor de proporcionar Nombre completo
						</div>
					</div>
					<div class="col-md-2">
						<label for="sexos_7" class="form-label  d-block d-lg-none">Sexo:</label>
						<select id="sexos_7" name="sexos[]" class="form-control">
							<option value="">Seleccionar...</option>
							<option value="m">Hombre</option>
							<option value="f">Mujer</option>
						</select>
						<div class="invalid-feedback">
							Favor de proporcionar Sexo
						</div>
					</div>
					<div class="col-md-2">
						<label for="edades_7" class="form-label  d-block d-lg-none">Edad:</label>
						<input type="number" id="edades_7" name="edades[]" class="form-control" min="0" max="69" value="">
						<div class="invalid-feedback">
							Favor de proporcionar Edad
						</div>
					</div>
				</div>
				<hr class="d-block d-lg-none">
			</div>
			<div class="row">
				<div class="col">
					<div class="form-row">
						<div class="col-md-12">
							<label for="nombre">Comentarios</label>
							<textarea id="comentarios" name="comentarios" class="form-control"></textarea>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<div class="form-row">
						<div class="col-md-12">
							<div class="form-check">
								<input class="form-check-input" type="checkbox" id="chkTerminos" name="chkTerminos" required>
								<label class="form-check-label">Acepto los <a href="/aviso-privacidad" target="_blank">términos, condiciones y aviso de privacidad</a></label>
								<div class="invalid-feedback">
									Favor de aceptar los términos, condiciones y aviso de privacidad
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row mt-3">
				<div class="col text-center">
					<button type="submit" class="btn btn-outline btn-primary font-weight-bold custom-btn-style-1 text-2" id="btnCotizar">Cotizar Ahora</button>
				</div>
			</div>
		</form>
		<div id="mi-cotizacion" class="invisible">
			<div class="row">
				<div class="col">
					<ul class="nav nav-tabs" role="tablist">
					</ul>
					<div class="tab-content mt-3">
					</div>
				</div>
			</div>
		</div>
	</div>
	<!--script type="text/javascript" src="https://optin.safetymails.com/main/safetyscript/ccd1a4d1fe03fc4a0389c9877261ebe62f285730"></script-->
</section>