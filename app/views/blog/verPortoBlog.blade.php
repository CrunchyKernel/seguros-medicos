@extends('layout.porto')

@section('contenido')
	<!--section class="page-header page-header-modern section-no-border custom-bg-color-1 page-header-lg mb-0">
		<div class="container">
			<div class="row">
				<div class="col-md-12 align-self-center p-static order-2 text-center">
					<h1 class="custom-primary-font text-11 font-weight-light">{{--$contenido->titulo--}}</h1>
				</div>
			</div>
		</div>
	</section-->
	@if($contenido->raw==0)
		<section class="section border-0 my-0">
			<div class="container">
				<div class="row">
					<div class="col">
						<article class="">
							<div class="post-event-content">
								@if(!isset($hideTitle))
									<h1 class="custom-primary-font font-weight-semibold text-transform-none text-9 text-center mb-0 mt-0 appear-animation" data-appear-animation="bounceInLeft">{{$contenido->titulo}}</h1>
								@endif
								@if(strlen($contenido->imagen_large) > 0)
									<div class="w-100 text-center pb-5">
										<img src="{{$contenido->imagen_large}}" class="img-fluid rounded mw-50pct appear-animation" data-appear-animation="zoomIn" data-appear-animation-delay="400">
									</div>
								@endif
								<?php
								if(strpos($contenido->contenido, '{{$cotizador}}')){
									$cotizador = View::make('layout.portoCotizador')->render();
									$contenido->contenido = str_replace('{{$cotizador}}', $cotizador, $contenido->contenido);
								}
								
								if(strpos($contenido->contenido, '{{$cotizador-nuevo}}')){
									$cotizador = View::make('layout.portoCotizadorNuevo')->render();
									$contenido->contenido = str_replace('{{$cotizador-nuevo}}', $cotizador, $contenido->contenido);
								}
								
								if(strpos($contenido->contenido, '{{$cotizador-test}}')){
									$cotizador = View::make('layout.portoCotizadorTest')->render();
									$contenido->contenido = str_replace('{{$cotizador-test}}', $cotizador, $contenido->contenido);
								}
								?>
								{{$contenido->contenido}}
							</div>
						</article>
					</div>
				</div>
			</div>
		</section>
	@else
		{{$contenido->contenido}}
	@endif
@stop