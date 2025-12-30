@extends('layout.porto')

@section('contenido')
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
					@foreach($contenidosArray AS $i => $contenido)
						@if($i>0)
							@if(($i%3)==0)
								</div>
								<div class="row justify-content-center">
							@endif
						@endif
						<div class="col-md-6 col-lg-4 mb-5">
							<article class="thumb-info thumb-info-hide-wrapper-bg border-0 appear-animation" data-appear-animation="expandIn" data-appear-animation-delay="600">
								<div class="thumb-info-wrapper m-0">
									<a href="{{URL::to('/' . $contenido->alias)}}"><img src="{{((strlen($contenido->imagen_medium) > 0) ? $contenido->imagen_medium : asset('assets/images/preview_medium.png'))}}" class="img-fluid" alt=""></a>
								</div>
								<div class="thumb-info-caption custom-padding-4 d-block">
									<h3 class="custom-primary-font text-transform-none text-5 mb-3"><a href="{{URL::to('/' . $contenido->alias)}}" class="text-decoration-none custom-link-style-1">{{str_limit($contenido->titulo, 100)}}</a></h3>
									<span class="thumb-info-caption-text text-3 p-0 m-0">{{strip_tags(html_entity_decode($contenido->introtext))}}</span>
								</div>
							</article>
						</div>
					@endforeach
				</div>
			</div>
		</section>
	@endif
@stop