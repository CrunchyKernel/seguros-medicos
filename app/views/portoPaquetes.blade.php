@extends('layout.porto')

@section('contenido')
	
	<div class="page_title">
		<div class="container">
			<h1 class="custom-primary-font font-weight-semibold text-transform-none text-9 text-center mb-5 appear-animation" data-appear-animation="bounceInLeft">Paquetes</h1>
		</div>
	</div>
	<div class="container">
		
			@if(isset($aseguradorasPaquetes) && count($aseguradorasPaquetes) > 0)
				@foreach($aseguradorasPaquetes AS $aseguradora => $paquetes)
					@if(count($paquetes) > 0)
						<div class="row no-gutters">
							<div class="col">
								<h3 class="text-primary">Paquetes {{ucwords(str_replace('_', ' ', $aseguradora))}}</h3>
							</div>
						</div>
						<div class="row pb-5 no-gutters">
							<div class="col-md-4">
								<div class="bg-primary text-white pt-2 pb-2 text-center">Paquete</div>
								<div class="cont-list">
					            	<ul class="list-unstyled">
					                	<li><strong>Suma asegurada</strong></li>
										<li><strong>Deducible por enfermedad</strong></li>
										<li><strong>Coaseguro por enfermedad</strong></li>
										<li><strong>Tope máximo coaseguro</strong></li>
										<li><strong>Deducible por accidente</strong></li>
										<li><strong>Coaseguro por accidente</strong></li>
										<li><strong>Maternidad</strong></li>
										<li><strong>Gastos de recien nacido</strong></li>
										<li><strong>Emergencia en el extranjero</strong></li>
										<li><strong>Hospitales incluidos</strong></li>
					                </ul>
					            </div>
					    	</div>
				           	
				           	@foreach($paquetes AS $paquete)
				           		<div class="col-md-4 text-center">
					            	<div class="bg-dark text-white pt-2 pb-2 text-center">{{$paquete["paqueteNombre"]}}</div>
					                <div class="cont-list">
					                	<ul class="list-unstyled">
					                    	@foreach($paquete["configuracion"] AS $key => $value)
												<li>{{$value}} &nbsp;</li>
											@endforeach
					                    </ul>
					                </div>
					                <a href="#" class="btn btn-primary">COTIZAR PLAN</a>
					            </div>
				           	@endforeach
				        </div>
			        @endif
		        @endforeach
	        @endif
    	
    </div>
	
@stop