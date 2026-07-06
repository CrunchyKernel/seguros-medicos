@extends('layout.porto')

@section('contenido')
<?php echo '<script>var idCotizacion="' . $idCotizacion . '"; var secret="' . $secret . '";</script>';?>
<style type="text/css">
	/* Fila de precios (contado-top): el espacio de arriba queda igual al de abajo */
	#tblCotizacion tr[data-tipo="contado-top"] th h4 {
		margin-top: 0;
	}

	/* Fila de títulos (hospitales): espacio simétrico arriba y abajo */
	#tblCotizacion tr[data-tipo="hospitales"] td h4 {
		margin-top: 0;
		margin-bottom: 0;
	}

	/* Botones Editar y Me Interesa: más compactos y del mismo ancho */
	#tblCotizacion .cmdEditar,
	#tblCotizacion .cmdMeInteresa {
		width: 100%;
		padding: 0.25rem 0.5rem;
	}

	/* Espacio entre los botones Cancelar y Recotizar */
	#tblCotizacion .cmdRecotizar {
		margin-left: 0.5rem;
	}

	/* Iconos de información "i": más pequeños y centrados verticalmente */
	#tblCotizacion th img[src*="ic-info"] {
		width: 16px;
		height: auto;
		vertical-align: middle;
	}

	/* Icono "i" en la versión móvil: más pequeño */
	#m-tabla img[src*="ic-info"] {
		width: 14px;
		height: auto;
		vertical-align: middle;
	}

	/* "Mostrar pagos diferidos": subrayado y efecto al pasar el cursor */
	#tblCotizacion tr[data-tipo="cabecera-diferidos"] th {
		text-decoration: underline;
	}

	#tblCotizacion tr[data-tipo="cabecera-diferidos"]:hover th {
		background-color: #eaf3fa;
	}

	/* --- Vista móvil (#m-tabla) --- */
	/* Margen inferior debajo del logo de cada aseguradora */
	#m-tabla img[src*="aseguradoras"] {
		margin-bottom: 1.5rem;
	}

	/* Títulos de los planes más grandes */
	#m-tabla .m-plan-titulo {
		font-size: 1.25rem;
	}

	/* Striping más limpio usando el azul de marca en lugar del gris pesado */
	#m-tabla .bg-gris-claro {
		background-color: rgba(6, 151, 180, 0.08);
	}

	/* Separación entre botones "Me interesa" en móvil */
	#m-tabla .cmdMeInteresa {
		margin-top: 0.25rem;
	}

	/* Fila de logos y fila de precios siempre en blanco, incluso al pasar el cursor */
	#tblCotizacion tr[data-tipo="logos"],
	#tblCotizacion tr[data-tipo="logos"] th,
	#tblCotizacion tr[data-tipo="contado-top"],
	#tblCotizacion tr[data-tipo="contado-top"] th,
	#tblCotizacion.table-hover tbody tr[data-tipo="contado-top"]:hover,
	#tblCotizacion.table-hover tbody tr[data-tipo="contado-top"]:hover th {
		background-color: #fff;
	}

	/* --- Formulario "Este Plan me agrada más" (mismo estilo que el cotizador) --- */
	/* Panel tipo tarjeta con degradado */
	#frmCotizacionContacto {
		background: linear-gradient(180deg, rgba(210, 240, 245, 1) 0%, rgba(255, 255, 255, 1) 100%);
		border-radius: 25px;
		padding: 1.5rem 2.5rem 2.5rem;
	}

	/* Títulos de sección con un divisor sutil */
	#frmCotizacionContacto>.form-row>label {
		width: 100%;
		font-size: 1rem;
		font-weight: 600;
		margin-bottom: 0.5rem;
		padding-bottom: 0.5rem;
		border-bottom: 1px solid rgba(0, 0, 0, 0.08);
	}

	/* Campos redondeados */
	#frmCotizacionContacto .form-control {
		border-radius: 10px;
	}

	/* Botón de envío estilo "Cotizar Ahora" */
	#frmCotizacionContacto button[type="submit"] {
		background-color: var(--azul-main-title);
		color: #fff;
		border: none;
		border-radius: 25px;
		padding: 0.5rem 1.5rem;
		font-size: 1.25rem;
		line-height: 1.5;
	}

	#frmCotizacionContacto button[type="submit"]:hover,
	#frmCotizacionContacto button[type="submit"]:focus {
		background-color: var(--azul-main-title);
		color: #fff;
		opacity: 0.9;
	}
</style>
<div class="steps-toggler text-azul d-flex justify-content-center align-items-center hand steps-open"
	data-toggle="collapse" data-target="#steps">

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
							<p class="lead text-dark"><a href="#" class="step" data-id="card3">Opciones adicionales</a>
							</p>
							<p class="text-muted pb-3 results"></p>
						</div>
					</div>
				</div>
			</div>
			<div class="row mb-5 d-none d-lg-block">
				<div class="col">
					<div class="card" id="card-tabla">
						<div class="card-header text-right text-azul" data-toggle="collapse"
							data-target="#cotizacion-body" id="cotizacion">
							No. Cotización: <span></span>
						</div>
						<div class="card-body collapse show" id="cotizacion-body"
							style="border-top-left-radius: 0; border-top-right-radius: 0;">
						</div>
						<div class="text-center mt-3">
							<a href="#"
								class="btn btn-xl btn-outline btn-rounded btn-primary text-1 ml-3 font-weight-bold btnPrint"
								target="_blank"><i class="fa fa-print"></i> Imprimir</a>
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
			@for($a = 0; $a < count($datos["aseguradoras"]); $a++)
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
							<div class="col" id="ccPaquetes">

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
								<input class="form-check-input" type="radio" name="por" value="Telefono"
									id="optTelefono" checked>
								<label class="form-check-label" for="optTelefono">Teléfono</label>
							</div>
							<div class="form-check pr-2">
								<input class="form-check-input" type="radio" name="por" value="Whatsapp"
									id="optWhatsapp">
								<label class="form-check-label" for="optWhatsapp">Whatsapp</label>
							</div>
						</div>
						<div class="form-row">
							<div class="col text-center">
								<button type="submit" class="btn btn-primary">Mayor Informaci&oacute;n</button>
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
							<p>Si lo prefieres, puedes llamarnos al <a href="tel:+523320020170">33 200 201 70</a> en
								horario hábil para atenderte más oportunamente.</p>
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
<script src="/assets/js/helpers/verPortoCotizacionNuevo.js?2026062302"></script>
@stop