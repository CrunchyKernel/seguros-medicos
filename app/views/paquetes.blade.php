
@section('contenido')
	
	<div class="page_title">
		<div class="container">
			<div class="title"><h1>Paquetes</h1></div>
	        <!--<div class="pagenation">&nbsp;<a href="index.html">Home</a> <i>/</i> <a href="#">Features</a> <i>/</i> Pricing Tables</div>-->
		</div>
	</div>
	<div class="container">
		<div class="content_fullwidth">
			@if(isset($aseguradorasPaquetes) && count($aseguradorasPaquetes) > 0)
				@foreach($aseguradorasPaquetes AS $aseguradora => $paquetes)
					@if(count($paquetes) > 0)
						<div class="pricing-tables-main" style="width: 100%;">
							<h2>Paquetes <strong>{{ucwords(str_replace('_', ' ', $aseguradora))}}</strong></h2>
							<div class="mar_top3"></div>
							<div class="clearfix"></div>
							<div class="pricing-tables-helight-two">
								<div class="title">Paquete</div>
								<div class="cont-list">
					            	<ul>
					                	<li style="text-align: left; margin-left: 50px !important;"><strong>Suma asegurada</strong></li>
										<li style="text-align: left; margin-left: 50px !important;"><strong>Deducible por enfermedad</strong></li>
										<li style="text-align: left; margin-left: 50px !important;"><strong>Coaseguro por enfermedad</strong></li>
										<li style="text-align: left; margin-left: 50px !important;"><strong>Tope máximo coaseguro</strong></li>
										<li style="text-align: left; margin-left: 50px !important;"><strong>Deducible por accidente</strong></li>
										<li style="text-align: left; margin-left: 50px !important;"><strong>Coaseguro por accidente</strong></li>
										<li style="text-align: left; margin-left: 50px !important;"><strong>Maternidad</strong></li>
										<li style="text-align: left; margin-left: 50px !important;"><strong>Gastos de recien nacido</strong></li>
										<li style="text-align: left; margin-left: 50px !important;"><strong>Emergencia en el extranjero</strong></li>
										<li class="last" style="text-align: left; margin-left: 50px !important;"><strong>Hospitales incluidos</strong></li>
					                </ul>
					            </div>
					            <!--<div class="ordernow"><a href="#" class="normalbut">Order Now</a></div>-->
				           	</div>
				           	@foreach($paquetes AS $paquete)
				           		<div class="pricing-tables-two">
					            	<div class="title">{{$paquete["paqueteNombre"]}}</div>
					                <div class="cont-list">
					                	<ul>
					                    	@foreach($paquete["configuracion"] AS $key => $value)
												<li>{{$value}} &nbsp;</li>
											@endforeach
					                    </ul>
					                </div>
					                <div class="ordernow"><a href="#" class="colorchan">COTIZAR PLAN</a></div>
					            </div>
				           	@endforeach
				        </div>
				        <div class="clearfix divider_line"></div>
			        @endif
		        @endforeach
	        @endif
    	</div>
    </div>
	
@stop