@extends('layout.porto')

@section('contenido')
<style type="text/css">
	/* ===== Listado de blog — diseño moderno (lenguaje azul de marca) ===== */

	/* Encabezado: título en blanco */
	#blog-listado .page-header h1 {
		color: #ffffff;
	}

	/* Sección del listado con un fondo muy claro para que resalten las tarjetas */
	#blog-listado .section.bg-color-quaternary {
		background-color: #f7fafb !important;
	}

	/* --- Tarjeta de artículo --- */
	#blog-listado .blog-card {
		height: 100%;
		display: flex;
		flex-direction: column;
		background: #ffffff;
		border: 1px solid #e7eef0;
		border-radius: 16px;
		overflow: hidden;
		box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
		transition: transform 0.2s ease, box-shadow 0.2s ease;
	}

	#blog-listado .blog-card:hover {
		transform: translateY(-5px);
		box-shadow: 0 14px 30px rgba(6, 151, 180, 0.15);
	}

	/* Imagen: altura uniforme + zoom sutil al pasar el cursor */
	#blog-listado .blog-card-media {
		display: block;
		overflow: hidden;
	}

	#blog-listado .blog-card-media img {
		display: block;
		width: 100%;
		height: 200px;
		object-fit: cover;
		transition: transform 0.4s ease;
	}

	#blog-listado .blog-card:hover .blog-card-media img {
		transform: scale(1.06);
	}

	/* Cuerpo de la tarjeta */
	#blog-listado .blog-card-body {
		display: flex;
		flex-direction: column;
		flex: 1 1 auto;
		padding: 1.1rem 1.25rem 1.25rem;
	}

	/* Fecha de publicación */
	#blog-listado .blog-card-date {
		font-size: 0.72rem;
		text-transform: uppercase;
		letter-spacing: 0.05em;
		font-weight: 600;
		color: #94a3a9;
		margin-bottom: 0.4rem;
	}

	/* Título (máx. 2 líneas) */
	#blog-listado .blog-card-title {
		margin: 0 0 0.5rem;
		font-size: 1.1rem;
		line-height: 1.35;
		font-weight: 700;
	}

	#blog-listado .blog-card-title a {
		color: #0697b4;
		text-decoration: none;
		display: -webkit-box;
		-webkit-line-clamp: 2;
		-webkit-box-orient: vertical;
		overflow: hidden;
	}

	#blog-listado .blog-card-title a:hover {
		color: #057e98;
	}

	/* Extracto (máx. 3 líneas) */
	#blog-listado .blog-card-text {
		color: #6b7c82;
		font-size: 0.9rem;
		line-height: 1.55;
		margin-bottom: 1rem;
		display: -webkit-box;
		-webkit-line-clamp: 3;
		-webkit-box-orient: vertical;
		overflow: hidden;
	}

	/* Enlace "Leer más" con flecha animada */
	#blog-listado .blog-card-more {
		margin-top: auto;
		align-self: flex-start;
		color: #0697b4;
		font-weight: 700;
		font-size: 0.85rem;
		text-decoration: none;
		display: inline-flex;
		align-items: center;
		gap: 0.35rem;
	}

	#blog-listado .blog-card-more i {
		transition: transform 0.2s ease;
	}

	#blog-listado .blog-card:hover .blog-card-more i {
		transform: translateX(4px);
	}

	/* Sin animaciones de entrada */
	#blog-listado .appear-animation {
		opacity: 1 !important;
		animation: none !important;
	}
</style>
<div id="blog-listado">
	<section class="page-header page-header-modern section-no-border custom-bg-color-1 page-header-lg mb-0">
		<div class="container">
			<div class="row">
				<div class="col-md-12 align-self-center p-static order-2 text-center">
					<h1 class="custom-primary-font text-11 font-weight-light">{{$categoria->categoria}}</h1>
				</div>
			</div>
		</div>
	</section>
	@if(isset($contenidosArray) && count($contenidosArray) > 0)
		<section class="section bg-color-quaternary custom-padding-3 border-0 my-0">
			<div class="container">
				<?php $meses = array('', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'); ?>
				<div class="row justify-content-center">
					@foreach($contenidosArray as $contenido)
						<div class="col-md-6 col-lg-4 mb-4">
							<article class="blog-card">
								<a class="blog-card-media" href="{{URL::to('/' . $contenido->alias)}}">
									<img src="{{((strlen($contenido->imagen_medium) > 0) ? $contenido->imagen_medium : asset('assets/images/preview_medium.png'))}}"
										alt="{{$contenido->titulo}}">
								</a>
								<div class="blog-card-body">
									@if(!empty($contenido->fecha_publicacion))
										<?php $ts = strtotime($contenido->fecha_publicacion); ?>
										<span class="blog-card-date">{{ date('d', $ts) }} {{ $meses[(int)date('n', $ts)] }} {{ date('Y', $ts) }}</span>
									@endif
									<h3 class="blog-card-title"><a href="{{URL::to('/' . $contenido->alias)}}">{{str_limit($contenido->titulo, 100)}}</a></h3>
									<p class="blog-card-text">{{strip_tags(html_entity_decode($contenido->introtext))}}</p>
									<a class="blog-card-more" href="{{URL::to('/' . $contenido->alias)}}">Leer más <i class="fa fa-arrow-right"></i></a>
								</div>
							</article>
						</div>
					@endforeach
				</div>
			</div>
		</section>
	@endif
</div>
@stop
