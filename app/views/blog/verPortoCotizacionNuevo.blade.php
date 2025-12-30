@extends('layout.porto')

@section('contenido')
	<?php echo '<script>var idCotizacion="' . $idCotizacion . '"; var secret="' . $secret . '";</script>';?>
	<div class="steps-toggler text-azul d-flex justify-content-center align-items-center hand steps-open" data-toggle="collapse" data-target="#steps">
		
	</div>
	<div class="container">
		<div class="row">
			<div class="col-md-3 collapse width" id="steps">
	
				<div class="stepper d-flex flex-column mt-5 ml-2">
					<div class="d-flex mb-1" id="step1">
						<div class="d-flex flex-column pr-4 align-items-center">
							<div class="rounded-circle py-2 px-3 bg-primary text-white mb-1">1</div>
							<div class="line h-100"></div>
						</div>
						<div>
							<p class="lead text-dark"><a href="#" class="step" data-id="card1">¿Donde te ubicas?</a></p>
							<p class="text-muted pb-3 results"></p>
						</div>
					</div>
					<div class="d-flex mb-1" id="step3">
						<div class="d-flex flex-column pr-4 align-items-center">
							<div class="rounded-circle py-2 px-3 bg-primary text-white mb-1">2</div>
							<div class="line h-100"></div>
						</div>
						<div>
							<p class="lead text-dark"><a href="#" class="step" data-id="card3">Opciones adicionales</a></p>
							<p class="text-muted pb-3 results"></p>
						</div>
					</div>
				</div>
	
			</div>
			<div class="col-md-12 mt-5" id="cotizacion-col">
				<div class="row mb-5">
					<div class="col">
					{{$cotizacionEncabezado}}
					</div>
				</div>
				<div class="row mb-5 d-block d-lg-none">
					<div class="stepper d-flex flex-column mt-5 ml-2">
						<div class="d-flex mb-1" id="m-cotizacion">
							<div class="d-flex flex-column pr-4 align-items-center">
								<div class="rounded-circle py-2 px-3 bg-primary text-white mb-1">&nbsp;</div>
								<div class="line h-100"></div>
							</div>
							<div>
								<p class="lead text-dark"><a href="#" class="step" data-id="card1">No. Cotización</a></p>
								<p class="text-muted pb-3 results"></p>
							</div>
						</div>
						<div class="d-flex mb-1" id="m-step1">
							<div class="d-flex flex-column pr-4 align-items-center">
								<div class="rounded-circle py-2 px-3 bg-primary text-white mb-1">1</div>
								<div class="line h-100"></div>
							</div>
							<div>
								<p class="lead text-dark"><a href="#" class="step" data-id="card1">¿Donde te ubicas?</a></p>
								<p class="text-muted pb-3 results"></p>
							</div>
						</div>
						<div class="d-flex mb-1" id="m-step3">
							<div class="d-flex flex-column pr-4 align-items-center">
								<div class="rounded-circle py-2 px-3 bg-primary text-white mb-1">2</div>
								<div class="line h-100"></div>
							</div>
							<div>
								<p class="lead text-dark"><a href="#" class="step" data-id="card3">Opciones adicionales</a></p>
								<p class="text-muted pb-3 results"></p>
							</div>
						</div>
					</div>
				</div>
				<div class="row mb-5 d-none d-lg-block">
					<div class="col">
						<div class="card" id="card-tabla">
							<div class="card-header text-right text-azul" data-toggle="collapse" data-target="#cotizacion-body" id="cotizacion">
								No. Cotización: <span></span>
							</div>
							<div class="card-body collapse show" id="cotizacion-body">
							</div>
							<div class="text-center">
								<a href="#" class="btn btn-xl btn-outline btn-rounded btn-primary text-1 ml-3 font-weight-bold text-uppercase btnPrint" target="_blank"><i class="fa fa-print"></i> IMPRIMIR</a>
							</div>
						</div>
					</div>
				</div>
				<div class="row mb-5 d-block d-lg-none" id="m-tabla">
					
				</div>
				<div class="row mb-5">
					<div class="col">
					{{$cotizacionAbajode}}
					</div>
				</div>
				@for($a=0;$a<count($datos["aseguradoras"]);$a++)
					@if(!is_null($datos["aseguradoras"][$a]["web"]))
						<div class="pt-5 d-none d-lg-block" id="notas-{{$datos['aseguradoras'][$a]['id']}}">
							&nbsp;
						</div>
						<div class="row pt-3 d-none d-lg-block">
							<div class="col text-center">
								<img src="/images_post/images/{{$datos['aseguradoras'][$a]['id']}}.jpg" class="img-fluid">
							</div>
						</div>
						<div class="row pt-1 d-none d-lg-block">
							<div class="col">
								{{$datos["aseguradoras"][$a]["web"]}}
							</div>
						</div>
					@endif
				@endfor
				<div class="row mb-5">
					<div class="col">
					{{$cotizacionPie}}
					</div>
				</div>
				<div class="row mb-5">
					<div class="col">
						<form id="frmCotizacionContacto">
							<input type="hidden" id="ccId" name="id" value="">
							<input type="hidden" id="ccSecret" name="secret" value="">
							<div class="form-row">
								<label><b>Este Plan me agrada más</b></label>
							</div>
							<div class="form-row">
								<div class="col-lg-4 offset-lg-4" id="ccPaquetes">
									
								</div>
							</div>
							<div class="form-row pt-5">
								<label><b>Quisiera más información de</b></label>
								<textarea class="form-control" name="comentarios" rows="6"></textarea>
							</div>
							<div class="form-row pt-5">
								<label><b>Contáctenme por</b></label>
							</div>
							<div class="form-row pb-2">
								<div class="form-check pr-2">
									<input class="form-check-input" type="radio" name="por" value="Telefono" id="optTelefono" checked>
									<label class="form-check-label" for="optTelefono">Teléfono</label>
								</div>
								<div class="form-check pr-2">
									<input class="form-check-input" type="radio" name="por" value="Whatsapp" id="optWhatsapp">
									<label class="form-check-label" for="optWhatsapp">Whatsapp</label>
								</div>
							</div>
							<div class="form-row">
								<div class="col text-center">
									<button type="submit" class="btn btn-primary">MAYOR INFORMACIÓN</button>
								</div>
							</div>
						</form>
					</div>
				</div>
				<div class="modal fade" id="modCuestionario" tabindex="-1">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-body">
								<p>Hemos recibido tu información.</p>
								<p>Pronto nos comunicaremos contigo de acuerdo a tu petición.</p>
								<p>Si lo prefieres, puedes llamarnos al <a href="tel:+523320020170">33 200 201 70</a> en horario hábil para atenderte más oportunamente.</p>
								<p>Gracias por cotizar en SegurodeGastosMedicosMayores.mx</p>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@stop

@section('js')
	<script src="/assets/js/helpers/verPortoCotizacionNuevo.js?20251007"></script>
@stop