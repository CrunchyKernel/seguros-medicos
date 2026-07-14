@extends('layout.porto')

@section('contenido')
<style type="text/css">
	/* --- Listado de blog con el lenguaje de diseño azul de marca --- */
	/* Título del encabezado en blanco */
	#blog-listado .page-header h1 {
		color: #ffffff;
	}

	/* Fondo de la sección del listado en blanco */
	#blog-listado .section.bg-color-quaternary {
		background-color: #ffffff !important;
	}

	/* Tarjetas de artículo modernas: redondeadas, con sombra y misma altura */
	#blog-listado .thumb-info {
		height: 100%;
		background: #f5f5f5;
		border-radius: 16px;
		box-shadow: 0 6px 10px rgba(0, 0, 0, 0.06);
		overflow: hidden;
		transition: transform 0.2s ease, box-shadow 0.2s ease;
	}

	/* Efecto al pasar el cursor: eleva la tarjeta */
	#blog-listado .thumb-info:hover {
		transform: translateY(-4px);
		box-shadow: 0 12px 28px rgba(0, 0, 0, 0.12);
	}

	/* Imágenes uniformes en la parte superior de cada tarjeta */
	#blog-listado .thumb-info-wrapper img {
		display: block;
		width: 100%;
		height: 200px;
		object-fit: cover;
	}

	/* Esquinas inferiores del thumbnail, consistentes con el radio superior de la tarjeta */
	#blog-listado .thumb-info-wrapper,
	#blog-listado .thumb-info-wrapper img {
		border-bottom-left-radius: 16px !important;
		border-bottom-right-radius: 16px !important;
	}

	/* Espaciado del contenido de la tarjeta (menos espacio arriba, junto a la imagen) */
	/* !important para vencer a .custom-padding-4 que usa !important */
	#blog-listado .thumb-info-caption {
		padding: 0.75rem 1.25rem 1.25rem !important;
	}

	/* El título pegado a la imagen: sin margen ni relleno arriba */
	#blog-listado .thumb-info-caption h3 {
		margin-top: 0 !important;
		padding-top: 0 !important;
	}

	/* Títulos de artículo en azul de marca */
	#blog-listado .thumb-info-caption h3 a {
		color: #0697b4;
	}

	#blog-listado .thumb-info-caption h3 a:hover {
		color: #0697b4 !important;
	}

	/* Texto del extracto */
	#blog-listado .thumb-info-caption-text {
		color: #666666;
	}

	/* Quitar todas las animaciones */
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
				<div class="row justify-content-center">
					@foreach($contenidosArray as $i => $contenido)
						@if($i > 0)
							@if(($i % 3) == 0)
								</div>
								<div class="row justify-content-center">
							@endif
						@endif
						<div class="col-md-6 col-lg-4 mb-5">
							<article class="thumb-info thumb-info-hide-wrapper-bg border-0 appear-animation"
								data-appear-animation="expandIn" data-appear-animation-delay="600">
								<div class="thumb-info-wrapper m-0">
									<a href="{{URL::to('/' . $contenido->alias)}}"><img
											src="{{((strlen($contenido->imagen_medium) > 0) ? $contenido->imagen_medium : asset('assets/images/preview_medium.png'))}}"
											class="img-fluid" alt=""></a>
								</div>
								<div class="thumb-info-caption custom-padding-4 d-block">
									<h3 class="custom-primary-font text-transform-none text-5 mb-3"><a
											href="{{URL::to('/' . $contenido->alias)}}"
											class="text-decoration-none custom-link-style-1">{{str_limit($contenido->titulo, 100)}}</a>
									</h3>
									<span
										class="thumb-info-caption-text text-3 p-0 m-0">{{strip_tags(html_entity_decode($contenido->introtext))}}</span>
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