@extends('layout.porto')

@section('contenido')
@if($contenido->raw == 0)
	<section class="section border-0 my-0">
		<div class="container">
			<div class="row">
				<div class="col">
					<article class="">
						<div class="post-event-content">
							@if(!isset($hideTitle))
								<h1 class="text-transform-none text-9 text-center mb-0 mt-0 w-40 mx-auto id-article-title">
									{{$contenido->titulo}}
								</h1>
							@endif
							<br>
							@if(strlen($contenido->imagen_large) > 0)
								<div class="w-100 text-center pb-5">
									<div class="mw-50pct">
										<img src="{{$contenido->imagen_large}}" class="img-fluid blog-image id-article-image">
									</div>
								</div>
							@endif
							<?php
		if (strpos($contenido->contenido, '{{$cotizador}}')) {
			$cotizador = View::make('layout.portoCotizador')->render();
			$contenido->contenido = str_replace('{{$cotizador}}', $cotizador, $contenido->contenido);
		}

		if (strpos($contenido->contenido, '{{$cotizador-nuevo}}')) {
			$cotizador = View::make('layout.portoCotizadorNuevo')->render();
			$contenido->contenido = str_replace('{{$cotizador-nuevo}}', $cotizador, $contenido->contenido);
		}

		if (strpos($contenido->contenido, '{{$cotizador-test}}')) {
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