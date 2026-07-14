@extends('layout.porto')

@section('contenido')
<style type="text/css">
	/* --- Modernización "Me interesa" (lenguaje de diseño azul de marca) --- */
	/* Color de texto base: todo el texto es turquesa (#0697b4) o gris (#37474f) */
	#me-interesa {
		color: #37474f;
	}

	/* Tarjetas con esquinas redondeadas, sombra sutil y borde turquesa */
	#me-interesa .card {
		border: 1px solid #0697b4;
		border-radius: 16px;
		box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
		overflow: hidden;
	}

	/* Encabezados de tarjeta rellenos con el azul de marca (redondeo solo arriba) */
	#me-interesa .card-header {
		background-color: #0697b4;
		color: #ffffff;
		font-weight: 600;
		border-bottom: none;
		border-radius: 16px 16px 0 0;
		padding: 0.85rem 1.25rem;
	}

	/* El cuerpo redondea solo las esquinas inferiores (arriba recto, sin radio) y fondo blanco */
	#me-interesa .card-body {
		background-color: #ffffff;
		border-top-left-radius: 0;
		border-top-right-radius: 0;
		border-bottom-left-radius: 16px;
		border-bottom-right-radius: 16px;
	}

	/* Igualar la altura de las tarjetas en la misma fila */
	#me-interesa .row>[class*="col-"]>.card.h-100 {
		height: 100%;
	}

	/* Todas las imágenes con esquinas redondeadas */
	#me-interesa img {
		border-radius: 12px;
	}

	/* Pestañas de formas de pago */
	#me-interesa .nav-tabs {
		border-bottom: 1px solid rgba(6, 151, 180, 0.2);
	}

	/* Pestañas inactivas: fondo gris con texto blanco */
	#me-interesa .nav-tabs .nav-link {
		color: #ffffff;
		background: #6b7c82;
		border: none;
		border-radius: 8px 8px 0 0;
		margin-right: 2px;
	}

	/* Pestaña activa: fondo blanco, texto turquesa y subrayado */
	#me-interesa .nav-tabs .nav-link.active {
		color: #0697b4;
		font-weight: 600;
		background: #ffffff;
		border: none;
		border-bottom: 3px solid #0697b4;
	}

	/* Campos redondeados */
	#me-interesa .form-control {
		border-radius: 10px;
	}

	/* Botones tipo píldora con el azul de marca (texto blanco, incluso los outline) */
	#me-interesa .btn-primary {
		background-color: #0697b4;
		border-color: #0697b4;
		color: #ffffff;
		border-radius: 25px;
	}

	#me-interesa .btn-primary:hover,
	#me-interesa .btn-primary:focus {
		background-color: #057e98;
		border-color: #057e98;
		color: #ffffff;
	}

	/* Solo la primera letra de los botones en mayúscula */
	#me-interesa .btn {
		text-transform: lowercase;
	}

	#me-interesa .btn::first-letter {
		text-transform: uppercase;
	}

	/* Quitar todas las animaciones (incluye el contenido inyectado) */
	#me-interesa .appear-animation {
		opacity: 1 !important;
		animation: none !important;
		transform: none !important;
	}

	/* Icono de WhatsApp idéntico al de verPortoCotizacionNuevo:
	   se reemplaza el GIF viejo por el SVG nuevo y se tiñe de azul con el mismo filtro.
	   (El src viene del contenido inyectado, por eso se sustituye vía content: url) */
	#me-interesa img[src*="whatsapp"] {
		width: 100px !important;
		height: 100px !important;
		display: block !important;
		margin: 1rem auto !important;
		border-radius: 0 !important;
		content: url('/images_post/images/whatsapp-svgrepo-com.svg') !important;
		filter: brightness(0) saturate(100%) invert(48%) sepia(98%) saturate(1574%) hue-rotate(159deg) brightness(94%) contrast(98%) !important;
	}

	/* Tablas de datos (coberturas y resumen): la primera fila NO debe verse como encabezado
		   (anula la regla global "table tr:first-child td" del tema). Solo estas tablas. */
	#coberturas-body table tr:first-child th,
	#coberturas-body table tr:first-child td,
	#resumen-body table tr:first-child th,
	#resumen-body table tr:first-child td {
		color: #0697b4 !important;
		padding: 4px 14px !important;
		border-bottom: 1px solid #cfdfe3 !important;
		border-radius: 0 !important;
	}

	/* El valor de la primera fila centrado y con peso normal */
	#coberturas-body table tr:first-child td,
	#resumen-body table tr:first-child td {
		font-weight: 400 !important;
		text-align: center !important;
	}

	/* Zebra solo en la segunda columna (valores): primera fila azul y luego alternando */
	#coberturas-body table tr:nth-child(odd) td,
	#resumen-body table tr:nth-child(odd) td {
		background: #dff3f6 !important;
	}

	#coberturas-body table tr:nth-child(even) td,
	#resumen-body table tr:nth-child(even) td {
		background: #ffffff !important;
	}

	/* Sin esquinas redondeadas en las tablas (coberturas y resumen de la cotización) */
	#coberturas-body table,
	#coberturas-body table th,
	#coberturas-body table td,
	#resumen-body table,
	#resumen-body table th,
	#resumen-body table td,
	#integrantes-body table,
	#integrantes-body table th,
	#integrantes-body table td {
		border-radius: 0 !important;
	}

	/* Tabla de integrantes: encabezado azul, cuerpo con zebra */
	#integrantes-body h3 {
		color: #0697b4;
		font-size: 1.1rem;
	}

	#integrantes-body table thead th {
		background: #0697b4 !important;
		color: #ffffff !important;
		font-weight: 600 !important;
		border: none !important;
		padding: 8px 14px !important;
		text-align: center !important;
	}

	/* Anula el "encabezado" que el tema aplica a la primera fila del cuerpo */
	#integrantes-body table tbody tr td {
		color: #0697b4 !important;
		padding: 6px 14px !important;
		border-bottom: 1px solid #cfdfe3 !important;
	}

	#integrantes-body table tbody tr:nth-child(odd) td {
		background: #dff3f6 !important;
	}

	#integrantes-body table tbody tr:nth-child(even) td {
		background: #ffffff !important;
	}

	/* ===== Layout mejorado de "Resúmen de la cotización" ===== */
	#me-interesa .resumen-grid {
		display: flex;
		flex-wrap: wrap;
		gap: 1.5rem;
		align-items: stretch;
	}

	/* Panel del plan + prima total */
	#me-interesa .resumen-plan {
		flex: 1 1 240px;
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: flex-start;
		text-align: center;
		background: #ffffff;
		border: 1px solid rgba(6, 151, 180, 0.25);
		border-radius: 16px;
		padding: 1.5rem;
	}

	#me-interesa .resumen-logo {
		max-width: 170px;
		height: auto;
		margin-bottom: 0.75rem;
		border-radius: 0;
	}

	#me-interesa .resumen-plan-nombre {
		color: #0697b4;
		font-size: 1.3rem;
		font-weight: 700;
		margin: 0 0 1.25rem;
	}

	#me-interesa .resumen-prima {
		margin-top: auto;
		width: 100%;
		padding-top: 1rem;
		border-top: 1px dashed rgba(6, 151, 180, 0.35);
	}

	#me-interesa .resumen-prima-label {
		display: block;
		text-transform: uppercase;
		letter-spacing: 0.06em;
		font-size: 0.72rem;
		font-weight: 600;
		color: #6b7c82;
	}

	#me-interesa .resumen-prima-valor {
		display: block;
		color: #0697b4;
		font-size: 2.1rem;
		font-weight: 800;
		line-height: 1.1;
	}

	/* Columna de detalle */
	#me-interesa .resumen-detalle {
		flex: 2 1 340px;
		display: flex;
		flex-direction: column;
		gap: 1.25rem;
	}

	/* Mosaico de métricas */
	#me-interesa .resumen-tiles {
		display: grid;
		grid-template-columns: repeat(2, 1fr);
		gap: 0.75rem;
	}

	#me-interesa .resumen-tile {
		display: flex;
		flex-direction: column;
		background: #f4fbfc;
		border: 1px solid #e0f0f3;
		border-radius: 12px;
		padding: 0.75rem 1rem;
	}

	#me-interesa .resumen-tile-label {
		text-transform: uppercase;
		letter-spacing: 0.04em;
		font-size: 0.7rem;
		font-weight: 600;
		color: #6b7c82;
		margin-bottom: 0.2rem;
	}

	#me-interesa .resumen-tile-value {
		color: #0697b4;
		font-size: 1.2rem;
		font-weight: 700;
	}

	/* Ubicación */
	#me-interesa .resumen-ubicacion {
		display: flex;
		align-items: center;
		gap: 0.4rem;
		color: #0697b4;
		font-weight: 600;
	}

	#me-interesa .resumen-ubicacion i {
		color: #0697b4;
	}

	@media (max-width: 575px) {
		#me-interesa .resumen-tiles {
			grid-template-columns: 1fr;
		}
	}

	/* ===== "Formas de pago": lista de pagos ===== */
	#me-interesa .pago-lista {
		list-style: none;
		margin: 0;
		padding: 0.75rem 0 0;
	}

	#me-interesa .pago-lista li {
		display: flex;
		justify-content: space-between;
		align-items: baseline;
		padding: 0.6rem 0.25rem;
		border-bottom: 1px solid #eef4f5;
	}

	#me-interesa .pago-lista li:last-child {
		border-bottom: none;
	}

	#me-interesa .pago-label {
		color: #6b7c82;
		font-weight: 500;
	}

	#me-interesa .pago-valor {
		color: #0697b4;
		font-weight: 700;
		font-size: 1.05rem;
	}

	#me-interesa .pago-lista li.pago-total {
		margin-top: 0.4rem;
		border-top: 2px solid rgba(6, 151, 180, 0.25);
		border-bottom: none;
	}

	#me-interesa .pago-lista li.pago-total .pago-label {
		color: #0697b4;
		font-weight: 700;
		text-transform: uppercase;
		letter-spacing: 0.04em;
		font-size: 0.8rem;
	}

	#me-interesa .pago-lista li.pago-total .pago-valor {
		font-size: 1.4rem;
		font-weight: 800;
	}

	#me-interesa .pago-nota {
		font-size: 0.8rem;
		color: #6b7c82;
		margin-top: 0.6rem;
	}

	/* ===== "Contactenme": datos de contacto ===== */
	#me-interesa .contacto-datos {
		list-style: none;
		margin: 0 0 1rem;
		padding: 0.75rem 1rem;
		background: #f4fbfc;
		border: 1px solid #e0f0f3;
		border-radius: 12px;
	}

	#me-interesa .contacto-datos li {
		display: flex;
		align-items: center;
		gap: 0.6rem;
		padding: 0.3rem 0;
		color: #37474f;
	}

	#me-interesa .contacto-datos li i {
		color: #0697b4;
		width: 18px;
		text-align: center;
		flex: 0 0 18px;
	}

	/* Sección "Esencial" (descripción del plan) con fondo blanco.
		   Se fuerza el blanco también en las secciones/contenedores inyectados. */
	#me-interesa .descripcion-panel,
	#me-interesa .descripcion-panel section,
	#me-interesa .descripcion-panel>div,
	#me-interesa .descripcion-panel .container,
	#me-interesa .descripcion-panel .container-fluid,
	#me-interesa .descripcion-panel .row {
		background: #ffffff !important;
		background-color: #ffffff !important;
		background-image: none !important;
	}

	#me-interesa .descripcion-panel {
		border-radius: 16px;
		padding: 1.5rem 2rem;
	}

	/* Descripción del plan: encabezados en turquesa y sin cursiva (p. ej. "¿Qué requisitos se necesitan?") */
	#descripcion-body .text-primary,
	#descripcion-body h1,
	#descripcion-body h2,
	#descripcion-body h3,
	#descripcion-body h4 {
		color: #0697b4 !important;
	}

	#descripcion-body h1 em,
	#descripcion-body h2 em,
	#descripcion-body h3 em,
	#descripcion-body h4 em {
		font-style: normal !important;
	}

	/* Cuerpo de la descripción en gris; enlaces en turquesa (nada de negro) */
	#descripcion-body,
	#descripcion-body p,
	#descripcion-body li,
	#descripcion-body td,
	#descripcion-body strong,
	#descripcion-body b {
		color: #37474f !important;
	}

	#descripcion-body a {
		color: #0697b4 !important;
	}

	/* Icono de descarga en el botón "Descarga PDF" (contenido inyectado) */
	#me-interesa a[href*="pdf" i]::before {
		font-family: "Font Awesome 5 Free";
		font-weight: 900;
		content: "\f019";
		margin-right: 0.5rem;
	}

	/* Chevron de la tarjeta colapsable "Mostrar/Ocultar coberturas" */
	#me-interesa [data-target="#coberturas-body"] .fa-chevron-down {
		transition: transform 0.2s ease;
	}

	#me-interesa [data-target="#coberturas-body"]:not(.collapsed) .fa-chevron-down {
		transform: rotate(180deg);
	}
</style>
<div class="container" id="me-interesa">
	<div class="row">
		<div class="col-md-12 mt-5">
			<div class="row">
				<div class="col">
					<div class="card">
						<div class="card-header text-azul">
							Resúmen de la cotización:
						</div>
						<div class="card-body">
							<div class="resumen-grid">
								<div class="resumen-plan">
									<img src="https://www.segurodegastosmedicosmayores.mx/assets/images/aseguradoras/{{$paquete[0]->id_aseguradora}}.jpg"
										class="resumen-logo" alt="">
									<h3 class="resumen-plan-nombre">{{$paquete[0]->paquete}}</h3>
									<div class="resumen-prima">
										<span class="resumen-prima-label">Total prima</span>
										<span class="resumen-prima-valor">{{$tabla['costos']['contado']}}</span>
									</div>
								</div>
								<div class="resumen-detalle">
									<div class="resumen-tiles">
										<div class="resumen-tile">
											<span class="resumen-tile-label">Suma asegurada</span>
											<span class="resumen-tile-value">{{$tabla['costos']['sa']}}</span>
										</div>
										<div class="resumen-tile">
											<span class="resumen-tile-label">Deducible</span>
											<span class="resumen-tile-value">{{$tabla['costos']['deducible']}}</span>
										</div>
										<div class="resumen-tile">
											<span class="resumen-tile-label">Coaseguro</span>
											<span class="resumen-tile-value">{{$tabla['costos']['coaseguro']}}</span>
										</div>
										<div class="resumen-tile">
											<span class="resumen-tile-label">Tope coaseguro</span>
											<span class="resumen-tile-value">{{$tabla['costos']['tope']}}</span>
										</div>
									</div>
									<div class="resumen-integrantes" id="integrantes-body">
										{{$tablaIntegrantes}}
									</div>
									<div class="resumen-ubicacion">
										<i class="fa fa-map-marker"></i> {{$cotizacionDatos->ciudad}},
										{{$cotizacionDatos->estado}}
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row mt-3">
				<div class="col-md-6">
					<div class="card h-100">
						<div class="card-header text-azul">
							Formas de pago
						</div>
						<div class="card-body">
							<ul id="tabs" class="nav nav-tabs">
								<li class="nav-item"><a href="" data-target="#anual" data-toggle="tab"
										class="nav-link active">Anual</a></li>
								<li class="nav-item"><a href="" data-target="#semestral" data-toggle="tab"
										class="nav-link">Semestral</a></li>
								<li class="nav-item"><a href="" data-target="#trimestral" data-toggle="tab"
										class="nav-link">Trimestral</a></li>
								<li class="nav-item"><a href="" data-target="#mensual" data-toggle="tab"
										class="nav-link">Mensual</a></li>
							</ul>
							<div class="tab-content pt-2">
								<div id="anual" class="tab-pane fade active show">
									<ul class="pago-lista">
										<li class="pago-total"><span class="pago-label">1 pago de contado</span><span
												class="pago-valor">{{$tabla['costos']['contado']}}</span></li>
									</ul>
									{{$aseguradora->descripcion_promo}}
								</div>
								<div id="semestral" class="tab-pane fade">
									<ul class="pago-lista">
										<li><span class="pago-label">1er pago</span><span
												class="pago-valor">{{$tabla['costos']['semestral-1']}}</span></li>
										<li><span class="pago-label">2do pago</span><span
												class="pago-valor">{{$tabla['costos']['semestral-2']}}</span></li>
										<li class="pago-total"><span class="pago-label">Total anual</span><span
												class="pago-valor">{{$tabla['costos']['semestral']}}</span></li>
									</ul>
								</div>
								<div id="trimestral" class="tab-pane fade">
									<ul class="pago-lista">
										<li><span class="pago-label">1er pago</span><span
												class="pago-valor">{{$tabla['costos']['trimestral-1']}}</span></li>
										<li><span class="pago-label">Pagos posteriores</span><span
												class="pago-valor">{{$tabla['costos']['trimestral-2']}}</span></li>
										<li class="pago-total"><span class="pago-label">Total anual</span><span
												class="pago-valor">{{$tabla['costos']['trimestral']}}</span></li>
									</ul>
								</div>
								<div id="mensual" class="tab-pane fade">
									<ul class="pago-lista">
										<li><span class="pago-label">1er pago</span><span
												class="pago-valor">{{$tabla['costos']['mensual-1']}}</span></li>
										<li><span class="pago-label">Pagos posteriores</span><span
												class="pago-valor">{{$tabla['costos']['mensual-2']}}</span></li>
										<li class="pago-total"><span class="pago-label">Total anual</span><span
												class="pago-valor">{{$tabla['costos']['mensual']}}</span></li>
									</ul>
									<p class="pago-nota">Mensual domiciliado a tarjeta.</p>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="card h-100">
						<div class="card-header text-azul">
							Contactenme
						</div>
						<div class="card-body">
							<form id="frmComentarios">
								<input type="hidden" id="id" name="id" value="{{$cotizacionDatos->id_cotizacion}}">
								<input type="hidden" id="secret" name="secret" value="{{$cotizacionDatos->secret}}">
								<input type="hidden" id="paquete" name="paquete"
									value="{{$cotizacionDatos->me_interesa}}">
								<ul class="contacto-datos">
									<li><i class="fa fa-user"></i> {{$cotizacionDatos->nombre}}</li>
									<li><i class="fa fa-envelope"></i> {{$cotizacionDatos->e_mail}}</li>
									<li><i class="fa fa-phone"></i> {{$cotizacionDatos->telefono}}</li>
								</ul>
								<div class="form-group">
									<label for="comentarios">Comentarios</label>
									<textarea id="comentarios" name="comentarios" class="form-control"></textarea>
								</div>
								<div class="text-center">
									<button type="submit" class="btn btn-lg btn-block btn-primary">Enviar</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<div class="row mt-3">
				<div class="col">
					<div class="card">
						<div class="card-header text-azul d-flex justify-content-between align-items-center collapsed"
							data-toggle="collapse" data-target="#coberturas-body" style="cursor:pointer;">
							<span>Mostrar/Ocultar coberturas</span>
							<i class="fa fa-chevron-down"></i>
						</div>
						<div class="card-body collapse" id="coberturas-body">
							<div class="row">
								<div class="col">
									<table class="table table-hover">
										<tbody>
											@foreach($tabla['coberturas'] as $t)
												<tr>
													<th role="col" class="text-azul">{{$t['concepto']}}</th>
													<td class="text-center">{{$t['valor']}}</td>
												</tr>
											@endforeach
										</tbody>
									</table>
								</div>
							</div>
							<div class="row">
								<div class="col text-center">
									<a class="btn btn-primary d-none d-lg-inline-block"
										href="https://www.segurodegastosmedicosmayores.mx/cotizacion-nuevo/{{$cotizacionDatos->id_cotizacion}}/{{$cotizacionDatos->secret}}">Quiero
										editar las coberturas</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row mt-3">
				<div class="col mt-5">
					<div class="descripcion-panel" id="descripcion-body">
						{{$paquete[0]->descripcion_me_interesa}}
					</div>
				</div>
			</div>
			<div class="modal fade" id="modContactanos" tabindex="-1">
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
<script src="/assets/js/helpers/verPortoMeInteresa.js"></script>
@stop