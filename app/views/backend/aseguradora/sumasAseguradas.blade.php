
@section('contenido')

	<ul class="nav nav-tabs nav-success">
		@if(isset($aseguradoras) && count($aseguradoras) > 0)
			<?php $n=0; ?>
        	@foreach($aseguradoras AS $aseguradora)
        		<li class="{{(($n == 0) ? 'active' : '')}}"><a href="#{{$aseguradora->aseguradora}}_tab" data-toggle="tab"><strong>{{$aseguradora->nombre}}</strong></a></li>
        		<?php $n++; ?>
        	@endforeach
        @endif
    </ul>
    <div class="tab-content mb30">
    	@if(isset($aseguradoras) && count($aseguradoras) > 0)

    		<?php $n=0; ?>
        	@foreach($aseguradoras AS $aseguradora)
        		<div class="tab-pane {{(($n == 0) ? 'active' : '')}}" id="{{$aseguradora->aseguradora}}_tab">
				<div class="row">
					<div class="col-md-4"><h3>Estado aseguradora:</h3></div>
  					<div class="col-md-4">
					  @if($aseguradora->activa == 1)
					   <h3><span class="label label-success">Activo</span></h3>
					  @else 
					   <h3><span class="label label-danger">Inactivo</span></h3>
					  @endif 
					</div>
  					<div class="col-md-4">
					  <br>
					  @if($aseguradora->activa == 1)
					  	<input checked class="switchAseguradora" data-id="{{$aseguradora->id_aseguradora}}" data-toggle="toggle" data-onstyle="success" data-offstyle="danger" type="checkbox">
					  @else
					  	<input class="switchAseguradora" data-id="{{$aseguradora->id_aseguradora}}" data-toggle="toggle" data-onstyle="success" data-offstyle="danger" type="checkbox">
					  @endif
					</div>
				</div>
				<br>
		            @if($aseguradora->Paquetes->count() > 0)
	        			<ul class="nav nav-tabs nav-info">
	        				<?php $m=0; ?>
		            		@foreach($aseguradora->Paquetes AS $paquete)
			                	<li class="{{(($m == 0) ? 'active' : '')}}"><a href="#{{$paquete->id_paquete . '-' . $paquete->paquete_campo}}" data-toggle="tab"><strong>{{$paquete->paquete}}</strong></a></li>
			                	<?php $m++; ?>
			                @endforeach
			            </ul>
			            <div class="tab-content tab-content-info mb30">
			            	<?php $m=0; ?>
			            	@foreach($aseguradora->Paquetes AS $paquete)
			                	<div class="tab-pane {{(($m == 0) ? 'active' : '')}}" id="{{$paquete->id_paquete . '-' . $paquete->paquete_campo}}">
									<div class="row">
										<div class="col-md-4"><h3>Estado plan:</h3></div>
										<div class="col-md-4">
										@if($paquete->activo > 0)
											<h3><span class="label label-success">Activo</span></h3>
										@else	
											<h3><span class="label label-danger">Inactivo</span></h3>
										@endif
										</div>
										<div class="col-md-4">
										@if($paquete->activo > 0)
											<input checked class="switchPlan" data-id="{{$paquete->id_paquete}}" data-toggle="toggle" data-onstyle="success" data-offstyle="danger" type="checkbox">
										@else	
											<input class="switchPlan" data-id="{{$paquete->id_paquete}}" data-toggle="toggle" data-onstyle="success" data-offstyle="danger" type="checkbox">
										@endif
											
										</div>
									</div>
									
									<br>
	                                <div class="table-responsive">
				                        <table class="table table-primary mb30">
				                            <thead>
												<tr>
													<th class="alignCenter"></th>
													<th class="alignCenter">Concepto</th>
													<th class="alignCenter">SA-DA</th>
													<th class="alignCenter">SA-DB</th>
													<th class="alignCenter">SB-DA</th>
													<th class="alignCenter">SB-DB</th>
												</tr>
				                            </thead>
				                            <tbody>
				                            	@if(isset($conceptos) && count($conceptos) > 0)
				                            		@foreach($conceptos AS $concepto)
				                            			<tr>
				                            				<td>{{$concepto->id_concepto}}</td>
				                            				<td>{{$concepto->concepto}}</td>
				                            				<td class="alignCenter"><a href="#" class="campo" data-type="text" data-original-title="SA-DA" data-pk="" data-idConcepto="{{$concepto->id_concepto}}" data-idPaquete="{{$paquete->id_paquete}}" data-campo="sa_da" data-value="{{(($concepto->tarifaValor()->first()->where('id_paquete', '=', $paquete->id_paquete)->where('id_concepto', '=', $concepto->id_concepto)->count() == 1) ? $concepto->tarifaValor()->first()->where('id_paquete', '=', $paquete->id_paquete)->where('id_concepto', '=', $concepto->id_concepto)->first()->sa_da : '')}}"></a></td>
				                            				<td class="alignCenter"><a href="#" class="campo" data-type="text" data-original-title="SA-DB" data-pk="" data-idConcepto="{{$concepto->id_concepto}}" data-idPaquete="{{$paquete->id_paquete}}" data-campo="sa_db" data-value="{{(($concepto->tarifaValor()->first()->where('id_paquete', '=', $paquete->id_paquete)->where('id_concepto', '=', $concepto->id_concepto)->count() == 1) ? $concepto->tarifaValor()->first()->where('id_paquete', '=', $paquete->id_paquete)->where('id_concepto', '=', $concepto->id_concepto)->first()->sa_db : '')}}"></a></td>
				                            				<td class="alignCenter"><a href="#" class="campo" data-type="text" data-original-title="SB-DA" data-pk="" data-idConcepto="{{$concepto->id_concepto}}" data-idPaquete="{{$paquete->id_paquete}}" data-campo="sb_da" data-value="{{(($concepto->tarifaValor()->first()->where('id_paquete', '=', $paquete->id_paquete)->where('id_concepto', '=', $concepto->id_concepto)->count() == 1) ? $concepto->tarifaValor()->first()->where('id_paquete', '=', $paquete->id_paquete)->where('id_concepto', '=', $concepto->id_concepto)->first()->sb_da : '')}}"></a></td>
				                            				<td class="alignCenter"><a href="#" class="campo" data-type="text" data-original-title="SB-DB" data-pk="" data-idConcepto="{{$concepto->id_concepto}}" data-idPaquete="{{$paquete->id_paquete}}" data-campo="sb_db" data-value="{{(($concepto->tarifaValor()->first()->where('id_paquete', '=', $paquete->id_paquete)->where('id_concepto', '=', $concepto->id_concepto)->count() == 1) ? $concepto->tarifaValor()->first()->where('id_paquete', '=', $paquete->id_paquete)->where('id_concepto', '=', $concepto->id_concepto)->first()->sb_db : '')}}"></a></td>
				                            			</tr>
				                            		@endforeach
				                            	@endif
				                            	<tr>
				                            		<td></td>
				                            		<td>Derecho póliza</td>
				                            		<td class="alignCenter"><a href="#" class="derecho_poliza" data-type="text" data-original-title="Derecho de póliza" data-pk="{{$paquete->id_paquete}}" data-campo="derecho_poliza" data-value="{{$paquete->derecho_poliza}}"></a></td>
				                            		<td colspan="3"></td>
				                            	</tr>
				                            </tbody>
				                        </table>
				                    </div>
	                            </div>
			                	<?php $m++; ?>
			                @endforeach
                        </div>
		            @endif
		        </div>
		        <?php $n++; ?>
        	@endforeach
        @endif
    </div>
@stop
