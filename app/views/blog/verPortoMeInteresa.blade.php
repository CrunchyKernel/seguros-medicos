@extends('layout.porto')

@section('contenido')
	<div class="container">
		<div class="row">
			<div class="col-md-12 mt-5">
				<div class="row">
					<div class="col">
						<div class="card">
							<div class="card-header text-azul">
								Resúmen de la cotización:
							</div>
							<div class="card-body">
								<div class="row">
									<div class="col-md-8">
										<div class="row">
											<div class="col text-center">
												<img src="https://www.segurodegastosmedicosmayores.mx/assets/images/aseguradoras/{{$paquete[0]->id_aseguradora}}.jpg" class="img-fluid">
												<h3 class="mt-0">{{$paquete[0]->paquete}}</h3>
											</div>
										</div>
										<div class="row">
											<div class="col text-center">
												<h4 class="text-azul mt-0 mb-0">Total prima:</h4>
												<h4 class="text-azul mt-0">{{$tabla['costos']['contado']}}</h4>
											</div>
										</div>
										<div class="row">
											<div class="col text-center">
												<h5 class="text-azul mt-0 mb-0">Suma asegurada:</h5>
												<h5 class="text-azul mt-0">{{$tabla['costos']['sa']}}</h5>
											</div>
											<div class="col text-center">
												<h5 class="text-azul mt-0 mb-0">Deducible:</h5>
												<h5 class="text-azul mt-0">{{$tabla['costos']['deducible']}}</h5>
											</div>
											<div class="col text-center">
												<h5 class="text-azul mt-0 mb-0">Coaseguro:</h5>
												<h5 class="text-azul mt-0">{{$tabla['costos']['coaseguro']}}</h5>
											</div>
											<div class="col text-center">
												<h5 class="text-azul mt-0 mb-0">Tope coaseguro:</h5>
												<h5 class="text-azul mt-0">{{$tabla['costos']['tope']}}</h5>
											</div>
										</div>
									</div>
									<div class="col-md-4">
										{{$tablaIntegrantes}}
										{{$cotizacionDatos->ciudad}}, {{$cotizacionDatos->estado}}
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row mt-3">
					<div class="col-md-6">
						<div class="card">
							<div class="card-header text-azul">
								Formas de pago
							</div>
							<div class="card-body">
								<ul id="tabs" class="nav nav-tabs">
					                <li class="nav-item"><a href="" data-target="#anual" data-toggle="tab" class="nav-link active">Anual</a></li>
					                <li class="nav-item"><a href="" data-target="#semestral" data-toggle="tab" class="nav-link">Semestral</a></li>
					                <li class="nav-item"><a href="" data-target="#trimestral" data-toggle="tab" class="nav-link">Trimestral</a></li>
					                <li class="nav-item"><a href="" data-target="#mensual" data-toggle="tab" class="nav-link">Mensual</a></li>
					            </ul>
					            <div class="tab-content">
					                <div id="anual" class="tab-pane fade active show">
					                    <p>1 pago de {{$tabla['costos']['contado']}}.</p>
					                    {{$aseguradora->descripcion_promo}}
					                </div>
					                <div id="semestral" class="tab-pane fade">
					                    <p>1er pago {{$tabla['costos']['semestral-1']}}.</p>
					                    <p>2do pago  {{$tabla['costos']['semestral-2']}}.</p>
					                    <p>Total Anual {{$tabla['costos']['semestral']}}.</p>
					                </div>
					                <div id="trimestral" class="tab-pane fade">
					                    <p>1er pago {{$tabla['costos']['trimestral-1']}}.</p>
					                    <p>Pagos posteriores {{$tabla['costos']['trimestral-2']}}.</p>
					                    <p>Total Anual {{$tabla['costos']['trimestral']}}.</p>
					                </div>
					                <div id="mensual" class="tab-pane fade">
					                	<p>Mensual domiciliado a tarjeta.</p>
					                    <p>1er pago {{$tabla['costos']['mensual-1']}}.</p>
					                    <p>Pagos posteriores {{$tabla['costos']['mensual-2']}}.</p>
					                    <p>Total Anual {{$tabla['costos']['mensual']}}.</p>
					                </div>
					            </div>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="card">
							<div class="card-header text-azul">
								Contactenme
							</div>
							<div class="card-body">
								<form id="frmComentarios">
									<input type="hidden" id="id" name="id" value="{{$cotizacionDatos->id_cotizacion}}">
									<input type="hidden" id="secret" name="secret" value="{{$cotizacionDatos->secret}}">
									<input type="hidden" id="paquete" name="paquete" value="{{$cotizacionDatos->me_interesa}}">
									<div class="form-group">
										<label>
											{{$cotizacionDatos->nombre}}<br>
											{{$cotizacionDatos->e_mail}}<br>
											{{$cotizacionDatos->telefono}}
										</label>
									</div>
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
							<div class="card-header text-azul" data-toggle="collapse" data-target="#coberturas-body" style="cursor:pointer;">
								Mostrar/Ocultar coberturas
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
										<a class="btn btn-primary" href="https://www.segurodegastosmedicosmayores.mx/cotizacion-nuevo/{{$cotizacionDatos->id_cotizacion}}/{{$cotizacionDatos->secret}}">Quiero editar las coberturas</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row mt-3">
					<div class="col mt-5">
						{{$paquete[0]->descripcion_me_interesa}}
					</div>
				</div>
				<div class="modal fade" id="modContactanos" tabindex="-1">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-body">
								<p>Hemos recibido tu información.</p>
								<p>Pronto nos comunicaremos contigo de acuerdo a tu petición.</p>
								<p>Si lo prefieres, puedes llamarnos al <a href="tel:+523320020170">33 200 201 70</a> en horario hábil para atenderte más oportunamente.</p>
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