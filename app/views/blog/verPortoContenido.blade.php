@extends('layout.porto')

@section('contenido')
	<div class="container pt-5 pb-5">
		<div class="row">
			<div class="col">
				<figure class="content-media content-media--video" id="featured-media">
					<iframe class="content-media__object" id="featured-video" src="https://www.youtube.com/embed/Eed_i4yfZkI?enablejsapi=1&rel=0&showinfo=0&controls=1&autoplay=0&origin=https://www.segurodegastosmedicosmayores.mx/la-ruta/que-escojas/prueba-cotizador" frameborder="0"></iframe>
			    </figure>
			</div>
		</div>
	</div>
    
    @if($contenido->incluir_cotizador==1)
    	{{HTML::script('assets/js/helpers/cotizador_nuevo.js?20220208')}}
		@yield('cotizador', View::make('layout.portoCotizador'))
	@endif
@stop