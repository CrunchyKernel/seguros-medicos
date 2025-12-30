@extends('layout.porto')

@section('contenido')
	<?php echo '<script>var idCotizacion = ' . $idCotizacion . '; var secret = "' . $secret . '";var toRecotizar = new Array();</script>';?>
	<section>
		<div class="container">
			<div class="row">
				<div class="col">
					{{$cotizacionEncabezado}}
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					{{$tablaClienteDatos}}
				</div>
				<div class="col-md-6">
					{{$tablaIntegrantes}}
				</div>
			</div>
		</div>
		<div class="container pt-5" id="cotizacion">
			<div class="row">
				<div class="col-md-12">
					<h2 class="custom-primary-font">
						Tabla de costos por aseguradora y planes
					</h2>
				</div>
			</div>
		</div>
		<div>
			<div class="container">
				<div class="row">
					<div class="col">
						<ul class="nav nav-tabs" role="tablist">
							@if(isset($tablaDatos["sa_db"]))
								<li class="nav-item {{(($tabDefault=='sa_db' || $tabDefault=='') ? 'active' : '')}}">
									<a href="#tabsa_db" id="tab-sa_db" class="nav-link {{(($tabDefault=='sa_db' || $tabDefault=='') ? 'active' : '')}}" data-toggle="tab" role="tab" aria-controls="tabsa_db" aria-selected="true">
										{{$tablaDatos["sa_db"]["titulo"]}}
									</a>
								</li>
							@endif
							@if(isset($tablaDatos["sa_da"]))
								<li class="nav-item {{(($tabDefault=='sa_da') ? 'active' : '')}}">
									<a href="#tabsa_da" id="tab-sa_da" class="nav-link {{(($tabDefault=='sa_da') ? 'active' : '')}}" data-toggle="tab" role="tab" aria-controls="tabsa_da" aria-selected="true">
										{{$tablaDatos["sa_da"]["titulo"]}}
									</a>
								</li>
							@endif
							@if(isset($tablaDatos["sb_db"]))
								<li class="nav-item {{(($tabDefault=='sb_db') ? 'active' : '')}}">
									<a href="#tabsb_db" id="tab-sb_db" class="nav-link {{(($tabDefault=='sb_db') ? 'active' : '')}}" data-toggle="tab" role="tab" aria-controls="tabsb_db" aria-selected="true">
										{{$tablaDatos["sb_db"]["titulo"]}}
									</a>
								</li>
							@endif
							@if(isset($tablaDatos["sb_da"]))
								<li class="nav-item {{(($tabDefault=='sb_da') ? 'active' : '')}}">
									<a href="#tabsb_da" id="tab-sb_da" class="nav-link {{(($tabDefault=='sb_da') ? 'active' : '')}}" data-toggle="tab" role="tab" aria-controls="tabsb_da" aria-selected="true">
										{{$tablaDatos["sb_da"]["titulo"]}}
									</a>
								</li>
							@endif
						</ul>
						<div class="tab-content mt-3">
							@if(isset($tablaDatos["sa_db"]))
								{{doTable($tablaDatos, "sa_db", (($tabDefault=="sa_db" || $tabDefault=="") ? true : false))}}
							@endif
							@if(isset($tablaDatos["sa_da"]))
								{{doTable($tablaDatos, "sa_da", (($tabDefault=="sa_da") ? true : false))}}
							@endif
							@if(isset($tablaDatos["sb_db"]))
								{{doTable($tablaDatos, "sb_db", (($tabDefault=="sb_db") ? true : false))}}
							@endif
							@if(isset($tablaDatos["sb_da"]))
								{{doTable($tablaDatos, "sb_da", (($tabDefault=="sb_da") ? true : false))}}
							@endif
						</div>
					</div>
				</div>
				<section class="call-to-action call-to-action-tertiary mb-5 mt-5">
					<div class="col-sm-9 col-lg-9">
						<div class="call-to-action-content">
							<h3>¡Quiero <strong>protegerme</strong> a mí y a mi&nbsp;<strong>familia!</strong></h3>
							<p class="mb-0 opacity-7"><em><strong>Deseo mayor información, tengo algunas dudas... quisiera contratar ...</strong></em></p>
						</div>
					</div>
					<div class="col-sm-3 col-lg-3">
						<div class="call-to-action-btn"><a class="btn btn-modern text-2 btn-light border-0" href="#cuestionario">Contactar</a></div>
					</div>
				</section>
				@for($a=0;$a<count($tablaDatos["sa_db"]["datos"]["aseguradoras"]);$a++)
					@if(!is_null($tablaDatos["sa_db"]["datos"]["aseguradoras"][$a]["web"]))
						<div class="pt-5 d-none d-lg-block" id="notas-{{$tablaDatos['sa_db']['datos']['aseguradoras'][$a]['id']}}">
							&nbsp;
						</div>
						<div class="row pt-3 d-none d-lg-block">
							<div class="col text-center">
								<img src="/images_post/images/{{$tablaDatos['sa_db']['datos']['aseguradoras'][$a]['id']}}.jpg" class="img-fluid">
							</div>
						</div>
						<div class="row pt-1 d-none d-lg-block">
							<div class="col">
								{{$tablaDatos["sa_db"]["datos"]["aseguradoras"][$a]["web"]}}
							</div>
						</div>
					@endif
				@endfor
				<div class="row">
					<div class="col">
						{{$cotizacionPie}}
					</div>
				</div>
				<div class="row pt-3 pb-5">
					<div class="col">
						<form id="frmCotizacionContacto">
							<input type="hidden" name="id" value="{{$cotizacionDatos->id_cotizacion}}">
							<input type="hidden" name="secret" value="{{$cotizacionDatos->secret}}">
							<div class="form-row">
								<label><b>Este Plan me agrada más</b></label>
							</div>
							<div class="form-row">
								<div class="col-lg-4 offset-lg-4">
									<?php $c = 1;?>
									@for($a=0;$a<count($tablaDatos["sa_db"]["datos"]["aseguradoras"]);$a++)
										@for($p=1;$p<=$tablaDatos["sa_db"]["datos"]["aseguradoras"][$a]["paquetes"];$p++)
											<div class="form-check pr-2">
												<input class="form-check-input" type="checkbox" value='{{$tablaDatos["sa_db"]["datos"]["aseguradoras"][$a]["nombre"]}} - {{$tablaDatos["sa_db"]["datos"]["tablas"][$c][2]}}' name="planes[]" id='plan-{{$tablaDatos["sa_db"]["datos"]["aseguradoras"][$a]["id"]}}-{{$tablaDatos["sa_db"]["datos"]["tablas"][$p][0]}}'>
												<label class="form-check-label" for='plan-{{$tablaDatos["sa_db"]["datos"]["aseguradoras"][$a]["id"]}}-{{$tablaDatos["sa_db"]["datos"]["tablas"][$p][0]}}'>{{$tablaDatos["sa_db"]["datos"]["aseguradoras"][$a]["nombre"]}} - {{$tablaDatos["sa_db"]["datos"]["tablas"][$c][2]}}</label>
											</div>
											<?php $c++;?>
										@endfor
									@endfor
								</div>
							</div>
							<div class="form-row pt-5">
								<label><b>Quisiera más información de</b></label>
								<textarea class="form-control" name="comentarios" rows="6"></textarea>
							</div>
							<div class="form-row pt-5">
								<label><b>Contáctenme por</b></label>
							</div>
							<div class="form-row pb-2">
								<div class="form-check pr-2">
									<input class="form-check-input" type="radio" name="por" value="Telefono" id="optTelefono" checked>
									<label class="form-check-label" for="optTelefono">Teléfono</label>
								</div>
								<div class="form-check pr-2">
									<input class="form-check-input" type="radio" name="por" value="Whatsapp" id="optWhatsapp">
									<label class="form-check-label" for="optWhatsapp">Whatsapp</label>
								</div>
							</div>
							<div class="form-row">
								<div class="col text-center">
									<button type="submit" class="btn btn-primary">MAYOR INFORMACIÓN</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
	<div class="modal fade" id="modCuestionario" tabindex="-1">
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
	<!-- Global site tag (gtag.js) - Google Ads: 1025142637 -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=AW-1025142637"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());

		gtag('config', 'AW-1025142637');
	</script>
	<?php
	function doTable($tablaDatos, $tipo, $active){
		$paquete = 0;
		echo '<div class="tab-pane ' . (($active) ? 'show active' : '') . '" id="tab' . $tipo . '" role="tabpanel" aria-labelledby="tab-' . $tipo . '">';
		echo	'<div class="container">';
		echo		'<div class="row">';
		echo			'<div class="col">';
		echo				'<h2>' . $tablaDatos[$tipo]["nombre"] . '</h2>';
		echo			'</div>';
		echo		'</div>';
		echo		'<div class="row">';
		echo			'<div class="col-lg cotizador-tabla d-none d-lg-block">';
		echo				'<div class="row borde">';
		echo					'<div class="col-12 text-center">';
		echo						'<img src="/assets/images/aseguradoras/0.jpg" class="img-fluid">';
		echo 						'<br>&nbsp;';
		echo					'</div>';
		echo				'</div>';
		echo				'<div class="row borde pt-1 pb-1 bg-gris-claro">';
		echo					'<div class="col-12">';
		echo						'<h3 class="text-center mb-0">&nbsp;</h3>';
		echo					'</div>';
		echo				'</div>';
							for($c=3;$c<count($tablaDatos[$tipo]["datos"]["tablas"][0]);$c++){
								if($c % 2 == 0)
									$bg = "bg-gris-claro";
								else
									$bg = "";
		echo					'<div class="row borde pt-1 pb-1 ' . $bg . '">';
		echo						'<div class="col col-12">';
									if($c<count($tablaDatos[$tipo]["datos"]["tablas"][0])-1)
		echo 							$tablaDatos[$tipo]["datos"]["tablas"][0][$c];
									else
		echo							'<b>' . $tablaDatos[$tipo]["datos"]["tablas"][0][$c] . '</b>';
		echo						'</div>';
		echo					'</div>';
							}
		echo				'<div class="row borde pt-1 pb-1 bg-gris-claro">';
		echo					'<div class="col col-6">';
		echo						'<b>Pago semestral</b>';
		echo					'</div>';
		echo					'<div class="col col-6">';
		echo						'Primer pago';
		echo					'</div>';
		echo				'</div>';
		echo				'<div class="row borde pt-1 pb-1 bg-gris-claro">';
		echo					'<div class="col col-6">';
		echo						'&nbsp;';
		echo					'</div>';
		echo					'<div class="col col-6">';
		echo						'Posteriores';
		echo					'</div>';
		echo				'</div>';
		echo				'<div class="row borde pt-1 pb-1">';
		echo					'<div class="col col-6">';
		echo						'<b>Pago trimestral</b>';
		echo					'</div>';
		echo					'<div class="col col-6">';
		echo						'Primer pago';
		echo					'</div>';
		echo				'</div>';
		echo				'<div class="row borde pt-1 pb-1">';
		echo					'<div class="col col-6">';
		echo						'&nbsp;';
		echo					'</div>';
		echo					'<div class="col col-6">';
		echo						'Posteriores';
		echo					'</div>';
		echo				'</div>';
		echo				'<div class="row borde pt-1 pb-1 bg-gris-claro">';
		echo					'<div class="col col-6">';
		echo						'<b>Pago mensual</b>';
		echo					'</div>';
		echo					'<div class="col col-6">';
		echo						'Primer pago';
		echo					'</div>';
		echo				'</div>';
		echo				'<div class="row borde pt-1 pb-1 bg-gris-claro">';
		echo					'<div class="col col-6">';
		echo						'&nbsp;';
		echo					'</div>';
		echo					'<div class="col col-6">';
		echo						'Posteriores';
		echo					'</div>';
		echo				'</div>';
		echo			'</div>';
						for($a=0;$a<count($tablaDatos[$tipo]["datos"]["aseguradoras"]);$a++){
							$colMD = floor(12 / $tablaDatos[$tipo]["datos"]["aseguradoras"][$a]["paquetes"]);
							$col = floor(12 / $tablaDatos[$tipo]["datos"]["aseguradoras"][$a]["paquetes"] + 1);
		echo				'<div class="col-lg cotizador-tabla">';
		echo					'<div class="row borde">';
		echo						'<div class="col-12 text-center">';
		echo							'<img src="/assets/images/aseguradoras/' . $tablaDatos[$tipo]['datos']['aseguradoras'][$a]['id'] . '.jpg" class="img-fluid">';
		echo							'<br><a href="#notas-' . $tablaDatos[$tipo]['datos']['aseguradoras'][$a]['id'] . '" class="d-none d-lg-block">Ver notas</a>';
		echo						'</div>';
		echo					'</div>';
								for($c=2;$c<count($tablaDatos[$tipo]["datos"]["tablas"][0]);$c++){
									if($c % 2 == 0)
										$bg = "bg-gris-claro";
									else
										$bg = "";
		echo						'<div class="row borde pt-1 pb-1 ' . $bg . ' d-block d-lg-none">';
		echo							'<div class="col">';
		echo								'<div class="row">';
		echo									'<div class="col">';
		echo										'<b>' . $tablaDatos[$tipo]["datos"]["tablas"][0][$c] . '</b>';
		echo									'</div>';
		echo								'</div>';
		echo							'</div>';
		echo						'</div>';
		echo						'<div class="row borde pt-1 pb-1 ' . $bg . '">';
		echo							'<div class="col-3 d-block d-lg-none">';
		echo								'<h3 class="text-center mb-0">&nbsp;</h3>';
		echo							'</div>';
		echo							'<div class="col-9 col-lg-12">';
		echo								'<div class="row">';
											if($c<count($tablaDatos[$tipo]["datos"]["tablas"][0])-1){
												for($p=1;$p<=$tablaDatos[$tipo]["datos"]["aseguradoras"][$a]["paquetes"];$p++){
		echo										'<div class="col col-' . $colMD . ' col-md-' . $colMD . ' text-right">';
													if($c==2)
		echo											'<h3 class="text-center mb-0"><b>' . $tablaDatos[$tipo]["datos"]["tablas"][$paquete + $p][$c] . '</b></h3>';
													else
		echo												$tablaDatos[$tipo]["datos"]["tablas"][$paquete + $p][$c];
		echo										'</div>';
												}
											}else{
												for($p=1;$p<=$tablaDatos[$tipo]["datos"]["aseguradoras"][$a]["paquetes"];$p++){
		echo										'<div class="col col-{{$colMD}} col-md-{{$colMD}} text-right" id="div-' . $tipo . '-' . $tablaDatos[$tipo]["datos"]["tablas"][$paquete + $p][1] . '-contado">';
														if($tablaDatos[$tipo]["datos"]["aseguradoras"][$a]["aseguradora"]!="mapfre")
		echo												'<b>$ ' . $tablaDatos[$tipo]["datos"]["tablas"][$paquete + $p][$c] . '</b>';
														else{
															if($tablaDatos[$tipo]["datos"]["tablas"][$paquete + $p][$c]!=-1)
		echo													'<b>$ ' . $tablaDatos[$tipo]["datos"]["tablas"][$paquete + $p][$c] . '</b>';
															else{
		echo													'<div class="spinner-border text-primary" style="width: 1rem; height: 1rem;" role="status" id="spin-' . $tipo . '-' . $tablaDatos[$tipo]["datos"]["tablas"][$paquete + $p][1] . '-contado"></div>';
		echo 													'<script>toRecotizar.push({"tipo":"' . $tipo . '", "hospitales":"' . $tablaDatos[$tipo]["datos"]["tablas"][$paquete + $p][1] . '"});</script>';
															}
														}
		echo										'</div>';
												}
											}
		echo								'</div>';
		echo							'</div>';
		echo						'</div>';
								}
		echo 					'<div class="row borde pt-1 pb-1 bg-gris-claro d-block d-lg-none">';
		echo						'<div class="col">';
		echo							'<b>Semestral</b>';
		echo						'</div>';
		echo					'</div>';
		echo					'<div class="row borde pt-1 pb-1 bg-gris-claro">';
		echo						'<div class="col-3 d-block d-lg-none">';
		echo							'Primer pago';
		echo						'</div>';
		echo						'<div class="col-9 col-lg-12">';
		echo							'<div class="row">';
										for($p=1;$p<=$tablaDatos[$tipo]["datos"]["aseguradoras"][$a]["paquetes"];$p++){
		echo									'<div class="col col-' . $colMD . ' col-md-' . $colMD . ' text-right" id="div-' . $tipo . '-' . $tablaDatos[$tipo]["datos"]["tablas"][$paquete + $p][1] . '-semestral-1">';
													if($tablaDatos[$tipo]["datos"]["aseguradoras"][$a]["aseguradora"]!="mapfre")
		echo											'$ ' . $tablaDatos[$tipo]["datos"]["pagos"][$paquete + $p][0];
													else{
														if($tablaDatos[$tipo]["datos"]["pagos"][$paquete + $p][0]!=-1)
		echo												'$ ' . $tablaDatos[$tipo]["datos"]["pagos"][$paquete + $p][0];
														else
		echo												'&nbsp;';
													}
		echo									'</div>';
										}
		echo							'</div>';
		echo						'</div>';
		echo					'</div>';
		echo					'<div class="row borde pt-1 pb-1 bg-gris-claro">';
		echo						'<div class="col-3 d-block d-lg-none">';
		echo							'Posteriores';
		echo						'</div>';
		echo						'<div class="col-9 col-lg-12">';
		echo							'<div class="row">';
										for($p=1;$p<=$tablaDatos[$tipo]["datos"]["aseguradoras"][$a]["paquetes"];$p++){
		echo									'<div class="col col-' . $colMD . ' col-md-' . $colMD . ' text-right" id="div-' . $tipo . '-' . $tablaDatos[$tipo]["datos"]["tablas"][$paquete + $p][1] . '-semestral-2">';
													if($tablaDatos[$tipo]["datos"]["aseguradoras"][$a]["aseguradora"]!="mapfre")
		echo											'$ ' . $tablaDatos[$tipo]["datos"]["pagos"][$paquete + $p][1];
													else{
														if($tablaDatos[$tipo]["datos"]["pagos"][$paquete + $p][1]!=-1)
		echo												'$ ' . $tablaDatos[$tipo]["datos"]["pagos"][$paquete + $p][1];
														else
		echo												'&nbsp;';
													}
		echo									'</div>';
										}
		echo							'</div>';
		echo						'</div>';
		echo					'</div>';
		echo					'<div class="row borde pt-1 pb-1 d-block d-lg-none">';
		echo						'<div class="col">';
		echo							'<b>Trimestral</b>';
		echo						'</div>';
		echo					'</div>';
		echo					'<div class="row borde pt-1 pb-1">';
		echo						'<div class="col-3 d-block d-lg-none">';
		echo							'Primer pago';
		echo						'</div>';
		echo						'<div class="col-9 col-lg-12">';
		echo							'<div class="row">';
										for($p=1;$p<=$tablaDatos[$tipo]["datos"]["aseguradoras"][$a]["paquetes"];$p++){
		echo									'<div class="col col-' . $colMD . ' col-md-' . $colMD . ' text-right" id="div-' . $tipo . '-' . $tablaDatos[$tipo]["datos"]["tablas"][$paquete + $p][1] . '-trimestral-1">';
													if($tablaDatos[$tipo]["datos"]["aseguradoras"][$a]["aseguradora"]!="mapfre")
		echo											'$ ' . $tablaDatos[$tipo]["datos"]["pagos"][$paquete + $p][2];
													else{
														if($tablaDatos[$tipo]["datos"]["pagos"][$paquete + $p][2]!=-1)
		echo												'$ ' . $tablaDatos[$tipo]["datos"]["pagos"][$paquete + $p][2];
														else
		echo												'&nbsp;';
													}
		echo									'</div>';
										}
		echo							'</div>';
		echo						'</div>';
		echo					'</div>';
		echo					'<div class="row borde pt-1 pb-1">';
		echo						'<div class="col-3 d-block d-lg-none">';
		echo							'Posteriores';
		echo						'</div>';
		echo						'<div class="col-9 col-lg-12">';
		echo							'<div class="row">';
										for($p=1;$p<=$tablaDatos[$tipo]["datos"]["aseguradoras"][$a]["paquetes"];$p++){
		echo									'<div class="col col-' . $colMD . ' col-md-' . $colMD . ' text-right" id="div-' . $tipo . '-' . $tablaDatos[$tipo]["datos"]["tablas"][$paquete + $p][1] . '-trimestral-2">';
													if($tablaDatos[$tipo]["datos"]["aseguradoras"][$a]["aseguradora"]!="mapfre")
		echo											'$ ' . $tablaDatos[$tipo]["datos"]["pagos"][$paquete + $p][3];
													else{
														if($tablaDatos[$tipo]["datos"]["pagos"][$paquete + $p][3]!=-1)
		echo												'$ ' . $tablaDatos[$tipo]["datos"]["pagos"][$paquete + $p][3];
														else
		echo												'&nbsp;';
													}
		echo									'</div>';
										}
		echo							'</div>';
		echo						'</div>';
		echo					'</div>';
		echo					'<div class="row borde pt-1 pb-1 bg-gris-claro d-block d-lg-none">';
		echo						'<div class="col">';
		echo							'<b>Mensual</b>';
		echo						'</div>';
		echo					'</div>';
		echo					'<div class="row borde pt-1 pb-1 bg-gris-claro">';
		echo						'<div class="col-3 d-block d-lg-none">';
		echo							'Primer pago';
		echo						'</div>';
		echo						'<div class="col-9 col-lg-12">';
		echo							'<div class="row">';
										for($p=1;$p<=$tablaDatos[$tipo]["datos"]["aseguradoras"][$a]["paquetes"];$p++){
		echo									'<div class="col col-' . $colMD . ' col-md-' . $colMD . ' text-right" id="div-' . $tipo . '-' . $tablaDatos[$tipo]["datos"]["tablas"][$paquete + $p][1] . '-mensual-1">';
													if($tablaDatos[$tipo]["datos"]["aseguradoras"][$a]["aseguradora"]!="mapfre")
		echo											'$ ' . $tablaDatos[$tipo]["datos"]["pagos"][$paquete + $p][4];
													else{
														if($tablaDatos[$tipo]["datos"]["pagos"][$paquete + $p][4]!=-1)
		echo												'$ ' . $tablaDatos[$tipo]["datos"]["pagos"][$paquete + $p][4];
														else
		echo												'&nbsp;';
													}
		echo									'</div>';
										}
		echo							'</div>';
		echo						'</div>';
		echo					'</div>';
		echo					'<div class="row borde pt-1 pb-1 bg-gris-claro">';
		echo						'<div class="col-3 d-block d-lg-none">';
		echo							'Posteriores';
		echo						'</div>';
		echo						'<div class="col-9 col-lg-12">';
		echo							'<div class="row">';
										for($p=1;$p<=$tablaDatos[$tipo]["datos"]["aseguradoras"][$a]["paquetes"];$p++){
		echo									'<div class="col col-' . $colMD . ' col-md-' . $colMD . ' text-right" id="div-' . $tipo . '-' . $tablaDatos[$tipo]["datos"]["tablas"][$paquete + $p][1] . '-mensual-2">';
													if($tablaDatos[$tipo]["datos"]["aseguradoras"][$a]["aseguradora"]!="mapfre")
		echo											'$ ' . $tablaDatos[$tipo]["datos"]["pagos"][$paquete + $p][5];
													else{
														if($tablaDatos[$tipo]["datos"]["pagos"][$paquete + $p][5]!=-1)
		echo												'$ ' . $tablaDatos[$tipo]["datos"]["pagos"][$paquete + $p][5];
														else
		echo												'&nbsp;';
													}
		echo									'</div>';
										}
		echo							'</div>';
		echo						'</div>';
		echo					'</div>';
		echo				'</div>';
						if(!is_null($tablaDatos[$tipo]["datos"]["aseguradoras"][$a]["movil"])){
		echo					'<div class="container pt-5 d-block d-lg-none">';
		echo						'<div class="row">';
		echo							'<div class="col">';
		echo								str_replace('{{$tipo}}', $tipo, $tablaDatos[$tipo]["datos"]["aseguradoras"][$a]["movil"]);
		echo							'</div>';
		echo						'</div>';
		echo					'</div>';
						}
						$paquete += $tablaDatos[$tipo]["datos"]["aseguradoras"][$a]["paquetes"];
					}
		echo		'</div>';
		echo 	'</div>';
		echo '</div>';
	}
	?>
@stop

@section('js')
	<script src="/assets/js/helpers/verPortoCotizacion.js?20220105"></script>
@stop