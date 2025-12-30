@extends('layout.nuevo')

@section('contenido')
	
	<figure class="content-media content-media--video" id="featured-media">
		<iframe class="content-media__object" id="featured-video" src="https://www.youtube.com/embed/Eed_i4yfZkI?enablejsapi=1&rel=0&showinfo=0&controls=1&autoplay=0&origin=https://www.segurodegastosmedicosmayores.mx/la-ruta/que-escojas/prueba-cotizador" frameborder="0"></iframe>
    </figure>
	
	@if($contenido->incluir_cotizador==1)
		<section id="contact" class="section-padding">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<h2 class="ser-title">Cotiza tu póliza</h2>
						<hr class="botm-line">
					</div>
				</div>
				<form id="frmCotizacion">
					<input type="hidden" name="id_origen" value="1">
					<input type="hidden" id="total" name="total" value="1">
					<div class="row">
						<div class="col-md-9">
							<div class="form-row">
								<div class="more-features-box-text-icon cotizador-pasos mb-3 mt-3">1</div>
							</div>
							<div class="form-row">
								<div class="col-md-4">
									<label for="nombre">Nombre completo</label>
									<input type="text" id="nombre" name="nombre" class="form-control" required>
								</div>
								<div class="col-md-4">
									<label for="e_mail">Correo electrónico</label>
									<input type="email" id="e_mail" name="e_mail" class="form-control" required>
								</div>
								<div class="col-md-4">
									<label for="telefono">Teléfono</label>
									<input type="text" id="telefono" name="telefono" class="form-control" required>
								</div>
							</div>
							<div class="form-row">
								<div class="col-md-4">
									<label for="estado">Estado</label>
									<input type="text" id="estado" name="estado" class="form-control" required value="Jalisco" readonly="">
								</div>
								<div class="col-md-4">
									<label for="ciudad">Ciudad</label>
									<input type="text" id="ciudad" name="ciudad" class="form-control" required>
								</div>
								<div class="col-md-4">
									<label for="telefono">¿Cuenta con alguna póliza actual?</label>
									<select id="poliza_actual" name="poliza_actual" class="form-control" required>
										<option value="no">No</option>
										<option value="si">Si</option>
									</select>
								</div>
							</div>
						</div>
						<div class="col-md-3">
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
						</div>
					</div>
					
					<div class="row">
						<div class="col">
							<div class="form-row">
								<div class="more-features-box-text-icon cotizador-pasos mb-3 mt-3">3</div>
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
						<div class="form-row miembros-header-sm pt-3 pb-3 mb-2 d-none">
							<div class="col text-center"><b>MIEMBROS</b></div>
						</div>
						<div class="form-row mb-2 adulto">
							<div class="col-md-2">
								<label for="titulos_1" class="form-label d-none">Parentesco:</label>
								<select id="titulos_1" name="titulos[]" class="form-control" required>
									<option value="Titular">Titular</option>
								</select>
							</div>
							<div class="col-md-6">
								<label for="nombres_1" class="form-label d-none">Nombre:</label>
								<input type="text" id="nombres_1" name="nombres[]" class="form-control" required>
							</div>
							<div class="col-md-2">
								<label for="sexos_1" class="form-label d-none">Sexo:</label>
								<select id="sexos_1" name="sexos[]" class="form-control" required>
									<option value="">Seleccionar...</option>
									<option value="m">Hombre</option>
									<option value="f">Mujer</option>
								</select>
							</div>
							<div class="col-md-2">
								<label for="edades_1" class="form-label d-none">Edad:</label>
								<input type="number" id="edades_1" name="edades[]" class="form-control" min="18" max="69" required>
							</div>
						</div>
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
					<div class="row mt-3">
						<div class="col text-center">
							<button class="btn btn-primary">Cotizar Ahora</button>
							<!--button id="btnVer" type="button" class="btn btn-secondary">Ver Cotizacion</button-->
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
		</section>
	@endif
@stop