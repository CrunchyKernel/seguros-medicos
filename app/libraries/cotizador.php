<?php

class Cotizador {
	public static $mostrarAseguradoras = array();
	public static $integrantes = array();
	public static $cotizacionDatos = array();
	public static $calculos = null;
	// Pruebas: 
	//public static $wsUrl = 'https://negociosuat.mapfre.com.mx/';
	// Produccion:
	public static $wsUrl = 'https://zonaliados.mapfre.com.mx/';
	private static $sumaAsegurada = 'db';
	private static $deducible = 'db';
	private static $aseguradoras = array();
	private static $pagosDiferidos = array(6 => 'semestral', 3 => 'trimestral', 1 => 'mensual');
	private static $mostrarMaternidad = false;
	private static $deducibleEnfermedadMapfre = '7,000';
	private static $planDefault = "sa_db";

	function __construct($cotizacionDatos = array(), $sumaAsegurada, $deducible){
		if(is_array($cotizacionDatos->integrantes))
			self::$integrantes = $cotizacionDatos->integrantes;
		else
			self::$integrantes = json_decode($cotizacionDatos->integrantes);
		foreach(self::$integrantes AS $integrante){
			if($integrante->sexo == 'f' && ($integrante->edad >= 18 && $integrante->edad <= 44)){
				self::$mostrarMaternidad = true;
			}
			if($integrante->edad > 54){
				self::$deducibleEnfermedadMapfre = '22,000';
			}
		}
		self::$cotizacionDatos = $cotizacionDatos;
		self::$sumaAsegurada = $sumaAsegurada;
		self::$deducible = $deducible;
		self::getAseguradoras();
	}

	public static function tablaIntegrantes($admin = false, $pdf = false){
		$tr = '';
		$integrantesOrden = array();
		$n = 2;
		foreach(self::$integrantes AS $integrante){
			switch($integrante->titulo){
				case 'titular':
					$integrantesOrden[0] = $integrante;
				break;
				case 'conyugue':
					$integrantesOrden[1] = $integrante;
				break;
				default:
					$integrantesOrden[$n] = $integrante;
					$n++;
				break;
			}
		}
		self::$integrantes = $integrantesOrden;
		ksort(self::$integrantes);
		foreach(self::$integrantes AS $integrante){
			$tr .= '<tr>
						<td class="alignLeft" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.(($pdf == true)?utf8_decode(ucwords(strtolower($integrante->nombre))):htmlspecialchars_decode(ucwords(strtolower($integrante->nombre)))).' </td>
						<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.(($integrante->sexo == 'm') ? 'Hombre' : 'Mujer').'</td>
						<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.$integrante->edad.'</td>
						<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.ucfirst($integrante->titulo).'</td>
					</tr>';
		}
		$html = '<div class="one_half last">
					<div class="address-info">
						'.(($pdf == true)?'':'<h3><strong>Integrantes</strong></h3>').'
						<table width="100%" '.(($admin == true) ? 'class="table table-bordered table-dark table-invoice"' : 'class="table table-responsive table-condensed"').' '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>
							<thead>
								<tr>
									<th class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>Nombre</th>
									<th class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>Sexo</th>
									<th class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>Edad</th>
									<th class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>Titulo</th>
								</tr>
							</thead>
							<tbody>
								'.$tr.'
							</tbody>
						</table>
					</div>
				</div>';
		return $html;
	}

	public static function tablaClienteDatos($admin = false){
		$html = '<div class="one_half">
					<div class="address-info">
						<h3><strong>Cliente</strong></h3>
						<table width="100%" '.(($admin == true) ? 'class="table table-bordered table-dark table-invoice"' : '').'>
							<tr>
								<td><strong>Cotización</strong></td>
								<td>'.self::$cotizacionDatos->id_cotizacion.'</td>
							</tr>
							<tr>
								<td><strong>Cliente</strong></td>
								<td>'.self::$cotizacionDatos->nombre.'</td>
							</tr>
							<tr>
								<td><strong>Estado</strong></td>
								<td>'.self::$cotizacionDatos->estado.'</td>
							</tr>
							<tr>
								<td><strong>Ciudad</strong></td>
								<td>'.self::$cotizacionDatos->ciudad.'</td>
							</tr>
							<tr>
								<td><strong>Fecha registro</strong></td>
								<td>'.self::$cotizacionDatos->fecha_registro.'</td>
							</tr>
						</table>
					</div>
				</div>';
		
		return $html;
	}
	
	public static function tablaDatos($admin = false, $mostrarContratar = true, $pdf = false){
		$html = '';
		$conceptos = Paqueteconcepto::orderBy('orden')->get();
		if(count($conceptos) > 0){
			$html = '<table width="100%" '.(($admin == true) ? 'class="table table-bordered table-dark table-invoice"' : 'class="table-list"').' '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>
						<thead>
							<tr>
								<th colspan="2"></th>';
			$numeroPaquetes = 0;
			foreach(self::$aseguradoras AS $aseguradora){
				if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
					//$html .= '<th colspan="'.count($aseguradora->paquetes).'" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.$aseguradora->id_aseguradora.' - '.$aseguradora->nombre.'</strong></th>';
					$html .= '<th colspan="'.count($aseguradora->paquetes).'" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.HTML::image('assets/images/aseguradoras/'.$aseguradora->id_aseguradora.'.jpg', '', (($pdf == true) ? ((count($aseguradora->paquetes) == 1) ? array('style' => "width: 100px;") : '') : '') ).'</strong></th>';
					$numeroPaquetes += count($aseguradora->paquetes);
				}
			}
			$html .= '</tr>
						</thead>
						<tr>
							<td colspan="2" width="'.(100/($numeroPaquetes)).'%"></td>';
			foreach(self::$aseguradoras AS $aseguradora){
				if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
					if(count($aseguradora->paquetes) > 0){
						foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
							$html .= '<td class="alignCenter" width="'.(100/($numeroPaquetes+2)).'%" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.$paquete->paquete.'</strong></td>';
						}
					}
				}
			}
			$html .= '</tr>';
			foreach($conceptos AS $concepto){
				$htmlTmp = '<tr>
							<td colspan="2" class="alignLeft" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.(($pdf == true)?utf8_decode($concepto->concepto):$concepto->concepto).'</td>';
				foreach(self::$aseguradoras AS $aseguradora){
					if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
						if(count($aseguradora->paquetes) > 0){
							foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
								$valorConcepto = $paquete->tarifaValor()->where('id_concepto', '=', $concepto->id_concepto)->get();
								if(count($valorConcepto) == 1){
									$valorConcepto = $valorConcepto[0]->{self::$sumaAsegurada.'_'.self::$deducible};
								}
								$htmlTmp .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.(($pdf == true)?utf8_decode($valorConcepto):$valorConcepto).'</td>';
							}
						}
					}
				}
				$htmlTmp .= '</tr>';
				switch ($concepto->id_concepto) {
					case 7:
					case 8:
						if(self::$mostrarMaternidad == true){
							$html .= $htmlTmp;
						}
					break;
					default:
						$html .= $htmlTmp;
					break;
				}
			}
			$html .= '<tr>
						<td colspan="2" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>Pago de contado</strong></td>';
			foreach(self::$aseguradoras AS $aseguradora){
				if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
					if(count($aseguradora->paquetes) > 0){
						foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
							$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.SistemaFunciones::formatMoney1((int)self::$calculos[$paquete->id_paquete]['total']).'</strong></td>';
						}
					}
				}
			}
			$html .= '</tr>';
			if($mostrarContratar == true){
				$html .= '<tr>
							<td colspan="2">&nbsp;</td>';
				foreach(self::$aseguradoras AS $aseguradora){
					if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
						if(count($aseguradora->paquetes) > 0){
							foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
								$html .= '<td><a href="'.URL::to('/contratarPaquete/'.self::$cotizacionDatos->id_cotizacion.'/'.self::$cotizacionDatos->secret.'/'.$paquete->paquete_campo.'/'.self::$sumaAsegurada.'/'.self::$deducible).'" class="but_phone" style="cursor: pointer;"><i class="fa fa-thumbs-o-up"></i> Contratar</a> </td>';
							}
						}
					}
				}
				$html .= '</tr>';
				
			}
			if(!$pdf && $admin == false){
				$html .= '<tr>
						<table width="100%" class="table-list">
							<tr>
								<td colspan="'.($numeroPaquetes+2).'"><a href="#" class="mostrarPreciosMensualidades"><strong>Mostrar pagos en mensualidades</strong></a></td>
							</tr>
						</table>
						<table width="100%" class="table-list tabla-precios hide">';
			}
			foreach(self::$pagosDiferidos AS $meses => $texto){
				$html .= '<tr>
							<td rowspan="2" class="alignVerticalMiddle" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>Pago '.$texto.'</strong></td>
							<td '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>Primer pago</td>';
				foreach(self::$aseguradoras AS $aseguradora){
					if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
						if(count($aseguradora->paquetes) > 0){
							foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
								$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>$ '.SistemaFunciones::formatMoney1((int)self::$calculos[$paquete->id_paquete]['diferidos'][$meses][1]).'</td>';
							}
						}
					}
				}
				$html .= '</tr>
						<tr>
							<td '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>Posteriores</td>';
				foreach(self::$aseguradoras AS $aseguradora){
					if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
						if(count($aseguradora->paquetes) > 0){
							foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
								$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>$ '.SistemaFunciones::formatMoney1((int)self::$calculos[$paquete->id_paquete]['diferidos'][$meses][2]).'</td>';
							}
						}
					}
				}
				$html .= '</tr>';
			}
			if(!$pdf && $admin == false){
				$html .= '</tr>
							</table>';
			}
			$html .= '</table>';
		}
		return $html;
	}

	public static function cotizar(){
		if(count(self::$aseguradoras) > 0 && count(self::$integrantes) > 0){
			self::$calculos = null;
			foreach(self::$aseguradoras AS $aseguradora){
				if(count($aseguradora->paquetes) > 0){
					foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
						foreach(self::$integrantes AS $integrante){
							switch($aseguradora->aseguradora){
								case 'mapfre':
									if($integrante->edad < 60){
										if(!in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
											self::$mostrarAseguradoras[] = $aseguradora->id_aseguradora;
										}
									}
								break;
								default:
									if($integrante->edad < 70){
										if(!in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
											self::$mostrarAseguradoras[] = $aseguradora->id_aseguradora;
										}
									}
								break;
							}
							$tarifa = $paquete->{'Tarifa'.self::$sumaAsegurada.self::$deducible}()->where('edad', '=', $integrante->edad)->get();
							if(count($tarifa) == 1){
								$integrante->paquete[$paquete->id_paquete]['costo'] = $tarifa[0]->{'tarifa_'.$integrante->sexo};
								@self::$calculos[$paquete->id_paquete]['derecho_poliza'] = $paquete->derecho_poliza;
								@self::$calculos[$paquete->id_paquete]['subtotal'] += $integrante->paquete[$paquete->id_paquete]['costo'];
							}
						}
						if($aseguradora->aseguradora == 'metlife'){
							if(count(self::$integrantes) >= 3){
								@self::$calculos[$paquete->id_paquete]['derecho_poliza'] = $paquete->derecho_poliza * 3;
							}else{
								@self::$calculos[$paquete->id_paquete]['derecho_poliza'] = $paquete->derecho_poliza * count(self::$integrantes);
							}
						}
						@self::$calculos[$paquete->id_paquete]['iva'] = ((self::$calculos[$paquete->id_paquete]['subtotal'] + self::$calculos[$paquete->id_paquete]['derecho_poliza']) * .16);
						@self::$calculos[$paquete->id_paquete]['total'] = (self::$calculos[$paquete->id_paquete]['iva'] + self::$calculos[$paquete->id_paquete]['subtotal'] + self::$calculos[$paquete->id_paquete]['derecho_poliza']);
						foreach(self::$pagosDiferidos AS $meses => $texto){
							@self::$calculos[$paquete->id_paquete]['diferidos'][$meses][1] = self::calcularPagosDiferidos(self::$calculos[$paquete->id_paquete]['total'], $paquete->derecho_poliza, $aseguradora->configuracion->{'interes_'.$texto}, $meses, 1);
							@self::$calculos[$paquete->id_paquete]['diferidos'][$meses][2] = self::calcularPagosDiferidos(self::$calculos[$paquete->id_paquete]['total'], $paquete->derecho_poliza, $aseguradora->configuracion->{'interes_'.$texto}, $meses, 2);
						}
					}
				}
			}
		}
	}

	private static function calcularPagosDiferidos($total, $derecho_poliza, $interes, $diferido, $n_pago){
        $pago = (($total - ($derecho_poliza * 1.16)) * ($interes / 100 + 1) / (12 / $diferido));
        if($n_pago == 1){
            $pago += $derecho_poliza * 1.16;
        }
        return $pago;
    }

	private static function getAseguradoras(){
		//En caso de error relativo al orden los paquetes se debe borrar el ordeby(id_paquete ) y id_aseguradora
		//en cambio poner orderBy(nombre) 
		self::$aseguradoras = Aseguradora::where('activa', '=', 1)->orderBy('id_aseguradora', 'DESC')->get();
		if(count(self::$aseguradoras) > 0){
			foreach(self::$aseguradoras AS $aseguradora){
				$aseguradora->configuracion = json_decode($aseguradora->configuracion);
				$paquetes = Paquete::where('id_aseguradora', '=', $aseguradora->id_aseguradora)->where('activo', '=', 1)->orderBy('id_paquete', 'DESC')->get();
				if(count($paquetes) > 0){
					$aseguradora->paquetes = $paquetes;
				}
			}
		}
	}
	
	//PDF 2016
	public static function tablaDatos2016($admin = false){
		$html = '';
		$conceptos = Paqueteconcepto::orderBy('orden')->get();
		if(count($conceptos) > 0){
			$html = '<table class="table table-bordered">
						<thead>
							<tr>
								<th  colspan="2"></th>';
			foreach(self::$aseguradoras AS $aseguradora){
				$html .= '<th colspan="'.count($aseguradora->paquetes).'" class="text-center"><strong>'.$aseguradora->nombre.'</strong></th>';
			}
			$html .= '</tr>
						</thead>
						<tr class="text-left">
							<td colspan="2"></td>';
			foreach(self::$aseguradoras AS $aseguradora){
				if(count($aseguradora->paquetes) > 0){
					foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
						$html .= '<td class="alignCenter"><strong>'.$paquete->paquete.'</strong></td>';
					}
				}
			}
			$html .= '</tr>';
			foreach($conceptos AS $concepto){
				$htmlTmp = '<tr>
							<td class="text-left" colspan="2">'.$concepto->concepto.'</td>';
				foreach(self::$aseguradoras AS $aseguradora){
					if(count($aseguradora->paquetes) > 0){
						foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
							$valorConcepto = $paquete->tarifaValor()->where('id_concepto', '=', $concepto->id_concepto)->get();
							if(count($valorConcepto) == 1){
								$valorConcepto = $valorConcepto[0]->{self::$sumaAsegurada.'_'.self::$deducible};
							}
							$htmlTmp .= '<td class="alignCenter">'.$valorConcepto.'</td>';
							/*
							if($concepto->id_concepto == 2 && $aseguradora->id_aseguradora == 2){
								$htmlTmp .= '<td class="alignCenter">'.self::$deducibleEnfermedadMapfre.'</td>';
							}else{
								$htmlTmp .= '<td class="alignCenter">'.$valorConcepto.'</td>';
							}
							*/
						}
					}
				}
				$htmlTmp .= '</tr>';
				switch ($concepto->id_concepto) {
					case 7:
					case 8:
						if(self::$mostrarMaternidad == true){
							$html .= $htmlTmp;
						}
					break;
					default:
						$html .= $htmlTmp;
					break;
				}
			}
			$html .= '<tr>
						<td colspan="2"><strong>Prima total anual</strong></td>';
			foreach(self::$aseguradoras AS $aseguradora){
				if(count($aseguradora->paquetes) > 0){
					foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
						$html .= '<td class="alignCenter"><strong>'.SistemaFunciones::formatMoney1((int)self::$calculos[$paquete->id_paquete]['total']).'</strong></td>';
					}
				}
			}
			$html .= '</tr>';
			/*foreach(self::$pagosDiferidos AS $meses => $texto){
				$html .= '<tr>
							<td rowspan="2" class="alignVerticalMiddle"><strong> '.$texto.'</strong></td>
							<td>Primer pago</td>';
				foreach(self::$aseguradoras AS $aseguradora){
					if(count($aseguradora->paquetes) > 0){
						foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
							$html .= '<td class="alignCenter">'.SistemaFunciones::formatMoney1((int)self::$calculos[$paquete->id_paquete]['diferidos'][$meses][1]).'</td>';
						}
					}
				}
				$html .= '</tr>
						<tr>
							<td>Posteriores</td>';
				foreach(self::$aseguradoras AS $aseguradora){
					if(count($aseguradora->paquetes) > 0){
						foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
							$html .= '<td class="alignCenter">'.SistemaFunciones::formatMoney1((int)self::$calculos[$paquete->id_paquete]['diferidos'][$meses][2]).'</td>';
						}
					}
				}
				$html .= '</tr>';
			}*/
			$html .= '</table>';
		}
		return $html;
	}
	
	public static function tablaClienteDatos2016($admin = false){
		$textoInicio = DB::table('texto_pdf')->where('texto_seccion', 'saludo_bienvenida')->select('texto_pdf')->get()[0];
		$html = "<div class='container-fluid'> 
					<p class='text-right'>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				 ".sistemaFunciones::fechaLetras(explode(" ",self::$cotizacionDatos->fecha_registro)[0]) ."  Guadalajara, Jalisco</br></br>
					</p>
					
					<h4>Hola ". self::$cotizacionDatos->nombre . " :</h4>   
					<br>
						". $textoInicio->texto_pdf ."				
				</div>";
		
		return $html;
	}


	public static function tablaIntegrantes2016($admin = false){
		$tr = '';
		$integrantesOrden = array();
		$n = 2;
		foreach(self::$integrantes AS $integrante){
			switch($integrante->titulo){
				case 'titular':
					$integrantesOrden[0] = $integrante;
				break;
				case 'conyugue':
					$integrantesOrden[1] = $integrante;
				break;
				default:
					$integrantesOrden[$n] = $integrante;
					$n++;
				break;
			}
		}
		self::$integrantes = $integrantesOrden;
		ksort(self::$integrantes);
		foreach(self::$integrantes AS $integrante){
			$tr .= '<tr>
						<td class="alignLeft">'.ucwords(strtolower($integrante->nombre)).'</td>
						<td class="alignCenter">'.(($integrante->sexo == 'm') ? 'Hombre' : 'Mujer').'</td>
						<td class="alignCenter">'.$integrante->edad.'</td>
						<td class="alignCenter">'.ucfirst($integrante->titulo).'</td>
					</tr>';
		}//<h3><strong>Integrantes</strong></h3>
		$html = '<div class="one_half last">
						
						<table width="100%" '.(($admin == true) ? 'class="table table-bordered table-dark table-invoice"' : 'class="table table-responsive table-condensed"').'>
							<thead>
								<tr>
									<th class="alignCenter">Nombre</th>
									<th class="alignCenter">Sexo</th>
									<th class="alignCenter">Edad</th>
									<th class="alignCenter">Titulo</th>
								</tr>
							</thead>
							<tbody>
								'.$tr.'
							</tbody>
						</table></br>
				</div>';
		return $html;
	}

	public static function datosTabla($admin = false, $mostrarContratar = true, $pdf = false){
		//$html = '';
		$datos = array();
		$datos["aseguradoras"] = array();
		$datos["tablas"] = array();
		$datos["tablas"][] = array();
		$datos["tablas"][0][] = '';
		$datos["tablas"][0][] = '';
		$datos["tablas"][0][] = '';
		$datos["pagos"] = array();
		$datos["pagos"][] = array();
		if(self::$mostrarMaternidad){
			$conceptos = Paqueteconcepto::where('id_concepto', '<>', 8)
				->orderBy('orden')->get();
		}else{
			$conceptos = Paqueteconcepto::where('id_concepto', '<>', 8)
				->where('id_concepto', '<>', 7)
				->orderBy('orden')->get();
		}	
		if(count($conceptos) > 0){
			//$html = '<table width="100%" '.(($admin == true) ? 'class="table table-bordered table-dark table-invoice"' : 'class="table-list"').' '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>
			//			<thead>
			//				<tr>
			//					<th colspan="2"></th>';
			//$numeroPaquetes = 0;
			foreach(self::$aseguradoras AS $aseguradora){
				if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
					//$html .= '<th colspan="'.count($aseguradora->paquetes).'" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.$aseguradora->id_aseguradora.' - '.$aseguradora->nombre.'</strong></th>';
					//$html .= '<th colspan="'.count($aseguradora->paquetes).'" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.HTML::image('assets/images/aseguradoras/'.$aseguradora->id_aseguradora.'.jpg', '', (($pdf == true) ? ((count($aseguradora->paquetes) == 1) ? array('style' => "width: 100px;") : '') : '') ).'</strong></th>';
					//$numeroPaquetes += count($aseguradora->paquetes);
					$datos["aseguradoras"][] = array(
						"id" => $aseguradora->id_aseguradora,
						"nombre" => $aseguradora->nombre,
						"paquetes" => count($aseguradora->paquetes),
						"web" => $aseguradora->descripcion_web,
						"movil" => $aseguradora->descripcion_movil,
						"logo" => $aseguradora->configuracion->logo
					);
				}
			}
			//$html .= '</tr>
			//			</thead>
			//			<tr>
			//				<td colspan="2" width="'.(100/($numeroPaquetes)).'%"></td>';
			$x = 1;
			foreach(self::$aseguradoras AS $aseguradora){
				if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
					if(count($aseguradora->paquetes) > 0){
						foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
							//$html .= '<td class="alignCenter" width="'.(100/($numeroPaquetes+2)).'%" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.$paquete->paquete.'</strong></td>';
							$datos["tablas"][] = array();
							$datos["tablas"][$x][] = $paquete->id_paquete;
							$datos["tablas"][$x][] = $paquete->paquete_campo;
							$datos["tablas"][$x][] = $paquete->paquete;
							$datos["pagos"][] = array();
							$x++;
						}
					}
				}
			}
			//$html .= '</tr>';
			foreach($conceptos AS $concepto){
				//$htmlTmp = '<tr>
				//			<td colspan="2" class="alignLeft" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.(($pdf == true)?utf8_decode($concepto->concepto):$concepto->concepto).'</td>';
				$datos["tablas"][0][] = $concepto->concepto;
				$x = 1;
				foreach(self::$aseguradoras AS $aseguradora){
					if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
						if(count($aseguradora->paquetes) > 0){
							foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
								$valorConcepto = $paquete->tarifaValor()->where('id_concepto', '=', $concepto->id_concepto)->get();
								if(count($valorConcepto) == 1){
									$valorConcepto = $valorConcepto[0]->{self::$sumaAsegurada.'_'.self::$deducible};
								}
								//$htmlTmp .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.(($pdf == true)?utf8_decode($valorConcepto):$valorConcepto).'</td>';
								$datos["tablas"][$x][] = $valorConcepto;
								$x++;
							}
						}
					}
				}
				//$htmlTmp .= '</tr>';
				//switch ($concepto->id_concepto) {
				//	case 7:
				//	case 8:
				//		if(self::$mostrarMaternidad == true){
				//			$html .= $htmlTmp;
				//		}
				//	break;
				//	default:
				//		$html .= $htmlTmp;
				//	break;
				//}
			}
			//$html .= '<tr>
			//			<td colspan="2" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>Pago de contado</strong></td>';
			$datos["tablas"][0][] = "Pago de contado";
			$x = 1;
			foreach(self::$aseguradoras AS $aseguradora){
				if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
					if(count($aseguradora->paquetes) > 0){
						foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
							//$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.SistemaFunciones::formatMoney1((int)self::$calculos[$paquete->id_paquete]['total']).'</strong></td>';
							$datos["tablas"][$x][] = SistemaFunciones::formatMoney1((int)self::$calculos[$paquete->id_paquete]['total']);
							$x++;
						}
					}
				}
			}
			//$html .= '</tr>';
			//if($mostrarContratar == true){
			//	$html .= '<tr>
			//				<td colspan="2">&nbsp;</td>';
			//	foreach(self::$aseguradoras AS $aseguradora){
			//		if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
			//			if(count($aseguradora->paquetes) > 0){
			//				foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
			//					$html .= '<td><a href="'.URL::to('/contratarPaquete/'.self::$cotizacionDatos->id_cotizacion.'/'.self::$cotizacionDatos->secret.'/'.$paquete->paquete_campo.'/'.self::$sumaAsegurada.'/'.self::$deducible).'" class="but_phone" style="cursor: pointer;"><i class="fa fa-thumbs-o-up"></i> Contratar</a> </td>';
			//				}
			//			}
			//		}
			//	}
			//	$html .= '</tr>';
			//	
			//}
			//if(!$pdf && $admin == false){
			//	$html .= '<tr>
			//			<table width="100%" class="table-list">
			//				<tr>
			//					<td colspan="'.($numeroPaquetes+2).'"><a href="#" class="mostrarPreciosMensualidades"><strong>Mostrar pagos en mensualidades</strong></a></td>
			//				</tr>
			//			</table>
			//			<table width="100%" class="table-list tabla-precios hide">';
			//}
			foreach(self::$pagosDiferidos AS $meses => $texto){
				//$html .= '<tr>
				//			<td rowspan="2" class="alignVerticalMiddle" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>Pago '.$texto.'</strong></td>
				//			<td '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>Primer pago</td>';
				$datos["pagos"][0][] = "Pago " . $texto;
				$datos["pagos"][0][] = "Primer pago";
				$datos["pagos"][0][] = "Posteriores";
				$x = 1;
				foreach(self::$aseguradoras AS $aseguradora){
					if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
						if(count($aseguradora->paquetes) > 0){
							foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
								//$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>$ '.SistemaFunciones::formatMoney1((int)self::$calculos[$paquete->id_paquete]['diferidos'][$meses][1]).'</td>';
								$datos["pagos"][$x][] = SistemaFunciones::formatMoney1((int)self::$calculos[$paquete->id_paquete]['diferidos'][$meses][1]);
								$x++;
							}
						}
					}
				}
				//$html .= '</tr>
				//		<tr>
				//			<td '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>Posteriores</td>';
				$x = 1;
				foreach(self::$aseguradoras AS $aseguradora){
					if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
						if(count($aseguradora->paquetes) > 0){
							foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
								//$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>$ '.SistemaFunciones::formatMoney1((int)self::$calculos[$paquete->id_paquete]['diferidos'][$meses][2]).'</td>';
								$datos["pagos"][$x][] = SistemaFunciones::formatMoney1((int)self::$calculos[$paquete->id_paquete]['diferidos'][$meses][2]);
								$x++;
							}
						}
					}
				}
				//$html .= '</tr>';
			}
			//if(!$pdf && $admin == false){
			//	$html .= '</tr>
			//				</table>';
			//}
			//$html .= '</table>';
		}
		//return $html;
		return $datos;
	}

	public static function cotizarWS(){
		if(count(self::$aseguradoras) > 0 && count(self::$integrantes) > 0){
			self::$calculos = null;
			if(self::$cotizacionDatos->estado=="Jalisco"){
				foreach(self::$aseguradoras AS $aseguradora){
					if(count($aseguradora->paquetes) > 0){
						foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
							foreach(self::$integrantes AS $integrante){
								switch($aseguradora->aseguradora){
									case 'mapfre':
										//if($integrante->edad < 60){
											if(!in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
												self::$mostrarAseguradoras[] = $aseguradora->id_aseguradora;
											}
										//}
									break;
									default:
										if($integrante->edad < 70){
											if(!in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
												self::$mostrarAseguradoras[] = $aseguradora->id_aseguradora;
											}
										}
									break;
								}
								$tarifa = $paquete->{'Tarifa'.self::$sumaAsegurada.self::$deducible}()->where('edad', '=', $integrante->edad)->get();
								if(count($tarifa) == 1){
									$integrante->paquete[$paquete->id_paquete]['costo'] = $tarifa[0]->{'tarifa_'.$integrante->sexo};
									@self::$calculos[$paquete->id_paquete]['derecho_poliza'] = $paquete->derecho_poliza;
									@self::$calculos[$paquete->id_paquete]['subtotal'] += $integrante->paquete[$paquete->id_paquete]['costo'];
								}
							}
							if($aseguradora->aseguradora == 'metlife'){
								if(count(self::$integrantes) >= 3){
									@self::$calculos[$paquete->id_paquete]['derecho_poliza'] = $paquete->derecho_poliza * 3;
								}else{
									@self::$calculos[$paquete->id_paquete]['derecho_poliza'] = $paquete->derecho_poliza * count(self::$integrantes);
								}
							}
							@self::$calculos[$paquete->id_paquete]['iva'] = ((self::$calculos[$paquete->id_paquete]['subtotal'] + self::$calculos[$paquete->id_paquete]['derecho_poliza']) * .16);
							@self::$calculos[$paquete->id_paquete]['total'] = (self::$calculos[$paquete->id_paquete]['iva'] + self::$calculos[$paquete->id_paquete]['subtotal'] + self::$calculos[$paquete->id_paquete]['derecho_poliza']);
							foreach(self::$pagosDiferidos AS $meses => $texto){
								@self::$calculos[$paquete->id_paquete]['diferidos'][$meses][1] = self::calcularPagosDiferidos(self::$calculos[$paquete->id_paquete]['total'], $paquete->derecho_poliza, $aseguradora->configuracion->{'interes_'.$texto}, $meses, 1);
								@self::$calculos[$paquete->id_paquete]['diferidos'][$meses][2] = self::calcularPagosDiferidos(self::$calculos[$paquete->id_paquete]['total'], $paquete->derecho_poliza, $aseguradora->configuracion->{'interes_'.$texto}, $meses, 2);
							}
						}
					}
				}
			}
			else{
				foreach(self::$aseguradoras AS $aseguradora){
					if($aseguradora->aseguradora=="mapfre"){
						if(count($aseguradora->paquetes) > 0){
							foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
								foreach(self::$integrantes AS $integrante){
									switch($aseguradora->aseguradora){
										case 'mapfre':
											//if($integrante->edad < 60){
												if(!in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
													self::$mostrarAseguradoras[] = $aseguradora->id_aseguradora;
												}
											//}
										break;
										default:
											if($integrante->edad < 70){
												if(!in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
													self::$mostrarAseguradoras[] = $aseguradora->id_aseguradora;
												}
											}
										break;
									}
									$tarifa = $paquete->{'Tarifa'.self::$sumaAsegurada.self::$deducible}()->where('edad', '=', $integrante->edad)->get();
									if(count($tarifa) == 1){
										$integrante->paquete[$paquete->id_paquete]['costo'] = $tarifa[0]->{'tarifa_'.$integrante->sexo};
										@self::$calculos[$paquete->id_paquete]['derecho_poliza'] = $paquete->derecho_poliza;
										@self::$calculos[$paquete->id_paquete]['subtotal'] += $integrante->paquete[$paquete->id_paquete]['costo'];
									}
								}
								if($aseguradora->aseguradora == 'metlife'){
									if(count(self::$integrantes) >= 3){
										@self::$calculos[$paquete->id_paquete]['derecho_poliza'] = $paquete->derecho_poliza * 3;
									}else{
										@self::$calculos[$paquete->id_paquete]['derecho_poliza'] = $paquete->derecho_poliza * count(self::$integrantes);
									}
								}
								@self::$calculos[$paquete->id_paquete]['iva'] = ((self::$calculos[$paquete->id_paquete]['subtotal'] + self::$calculos[$paquete->id_paquete]['derecho_poliza']) * .16);
								@self::$calculos[$paquete->id_paquete]['total'] = (self::$calculos[$paquete->id_paquete]['iva'] + self::$calculos[$paquete->id_paquete]['subtotal'] + self::$calculos[$paquete->id_paquete]['derecho_poliza']);
								foreach(self::$pagosDiferidos AS $meses => $texto){
									@self::$calculos[$paquete->id_paquete]['diferidos'][$meses][1] = self::calcularPagosDiferidos(self::$calculos[$paquete->id_paquete]['total'], $paquete->derecho_poliza, $aseguradora->configuracion->{'interes_'.$texto}, $meses, 1);
									@self::$calculos[$paquete->id_paquete]['diferidos'][$meses][2] = self::calcularPagosDiferidos(self::$calculos[$paquete->id_paquete]['total'], $paquete->derecho_poliza, $aseguradora->configuracion->{'interes_'.$texto}, $meses, 2);
								}
							}
						}
					}
				}
			}
		}
	}

	public static function datosTablaWS($admin = false, $mostrarContratar = true, $pdf = false){
		//$html = '';
		$datos = array();
		$datos["aseguradoras"] = array();
		$datos["tablas"] = array();
		$datos["tablas"][] = array();
		$datos["tablas"][0][] = '';
		$datos["tablas"][0][] = '';
		$datos["tablas"][0][] = '';
		$datos["pagos"] = array();
		$datos["pagos"][] = array();
		if(self::$mostrarMaternidad){
			$conceptos = Paqueteconcepto::where('id_concepto', '<>', 8)
				->orderBy('orden')->get();
		}else{
			$conceptos = Paqueteconcepto::where('id_concepto', '<>', 8)
				->where('id_concepto', '<>', 7)
				->orderBy('orden')->get();
		}	
		if(count($conceptos) > 0){
			//$html = '<table width="100%" '.(($admin == true) ? 'class="table table-bordered table-dark table-invoice"' : 'class="table-list"').' '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>
			//			<thead>
			//				<tr>
			//					<th colspan="2"></th>';
			//$numeroPaquetes = 0;
			foreach(self::$aseguradoras AS $aseguradora){
				if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
					//$html .= '<th colspan="'.count($aseguradora->paquetes).'" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.$aseguradora->id_aseguradora.' - '.$aseguradora->nombre.'</strong></th>';
					//$html .= '<th colspan="'.count($aseguradora->paquetes).'" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.HTML::image('assets/images/aseguradoras/'.$aseguradora->id_aseguradora.'.jpg', '', (($pdf == true) ? ((count($aseguradora->paquetes) == 1) ? array('style' => "width: 100px;") : '') : '') ).'</strong></th>';
					//$numeroPaquetes += count($aseguradora->paquetes);
					$datos["aseguradoras"][] = array(
						"id" => $aseguradora->id_aseguradora,
						"nombre" => $aseguradora->nombre,
						"paquetes" => count($aseguradora->paquetes),
						"web" => $aseguradora->descripcion_web,
						"movil" => $aseguradora->descripcion_movil,
						"logo" => $aseguradora->configuracion->logo,
						"aseguradora" => $aseguradora->aseguradora
					);
				}
			}
			//$html .= '</tr>
			//			</thead>
			//			<tr>
			//				<td colspan="2" width="'.(100/($numeroPaquetes)).'%"></td>';
			$x = 1;
			foreach(self::$aseguradoras AS $aseguradora){
				if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
					if(count($aseguradora->paquetes) > 0){
						foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
							//$html .= '<td class="alignCenter" width="'.(100/($numeroPaquetes+2)).'%" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.$paquete->paquete.'</strong></td>';
							$datos["tablas"][] = array();
							$datos["tablas"][$x][] = $paquete->id_paquete;
							$datos["tablas"][$x][] = $paquete->paquete_campo;
							$datos["tablas"][$x][] = $paquete->paquete;
							$datos["pagos"][] = array();
							$x++;
						}
					}
				}
			}
			//$html .= '</tr>';
			foreach($conceptos AS $concepto){
				//$htmlTmp = '<tr>
				//			<td colspan="2" class="alignLeft" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.(($pdf == true)?utf8_decode($concepto->concepto):$concepto->concepto).'</td>';
				$datos["tablas"][0][] = $concepto->concepto;
				$x = 1;
				foreach(self::$aseguradoras AS $aseguradora){
					if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
						if(count($aseguradora->paquetes) > 0){
							foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
								$valorConcepto = $paquete->tarifaValor()->where('id_concepto', '=', $concepto->id_concepto)->get();
								if(count($valorConcepto) == 1){
									$valorConcepto = $valorConcepto[0]->{self::$sumaAsegurada.'_'.self::$deducible};
								}
								//$htmlTmp .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.(($pdf == true)?utf8_decode($valorConcepto):$valorConcepto).'</td>';
								$datos["tablas"][$x][] = $valorConcepto;
								$x++;
							}
						}
					}
				}
				//$htmlTmp .= '</tr>';
				//switch ($concepto->id_concepto) {
				//	case 7:
				//	case 8:
				//		if(self::$mostrarMaternidad == true){
				//			$html .= $htmlTmp;
				//		}
				//	break;
				//	default:
				//		$html .= $htmlTmp;
				//	break;
				//}
			}
			//$html .= '<tr>
			//			<td colspan="2" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>Pago de contado</strong></td>';
			$datos["tablas"][0][] = "Pago de contado";
			$x = 1;
			foreach(self::$aseguradoras AS $aseguradora){
				if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
					if(count($aseguradora->paquetes) > 0){
						foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
							//$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.SistemaFunciones::formatMoney1((int)self::$calculos[$paquete->id_paquete]['total']).'</strong></td>';
							if($aseguradora->aseguradora!="mapfre")
								$datos["tablas"][$x][] = SistemaFunciones::formatMoney1((int)self::$calculos[$paquete->id_paquete]['total']);
							else{
								$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
									->where("tipo", "=", self::$sumaAsegurada . self::$deducible)
									->where("hospitales", "=", $paquete->paquete_campo)
									->first();
								if($recotizacion)
									$datos["tablas"][$x][] = number_format($recotizacion->contado, 2);
								else
									$datos["tablas"][$x][] = -1;
							}
							$x++;
						}
					}
				}
			}
			//$html .= '</tr>';
			//if($mostrarContratar == true){
			//	$html .= '<tr>
			//				<td colspan="2">&nbsp;</td>';
			//	foreach(self::$aseguradoras AS $aseguradora){
			//		if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
			//			if(count($aseguradora->paquetes) > 0){
			//				foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
			//					$html .= '<td><a href="'.URL::to('/contratarPaquete/'.self::$cotizacionDatos->id_cotizacion.'/'.self::$cotizacionDatos->secret.'/'.$paquete->paquete_campo.'/'.self::$sumaAsegurada.'/'.self::$deducible).'" class="but_phone" style="cursor: pointer;"><i class="fa fa-thumbs-o-up"></i> Contratar</a> </td>';
			//				}
			//			}
			//		}
			//	}
			//	$html .= '</tr>';
			//	
			//}
			//if(!$pdf && $admin == false){
			//	$html .= '<tr>
			//			<table width="100%" class="table-list">
			//				<tr>
			//					<td colspan="'.($numeroPaquetes+2).'"><a href="#" class="mostrarPreciosMensualidades"><strong>Mostrar pagos en mensualidades</strong></a></td>
			//				</tr>
			//			</table>
			//			<table width="100%" class="table-list tabla-precios hide">';
			//}
			foreach(self::$pagosDiferidos AS $meses => $texto){
				//$html .= '<tr>
				//			<td rowspan="2" class="alignVerticalMiddle" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>Pago '.$texto.'</strong></td>
				//			<td '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>Primer pago</td>';
				$datos["pagos"][0][] = "Pago " . $texto;
				$datos["pagos"][0][] = "Primer pago";
				$datos["pagos"][0][] = "Posteriores";
				$x = 1;
				foreach(self::$aseguradoras AS $aseguradora){
					if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
						if(count($aseguradora->paquetes) > 0){
							foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
								//$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>$ '.SistemaFunciones::formatMoney1((int)self::$calculos[$paquete->id_paquete]['diferidos'][$meses][1]).'</td>';
								if($aseguradora->aseguradora!="mapfre")
									$datos["pagos"][$x][] = SistemaFunciones::formatMoney1((int)self::$calculos[$paquete->id_paquete]['diferidos'][$meses][1]);
								else{
									$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
										->where("tipo", "=", self::$sumaAsegurada . self::$deducible)
										->where("hospitales", "=", $paquete->paquete_campo)
										->first();
									if($recotizacion){
										switch($meses){
											case 6:
												$datos["pagos"][$x][] = number_format($recotizacion->semestral_primer, 2);
												break;
											case 3:
												$datos["pagos"][$x][] = number_format($recotizacion->trimestral_primer, 2);
												break;
											case 1:
												$datos["pagos"][$x][] = number_format($recotizacion->mensual_primer, 2);
												break;
										}
									}
									else
										$datos["pagos"][$x][] = -1;
								}
								$x++;
							}
						}
					}
				}
				//$html .= '</tr>
				//		<tr>
				//			<td '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>Posteriores</td>';
				$x = 1;
				foreach(self::$aseguradoras AS $aseguradora){
					if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
						if(count($aseguradora->paquetes) > 0){
							foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
								//$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>$ '.SistemaFunciones::formatMoney1((int)self::$calculos[$paquete->id_paquete]['diferidos'][$meses][2]).'</td>';
								if($aseguradora->aseguradora!="mapfre")
									$datos["pagos"][$x][] = SistemaFunciones::formatMoney1((int)self::$calculos[$paquete->id_paquete]['diferidos'][$meses][2]);
								else{
									$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
										->where("tipo", "=", self::$sumaAsegurada . self::$deducible)
										->where("hospitales", "=", $paquete->paquete_campo)
										->first();
									if($recotizacion){
										switch($meses){
											case 6:
												$datos["pagos"][$x][] = number_format($recotizacion->semestral_posterior, 2);
												break;
											case 3:
												$datos["pagos"][$x][] = number_format($recotizacion->trimestral_posterior, 2);
												break;
											case 1:
												$datos["pagos"][$x][] = number_format($recotizacion->mensual_posterior, 2);
												break;
										}
									}
									else
										$datos["pagos"][$x][] = -1;
								}
								$x++;
							}
						}
					}
				}
				//$html .= '</tr>';
			}
			//if(!$pdf && $admin == false){
			//	$html .= '</tr>
			//				</table>';
			//}
			//$html .= '</table>';
		}
		//return $html;
		return $datos;
	}
	
	public static function datosTablaWS2023($admin = false, $mostrarContratar = true, $pdf = false){
		//$html = '';
		$datos = array();
		$datos["aseguradoras"] = array();
		$datos["tablas"] = array();
		$datos["tablas"][] = array();
		$datos["tablas"][0][] = '';
		$datos["tablas"][0][] = '';
		$datos["tablas"][0][] = '';
		$datos["pagos"] = array();
		$datos["pagos"][] = array();
		if(self::$mostrarMaternidad){
			$conceptos = Paqueteconcepto2023::where('id_concepto', '<>', 8)
				->where('id_concepto', '<>', 5)
				->where('id_concepto', '<>', 6)
				->orderBy('orden')->get();
		}else{
			$conceptos = Paqueteconcepto2023::where('id_concepto', '<>', 8)
				->where('id_concepto', '<>', 5)
				->where('id_concepto', '<>', 6)
				->where('id_concepto', '<>', 7)
				->orderBy('orden')->get();
		}	
		if(count($conceptos) > 0){
			//$html = '<table width="100%" '.(($admin == true) ? 'class="table table-bordered table-dark table-invoice"' : 'class="table-list"').' '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>
			//			<thead>
			//				<tr>
			//					<th colspan="2"></th>';
			//$numeroPaquetes = 0;
			foreach(self::$aseguradoras AS $aseguradora){
				if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
					//$html .= '<th colspan="'.count($aseguradora->paquetes).'" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.$aseguradora->id_aseguradora.' - '.$aseguradora->nombre.'</strong></th>';
					//$html .= '<th colspan="'.count($aseguradora->paquetes).'" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.HTML::image('assets/images/aseguradoras/'.$aseguradora->id_aseguradora.'.jpg', '', (($pdf == true) ? ((count($aseguradora->paquetes) == 1) ? array('style' => "width: 100px;") : '') : '') ).'</strong></th>';
					//$numeroPaquetes += count($aseguradora->paquetes);
					$paqs = 1;
					if(self::$cotizacionDatos->nivel_amplio==1)
						$paqs = 2;
					if($aseguradora->id_aseguradora==2){
						$datos["aseguradoras"][] = array(
							"id" => $aseguradora->id_aseguradora,
							"nombre" => $aseguradora->nombre,
							"paquetes" => count($aseguradora->paquetes) + $paqs,
							"web" => $aseguradora->descripcion_web,
							"movil" => $aseguradora->descripcion_movil,
							"logo" => $aseguradora->configuracion->logo,
							"aseguradora" => $aseguradora->aseguradora,
							"inflar" => $aseguradora->configuracion->inflar
						);
					}
					else{
						$datos["aseguradoras"][] = array(
							"id" => $aseguradora->id_aseguradora,
							"nombre" => $aseguradora->nombre,
							"paquetes" => count($aseguradora->paquetes),
							"web" => $aseguradora->descripcion_web,
							"movil" => $aseguradora->descripcion_movil,
							"logo" => $aseguradora->configuracion->logo,
							"aseguradora" => $aseguradora->aseguradora,
							"inflar" => $aseguradora->configuracion->inflar
						);
					}
				}
			}
			//$html .= '</tr>
			//			</thead>
			//			<tr>
			//				<td colspan="2" width="'.(100/($numeroPaquetes)).'%"></td>';
			$x = 1;
			foreach(self::$aseguradoras AS $aseguradora){
				if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
					if(count($aseguradora->paquetes) > 0){
						if($aseguradora->id_aseguradora==2){
							if(self::$cotizacionDatos->nivel_amplio==0){
								foreach($aseguradora->paquetes()->where('activo', '=', 1)->orWhere('id_paquete', '=', 10)->orderBy('orden')->get() AS $paquete){
									//$html .= '<td class="alignCenter" width="'.(100/($numeroPaquetes+2)).'%" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.$paquete->paquete.'</strong></td>';
									$datos["tablas"][] = array();
									$datos["tablas"][$x][] = $paquete->id_paquete;
									$datos["tablas"][$x][] = $paquete->paquete_campo;
									$datos["tablas"][$x][] = $paquete->paquete;
									$datos["pagos"][] = array();
									$x++;
								}
							}
							else{
								foreach($aseguradora->paquetes()->where('activo', '=', 1)
									->orWhere('id_paquete', '=', 10)
									->orWhere('id_paquete', '=', 11)
									->orderBy('orden')->get() AS $paquete){
									//$html .= '<td class="alignCenter" width="'.(100/($numeroPaquetes+2)).'%" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.$paquete->paquete.'</strong></td>';
									$datos["tablas"][] = array();
									$datos["tablas"][$x][] = $paquete->id_paquete;
									$datos["tablas"][$x][] = $paquete->paquete_campo;
									$datos["tablas"][$x][] = $paquete->paquete;
									$datos["pagos"][] = array();
									$x++;
								}
							}
						}
						else{
							foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
								//$html .= '<td class="alignCenter" width="'.(100/($numeroPaquetes+2)).'%" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.$paquete->paquete.'</strong></td>';
								$datos["tablas"][] = array();
								$datos["tablas"][$x][] = $paquete->id_paquete;
								$datos["tablas"][$x][] = $paquete->paquete_campo;
								$datos["tablas"][$x][] = $paquete->paquete;
								$datos["pagos"][] = array();
								$x++;
							}
						}
					}
				}
			}
			//$html .= '</tr>';
			foreach($conceptos AS $concepto){
				//$htmlTmp = '<tr>
				//			<td colspan="2" class="alignLeft" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.(($pdf == true)?utf8_decode($concepto->concepto):$concepto->concepto).'</td>';
				$datos["tablas"][0][] = $concepto->concepto . "|" . $concepto->tooltip;
				$x = 1;
				foreach(self::$aseguradoras AS $aseguradora){
					if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
						if(count($aseguradora->paquetes) > 0){
							if($aseguradora->id_aseguradora==2){
								if(self::$cotizacionDatos->nivel_amplio==0){
									foreach($aseguradora->paquetes()->where('activo', '=', 1)->orWhere('id_paquete', '=', 10)->orderBy('orden')->get() AS $paquete){
										$valorConcepto = $paquete->tarifaValor2023()->where('id_concepto', '=', $concepto->id_concepto)->get();
										if(count($valorConcepto) == 1){
											$valorConcepto = $valorConcepto[0]->{'sa_db'};
										}
										//$htmlTmp .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.(($pdf == true)?utf8_decode($valorConcepto):$valorConcepto).'</td>';
										$datos["tablas"][$x][] = $concepto->id_concepto . "|" . $valorConcepto;
										$x++;
									}
								}
								else{
									foreach($aseguradora->paquetes()->where('activo', '=', 1)
										->orWhere('id_paquete', '=', 10)
										->orWhere('id_paquete', '=', 11)
										->orderBy('orden')->get() AS $paquete){
										$valorConcepto = $paquete->tarifaValor2023()->where('id_concepto', '=', $concepto->id_concepto)->get();
										if(count($valorConcepto) == 1){
											$valorConcepto = $valorConcepto[0]->{'sa_db'};
										}
										//$htmlTmp .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.(($pdf == true)?utf8_decode($valorConcepto):$valorConcepto).'</td>';
										$datos["tablas"][$x][] = $concepto->id_concepto . "|" . $valorConcepto;
										$x++;
									}
								}
							}
							else{
								foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
									$valorConcepto = $paquete->tarifaValor2023()->where('id_concepto', '=', $concepto->id_concepto)->get();
									if(count($valorConcepto) == 1){
										$valorConcepto = $valorConcepto[0]->{self::$sumaAsegurada.'_'.self::$deducible};
									}
									//$htmlTmp .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.(($pdf == true)?utf8_decode($valorConcepto):$valorConcepto).'</td>';
									$datos["tablas"][$x][] = $concepto->id_concepto . "|" . $valorConcepto;
									$x++;
								}
							}
						}
					}
				}
				//$htmlTmp .= '</tr>';
				//switch ($concepto->id_concepto) {
				//	case 7:
				//	case 8:
				//		if(self::$mostrarMaternidad == true){
				//			$html .= $htmlTmp;
				//		}
				//	break;
				//	default:
				//		$html .= $htmlTmp;
				//	break;
				//}
			}
			//$html .= '<tr>
			//			<td colspan="2" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>Pago de contado</strong></td>';
			$datos["tablas"][0][] = "PAGO DE CONTADO";
			$x = 1;
			foreach(self::$aseguradoras AS $aseguradora){
				if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
					if(count($aseguradora->paquetes) > 0){
						if($aseguradora->id_aseguradora==2){
							if(self::$cotizacionDatos->nivel_amplio==0){
								foreach($aseguradora->paquetes()->where('activo', '=', 1)->orWhere('id_paquete', '=', 10)->orderBy('orden')->get() AS $paquete){
									//$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.SistemaFunciones::formatMoney1((int)self::$calculos[$paquete->id_paquete]['total']).'</strong></td>';
									$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
										->where("tipo", "=", "sadb")
										->where("hospitales", "=", $paquete->paquete_campo)
										->first();
									if($recotizacion)
										$datos["tablas"][$x][] = "$" . number_format($recotizacion->contado, 0);
									else
										$datos["tablas"][$x][] = -1;
									$x++;
								}
							}
							else{
								foreach($aseguradora->paquetes()->where('activo', '=', 1)
									->orWhere('id_paquete', '=', 10)
									->orWhere('id_paquete', '=', 11)
									->orderBy('orden')->get() AS $paquete){
									//$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.SistemaFunciones::formatMoney1((int)self::$calculos[$paquete->id_paquete]['total']).'</strong></td>';
									$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
										->where("tipo", "=", "sadb")
										->where("hospitales", "=", $paquete->paquete_campo)
										->first();
									if($recotizacion)
										$datos["tablas"][$x][] = "$" . number_format($recotizacion->contado, 0);
									else
										$datos["tablas"][$x][] = -1;
									$x++;
								}
							}
						}
						else{
							foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
								//$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.SistemaFunciones::formatMoney1((int)self::$calculos[$paquete->id_paquete]['total']).'</strong></td>';
								$datos["tablas"][$x][] = SistemaFunciones::formatMoney1((int)self::$calculos[$paquete->id_paquete]['total']);
								$x++;
							}
						}
					}
				}
			}
			//$html .= '</tr>';
			//if($mostrarContratar == true){
			//	$html .= '<tr>
			//				<td colspan="2">&nbsp;</td>';
			//	foreach(self::$aseguradoras AS $aseguradora){
			//		if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
			//			if(count($aseguradora->paquetes) > 0){
			//				foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
			//					$html .= '<td><a href="'.URL::to('/contratarPaquete/'.self::$cotizacionDatos->id_cotizacion.'/'.self::$cotizacionDatos->secret.'/'.$paquete->paquete_campo.'/'.self::$sumaAsegurada.'/'.self::$deducible).'" class="but_phone" style="cursor: pointer;"><i class="fa fa-thumbs-o-up"></i> Contratar</a> </td>';
			//				}
			//			}
			//		}
			//	}
			//	$html .= '</tr>';
			//	
			//}
			//if(!$pdf && $admin == false){
			//	$html .= '<tr>
			//			<table width="100%" class="table-list">
			//				<tr>
			//					<td colspan="'.($numeroPaquetes+2).'"><a href="#" class="mostrarPreciosMensualidades"><strong>Mostrar pagos en mensualidades</strong></a></td>
			//				</tr>
			//			</table>
			//			<table width="100%" class="table-list tabla-precios hide">';
			//}
			$bFirst = false;
			foreach(self::$pagosDiferidos AS $meses => $texto){
				//$html .= '<tr>
				//			<td rowspan="2" class="alignVerticalMiddle" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>Pago '.$texto.'</strong></td>
				//			<td '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>Primer pago</td>';
				$datos["pagos"][0][] = "PAGO " . strtoupper($texto);
				$datos["pagos"][0][] = "    Primer pago";
				if(!$bFirst)
					$datos["pagos"][0][] = "    Posterior";
				else
					$datos["pagos"][0][] = "    Posteriores";
				$bFirst = true;
				$x = 1;
				foreach(self::$aseguradoras AS $aseguradora){
					if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
						if(count($aseguradora->paquetes) > 0){
							if($aseguradora->id_aseguradora==2){
								if(self::$cotizacionDatos->nivel_amplio==0){
									foreach($aseguradora->paquetes()->where('activo', '=', 1)->orWhere('id_paquete', '=', 10)->orderBy('orden')->get() AS $paquete){
										//$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>$ '.SistemaFunciones::formatMoney1((int)self::$calculos[$paquete->id_paquete]['diferidos'][$meses][1]).'</td>';
										$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
											->where("tipo", "=", "sadb")
											->where("hospitales", "=", $paquete->paquete_campo)
											->first();
										if($recotizacion){
											switch($meses){
												case 6:
													$datos["pagos"][$x][] = "$" . number_format($recotizacion->semestral_primer, 0);
													break;
												case 3:
													$datos["pagos"][$x][] = "$" . number_format($recotizacion->trimestral_primer, 0);
													break;
												case 1:
													$datos["pagos"][$x][] = "$" . number_format($recotizacion->mensual_primer, 0);
													break;
											}
										}
										else
											$datos["pagos"][$x][] = -1;
										$x++;
									}
								}
								else{
									foreach($aseguradora->paquetes()->where('activo', '=', 1)
										->orWhere('id_paquete', '=', 10)
										->orWhere('id_paquete', '=', 11)
										->orderBy('orden')->get() AS $paquete){
										//$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>$ '.SistemaFunciones::formatMoney1((int)self::$calculos[$paquete->id_paquete]['diferidos'][$meses][1]).'</td>';
										$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
											->where("tipo", "=", "sadb")
											->where("hospitales", "=", $paquete->paquete_campo)
											->first();
										if($recotizacion){
											switch($meses){
												case 6:
													$datos["pagos"][$x][] = "$" . number_format($recotizacion->semestral_primer, 0);
													break;
												case 3:
													$datos["pagos"][$x][] = "$" . number_format($recotizacion->trimestral_primer, 0);
													break;
												case 1:
													$datos["pagos"][$x][] = "$" . number_format($recotizacion->mensual_primer, 0);
													break;
											}
										}
										else
											$datos["pagos"][$x][] = -1;
										$x++;
									}
								}
							}
							else{
								foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
									//$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>$ '.SistemaFunciones::formatMoney1((int)self::$calculos[$paquete->id_paquete]['diferidos'][$meses][1]).'</td>';
									$datos["pagos"][$x][] = SistemaFunciones::formatMoney1((int)self::$calculos[$paquete->id_paquete]['diferidos'][$meses][1]);
									$x++;
								}
							}
						}
					}
				}
				//$html .= '</tr>
				//		<tr>
				//			<td '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>Posteriores</td>';
				$x = 1;
				foreach(self::$aseguradoras AS $aseguradora){
					if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
						if(count($aseguradora->paquetes) > 0){
							if($aseguradora->id_aseguradora==2){
								if(self::$cotizacionDatos->nivel_amplio==0){
									foreach($aseguradora->paquetes()->where('activo', '=', 1)->orWhere('id_paquete', '=', 10)->orderBy('orden')->get() AS $paquete){
										//$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>$ '.SistemaFunciones::formatMoney1((int)self::$calculos[$paquete->id_paquete]['diferidos'][$meses][2]).'</td>';
										$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
											->where("tipo", "=", "sadb")
											->where("hospitales", "=", $paquete->paquete_campo)
											->first();
										if($recotizacion){
											switch($meses){
												case 6:
													$datos["pagos"][$x][] = "$" . number_format($recotizacion->semestral_posterior, 0);
													break;
												case 3:
													$datos["pagos"][$x][] = "$" . number_format($recotizacion->trimestral_posterior, 0);
													break;
												case 1:
													$datos["pagos"][$x][] = "$" . number_format($recotizacion->mensual_posterior, 0);
													break;
											}
										}
										else
											$datos["pagos"][$x][] = -1;
										$x++;
									}
								}
								else{
									foreach($aseguradora->paquetes()->where('activo', '=', 1)
										->orWhere('id_paquete', '=', 10)
										->orWhere('id_paquete', '=', 11)
										->orderBy('orden')->get() AS $paquete){
										//$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>$ '.SistemaFunciones::formatMoney1((int)self::$calculos[$paquete->id_paquete]['diferidos'][$meses][2]).'</td>';
										$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
											->where("tipo", "=", "sadb")
											->where("hospitales", "=", $paquete->paquete_campo)
											->first();
										if($recotizacion){
											switch($meses){
												case 6:
													$datos["pagos"][$x][] = "$" . number_format($recotizacion->semestral_posterior, 0);
													break;
												case 3:
													$datos["pagos"][$x][] = "$" . number_format($recotizacion->trimestral_posterior, 0);
													break;
												case 1:
													$datos["pagos"][$x][] = "$" . number_format($recotizacion->mensual_posterior, 0);
													break;
											}
										}
										else
											$datos["pagos"][$x][] = -1;
										$x++;
									}
								}
							}
							else{
								foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
									//$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>$ '.SistemaFunciones::formatMoney1((int)self::$calculos[$paquete->id_paquete]['diferidos'][$meses][2]).'</td>';
									$datos["pagos"][$x][] = SistemaFunciones::formatMoney1((int)self::$calculos[$paquete->id_paquete]['diferidos'][$meses][2]);
									$x++;
								}
							}
						}
					}
				}
				//$html .= '</tr>';
			}
			//if(!$pdf && $admin == false){
			//	$html .= '</tr>
			//				</table>';
			//}
			//$html .= '</table>';
		}
		//return $html;
		return $datos;
	}
	
	public static function datosTablaWSMapfre($update = false){
		$datos = array();
		$datos["aseguradoras"] = array();
		$datos["tablas"] = array();
		$datos["tablas"][] = array();
		$datos["tablas"][0][] = '';
		$datos["tablas"][0][] = '';
		$datos["tablas"][0][] = '';
		$datos["pagos"] = array();
		$datos["pagos"][] = array();
		//if(self::$mostrarMaternidad){
		if(self::$cotizacionDatos->maternidad==1){
			$conceptos = Paqueteconcepto::where('id_concepto', '<>', 8)
				->orderBy('orden')->get();
		}else{
			$conceptos = Paqueteconcepto::where('id_concepto', '<>', 8)
				->where('id_concepto', '<>', 7)
				->orderBy('orden')->get();
		}	
		if(count($conceptos) > 0){
			foreach(self::$aseguradoras AS $aseguradora){
				//if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
				if($aseguradora->aseguradora=="mapfre"){
					$noPaquetes = 3;
					if(self::$cotizacionDatos->nivel_amplio==1)
						$noPaquetes = 4;
					$datos["aseguradoras"][] = array(
						"id" => $aseguradora->id_aseguradora,
						"nombre" => $aseguradora->nombre,
						"paquetes" => $noPaquetes,
						"web" => $aseguradora->descripcion_web,
						"movil" => $aseguradora->descripcion_movil,
						"logo" => $aseguradora->configuracion->logo,
						"aseguradora" => $aseguradora->aseguradora
					);
				}
			}
			$x = 1;
			foreach(self::$aseguradoras AS $aseguradora){
				//if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
				if($aseguradora->aseguradora=="mapfre"){
					if(count($aseguradora->paquetes) > 0){
						if(self::$cotizacionDatos->nivel_amplio==0)
							$paquetes = $aseguradora->paquetes()->where('activo', '=', 1)->orWhere('paquete_campo', '=', 'completa')->orderBy('orden')->get();
						else
							$paquetes = $aseguradora->paquetes()->where('activo', '=', 1)->orWhere('paquete_campo', '=', 'completa')->orWhere('paquete_campo', '=', 'amplia')->orderBy('orden')->get();
						foreach($paquetes AS $paquete){
							$datos["tablas"][] = array();
							$datos["tablas"][$x][] = $paquete->id_paquete;
							$datos["tablas"][$x][] = $paquete->paquete_campo;
							$datos["tablas"][$x][] = $paquete->paquete;
							$datos["pagos"][] = array();
							$x++;
						}
					}
				}
			}
			foreach($conceptos AS $concepto){
				$datos["tablas"][0][] = $concepto->concepto;
				$x = 1;
				foreach(self::$aseguradoras AS $aseguradora){
					//if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
					if($aseguradora->aseguradora=="mapfre"){
						if(count($aseguradora->paquetes) > 0){
							if(self::$cotizacionDatos->nivel_amplio==0)
								$paquetes = $aseguradora->paquetes()->where('activo', '=', 1)->orWhere('paquete_campo', '=', 'completa')->orderBy('orden')->get();
							else
								$paquetes = $aseguradora->paquetes()->where('activo', '=', 1)->orWhere('paquete_campo', '=', 'completa')->orWhere('paquete_campo', '=', 'amplia')->orderBy('orden')->get();
							foreach($paquetes AS $paquete){
								$valorConcepto = $paquete->tarifaValor()->where('id_concepto', '=', $concepto->id_concepto)->get();
								if(count($valorConcepto) == 1){
									$valorConcepto = $valorConcepto[0]->{self::$sumaAsegurada.'_'.self::$deducible};
								}
								if(!$update)
									$datos["tablas"][$x][] = $concepto->id_concepto . "|" . $valorConcepto;
								else
									$datos["tablas"][$x][] = $concepto->id_concepto . "|";
								$x++;
							}
						}
					}
				}
			}
			$datos["tablas"][0][] = "Pago de contado";
			$x = 1;
			foreach(self::$aseguradoras AS $aseguradora){
				//if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
				if($aseguradora->aseguradora=="mapfre"){
					if(count($aseguradora->paquetes) > 0){
						if(self::$cotizacionDatos->nivel_amplio==0)
							$paquetes = $aseguradora->paquetes()->where('activo', '=', 1)->orWhere('paquete_campo', '=', 'completa')->orderBy('orden')->get();
						else
							$paquetes = $aseguradora->paquetes()->where('activo', '=', 1)->orWhere('paquete_campo', '=', 'completa')->orWhere('paquete_campo', '=', 'amplia')->orderBy('orden')->get();
						foreach($paquetes AS $paquete){
							if(!$update){
								$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
									//->where("tipo", "=", self::$sumaAsegurada . self::$deducible)
									->where("hospitales", "=", $paquete->paquete_campo)
									->first();
								if($recotizacion)
									$datos["tablas"][$x][] = number_format($recotizacion->contado, 2);
								else
									$datos["tablas"][$x][] = -1;
							}
							else
								$datos["tablas"][$x][] = -1;
							$x++;
						}
					}
				}
			}
			foreach(self::$pagosDiferidos AS $meses => $texto){
				$datos["pagos"][0][] = "Pago " . $texto;
				$datos["pagos"][0][] = "Primer pago";
				$datos["pagos"][0][] = "Posteriores";
				$x = 1;
				foreach(self::$aseguradoras AS $aseguradora){
					//if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
					if($aseguradora->aseguradora=="mapfre"){
						if(count($aseguradora->paquetes) > 0){
							if(self::$cotizacionDatos->nivel_amplio==0)
								$paquetes = $aseguradora->paquetes()->where('activo', '=', 1)->orWhere('paquete_campo', '=', 'completa')->orderBy('orden')->get();
							else
								$paquetes = $aseguradora->paquetes()->where('activo', '=', 1)->orWhere('paquete_campo', '=', 'completa')->orWhere('paquete_campo', '=', 'amplia')->orderBy('orden')->get();
							foreach($paquetes AS $paquete){
								if(!$update){
									$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
										//->where("tipo", "=", self::$sumaAsegurada . self::$deducible)
										->where("hospitales", "=", $paquete->paquete_campo)
										->first();
									if($recotizacion){
										switch($meses){
											case 6:
												$datos["pagos"][$x][] = number_format($recotizacion->semestral_primer, 2);
												break;
											case 3:
												$datos["pagos"][$x][] = number_format($recotizacion->trimestral_primer, 2);
												break;
											case 1:
												$datos["pagos"][$x][] = number_format($recotizacion->mensual_primer, 2);
												break;
										}
									}
									else
										$datos["pagos"][$x][] = -1;
								}
								else
									$datos["pagos"][$x][] = -1;
								$x++;
							}
						}
					}
				}
				$x = 1;
				foreach(self::$aseguradoras AS $aseguradora){
					//if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
					if($aseguradora->aseguradora=="mapfre"){
						if(count($aseguradora->paquetes) > 0){
							if(self::$cotizacionDatos->nivel_amplio==0)
								$paquetes = $aseguradora->paquetes()->where('activo', '=', 1)->orWhere('paquete_campo', '=', 'completa')->orderBy('orden')->get();
							else
								$paquetes = $aseguradora->paquetes()->where('activo', '=', 1)->orWhere('paquete_campo', '=', 'completa')->orWhere('paquete_campo', '=', 'amplia')->orderBy('orden')->get();
							foreach($paquetes AS $paquete){
								if(!$update){
									$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
										//->where("tipo", "=", self::$sumaAsegurada . self::$deducible)
										->where("hospitales", "=", $paquete->paquete_campo)
										->first();
									if($recotizacion){
										switch($meses){
											case 6:
												$datos["pagos"][$x][] = number_format($recotizacion->semestral_posterior, 2);
												break;
											case 3:
												$datos["pagos"][$x][] = number_format($recotizacion->trimestral_posterior, 2);
												break;
											case 1:
												$datos["pagos"][$x][] = number_format($recotizacion->mensual_posterior, 2);
												break;
										}
									}
									else
										$datos["pagos"][$x][] = -1;
								}
								else
									$datos["pagos"][$x][] = -1;
								$x++;
							}
						}
					}
				}
			}
		}
		return $datos;
	}

	public static function tablaDatosWS($admin = false, $mostrarContratar = true, $pdf = false){
		$html = '';
		$conceptos = Paqueteconcepto::orderBy('orden')->get();
		if(count($conceptos) > 0){
			$html = '<table width="100%" '.(($admin == true) ? 'class="table table-bordered table-dark table-invoice"' : 'class="table-list"').' '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>
						<thead>
							<tr>
								<th colspan="2"></th>';
			$numeroPaquetes = 0;
			foreach(self::$aseguradoras AS $aseguradora){
				if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
					//$html .= '<th colspan="'.count($aseguradora->paquetes).'" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.$aseguradora->id_aseguradora.' - '.$aseguradora->nombre.'</strong></th>';
					$html .= '<th colspan="'.count($aseguradora->paquetes).'" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.HTML::image('assets/images/aseguradoras/'.$aseguradora->id_aseguradora.'.jpg', '', (($pdf == true) ? ((count($aseguradora->paquetes) == 1) ? array('style' => "width: 100px;") : '') : '') ).'</strong></th>';
					$numeroPaquetes += count($aseguradora->paquetes);
				}
			}
			$html .= '</tr>
						</thead>
						<tr>
							<td colspan="2" width="'.(100/($numeroPaquetes)).'%"></td>';
			foreach(self::$aseguradoras AS $aseguradora){
				if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
					if(count($aseguradora->paquetes) > 0){
						foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
							$html .= '<td class="alignCenter" width="'.(100/($numeroPaquetes+2)).'%" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.$paquete->paquete.'</strong></td>';
						}
					}
				}
			}
			$html .= '</tr>';
			foreach($conceptos AS $concepto){
				$htmlTmp = '<tr>
							<td colspan="2" class="alignLeft" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.(($pdf == true)?utf8_decode($concepto->concepto):$concepto->concepto).'</td>';
				foreach(self::$aseguradoras AS $aseguradora){
					if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
						if(count($aseguradora->paquetes) > 0){
							foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
								$valorConcepto = $paquete->tarifaValor()->where('id_concepto', '=', $concepto->id_concepto)->get();
								if(count($valorConcepto) == 1){
									$valorConcepto = $valorConcepto[0]->{self::$sumaAsegurada.'_'.self::$deducible};
								}
								$htmlTmp .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.(($pdf == true)?utf8_decode($valorConcepto):$valorConcepto).'</td>';
							}
						}
					}
				}
				$htmlTmp .= '</tr>';
				switch ($concepto->id_concepto) {
					case 7:
					case 8:
						if(self::$mostrarMaternidad == true){
							$html .= $htmlTmp;
						}
					break;
					default:
						$html .= $htmlTmp;
					break;
				}
			}
			$html .= '<tr>
						<td colspan="2" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>Pago de contado</strong></td>';
			foreach(self::$aseguradoras AS $aseguradora){
				if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
					if(count($aseguradora->paquetes) > 0){
						foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
							if($aseguradora->aseguradora!="mapfre")
								$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.SistemaFunciones::formatMoney1((int)self::$calculos[$paquete->id_paquete]['total']).'</strong></td>';
							else{
								$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
									->where("tipo", "=", self::$sumaAsegurada . self::$deducible)
									->where("hospitales", "=", $paquete->paquete_campo)
									->first();
								if($recotizacion)
									$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="contado-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '"><strong>'.number_format($recotizacion->contado, 2).'</strong></td>';
								else
									$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="contado-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '"><strong>-</strong></td>';
							}
						}
					}
				}
			}
			$html .= '</tr>';
			if($mostrarContratar == true){
				$html .= '<tr>
							<td colspan="2">&nbsp;</td>';
				foreach(self::$aseguradoras AS $aseguradora){
					if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
						if(count($aseguradora->paquetes) > 0){
							foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
								$html .= '<td><a href="'.URL::to('/contratarPaquete/'.self::$cotizacionDatos->id_cotizacion.'/'.self::$cotizacionDatos->secret.'/'.$paquete->paquete_campo.'/'.self::$sumaAsegurada.'/'.self::$deducible).'" class="but_phone" style="cursor: pointer;"><i class="fa fa-thumbs-o-up"></i> Contratar</a> </td>';
							}
						}
					}
				}
				$html .= '</tr>';
				
			}
			if(!$pdf && $admin == false){
				$html .= '<tr>
						<table width="100%" class="table-list">
							<tr>
								<td colspan="'.($numeroPaquetes+2).'"><a href="#" class="mostrarPreciosMensualidades"><strong>Mostrar pagos en mensualidades</strong></a></td>
							</tr>
						</table>
						<table width="100%" class="table-list tabla-precios hide">';
			}
			foreach(self::$pagosDiferidos AS $meses => $texto){
				$html .= '<tr>
							<td rowspan="2" class="alignVerticalMiddle" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>Pago '.$texto.'</strong></td>
							<td '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>Primer pago</td>';
				foreach(self::$aseguradoras AS $aseguradora){
					if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
						if(count($aseguradora->paquetes) > 0){
							foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
								if($aseguradora->aseguradora!="mapfre")
									$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>$ '.SistemaFunciones::formatMoney1((int)self::$calculos[$paquete->id_paquete]['diferidos'][$meses][1]).'</td>';
								else{
									$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
										->where("tipo", "=", self::$sumaAsegurada . self::$deducible)
										->where("hospitales", "=", $paquete->paquete_campo)
										->first();
									if($recotizacion){
										switch($meses){
											case 6:
												$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="semestral-1-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ '.$recotizacion->semestral_primer.'</td>';
												break;
											case 3:
												$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="trimestral-1-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ '.$recotizacion->trimestral_primer.'</td>';
												break;
											case 1:
												$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="mensual-1-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ '.$recotizacion->mensual_primer.'</td>';
												break;
										}
									}
									else{
										switch($meses){
											case 6:
												$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="semestral-1-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ -</td>';
												break;
											case 3:
												$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="trimestral-1-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ -</td>';
												break;
											case 1:
												$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="mensual-1-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ -</td>';
												break;
										}
									}
								}
							}
						}
					}
				}
				$html .= '</tr>
						<tr>
							<td '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>Posteriores</td>';
				foreach(self::$aseguradoras AS $aseguradora){
					if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
						if(count($aseguradora->paquetes) > 0){
							foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
								if($aseguradora->aseguradora!="mapfre")
									$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>$ '.SistemaFunciones::formatMoney1((int)self::$calculos[$paquete->id_paquete]['diferidos'][$meses][2]).'</td>';
								else{
									$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
										->where("tipo", "=", self::$sumaAsegurada . self::$deducible)
										->where("hospitales", "=", $paquete->paquete_campo)
										->first();
									if($recotizacion){
										switch($meses){
											case 6:
												$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="semestral-2-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ '.$recotizacion->semestral_posterior.'</td>';
												break;
											case 3:
												$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="trimestral-2-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ '.$recotizacion->trimestral_posterior.'</td>';
												break;
											case 1:
												$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="mensual-2-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ '.$recotizacion->mensual_posterior.'</td>';
												break;
										}
									}
									else{
										switch($meses){
											case 6:
												$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="semestral-2-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ -</td>';
												break;
											case 3:
												$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="trimestral-2-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ -</td>';
												break;
											case 1:
												$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="mensual-2-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ -</td>';
												break;
										}
									}
								}
							}
						}
					}
				}
				$html .= '</tr>';
			}
			if(!$pdf && $admin == false){
				$html .= '</tr>
							</table>';
			}
			$html .= '</table>';
		}
		return $html;
	}

	public static function mapfreCotizacion($integrantes, $estado){
		$doc = new DOMDocument("1.0", "UTF-8");
		$docXml = $doc->createElement("xml");
		$docCotizar = $doc->createElement("cotizar");
		
		$datosFijos = $doc->createElement("datos_fijos");
		$el = $doc->createElement("cod_usr_captura", "APPZALID");
		$datosFijos->appendChild($el);
		$el = $doc->createElement("ramo", 288);
		$datosFijos->appendChild($el);
		$el = $doc->createElement("agente", 15770);
		$datosFijos->appendChild($el);
		$el = $doc->createElement("fec_efec", date("dmY"));
		$datosFijos->appendChild($el);
		$el = $doc->createElement("fec_vcto", date("dmY", strtotime("+1 year")));
		$datosFijos->appendChild($el);
		$el = $doc->createElement("mca_personalizado", "N");
		$datosFijos->appendChild($el);
		$el = $doc->createElement("mca_perfilador", "N");
		$datosFijos->appendChild($el);
		$el = $doc->createElement("empresarial", "N");
		$datosFijos->appendChild($el);
		$el = $doc->createElement("num_asegurados", count($integrantes));
		$datosFijos->appendChild($el);
		$el = $doc->createElement("desc_paquete");
		$datosFijos->appendChild($el);
		$docCotizar->appendChild($datosFijos);
		
		$dependientes = $doc->createElement("dependientes");
		$x = 1;
		foreach($integrantes as $i){
			$nombre = str_replace("ñ", "n", str_replace("Ñ", "N", str_replace("ú", "u", str_replace("ó", "o", str_replace("í", "i", str_replace("é", "e", str_replace("á", "a", str_replace("Ú", "U", str_replace("Ó", "O", str_replace("Í", "I", str_replace("É", "E", str_replace("Á", "A", $i["nombre"]))))))))))));
			$dependiente = $doc->createElement("dependiente");
			$el = $doc->createElement("num_riesgo", $x);
			$dependiente->appendChild($el);
			$el = $doc->createElement("id_parentesco", $i["id_parentesco"]);
			$dependiente->appendChild($el);
			$el = $doc->createElement("nombre", $nombre);
			$dependiente->appendChild($el);
			$el = $doc->createElement("apll_pat");
			$dependiente->appendChild($el);
			$el = $doc->createElement("apll_mat");
			$dependiente->appendChild($el);
			$el = $doc->createElement("sexo", $i["id_sexo"]);
			$dependiente->appendChild($el);
			$el = $doc->createElement("id_ocupacion");
			$dependiente->appendChild($el);
			$el = $doc->createElement("parentesco_desc", $i["parentesco"]);
			$dependiente->appendChild($el);
			$el = $doc->createElement("sexo_desc", $i["sexo"]);
			$dependiente->appendChild($el);
			$el = $doc->createElement("ocupacion_desc");
			$dependiente->appendChild($el);
			$el = $doc->createElement("fecha_nac");
			$dependiente->appendChild($el);
			$el = $doc->createElement("edad", $i["edad"]);
			$dependiente->appendChild($el);
			$el = $doc->createElement("rfc");
			$dependiente->appendChild($el);
			$el = $doc->createElement("curp");
			$dependiente->appendChild($el);
			$el = $doc->createElement("clm");
			$dependiente->appendChild($el);
			$el = $doc->createElement("ca", 0);
			$dependiente->appendChild($el);
			$el = $doc->createElement("ca2", 0);
			$dependiente->appendChild($el);
			$el = $doc->createElement("descuento", 0);
			$dependiente->appendChild($el);
			$el = $doc->createElement("num_renovacion", 0);
			$dependiente->appendChild($el);
			$el = $doc->createElement("id_deporte");
			$dependiente->appendChild($el);
			$el = $doc->createElement("deporte_desc");
			$dependiente->appendChild($el);
			$dependientes->appendChild($dependiente);
			$x++;
		}
		
		$datosVar = $doc->createElement("datos_var");
		$el = $doc->createElement("codigo_val");
		$datosVar->appendChild($el);
		$el = $doc->createElement("campania_val");
		$datosVar->appendChild($el);
		$el = $doc->createElement("campania_desc", 0);
		$datosVar->appendChild($el);
		$datosVar->appendChild($dependientes);
		$el = $doc->createElement("cesion_comision", 0);
		$datosVar->appendChild($el);
		$el = $doc->createElement("estado", $estado->id_estado);
		$datosVar->appendChild($el);
		$el = $doc->createElement("estado_desc", $estado->estado);
		$datosVar->appendChild($el);
		$el = $doc->createElement("prov");
		$datosVar->appendChild($el);
		$el = $doc->createElement("prov_desc");
		$datosVar->appendChild($el);
		$el = $doc->createElement("cod_proveedor", 3);
		$datosVar->appendChild($el);
		
		$docCotizar->appendChild($datosVar);
		
		$docXml->appendChild($docCotizar);
		$doc->appendChild($docXml);
		$xml = $doc->saveHTML();
		if(strpos($xml, "\n"))
			$xml = substr($xml, 0, strpos($xml, "\n"));
		$xml = '"' . $xml . '"';
		
		$ch = curl_init(self::$wsUrl . "Zonaliados.Negocio/Api/_AYESalud/Cotiza?ramo=288");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		$res = curl_exec($ch);

		$cotizacion = json_decode($res, true);
		return array("cotizacion" => $cotizacion, "xml" => $xml);
	}

	public static function mapfreRecotizacion($integrantes, $estado, $cotizacion, $tipo, $hospitales){
		switch($tipo){
			case "sada":
				$suma = 40000000;
				$deducible = 30000;
				$tope = 5;
				break;
			case "sadb":
				$suma = 40000000;
				$deducible = 15000;
				$tope = 5;
				break;
			case "sbda":
				$suma = 10000000;
				$deducible = 30000;
				$tope = 3;
				break;
			case "sbdb":
				$suma = 10000000;
				$deducible = 15000;
				$tope = 3;
				break;
		}
		
		$multiPaquetes = true;
		$idPaquete = "207";
		if(isset($cotizacion["xml"]["xmlCoberturas"]["paquetes"]["paquete"]["cod_paquete"])){
			$idPaquete = $cotizacion["xml"]["xmlCoberturas"]["paquetes"]["paquete"]["cod_paquete"];
			$multiPaquetes = false;
		}
		
		$doc = new DOMDocument("1.0", "UTF-8");
		$docXml = $doc->createElement("xml");
		$docCotizar = $doc->createElement("cotizar");
		$docCoberturas = $doc->createElement("coberturas");
		
		foreach($cotizacion["xml"]["xmlCoberturas"]["agrupado"]["grupo"] as $grupo){
			foreach($grupo["coberturas"]["cobertura"] as $cobertura){
				$docCobertura = $doc->createElement("cobertura");
				
				$quitar = false;
				$agregar = false;
				$cod_cob = $doc->createElement("cod_cob", $cobertura["cod_cob"]);
				switch($cobertura["cod_cob"]){
					case 1:
						$suma_aseg = $doc->createElement("suma_aseg", $suma);
						break;
					case 18:
						$suma_aseg = $doc->createElement("suma_aseg", $cobertura["sa_def"]);
						$quitar = true;
						break;
					case 19:
						$suma_aseg = $doc->createElement("suma_aseg", $cobertura["sa_def"]);
						$quitar = true;
						break;
					case 20:
						$suma_aseg = $doc->createElement("suma_aseg", $cobertura["sa_def"]);
						$quitar = true;
						break;
					case 21:
						$suma_aseg = $doc->createElement("suma_aseg", $cobertura["sa_def"]);
						$quitar = true;
						break;
					case 22:
						$suma_aseg = $doc->createElement("suma_aseg", 100000);
						$agregar = true;
						break;
					case 24:
						$suma_aseg = $doc->createElement("suma_aseg", $cobertura["sa_def"]);
						$quitar = true;
						break;
					case 28:
						$suma_aseg = $doc->createElement("suma_aseg", $cobertura["sa_def"]);
						break;
					case 31:
						$suma_aseg = $doc->createElement("suma_aseg", $deducible);
						$agregar = true;
						break;
					case 40:
						$suma_aseg = $doc->createElement("suma_aseg", $cobertura["sa_def"]);
						break;
					case 41:
						$suma_aseg = $doc->createElement("suma_aseg", $cobertura["sa_def"]);
						break;
					case 42:
						$suma_aseg = $doc->createElement("suma_aseg", $cobertura["sa_def"]);
						break;
					case 43:
						$suma_aseg = $doc->createElement("suma_aseg", $cobertura["sa_def"]);
						break;
					case 44:
						$suma_aseg = $doc->createElement("suma_aseg", $cobertura["sa_def"]);
						break;
					case 45:
						$suma_aseg = $doc->createElement("suma_aseg", $cobertura["sa_def"]);
						break;
					case 47:
						$suma_aseg = $doc->createElement("suma_aseg", $cobertura["sa_def"]);
						break;
					case 48:
						$suma_aseg = $doc->createElement("suma_aseg", $cobertura["sa_def"]);
						break;
					case 52:
						$suma_aseg = $doc->createElement("suma_aseg", 1);
						$quitar = true;
						break;
					case 53:
						$suma_aseg = $doc->createElement("suma_aseg", $cobertura["sa_def"]);
						$quitar = true;
						break;
					default:
						if($cobertura["sa_def"]=="Amparada")
							$suma_aseg = $doc->createElement("suma_aseg", 1);
						else
							$suma_aseg = $doc->createElement("suma_aseg", $cobertura["sa_def"]);
						break;
				}
				$paquetes = $doc->createElement("paquetes");
				if($multiPaquetes){
					foreach($cobertura["paquetes"]["paquete"] as $paq){
						$paquete = $doc->createElement("paquete");
						$cod_paquete = $doc->createElement("cod_paquete", $paq["cod_paquete"]);
						if($paq["cod_paquete"]!="207"){
							if($paq["mca_opc"]=="null")
								$mca_contrata = $doc->createElement("mca_contrata", "N");
							else{
								if($paq["mca_opc"]=="N")
									$mca_contrata = $doc->createElement("mca_contrata", "S");
								else{
									if($paq["mod_chk"]==1)
										$mca_contrata = $doc->createElement("mca_contrata", "S");
									else
										$mca_contrata = $doc->createElement("mca_contrata", "N");
								}
							}
						}
						else{
							if(!$quitar){
								if($paq["mca_opc"]=="null")
									$mca_contrata = $doc->createElement("mca_contrata", "N");
								else{
									if($paq["mca_opc"]=="N")
										$mca_contrata = $doc->createElement("mca_contrata", "S");
									else{
										if($paq["mod_chk"]==1)
											$mca_contrata = $doc->createElement("mca_contrata", "S");
										else
											$mca_contrata = $doc->createElement("mca_contrata", "N");
									}
								}
							}
							else
								$mca_contrata = $doc->createElement("mca_contrata", "N");
							if($agregar)
								$mca_contrata = $doc->createElement("mca_contrata", "S");
						}
						
						$paquete->appendChild($cod_paquete);
						$paquete->appendChild($mca_contrata);
						$paquetes->appendChild($paquete);
					}
				}
				else{
					$paquete = $doc->createElement("paquete");
					$cod_paquete = $doc->createElement("cod_paquete", $cobertura["paquetes"]["paquete"]["cod_paquete"]);
					if(!$quitar){
						if($cobertura["paquetes"]["paquete"]["mca_opc"]=="null")
							$mca_contrata = $doc->createElement("mca_contrata", "N");
						else{
							if($cobertura["paquetes"]["paquete"]["mca_opc"]=="N")
								$mca_contrata = $doc->createElement("mca_contrata", "S");
							else{
								if($cobertura["paquetes"]["paquete"]["mod_chk"]==1)
									$mca_contrata = $doc->createElement("mca_contrata", "S");
								else
									$mca_contrata = $doc->createElement("mca_contrata", "N");
							}
						}
					}
					else
						$mca_contrata = $doc->createElement("mca_contrata", "N");
					if($agregar)
						$mca_contrata = $doc->createElement("mca_contrata", "S");
					$paquete->appendChild($cod_paquete);
					$paquete->appendChild($mca_contrata);
					$paquetes->appendChild($paquete);
				}
				
				$docCobertura->appendChild($cod_cob);
				$docCobertura->appendChild($suma_aseg);
				$docCobertura->appendChild($paquetes);
				
				$docCoberturas->appendChild($docCobertura);
			}
		}
		
		$docCotizar->appendChild($docCoberturas);
		
		$datosVarCob = $doc->createElement("datos_var_cob");
		foreach($cotizacion["xml"]["ofertaComercial"]["datos_var_cob"] as $key => $val){
			if($val!=""){
				switch($key){
					case "suma_aseg_2800":
						$docVar = $doc->createElement($key, $suma);
						break;
					case "imp_deducible_2800":
						$docVar = $doc->createElement($key, $deducible);
						break;
					case "pct_tope_coaseguro_2800":
						//$docVar = $doc->createElement($key, $cotizacion["xml"]["xmlCoberturas"]["agrupado"]["grupo"][0]["coberturas"]["cobertura"][0]["intervalos"]["topecoaseguro"]["intervalo"][(count($cotizacion["xml"]["xmlCoberturas"]["agrupado"]["grupo"][0]["coberturas"]["cobertura"][0]["intervalos"]["topecoaseguro"]["intervalo"])-1)]["key"]);
						$docVar = $doc->createElement($key, $tope);
						break;
					case "cod_deducible_2800":
						$docVar = $doc->createElement($key, 1);
						break;
					case "cod_tabulador_2800":
						$docVar = $doc->createElement($key, 4);
						break;
					case "cod_red_hosp_2800":
						$docVar = $doc->createElement($key, $hospitales);
						break;
					case "suma_aseg_2830":
						$docVar = $doc->createElement($key, $deducible);
						break;
					case "suma_aseg_2821":
						$docVar = $doc->createElement($key, 100000);
						break;
					default:
						$docVar = $doc->createElement($key, $val);
						break;
				}
				
				$datosVarCob->appendChild($docVar);
			}
		}
		$docCotizar->appendChild($datosVarCob);
		
		$datosFijos = $doc->createElement("datos_fijos");
		$cod_user_captura = $doc->createElement("cod_usr_captura", "APPZALID");
		$ramo = $doc->createElement("ramo", 288);
		$agente = $doc->createElement("agente", 15770);
		$fec_efec = $doc->createElement("fec_efec",  date("dmY"));
		$fec_vcto = $doc->createElement("fec_vcto", date("dmY", strtotime("+1 year")));
		$mca_personalizado = $doc->createElement("mca_personalizado", "N");
		$mca_perfilador = $doc->createElement("mca_perfilador", "N");
		$empresarial = $doc->createElement("empresarial", "N");
		$num_asegurados = $doc->createElement("num_asegurados", count($integrantes));
		$desc_paquete = $doc->createElement("desc_paquete", "paquete 1");
		$contrato = $doc->createElement("contrato");
		$poliza_grupo = $doc->createElement("poliza_grupo");
		
		$datosFijos->appendChild($cod_user_captura);
		$datosFijos->appendChild($ramo);
		$datosFijos->appendChild($agente);
		$datosFijos->appendChild($fec_efec);
		$datosFijos->appendChild($fec_vcto);
		$datosFijos->appendChild($mca_personalizado);
		$datosFijos->appendChild($mca_perfilador);
		$datosFijos->appendChild($empresarial);
		$datosFijos->appendChild($num_asegurados);
		$datosFijos->appendChild($desc_paquete);
		$datosFijos->appendChild($contrato);
		$datosFijos->appendChild($poliza_grupo);
		$docCotizar->appendChild($datosFijos);
		
		$datosVar = $doc->createElement("datos_var");
		$codigo_val = $doc->createElement("codigo_val");
		$campania_val = $doc->createElement("campania_val");
		$campania_desc = $doc->createElement("campania_desc", 0);
		
		$cesion_comision = $doc->createElement("cesion_comision", 0);
		$elEstado = $doc->createElement("estado", $estado->id_estado);
		$estado_desc = $doc->createElement("estado_desc", $estado->estado);
		$prov = $doc->createElement("prov");
		$prov_desc = $doc->createElement("prov_desc");
		$cod_proveedor = $doc->createElement("cod_proveedor", 3);
		
		$dependientes = $doc->createElement("dependientes");
		$x = 1;
		foreach($integrantes as $i){
			$nombre = str_replace("ñ", "n", str_replace("Ñ", "N", str_replace("ú", "u", str_replace("ó", "o", str_replace("í", "i", str_replace("é", "e", str_replace("á", "a", str_replace("Ú", "U", str_replace("Ó", "O", str_replace("Í", "I", str_replace("É", "E", str_replace("Á", "A", $i["nombre"]))))))))))));
			$dependiente = $doc->createElement("dependiente");
			$el = $doc->createElement("num_riesgo", $x);
			$dependiente->appendChild($el);
			$el = $doc->createElement("id_parentesco", $i["id_parentesco"]);
			$dependiente->appendChild($el);
			$el = $doc->createElement("nombre", $nombre);
			$dependiente->appendChild($el);
			$el = $doc->createElement("apll_pat");
			$dependiente->appendChild($el);
			$el = $doc->createElement("apll_mat");
			$dependiente->appendChild($el);
			$el = $doc->createElement("sexo", $i["id_sexo"]);
			$dependiente->appendChild($el);
			$el = $doc->createElement("id_ocupacion");
			$dependiente->appendChild($el);
			$el = $doc->createElement("parentesco_desc", $i["parentesco"]);
			$dependiente->appendChild($el);
			$el = $doc->createElement("sexo_desc", $i["sexo"]);
			$dependiente->appendChild($el);
			$el = $doc->createElement("ocupacion_desc");
			$dependiente->appendChild($el);
			$el = $doc->createElement("fecha_nac");
			$dependiente->appendChild($el);
			$el = $doc->createElement("edad", $i["edad"]);
			$dependiente->appendChild($el);
			$el = $doc->createElement("rfc");
			$dependiente->appendChild($el);
			$el = $doc->createElement("curp");
			$dependiente->appendChild($el);
			$el = $doc->createElement("clm");
			$dependiente->appendChild($el);
			$el = $doc->createElement("ca", 0);
			$dependiente->appendChild($el);
			$el = $doc->createElement("ca2", 0);
			$dependiente->appendChild($el);
			$el = $doc->createElement("descuento", 0);
			$dependiente->appendChild($el);
			$el = $doc->createElement("num_renovacion", 0);
			$dependiente->appendChild($el);
			$el = $doc->createElement("id_deporte");
			$dependiente->appendChild($el);
			$el = $doc->createElement("deporte_desc");
			$dependiente->appendChild($el);
			$dependientes->appendChild($dependiente);
			$x++;
		}
		
		$datosVar->appendChild($codigo_val);
		$datosVar->appendChild($campania_val);
		$datosVar->appendChild($campania_desc);
		$datosVar->appendChild($dependientes);
		$datosVar->appendChild($cesion_comision);
		$datosVar->appendChild($elEstado);
		$datosVar->appendChild($estado_desc);
		$datosVar->appendChild($prov);
		$datosVar->appendChild($prov_desc);
		$datosVar->appendChild($cod_proveedor);
		$docCotizar->appendChild($datosVar);
		
		$docXml->appendChild($docCotizar);
		$doc->appendChild($docXml);
		$xml = $doc->saveHTML();
		if(strpos($xml, "\n"))
			$xml = substr($xml, 0, strpos($xml, "\n"));
		$xml = '"' . $xml . '"';
		
		$ch = curl_init(self::$wsUrl . "WebApiAARCO/api/recotiza/" . $cotizacion["xml"]["num_solicitud"]);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		$res = curl_exec($ch);
		
		$recotizacion = json_decode($res, true);
		return array("cotizacion" => $recotizacion, "xml" => $xml);
	}

	public static function mapfreRecotizacion2023($cotizacionDatos, &$recotizacion, $hospitales){
		// por default sadb
		$tipo = "sadb";
		$suma = 40000000;
		$deducible = 15000;
		$tope = 5;
		//if(!is_null($cotizacionDatos->suma_asegurada))
		//	$suma = number_format($cotizacionDatos->suma_asegurada, 0, "", "");
		$suma = $recotizacion->sa;
		//if(!is_null($cotizacionDatos->deducible))
		//	$deducible = number_format($cotizacionDatos->deducible, 0, "", "");
		$deducible = $recotizacion->deducible;
		$emergenciaExtranjero = 0;
		switch($suma){
			case 5000000:
				$tope = 3;  // $40,000.00
				$recotizacion->tope_coaseguro = 40000;
				break;
			case 10000000:
				$tope = 3;  // $50,000.00
				$recotizacion->tope_coaseguro = 50000;
				break;
			case 15000000:
				$tope = 11; // $52,500.00
				$recotizacion->tope_coaseguro = 52500;
				break;
			case 20000000:
				$tope = 4;  // $50,000.00
				$recotizacion->tope_coaseguro = 50000;
				break;
			case 25000000:
				$tope = 4;  // $62,500.00
				$recotizacion->tope_coaseguro = 62500;
				break;
			case 40000000:
				$tope = 5;  // $40,000.00
				$recotizacion->tope_coaseguro = 40000;
				break;
			case 100000000:
				$tope = 13; // $60,000.00
				$recotizacion->tope_coaseguro = 60000;
				break;
			case 130000000:
				$tope = 14; // $60,000.00
				$recotizacion->tope_coaseguro = 60000;
				break;
		}
		$recotizacion->save();
		// Se reubican estas rutinas, estaban en CotizadorController
		$inte = json_decode($cotizacionDatos->integrantes);
		$estado = Estado::where('clave', '=', $cotizacionDatos->estado)->first();
		$poblacion = Poblacion::where('id_estado', '=', $estado->id_estado)->where('poblacion', '=', $cotizacionDatos->ciudad)->first();
		foreach($inte as $i){
			$parentesco = Parentesco::where('parentesco', '=', $i->titulo)->first();
			if($i->sexo=="m"){
				$sexo = "Masculino";
				$idSexo = 1;
			}
			else{
				$sexo = "Femenino";
				$idSexo = 0;
			}
			$integrantes[] = array(
				"nombre" => $i->nombre,
				"id_parentesco" => $parentesco->clave_mapfre,
				"parentesco" => $parentesco->parentesco,
				"id_sexo" => $idSexo,
				"sexo" => $sexo,
				"edad" => $i->edad
			);
		}
		$cotizacion = json_decode($cotizacionDatos->mapfre_respuesta, true);
		
		$multiPaquetes = true;
		$idPaquete = "207";
		if(isset($cotizacion["xml"]["xmlCoberturas"]["paquetes"]["paquete"]["cod_paquete"])){
			$idPaquete = $cotizacion["xml"]["xmlCoberturas"]["paquetes"]["paquete"]["cod_paquete"];
			$multiPaquetes = false;
		}
		
		$doc = new DOMDocument("1.0", "UTF-8");
		$docXml = $doc->createElement("xml");
		$docCotizar = $doc->createElement("cotizar");
		$docCoberturas = $doc->createElement("coberturas");
		
		foreach($cotizacion["xml"]["xmlCoberturas"]["agrupado"]["grupo"] as $grupo){
			foreach($grupo["coberturas"]["cobertura"] as $cobertura){
				$docCobertura = $doc->createElement("cobertura");
				
				$quitar = false;
				$agregar = false;
				$cod_cob = $doc->createElement("cod_cob", $cobertura["cod_cob"]);
				switch($cobertura["cod_cob"]){
					case 1:
						$suma_aseg = $doc->createElement("suma_aseg", $suma);
						break;
					case 16:
						//$suma_aseg = $doc->createElement("suma_aseg", $cobertura["sa_def"]);
						$suma_aseg = $doc->createElement("suma_aseg", 1);
						if($recotizacion->complicaciones==1)
							$agregar = true;
						else
							$quitar = true;
						break;
					case 17:
						//$suma_aseg = $doc->createElement("suma_aseg", $cobertura["sa_def"]);
						$suma_aseg = $doc->createElement("suma_aseg", 1);
						if($recotizacion->vanguardia==1)
							$agregar = true;
						else
							$quitar = true;
						break;
					case 18:
						$suma_aseg = $doc->createElement("suma_aseg", $cobertura["sa_def"]);
						if($recotizacion->preexistentes==1)
							$agregar = true;
						else
							$quitar = true;
						break;
					case 19:
						$suma_aseg = $doc->createElement("suma_aseg", $cobertura["sa_def"]);
						$quitar = true;
						break;
					case 20:
						$suma_aseg = $doc->createElement("suma_aseg", $cobertura["sa_def"]);
						$quitar = true;
						break;
					case 21:
						$suma_aseg = $doc->createElement("suma_aseg", $cobertura["sa_def"]);
						$quitar = true;
						break;
					case 22:
						//if($cotizacionDatos->viajes==1){
						if(!is_null($recotizacion->emergencia_extranjero)){
							$agregar = true;
							$suma_aseg = $doc->createElement("suma_aseg", 100000);
							$emergenciaExtranjero = 100000;
						}
						else{
							$quitar = true;
							$suma_aseg = $doc->createElement("suma_aseg", $cobertura["sa_def"]);
							$emergenciaExtranjero = $cobertura["sa_def"];
						}
						break;
					case 23:
						//$suma_aseg = $doc->createElement("suma_aseg", $cobertura["sa_def"]);
						$suma_aseg = $doc->createElement("suma_aseg", 1);
						if($recotizacion->catastroficas==1)
							$agregar = true;
						else
							$quitar = true;
						break;
					case 24:
						$suma_aseg = $doc->createElement("suma_aseg", $cobertura["sa_def"]);
						$quitar = true;
						break;
					case 25:
					case 26:
						//if($cotizacionDatos->dental==1)
						if(!is_null($recotizacion->dental))
							$agregar = true;
						else
							$quitar = true;
						$suma_aseg = $doc->createElement("suma_aseg", 1);
						break;
					case 28:
						//if(is_null($cotizacionDatos->sa_maternidad)){
						if(is_null($recotizacion->sa_maternidad)){
							$suma_aseg = $doc->createElement("suma_aseg", $cobertura["sa_def"]);
							$quitar = true;
						}
						else{
							//$suma_aseg = $doc->createElement("suma_aseg", $cotizacionDatos->sa_maternidad);
							$suma_aseg = $doc->createElement("suma_aseg", $recotizacion->sa_maternidad);
							$agregar = true;
						}
						//if($cotizacionDatos->maternidad==1){
						//	$agregar = true;
						//	
						//}
						//else{
						//	$quitar = true;
						//}
						break;
					case 30:
						//if($cotizacionDatos->otros_estados==1)
						if($recotizacion->multiregion==1)
							$agregar = true;
						else
							$quitar = true;
						$suma_aseg = $doc->createElement("suma_aseg", 1);
						break;
					case 31:
						//if($cotizacionDatos->reduccion_deducible==1)
						if($recotizacion->reduccion_deducible==1)
							$agregar = true;
						else
							$quitar = true;
						$suma_aseg = $doc->createElement("suma_aseg", $deducible);
						break;
					case 36:
						//$suma_aseg = $doc->createElement("suma_aseg", $cobertura["sa_def"]);
						$suma_aseg = $doc->createElement("suma_aseg", 1);
						if($recotizacion->funeraria==1)
							$agregar = true;
						else
							$quitar = true;
						break;
					case 40:
						$suma_aseg = $doc->createElement("suma_aseg", $cobertura["sa_def"]);
						break;
					case 41:
						$suma_aseg = $doc->createElement("suma_aseg", $cobertura["sa_def"]);
						break;
					case 42:
						$suma_aseg = $doc->createElement("suma_aseg", $cobertura["sa_def"]);
						break;
					case 43:
						$suma_aseg = $doc->createElement("suma_aseg", $cobertura["sa_def"]);
						break;
					case 44:
						$suma_aseg = $doc->createElement("suma_aseg", $cobertura["sa_def"]);
						break;
					case 45:
						$suma_aseg = $doc->createElement("suma_aseg", $cobertura["sa_def"]);
						break;
					case 47:
						$suma_aseg = $doc->createElement("suma_aseg", $cobertura["sa_def"]);
						break;
					case 48:
						$suma_aseg = $doc->createElement("suma_aseg", $cobertura["sa_def"]);
						break;
					case 52:
						$suma_aseg = $doc->createElement("suma_aseg", 1);
						$quitar = true;
						break;
					case 53:
						$suma_aseg = $doc->createElement("suma_aseg", $cobertura["sa_def"]);
						$quitar = true;
						break;
					default:
						if($cobertura["sa_def"]=="Amparada")
							$suma_aseg = $doc->createElement("suma_aseg", 1);
						else
							$suma_aseg = $doc->createElement("suma_aseg", $cobertura["sa_def"]);
						break;
				}
				$paquetes = $doc->createElement("paquetes");
				if($multiPaquetes){
					foreach($cobertura["paquetes"]["paquete"] as $paq){
						$paquete = $doc->createElement("paquete");
						$cod_paquete = $doc->createElement("cod_paquete", $paq["cod_paquete"]);
						if($paq["cod_paquete"]!="207"){
							if($paq["mca_opc"]=="null")
								$mca_contrata = $doc->createElement("mca_contrata", "N");
							else{
								if($paq["mca_opc"]=="N")
									$mca_contrata = $doc->createElement("mca_contrata", "S");
								else{
									if($paq["mod_chk"]==1)
										$mca_contrata = $doc->createElement("mca_contrata", "S");
									else
										$mca_contrata = $doc->createElement("mca_contrata", "N");
								}
							}
						}
						else{
							if(!$quitar){
								if($paq["mca_opc"]=="null")
									$mca_contrata = $doc->createElement("mca_contrata", "N");
								else{
									if($paq["mca_opc"]=="N")
										$mca_contrata = $doc->createElement("mca_contrata", "S");
									else{
										if($paq["mod_chk"]==1)
											$mca_contrata = $doc->createElement("mca_contrata", "S");
										else
											$mca_contrata = $doc->createElement("mca_contrata", "N");
									}
								}
							}
							else
								$mca_contrata = $doc->createElement("mca_contrata", "N");
							if($agregar)
								$mca_contrata = $doc->createElement("mca_contrata", "S");
						}
						
						$paquete->appendChild($cod_paquete);
						$paquete->appendChild($mca_contrata);
						$paquetes->appendChild($paquete);
					}
				}
				else{
					$paquete = $doc->createElement("paquete");
					$cod_paquete = $doc->createElement("cod_paquete", $cobertura["paquetes"]["paquete"]["cod_paquete"]);
					if(!$quitar){
						if($cobertura["paquetes"]["paquete"]["mca_opc"]=="null")
							$mca_contrata = $doc->createElement("mca_contrata", "N");
						else{
							if($cobertura["paquetes"]["paquete"]["mca_opc"]=="N")
								$mca_contrata = $doc->createElement("mca_contrata", "S");
							else{
								if($cobertura["paquetes"]["paquete"]["mod_chk"]==1)
									$mca_contrata = $doc->createElement("mca_contrata", "S");
								else
									$mca_contrata = $doc->createElement("mca_contrata", "N");
							}
						}
					}
					else
						$mca_contrata = $doc->createElement("mca_contrata", "N");
					if($agregar)
						$mca_contrata = $doc->createElement("mca_contrata", "S");
					$paquete->appendChild($cod_paquete);
					$paquete->appendChild($mca_contrata);
					$paquetes->appendChild($paquete);
				}
				
				$docCobertura->appendChild($cod_cob);
				$docCobertura->appendChild($suma_aseg);
				$docCobertura->appendChild($paquetes);
				
				$docCoberturas->appendChild($docCobertura);
			}
		}
		
		$docCotizar->appendChild($docCoberturas);
		
		$datosVarCob = $doc->createElement("datos_var_cob");
		foreach($cotizacion["xml"]["ofertaComercial"]["datos_var_cob"] as $key => $val){
			if($val!=""){
				switch($key){
					case "suma_aseg_2800":
						$docVar = $doc->createElement($key, $suma);
						break;
					case "imp_deducible_2800":
						$docVar = $doc->createElement($key, $deducible);
						break;
					case "pct_tope_coaseguro_2800":
						//$docVar = $doc->createElement($key, $cotizacion["xml"]["xmlCoberturas"]["agrupado"]["grupo"][0]["coberturas"]["cobertura"][0]["intervalos"]["topecoaseguro"]["intervalo"][(count($cotizacion["xml"]["xmlCoberturas"]["agrupado"]["grupo"][0]["coberturas"]["cobertura"][0]["intervalos"]["topecoaseguro"]["intervalo"])-1)]["key"]);
						$docVar = $doc->createElement($key, $tope);
						break;
					case "cod_deducible_2800":
						$docVar = $doc->createElement($key, 1);
						break;
					case "cod_tabulador_2800":
						//if(is_null($cotizacionDatos->tabulador))
						//	$docVar = $doc->createElement($key, 4);
						//else{
							//switch($cotizacionDatos->tabulador){
							switch($recotizacion->tabulador){
								case "C":
									$docVar = $doc->createElement($key, 3);
									break;
								case "D":
									$docVar = $doc->createElement($key, 4);
									break;
								case "E":
									$docVar = $doc->createElement($key, 5);
									break;
								case "F":
									$docVar = $doc->createElement($key, 6);
									break;
							}
						//}
						break;
					case "cod_red_hosp_2800":
						$docVar = $doc->createElement($key, $hospitales);
						break;
					case "suma_aseg_2830":
						$docVar = $doc->createElement($key, $deducible);
						break;
					case "suma_aseg_2821":
						$docVar = $doc->createElement($key, $emergenciaExtranjero);
						break;
					case "cod_plan_dental_2824":
					case "cod_plan_vision_2825":
						//switch($cotizacionDatos->sa_dental){
						switch($recotizacion->dental){
							case "plata":
								$docVar = $doc->createElement($key, 1);
								break;
							case "oro":
								$docVar = $doc->createElement($key, 2);
								break;
							case null:
								$docVar = $doc->createElement($key, 1);
								break;
						}
						break;
					case "suma_aseg_2827":
						if(!is_null($recotizacion->sa_maternidad)){
							if($recotizacion->sa_maternidad>0)
								$docVar = $doc->createElement($key, $recotizacion->sa_maternidad);
							else
								$docVar = $doc->createElement($key, $val);
						}
						else
							$docVar = $doc->createElement($key, $val);
						break;
					default:
						$docVar = $doc->createElement($key, $val);
						break;
				}
				
				$datosVarCob->appendChild($docVar);
			}
		}
		//if(!is_null($cotizacionDatos->sa_maternidad)){
		if(!is_null($recotizacion->sa_maternidad)){
			$docVar = $doc->createElement("imp_gtos_recien_nac", 5000);
			$datosVarCob->appendChild($docVar);
		}
			
		$docCotizar->appendChild($datosVarCob);
		
		$datosFijos = $doc->createElement("datos_fijos");
		$cod_user_captura = $doc->createElement("cod_usr_captura", "APPZALID");
		$ramo = $doc->createElement("ramo", 288);
		$agente = $doc->createElement("agente", 15770);
		$fec_efec = $doc->createElement("fec_efec",  date("dmY"));
		$fec_vcto = $doc->createElement("fec_vcto", date("dmY", strtotime("+1 year")));
		$mca_personalizado = $doc->createElement("mca_personalizado", "N");
		$mca_perfilador = $doc->createElement("mca_perfilador", "N");
		$empresarial = $doc->createElement("empresarial", "N");
		$num_asegurados = $doc->createElement("num_asegurados", count($integrantes));
		$desc_paquete = $doc->createElement("desc_paquete", "paquete 1");
		$contrato = $doc->createElement("contrato");
		$poliza_grupo = $doc->createElement("poliza_grupo");
		
		$datosFijos->appendChild($cod_user_captura);
		$datosFijos->appendChild($ramo);
		$datosFijos->appendChild($agente);
		$datosFijos->appendChild($fec_efec);
		$datosFijos->appendChild($fec_vcto);
		$datosFijos->appendChild($mca_personalizado);
		$datosFijos->appendChild($mca_perfilador);
		$datosFijos->appendChild($empresarial);
		$datosFijos->appendChild($num_asegurados);
		$datosFijos->appendChild($desc_paquete);
		$datosFijos->appendChild($contrato);
		$datosFijos->appendChild($poliza_grupo);
		$docCotizar->appendChild($datosFijos);
		
		$datosVar = $doc->createElement("datos_var");
		$codigo_val = $doc->createElement("codigo_val");
		$campania_val = $doc->createElement("campania_val");
		$campania_desc = $doc->createElement("campania_desc", 0);
		
		$cesion_comision = $doc->createElement("cesion_comision", 0);
		$elEstado = $doc->createElement("estado", $estado->id_estado);
		$estado_desc = $doc->createElement("estado_desc", $estado->estado);
		if($poblacion){
			$prov = $doc->createElement("prov", $poblacion->clave_mapfre);
			$prov_desc = $doc->createElement("prov_desc", $poblacion->poblacion);
		}
		else{
			$prov = $doc->createElement("prov");
			$prov_desc = $doc->createElement("prov_desc");
		}
		$cod_proveedor = $doc->createElement("cod_proveedor", 3);
		
		$dependientes = $doc->createElement("dependientes");
		$x = 1;
		foreach($integrantes as $i){
			$nombre = str_replace("ñ", "n", str_replace("Ñ", "N", str_replace("ú", "u", str_replace("ó", "o", str_replace("í", "i", str_replace("é", "e", str_replace("á", "a", str_replace("Ú", "U", str_replace("Ó", "O", str_replace("Í", "I", str_replace("É", "E", str_replace("Á", "A", $i["nombre"]))))))))))));
			$dependiente = $doc->createElement("dependiente");
			$el = $doc->createElement("num_riesgo", $x);
			$dependiente->appendChild($el);
			$el = $doc->createElement("id_parentesco", $i["id_parentesco"]);
			$dependiente->appendChild($el);
			$el = $doc->createElement("nombre", $nombre);
			$dependiente->appendChild($el);
			$el = $doc->createElement("apll_pat");
			$dependiente->appendChild($el);
			$el = $doc->createElement("apll_mat");
			$dependiente->appendChild($el);
			$el = $doc->createElement("sexo", $i["id_sexo"]);
			$dependiente->appendChild($el);
			$el = $doc->createElement("id_ocupacion");
			$dependiente->appendChild($el);
			$el = $doc->createElement("parentesco_desc", $i["parentesco"]);
			$dependiente->appendChild($el);
			$el = $doc->createElement("sexo_desc", $i["sexo"]);
			$dependiente->appendChild($el);
			$el = $doc->createElement("ocupacion_desc");
			$dependiente->appendChild($el);
			$el = $doc->createElement("fecha_nac");
			$dependiente->appendChild($el);
			$el = $doc->createElement("edad", $i["edad"]);
			$dependiente->appendChild($el);
			$el = $doc->createElement("rfc");
			$dependiente->appendChild($el);
			$el = $doc->createElement("curp");
			$dependiente->appendChild($el);
			$el = $doc->createElement("clm");
			$dependiente->appendChild($el);
			$el = $doc->createElement("ca", 0);
			$dependiente->appendChild($el);
			$el = $doc->createElement("ca2", 0);
			$dependiente->appendChild($el);
			$el = $doc->createElement("descuento", 0);
			$dependiente->appendChild($el);
			$el = $doc->createElement("num_renovacion", 0);
			$dependiente->appendChild($el);
			$el = $doc->createElement("id_deporte");
			$dependiente->appendChild($el);
			$el = $doc->createElement("deporte_desc");
			$dependiente->appendChild($el);
			$dependientes->appendChild($dependiente);
			$x++;
		}
		
		$datosVar->appendChild($codigo_val);
		$datosVar->appendChild($campania_val);
		$datosVar->appendChild($campania_desc);
		$datosVar->appendChild($dependientes);
		$datosVar->appendChild($cesion_comision);
		$datosVar->appendChild($elEstado);
		$datosVar->appendChild($estado_desc);
		$datosVar->appendChild($prov);
		$datosVar->appendChild($prov_desc);
		$datosVar->appendChild($cod_proveedor);
		$docCotizar->appendChild($datosVar);
		
		$docXml->appendChild($docCotizar);
		$doc->appendChild($docXml);
		$xml = $doc->saveHTML();
		if(strpos($xml, "\n"))
			$xml = substr($xml, 0, strpos($xml, "\n"));
		$xml = '"' . $xml . '"';
		
		$ch = curl_init(self::$wsUrl . "WebApiAARCO/api/recotiza/" . $cotizacion["xml"]["num_solicitud"]);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		$res = curl_exec($ch);
		
		$cotizacion = json_decode($res, true);
		return array("cotizacion" => $cotizacion, "xml" => $xml);
	}

	public static function tablaDatosWS2023($admin = false, $mostrarContratar = true, $pdf = false){
		$html = '';
		//$conceptos = Paqueteconcepto::orderBy('orden')->get();
		if(self::$mostrarMaternidad){
			$conceptos = Paqueteconcepto2023::where('id_concepto', '<>', 8)
				->where('id_concepto', '<>', 5)
				->where('id_concepto', '<>', 6)
				->orderBy('orden')->get();
		}else{
			$conceptos = Paqueteconcepto2023::where('id_concepto', '<>', 8)
				->where('id_concepto', '<>', 5)
				->where('id_concepto', '<>', 6)
				->where('id_concepto', '<>', 7)
				->orderBy('orden')->get();
		}
		if(count($conceptos) > 0){
			$html = '<table width="100%" '.(($admin == true) ? 'class="table table-bordered table-dark table-invoice"' : 'class="table-list"').' '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>
						<thead>
							<tr>
								<th colspan="2"></th>';
			//$numeroPaquetes = 0;
			$numeroPaquetes = 1;
			if(self::$cotizacionDatos->nivel_amplio==1)
				$numeroPaquetes = 2;
			foreach(self::$aseguradoras AS $aseguradora){
				if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
					//$html .= '<th colspan="'.count($aseguradora->paquetes).'" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.$aseguradora->id_aseguradora.' - '.$aseguradora->nombre.'</strong></th>';
					if($aseguradora->id_aseguradora==2){
						if(self::$cotizacionDatos->nivel_amplio==1)
							$html .= '<th colspan="'.(count($aseguradora->paquetes) + 1).'" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.HTML::image('assets/images/aseguradoras/'.$aseguradora->id_aseguradora.'.jpg', '', (($pdf == true) ? ((count($aseguradora->paquetes) == 1) ? array('style' => "width: 100px;") : '') : '') ).'</strong></th>';
						else
							$html .= '<th colspan="'.(count($aseguradora->paquetes) + 2).'" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.HTML::image('assets/images/aseguradoras/'.$aseguradora->id_aseguradora.'.jpg', '', (($pdf == true) ? ((count($aseguradora->paquetes) == 1) ? array('style' => "width: 100px;") : '') : '') ).'</strong></th>';
					}
					else
						$html .= '<th colspan="'.count($aseguradora->paquetes).'" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.HTML::image('assets/images/aseguradoras/'.$aseguradora->id_aseguradora.'.jpg', '', (($pdf == true) ? ((count($aseguradora->paquetes) == 1) ? array('style' => "width: 100px;") : '') : '') ).'</strong></th>';
					$numeroPaquetes += count($aseguradora->paquetes);
				}
			}
			$html .= '</tr>
						</thead>';
			// Se agrega precio superior con descuento
			$html .= '<tr>
							<td colspan="2" width="'.(100/($numeroPaquetes)).'%"></td>"';
			//$html .= '<td>1</td><td>2</td><td>3</td>';
			foreach(self::$aseguradoras AS $aseguradora){
				$pct = 1 + ($aseguradora->configuracion->inflar / 100);
				if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
					if(count($aseguradora->paquetes) > 0){
						if($aseguradora->id_aseguradora==2){
							if(self::$cotizacionDatos->nivel_amplio==0){
								foreach($aseguradora->paquetes()->where('activo', '=', 1)->orWhere('id_paquete', '=', 10)->orderBy('orden')->get() AS $paquete){
									$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
										->where("tipo", "=", self::$sumaAsegurada . self::$deducible)
										->where("hospitales", "=", $paquete->paquete_campo)
										->first();
									if($recotizacion){
										$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="contado-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">
													<strike>' . number_format($recotizacion->contado * $pct, 0, ".", ",") . '</strike> 
													<br><strong>' . number_format($recotizacion->contado, 0, ".", ",") . 
													'</strong></td>';
										//$html .= '<td>Si paso por aqui</td>';
									}
									else
										$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="contado-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '"><strong>-</strong></td>';
								}
							}
							else{
								foreach($aseguradora->paquetes()->where('activo', '=', 1)->orWhere('id_paquete', '=', 10)->orWhere('id_paquete', '=', 11)->orderBy('orden')->get() AS $paquete){
									$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
										->where("tipo", "=", self::$sumaAsegurada . self::$deducible)
										->where("hospitales", "=", $paquete->paquete_campo)
										->first();
									if($recotizacion){
										$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="contado-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">
													<strike>' . number_format($recotizacion->contado * $pct, 0, ".", ",") . '</strike>
													<br><strong>' . number_format($recotizacion->contado, 0, ".", ",") .
													'</strong></td>';
										//$html .= '<td>Si paso por aqui 2</td>';
									}
									else
										$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="contado-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '"><strong>-</strong></td>';
								}
							}
						}
						else{
							foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
								$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>
											<strike>' . number_format((int)self::$calculos[$paquete->id_paquete]['total'] * $pct, 0, ".", ",") . '</strike>
											<br><strong>' . SistemaFunciones::formatMoney1((int)self::$calculos[$paquete->id_paquete]['total']) .
											'</strong></td>';
								//$html .= '<td>Si paso por aqui 3</td>';
							}
						}
					}
				}
			}
			$html .= '</tr>';
			// Fin de precio superior con descuento
			$html .= '<tr>
							<td colspan="2" width="'.(100/($numeroPaquetes)).'%"></td>';
			foreach(self::$aseguradoras AS $aseguradora){
				if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
					if(count($aseguradora->paquetes) > 0){
						if($aseguradora->id_aseguradora==2){
							if(self::$cotizacionDatos->nivel_amplio==0){
								foreach($aseguradora->paquetes()->where('activo', '=', 1)->orWhere('id_paquete', '=', 10)->orderBy('orden')->get() AS $paquete){
									$html .= '<td class="alignCenter mapfrePaquete" data-hospitales="' . $paquete->paquete_campo . '" width="'.(100/($numeroPaquetes+2)).'%" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.$paquete->paquete.'</strong></td>';
								}
							}
							else{
								foreach($aseguradora->paquetes()->where('activo', '=', 1)->orWhere('id_paquete', '=', 10)->orWhere('id_paquete', '=', 11)->orderBy('orden')->get() AS $paquete){
									$html .= '<td class="alignCenter mapfrePaquete" data-hospitales="' . $paquete->paquete_campo . '" width="'.(100/($numeroPaquetes+2)).'%" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.$paquete->paquete.'</strong></td>';
								}
							}
						}
						else{
							foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
								$html .= '<td class="alignCenter" width="'.(100/($numeroPaquetes+2)).'%" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.$paquete->paquete.'</strong></td>';
							}
						}
					}
				}
			}
			$html .= '</tr>';
			
			if(self::$cotizacionDatos->nivel_amplio==0){
				$paquetes = Paquete::where('id_aseguradora', '=', 2)
						->where(function($query){
							$query->where('activo', '=', 1)
								->orWhere('id_paquete', '=', 10);
						})
						->orderBy('orden')
						->get();
			}
			else{
				$paquetes = Paquete::where('id_aseguradora', '=', 2)
						->where(function($query){
							$query->where('activo', '=', 1)
								->orWhere('id_paquete', '=', 10)
								->orWhere('id_paquete', '=', 11);
						})
						->orderBy('orden')
						->get();
			}
			
			foreach($conceptos AS $concepto){
				$htmlTmp = '<tr>
							<td colspan="2" class="alignLeft" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.(($pdf == true)?utf8_decode($concepto->concepto):$concepto->concepto).'</td>';
				foreach(self::$aseguradoras AS $aseguradora){
					if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
						if(count($aseguradora->paquetes) > 0){
							if($aseguradora->id_aseguradora==2){
								switch($concepto->id_concepto){
									case 1:
										foreach($paquetes as $paquete){
											$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
													->where("tipo", "=", "sadb")
													->where("hospitales", "=", $paquete->paquete_campo)
													->first();
											$htmlTmp .= '<td id="' . $paquete->paquete_campo . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.number_format($recotizacion->sa, 0, ".", ",").'</td>';
										}
										break;
									case 2:
										foreach($paquetes as $paquete){
											$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
													->where("tipo", "=", "sadb")
													->where("hospitales", "=", $paquete->paquete_campo)
													->first();
											$htmlTmp .= '<td id="' . $paquete->paquete_campo . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.number_format($recotizacion->deducible, 0, ".", ",").'</td>';
										}
										break;
									case 3:
										foreach($paquetes as $paquete){
											$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
													->where("tipo", "=", "sadb")
													->where("hospitales", "=", $paquete->paquete_campo)
													->first();
											$htmlTmp .= '<td id="' . $paquete->paquete_campo . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.$recotizacion->coaseguro . '%</td>';
										}
										break;
									case 4:
										foreach($paquetes as $paquete){
											$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
													->where("tipo", "=", "sadb")
													->where("hospitales", "=", $paquete->paquete_campo)
													->first();
											$htmlTmp .= '<td id="' . $paquete->paquete_campo . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.number_format($recotizacion->tope_coaseguro, 0, ".", ",").'</td>';
										}
										break;
									case 7:
										foreach($paquetes as $paquete){
											$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
													->where("tipo", "=", "sadb")
													->where("hospitales", "=", $paquete->paquete_campo)
													->first();
											if(!is_null($recotizacion->sa_maternidad))
												$htmlTmp .= '<td id="' . $paquete->paquete_campo . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.number_format($recotizacion->sa_maternidad, 0, ".", ",").'</td>';
											else
												$htmlTmp .= '<td id="' . $paquete->paquete_campo . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>No</td>';
										}
										break;
									case 10:
										foreach($paquetes as $paquete){
											switch($paquete->paquete_campo){
												case "esencial":
													$htmlTmp .= '<td id="' . $paquete->paquete_campo . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>C</td>';
													break;
												case "optima":
													$htmlTmp .= '<td id="' . $paquete->paquete_campo . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>B y C</td>';
													break;
												case "completa":
													$htmlTmp .= '<td id="' . $paquete->paquete_campo . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>A, B y C</td>';
													break;
												case "amplia":
													$htmlTmp .= '<td id="' . $paquete->paquete_campo . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>AA, A, B y C</td>';
													break;
											}
										}
										break;
									case 16:
										foreach($paquetes as $paquete){
											$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
													->where("tipo", "=", "sadb")
													->where("hospitales", "=", $paquete->paquete_campo)
													->first();
											switch($recotizacion->tabulador){
												case "C":
													$htmlTmp .= '<td id="' . $paquete->paquete_campo . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>B&aacute;sico</td>';
													break;
												case "D":
													$htmlTmp .= '<td id="' . $paquete->paquete_campo . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>Normal</td>';
													break;
												case "E":
													$htmlTmp .= '<td id="' . $paquete->paquete_campo . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>Medio</td>';
													break;
												case "F":
													$htmlTmp .= '<td id="' . $paquete->paquete_campo . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>Alto</td>';
													break;
											}
										}
										break;
									case 9:
										foreach($paquetes as $paquete){
											$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
													->where("tipo", "=", "sadb")
													->where("hospitales", "=", $paquete->paquete_campo)
													->first();
											$htmlTmp .= '<td id="' . $paquete->paquete_campo . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.number_format($recotizacion->emergencia_extranjero, 0, ".", ",").'</td>';
										}
										break;
									case 17:
										foreach($paquetes as $paquete){
											$htmlTmp .= '<td id="' . $paquete->paquete_campo . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'. 'S&iacute;' .'</td>';
										}
										break;
									case 18:
										foreach($paquetes as $paquete){
											$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
													->where("tipo", "=", "sadb")
													->where("hospitales", "=", $paquete->paquete_campo)
													->first();
											$htmlTmp .= '<td id="' . $paquete->paquete_campo . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.(($recotizacion->reduccion_deducible==1) ? 'S&iacute;' : 'No').'</td>';
										}
										break;
									case 19:
										foreach($paquetes as $paquete){
											$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
													->where("tipo", "=", "sadb")
													->where("hospitales", "=", $paquete->paquete_campo)
													->first();
											$htmlTmp .= '<td id="' . $paquete->paquete_campo . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.((!is_null($recotizacion->dental)) ? (($recotizacion->dental=="plata") ? "Plata" : "Oro") : "No").'</td>';
										}
										break;
									case 20:
										foreach($paquetes as $paquete){
											$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
													->where("tipo", "=", "sadb")
													->where("hospitales", "=", $paquete->paquete_campo)
													->first();
											$htmlTmp .= '<td id="' . $paquete->paquete_campo . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.(($recotizacion->complicaciones==1) ? 'S&iacute;' : 'No').'</td>';
										}
										break;
									case 21:
										foreach($paquetes as $paquete){
											$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
													->where("tipo", "=", "sadb")
													->where("hospitales", "=", $paquete->paquete_campo)
													->first();
											$htmlTmp .= '<td id="' . $paquete->paquete_campo . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.(($recotizacion->vanguardia==1) ? 'S&iacute;' : 'No').'</td>';
										}
										break;
									case 22:
										foreach($paquetes as $paquete){
											$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
													->where("tipo", "=", "sadb")
													->where("hospitales", "=", $paquete->paquete_campo)
													->first();
											$htmlTmp .= '<td id="' . $paquete->paquete_campo . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.(($recotizacion->multiregion==1) ? 'S&iacute;' : 'No').'</td>';
										}
										break;
									case 23:
										foreach($paquetes as $paquete){
											$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
													->where("tipo", "=", "sadb")
													->where("hospitales", "=", $paquete->paquete_campo)
													->first();
											$htmlTmp .= '<td id="' . $paquete->paquete_campo . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.(($recotizacion->preexistentes==1) ? 'S&iacute;' : 'No').'</td>';
										}
										break;
									case 24:
										foreach($paquetes as $paquete){
											$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
													->where("tipo", "=", "sadb")
													->where("hospitales", "=", $paquete->paquete_campo)
													->first();
											$htmlTmp .= '<td id="' . $paquete->paquete_campo . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.(($recotizacion->catastroficas==1) ? 'S&iacute;' : 'No').'</td>';
										}
										break;
									case 25:
										foreach($paquetes as $paquete){
											$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
													->where("tipo", "=", "sadb")
													->where("hospitales", "=", $paquete->paquete_campo)
													->first();
											$htmlTmp .= '<td id="' . $paquete->paquete_campo . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.(($recotizacion->funeraria==1) ? 'S&iacute;' : 'No').'</td>';
										}
										break;
									default:
										if(self::$cotizacionDatos->nivel_amplio==0){
											foreach($aseguradora->paquetes()->where('activo', '=', 1)->orWhere('id_paquete', '=', 10)->orderBy('orden')->get() AS $paquete){
												$valorConcepto = $paquete->tarifaValor2023()->where('id_concepto', '=', $concepto->id_concepto)->get();
												if(count($valorConcepto) == 1){
													$valorConcepto = $valorConcepto[0]->{self::$sumaAsegurada.'_'.self::$deducible};
												}
												$htmlTmp .= '<td id="' . $paquete->paquete_campo . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.(($pdf == true)?utf8_decode($valorConcepto):$valorConcepto).'</td>';
											}
										}
										else{
											foreach($aseguradora->paquetes()->where('activo', '=', 1)->orWhere('id_paquete', '=', 10)->orWhere('id_paquete', '=', 11)->orderBy('orden')->get() AS $paquete){
												$valorConcepto = $paquete->tarifaValor2023()->where('id_concepto', '=', $concepto->id_concepto)->get();
												if(count($valorConcepto) == 1){
													$valorConcepto = $valorConcepto[0]->{self::$sumaAsegurada.'_'.self::$deducible};
												}
												$htmlTmp .= '<td id="' . $paquete->paquete_campo . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.(($pdf == true)?utf8_decode($valorConcepto):$valorConcepto).'</td>';
											}
										}
										break;
								}
							}
							else{
								foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
									$valorConcepto = $paquete->tarifaValor2023()->where('id_concepto', '=', $concepto->id_concepto)->get();
									if(count($valorConcepto) == 1){
										$valorConcepto = $valorConcepto[0]->{self::$sumaAsegurada.'_'.self::$deducible};
									}
									$htmlTmp .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.(($pdf == true)?utf8_decode($valorConcepto):$valorConcepto).'</td>';
								}
							}
						}
					}
				}
				$htmlTmp .= '</tr>';
				switch ($concepto->id_concepto) {
					case 7:
					case 8:
						if(self::$mostrarMaternidad == true){
							$html .= $htmlTmp;
						}
					break;
					default:
						$html .= $htmlTmp;
					break;
				}
			}
			$html .= '<tr>
						<td colspan="2" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>Pago de contado</strong></td>';
			foreach(self::$aseguradoras AS $aseguradora){
				if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
					if(count($aseguradora->paquetes) > 0){
						if($aseguradora->id_aseguradora==2){
							if(self::$cotizacionDatos->nivel_amplio==0){
								foreach($aseguradora->paquetes()->where('activo', '=', 1)->orWhere('id_paquete', '=', 10)->orderBy('orden')->get() AS $paquete){
									$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
										->where("tipo", "=", self::$sumaAsegurada . self::$deducible)
										->where("hospitales", "=", $paquete->paquete_campo)
										->first();
									if($recotizacion)
										$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="contado-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '"><strong>'.number_format($recotizacion->contado, 0, ".", ",").'</strong></td>';
									else
										$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="contado-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '"><strong>-</strong></td>';
								}
							}
							else{
								foreach($aseguradora->paquetes()->where('activo', '=', 1)->orWhere('id_paquete', '=', 10)->orWhere('id_paquete', '=', 11)->orderBy('orden')->get() AS $paquete){
									$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
										->where("tipo", "=", self::$sumaAsegurada . self::$deducible)
										->where("hospitales", "=", $paquete->paquete_campo)
										->first();
									if($recotizacion)
										$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="contado-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '"><strong>'.number_format($recotizacion->contado, 0, ".", ",").'</strong></td>';
									else
										$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="contado-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '"><strong>-</strong></td>';
								}
							}
						}
						else{
							foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
								$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.SistemaFunciones::formatMoney1((int)self::$calculos[$paquete->id_paquete]['total']).'</strong></td>';
							}
						}
					}
				}
			}
			$html .= '</tr>';
			if($mostrarContratar == true){
				$html .= '<tr>
							<td colspan="2">&nbsp;</td>';
				foreach(self::$aseguradoras AS $aseguradora){
					if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
						if(count($aseguradora->paquetes) > 0){
							if($aseguradora->id_aseguradora==2){
								if(self::$cotizacionDatos->nivel_amplio==0){
									foreach($aseguradora->paquetes()->where('activo', '=', 1)->orWhere('id_paquete', '=', 10)->orderBy('orden')->get() AS $paquete){
										$html .= '<td><a href="'.URL::to('/contratarPaquete/'.self::$cotizacionDatos->id_cotizacion.'/'.self::$cotizacionDatos->secret.'/'.$paquete->paquete_campo.'/'.self::$sumaAsegurada.'/'.self::$deducible).'" class="but_phone" style="cursor: pointer;"><i class="fa fa-thumbs-o-up"></i> Contratar</a> </td>';
									}
								}
								else{
									foreach($aseguradora->paquetes()->where('activo', '=', 1)->orWhere('id_paquete', '=', 10)->orWhere('id_paquete', '=', 11)->orderBy('orden')->get() AS $paquete){
										$html .= '<td><a href="'.URL::to('/contratarPaquete/'.self::$cotizacionDatos->id_cotizacion.'/'.self::$cotizacionDatos->secret.'/'.$paquete->paquete_campo.'/'.self::$sumaAsegurada.'/'.self::$deducible).'" class="but_phone" style="cursor: pointer;"><i class="fa fa-thumbs-o-up"></i> Contratar</a> </td>';
									}
								}
							}
							else{
								foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
									$html .= '<td><a href="'.URL::to('/contratarPaquete/'.self::$cotizacionDatos->id_cotizacion.'/'.self::$cotizacionDatos->secret.'/'.$paquete->paquete_campo.'/'.self::$sumaAsegurada.'/'.self::$deducible).'" class="but_phone" style="cursor: pointer;"><i class="fa fa-thumbs-o-up"></i> Contratar</a> </td>';
								}
							}
						}
					}
				}
				$html .= '</tr>';
				
			}
			if(!$pdf && $admin == false){
				$html .= '<tr>
						<table width="100%" class="table-list">
							<tr>
								<td colspan="'.($numeroPaquetes+2).'"><a href="#" class="mostrarPreciosMensualidades"><strong>Mostrar pagos en mensualidades</strong></a></td>
							</tr>
						</table>
						<table width="100%" class="table-list tabla-precios hide">';
			}
			foreach(self::$pagosDiferidos AS $meses => $texto){
				$html .= '<tr>
							<td rowspan="2" class="alignVerticalMiddle" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>Pago '.$texto.'</strong></td>
							<td '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>Primer pago</td>';
				foreach(self::$aseguradoras AS $aseguradora){
					if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
						if(count($aseguradora->paquetes) > 0){
							if($aseguradora->id_aseguradora==2){
								if(self::$cotizacionDatos->nivel_amplio==0){
									foreach($aseguradora->paquetes()->where('activo', '=', 1)->orWhere('id_paquete', '=', 10)->orderBy('orden')->get() AS $paquete){
										$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
											->where("tipo", "=", self::$sumaAsegurada . self::$deducible)
											->where("hospitales", "=", $paquete->paquete_campo)
											->first();
										if($recotizacion){
											switch($meses){
												case 6:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="semestral-1-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ '.number_format($recotizacion->semestral_primer, 0, ".", ",").'</td>';
													break;
												case 3:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="trimestral-1-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ '.number_format($recotizacion->trimestral_primer, 0, ".", ",").'</td>';
													break;
												case 1:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="mensual-1-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ '.number_format($recotizacion->mensual_primer, 0, ".", ",").'</td>';
													break;
											}
										}
										else{
											switch($meses){
												case 6:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="semestral-1-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ -</td>';
													break;
												case 3:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="trimestral-1-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ -</td>';
													break;
												case 1:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="mensual-1-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ -</td>';
													break;
											}
										}
									}
								}
								else{
									foreach($aseguradora->paquetes()->where('activo', '=', 1)->orWhere('id_paquete', '=', 10)->orWhere('id_paquete', '=', 11)->orderBy('orden')->get() AS $paquete){
										$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
											->where("tipo", "=", self::$sumaAsegurada . self::$deducible)
											->where("hospitales", "=", $paquete->paquete_campo)
											->first();
										if($recotizacion){
											switch($meses){
												case 6:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="semestral-1-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ '.number_format($recotizacion->semestral_primer, 0, ".", ",").'</td>';
													break;
												case 3:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="trimestral-1-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ '.number_format($recotizacion->trimestral_primer, 0, ".", ",").'</td>';
													break;
												case 1:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="mensual-1-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ '.number_format($recotizacion->mensual_primer, 0, ".", ",").'</td>';
													break;
											}
										}
										else{
											switch($meses){
												case 6:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="semestral-1-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ -</td>';
													break;
												case 3:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="trimestral-1-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ -</td>';
													break;
												case 1:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="mensual-1-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ -</td>';
													break;
											}
										}
									}
								}
							}
							else{
								foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
									$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>$ '.SistemaFunciones::formatMoney1((int)self::$calculos[$paquete->id_paquete]['diferidos'][$meses][1]).'</td>';
								}
							}
						}
					}
				}
				$html .= '</tr>
						<tr>
							<td '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>Posteriores</td>';
				foreach(self::$aseguradoras AS $aseguradora){
					if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
						if(count($aseguradora->paquetes) > 0){
							if($aseguradora->id_aseguradora==2){
								if(self::$cotizacionDatos->nivel_amplio==0){
									foreach($aseguradora->paquetes()->where('activo', '=', 1)->orWhere('id_paquete', '=', 10)->orderBy('orden')->get() AS $paquete){
										$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
											->where("tipo", "=", self::$sumaAsegurada . self::$deducible)
											->where("hospitales", "=", $paquete->paquete_campo)
											->first();
										if($recotizacion){
											switch($meses){
												case 6:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="semestral-2-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ '.number_format($recotizacion->semestral_posterior, 0, ".", ",").'</td>';
													break;
												case 3:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="trimestral-2-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ '.number_format($recotizacion->trimestral_posterior, 0, ".", ",").'</td>';
													break;
												case 1:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="mensual-2-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ '.number_format($recotizacion->mensual_posterior, 0, ".", ",").'</td>';
													break;
											}
										}
										else{
											switch($meses){
												case 6:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="semestral-2-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ -</td>';
													break;
												case 3:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="trimestral-2-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ -</td>';
													break;
												case 1:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="mensual-2-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ -</td>';
													break;
											}
										}
									}
								}
								else{
									foreach($aseguradora->paquetes()->where('activo', '=', 1)->orWhere('id_paquete', '=', 10)->orWhere('id_paquete', '=', 11)->orderBy('orden')->get() AS $paquete){
										$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
											->where("tipo", "=", self::$sumaAsegurada . self::$deducible)
											->where("hospitales", "=", $paquete->paquete_campo)
											->first();
										if($recotizacion){
											switch($meses){
												case 6:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="semestral-2-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ '.number_format($recotizacion->semestral_posterior, 0, ".", ",").'</td>';
													break;
												case 3:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="trimestral-2-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ '.number_format($recotizacion->trimestral_posterior, 0, ".", ",").'</td>';
													break;
												case 1:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="mensual-2-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ '.number_format($recotizacion->mensual_posterior, 0, ".", ",").'</td>';
													break;
											}
										}
										else{
											switch($meses){
												case 6:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="semestral-2-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ -</td>';
													break;
												case 3:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="trimestral-2-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ -</td>';
													break;
												case 1:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="mensual-2-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ -</td>';
													break;
											}
										}
									}
								}
							}
							else{
								foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
									$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>$ '.SistemaFunciones::formatMoney1((int)self::$calculos[$paquete->id_paquete]['diferidos'][$meses][2]).'</td>';
								}
							}
						}
					}
				}
				$html .= '</tr>';
			}
			if(!$pdf && $admin == false){
				$html .= '</tr>
							</table>';
			}
			$html .= '</table>';
		}
		return $html;
	}
	
	public static function tablaDatosWS2023Paquetes($admin = false, $mostrarContratar = true, $pdf = false, $paquetes = array()){
		$html = '';
		//$conceptos = Paqueteconcepto::orderBy('orden')->get();
		if(self::$mostrarMaternidad){
			$conceptos = Paqueteconcepto2023::where('id_concepto', '<>', 8)
				->where('id_concepto', '<>', 5)
				->where('id_concepto', '<>', 6)
				->orderBy('orden')->get();
		}else{
			$conceptos = Paqueteconcepto2023::where('id_concepto', '<>', 8)
				->where('id_concepto', '<>', 5)
				->where('id_concepto', '<>', 6)
				->where('id_concepto', '<>', 7)
				->orderBy('orden')->get();
		}
		if(count($conceptos) > 0){
			$numeroPaquetes = 1;
			$aAseguradoras = array();
			foreach(DB::table('paquetes')
				->select('aseguradoras.id_aseguradora', 'aseguradoras.nombre')->distinct()
				->join('aseguradoras', 'paquetes.id_aseguradora', '=', 'aseguradoras.id_aseguradora')
				->whereIn('paquetes.id_paquete', $paquetes)
				->orderBy('aseguradoras.orden', 'asc')
				->get() as $a){
				$aAseguradoras[] = array(
					"id" => $a->id_aseguradora,
					"nombre" => $a->nombre,
					"paquetes" => array()
				);
			}
			foreach(Paquete::whereIn('id_paquete', $paquetes)->orderBy('orden', 'asc')->get() as $p){
				for($x=0;$x<count($aAseguradoras);$x++){
					if($aAseguradoras[$x]["id"]==$p->id_aseguradora){
						$aAseguradoras[$x]["paquetes"][] = array(
							"id_paquete" => $p['id_paquete'],
							"paquete" => $p['paquete'],
							"paquete_campo" => $p['paquete_campo'],
							"descripcion_backend" => $p['descripcion_backend']
						);
						$numeroPaquetes ++;
						break;
					}
				}
			}
			
			$html = '<table width="100%" '.(($admin == true) ? 'class="table table-bordered table-dark table-invoice"' : 'class="table-list"').' '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>
						<thead>
							<tr>
								<th colspan="2"></th>';
			//$numeroPaquetes = 0;
			//$numeroPaquetes = 1;
			//if(self::$cotizacionDatos->nivel_amplio==1)
			//	$numeroPaquetes = 2;
			
			/*foreach(self::$aseguradoras AS $aseguradora){
				if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
					//$html .= '<th colspan="'.count($aseguradora->paquetes).'" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.$aseguradora->id_aseguradora.' - '.$aseguradora->nombre.'</strong></th>';
					if($aseguradora->id_aseguradora==2){
						if(self::$cotizacionDatos->nivel_amplio==1)
							$html .= '<th colspan="'.(count($aseguradora->paquetes) + 1).'" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.HTML::image('assets/images/aseguradoras/'.$aseguradora->id_aseguradora.'.jpg', '', (($pdf == true) ? ((count($aseguradora->paquetes) == 1) ? array('style' => "width: 100px;") : '') : '') ).'</strong></th>';
						else
							$html .= '<th colspan="'.(count($aseguradora->paquetes) + 2).'" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.HTML::image('assets/images/aseguradoras/'.$aseguradora->id_aseguradora.'.jpg', '', (($pdf == true) ? ((count($aseguradora->paquetes) == 1) ? array('style' => "width: 100px;") : '') : '') ).'</strong></th>';
					}
					else
						$html .= '<th colspan="'.count($aseguradora->paquetes).'" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.HTML::image('assets/images/aseguradoras/'.$aseguradora->id_aseguradora.'.jpg', '', (($pdf == true) ? ((count($aseguradora->paquetes) == 1) ? array('style' => "width: 100px;") : '') : '') ).'</strong></th>';
					$numeroPaquetes += count($aseguradora->paquetes);
				}
			}*/
			foreach($aAseguradoras as $aseguradora){
				$html .= '<th colspan="'.count($aseguradora['paquetes']).'" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.HTML::image('assets/images/aseguradoras/'.$aseguradora['id'].'.jpg', '', (($pdf == true) ? ((count($aseguradora['paquetes']) == 1) ? array('style' => "width: 100px;") : '') : '') ).'</strong></th>';
			}
			
			$html .= '</tr>
						</thead>
						<tr>
							<td colspan="2" width="'.(100/($numeroPaquetes)).'%"></td>';
			
			/*foreach(self::$aseguradoras AS $aseguradora){
				if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
					if(count($aseguradora->paquetes) > 0){
						if($aseguradora->id_aseguradora==2){
							if(self::$cotizacionDatos->nivel_amplio==0){
								foreach($aseguradora->paquetes()->where('activo', '=', 1)->orWhere('id_paquete', '=', 10)->orderBy('orden')->get() AS $paquete){
									$html .= '<td class="alignCenter mapfrePaquete" data-hospitales="' . $paquete->paquete_campo . '" width="'.(100/($numeroPaquetes+2)).'%" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.$paquete->paquete.'</strong></td>';
								}
							}
							else{
								foreach($aseguradora->paquetes()->where('activo', '=', 1)->orWhere('id_paquete', '=', 10)->orWhere('id_paquete', '=', 11)->orderBy('orden')->get() AS $paquete){
									$html .= '<td class="alignCenter mapfrePaquete" data-hospitales="' . $paquete->paquete_campo . '" width="'.(100/($numeroPaquetes+2)).'%" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.$paquete->paquete.'</strong></td>';
								}
							}
						}
						else{
							foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
								$html .= '<td class="alignCenter" width="'.(100/($numeroPaquetes+2)).'%" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.$paquete->paquete.'</strong></td>';
							}
						}
					}
				}
			}*/
			foreach($aAseguradoras as $aseguradora){
				foreach($aseguradora['paquetes'] as $paquete){
					$html .= '<td class="alignCenter mapfrePaquete" data-hospitales="' . $paquete['paquete_campo'] . '" width="'.(100/($numeroPaquetes+2)).'%" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.$paquete['paquete'].'</strong></td>';
				}
			}
			
			$html .= '</tr>';
			
			/*if(self::$cotizacionDatos->nivel_amplio==0){
				$paquetes = Paquete::where('id_aseguradora', '=', 2)
						->where(function($query){
							$query->where('activo', '=', 1)
								->orWhere('id_paquete', '=', 10);
						})
						->orderBy('orden')
						->get();
			}
			else{
				$paquetes = Paquete::where('id_aseguradora', '=', 2)
						->where(function($query){
							$query->where('activo', '=', 1)
								->orWhere('id_paquete', '=', 10)
								->orWhere('id_paquete', '=', 11);
						})
						->orderBy('orden')
						->get();
			}*/
			
			foreach($conceptos AS $concepto){
				$htmlTmp = '<tr>
							<td colspan="2" class="alignLeft" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.(($pdf == true)?utf8_decode($concepto->concepto):$concepto->concepto).'</td>';
				//foreach(self::$aseguradoras AS $aseguradora){
				foreach($aAseguradoras as $aseguradora){
					//if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
					//	if(count($aseguradora->paquetes) > 0){
							if($aseguradora['id']==2){
								switch($concepto->id_concepto){
									case 1:
										//foreach($paquetes as $paquete){
										foreach($aseguradora['paquetes'] as $paquete){
											$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
													->where("tipo", "=", "sadb")
													->where("hospitales", "=", $paquete['paquete_campo'])
													->first();
											$htmlTmp .= '<td id="' . $paquete['paquete_campo'] . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.number_format($recotizacion->sa, 0, ".", ",").'</td>';
										}
										break;
									case 2:
										//foreach($paquetes as $paquete){
										foreach($aseguradora['paquetes'] as $paquete){
											$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
													->where("tipo", "=", "sadb")
													->where("hospitales", "=", $paquete['paquete_campo'])
													->first();
											$htmlTmp .= '<td id="' . $paquete['paquete_campo'] . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.number_format($recotizacion->deducible, 0, ".", ",").'</td>';
										}
										break;
									case 3:
										//foreach($paquetes as $paquete){
										foreach($aseguradora['paquetes'] as $paquete){
											$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
													->where("tipo", "=", "sadb")
													->where("hospitales", "=", $paquete['paquete_campo'])
													->first();
											$htmlTmp .= '<td id="' . $paquete['paquete_campo'] . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.$recotizacion->coaseguro . '%</td>';
										}
										break;
									case 4:
										//foreach($paquetes as $paquete){
										foreach($aseguradora['paquetes'] as $paquete){
											$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
													->where("tipo", "=", "sadb")
													->where("hospitales", "=", $paquete['paquete_campo'])
													->first();
											$htmlTmp .= '<td id="' . $paquete['paquete_campo'] . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.number_format($recotizacion->tope_coaseguro, 0, ".", ",").'</td>';
										}
										break;
									case 7:
										//foreach($paquetes as $paquete){
										foreach($aseguradora['paquetes'] as $paquete){
											$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
													->where("tipo", "=", "sadb")
													->where("hospitales", "=", $paquete['paquete_campo'])
													->first();
											if(!is_null($recotizacion->sa_maternidad))
												$htmlTmp .= '<td id="' . $paquete['paquete_campo'] . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.number_format($recotizacion->sa_maternidad, 0, ".", ",").'</td>';
											else
												$htmlTmp .= '<td id="' . $paquete['paquete_campo'] . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>No</td>';
										}
										break;
									case 10:
										//foreach($paquetes as $paquete){
										foreach($aseguradora['paquetes'] as $paquete){
											switch($paquete['paquete_campo']){
												case "esencial":
													$htmlTmp .= '<td id="' . $paquete['paquete_campo'] . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>C</td>';
													break;
												case "optima":
													$htmlTmp .= '<td id="' . $paquete['paquete_campo'] . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>B y C</td>';
													break;
												case "completa":
													$htmlTmp .= '<td id="' . $paquete['paquete_campo'] . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>A, B y C</td>';
													break;
												case "amplia":
													$htmlTmp .= '<td id="' . $paquete['paquete_campo'] . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>AA, A, B y C</td>';
													break;
											}
										}
										break;
									case 16:
										//foreach($paquetes as $paquete){
										foreach($aseguradora['paquetes'] as $paquete){
											$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
													->where("tipo", "=", "sadb")
													->where("hospitales", "=", $paquete['paquete_campo'])
													->first();
											switch($recotizacion->tabulador){
												case "C":
													$htmlTmp .= '<td id="' . $paquete['paquete_campo'] . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>B&aacute;sico</td>';
													break;
												case "D":
													$htmlTmp .= '<td id="' . $paquete['paquete_campo'] . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>Normal</td>';
													break;
												case "E":
													$htmlTmp .= '<td id="' . $paquete['paquete_campo'] . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>Medio</td>';
													break;
												case "F":
													$htmlTmp .= '<td id="' . $paquete['paquete_campo'] . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>Alto</td>';
													break;
											}
										}
										break;
									case 9:
										//foreach($paquetes as $paquete){
										foreach($aseguradora['paquetes'] as $paquete){
											$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
													->where("tipo", "=", "sadb")
													->where("hospitales", "=", $paquete['paquete_campo'])
													->first();
											$htmlTmp .= '<td id="' . $paquete['paquete_campo'] . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.number_format($recotizacion->emergencia_extranjero, 0, ".", ",").'</td>';
										}
										break;
									case 17:
										//foreach($paquetes as $paquete){
										foreach($aseguradora['paquetes'] as $paquete){
											$htmlTmp .= '<td id="' . $paquete['paquete_campo'] . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'. 'S&iacute;' .'</td>';
										}
										break;
									case 18:
										//foreach($paquetes as $paquete){
										foreach($aseguradora['paquetes'] as $paquete){
											$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
													->where("tipo", "=", "sadb")
													->where("hospitales", "=", $paquete['paquete_campo'])
													->first();
											$htmlTmp .= '<td id="' . $paquete['paquete_campo'] . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.(($recotizacion->reduccion_deducible==1) ? 'S&iacute;' : 'No').'</td>';
										}
										break;
									case 19:
										//foreach($paquetes as $paquete){
										foreach($aseguradora['paquetes'] as $paquete){
											$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
													->where("tipo", "=", "sadb")
													->where("hospitales", "=", $paquete['paquete_campo'])
													->first();
											$htmlTmp .= '<td id="' . $paquete['paquete_campo'] . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.((!is_null($recotizacion->dental)) ? (($recotizacion->dental=="plata") ? "Plata" : "Oro") : "No").'</td>';
										}
										break;
									case 20:
										//foreach($paquetes as $paquete){
										foreach($aseguradora['paquetes'] as $paquete){
											$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
													->where("tipo", "=", "sadb")
													->where("hospitales", "=", $paquete['paquete_campo'])
													->first();
											$htmlTmp .= '<td id="' . $paquete['paquete_campo'] . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.(($recotizacion->complicaciones==1) ? 'S&iacute;' : 'No').'</td>';
										}
										break;
									case 21:
										//foreach($paquetes as $paquete){
										foreach($aseguradora['paquetes'] as $paquete){
											$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
													->where("tipo", "=", "sadb")
													->where("hospitales", "=", $paquete['paquete_campo'])
													->first();
											$htmlTmp .= '<td id="' . $paquete['paquete_campo'] . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.(($recotizacion->vanguardia==1) ? 'S&iacute;' : 'No').'</td>';
										}
										break;
									case 22:
										//foreach($paquetes as $paquete){
										foreach($aseguradora['paquetes'] as $paquete){
											$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
													->where("tipo", "=", "sadb")
													->where("hospitales", "=", $paquete['paquete_campo'])
													->first();
											$htmlTmp .= '<td id="' . $paquete['paquete_campo'] . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.(($recotizacion->multiregion==1) ? 'S&iacute;' : 'No').'</td>';
										}
										break;
									case 23:
										//foreach($paquetes as $paquete){
										foreach($aseguradora['paquetes'] as $paquete){
											$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
													->where("tipo", "=", "sadb")
													->where("hospitales", "=", $paquete['paquete_campo'])
													->first();
											$htmlTmp .= '<td id="' . $paquete['paquete_campo'] . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.(($recotizacion->preexistentes==1) ? 'S&iacute;' : 'No').'</td>';
										}
										break;
									case 24:
										//foreach($paquetes as $paquete){
										foreach($aseguradora['paquetes'] as $paquete){
											$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
													->where("tipo", "=", "sadb")
													->where("hospitales", "=", $paquete['paquete_campo'])
													->first();
											$htmlTmp .= '<td id="' . $paquete['paquete_campo'] . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.(($recotizacion->catastroficas==1) ? 'S&iacute;' : 'No').'</td>';
										}
										break;
									case 25:
										//foreach($paquetes as $paquete){
										foreach($aseguradora['paquetes'] as $paquete){
											$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
													->where("tipo", "=", "sadb")
													->where("hospitales", "=", $paquete['paquete_campo'])
													->first();
											$htmlTmp .= '<td id="' . $paquete['paquete_campo'] . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.(($recotizacion->funeraria==1) ? 'S&iacute;' : 'No').'</td>';
										}
										break;
									default:
										/*if(self::$cotizacionDatos->nivel_amplio==0){
											foreach($aseguradora->paquetes()->where('activo', '=', 1)->orWhere('id_paquete', '=', 10)->orderBy('orden')->get() AS $paquete){
												$valorConcepto = $paquete->tarifaValor2023()->where('id_concepto', '=', $concepto->id_concepto)->get();
												if(count($valorConcepto) == 1){
													$valorConcepto = $valorConcepto[0]->{self::$sumaAsegurada.'_'.self::$deducible};
												}
												$htmlTmp .= '<td id="' . $paquete->paquete_campo . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.(($pdf == true)?utf8_decode($valorConcepto):$valorConcepto).'</td>';
											}
										}
										else{
											foreach($aseguradora->paquetes()->where('activo', '=', 1)->orWhere('id_paquete', '=', 10)->orWhere('id_paquete', '=', 11)->orderBy('orden')->get() AS $paquete){
												$valorConcepto = $paquete->tarifaValor2023()->where('id_concepto', '=', $concepto->id_concepto)->get();
												if(count($valorConcepto) == 1){
													$valorConcepto = $valorConcepto[0]->{self::$sumaAsegurada.'_'.self::$deducible};
												}
												$htmlTmp .= '<td id="' . $paquete->paquete_campo . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.(($pdf == true)?utf8_decode($valorConcepto):$valorConcepto).'</td>';
											}
										}*/
										foreach($aseguradora['paquetes'] as $paquete){
											//$valorConcepto = $paquete->tarifaValor2023()->where('id_concepto', '=', $concepto->id_concepto)->get();
											$valorConcepto = Paqueteconceptotarifa2023::where('id_paquete', '=', $paquete['id_paquete'])->where('id_concepto', '=', $concepto->id_concepto)->get();
											if(count($valorConcepto) == 1){
												$valorConcepto = $valorConcepto[0]->{self::$sumaAsegurada.'_'.self::$deducible};
											}
											$htmlTmp .= '<td id="' . $paquete['paquete_campo'] . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.(($pdf == true)?utf8_decode($valorConcepto):$valorConcepto).'</td>';
										}
										break;
								}
							}
							else{
								/*foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
									$valorConcepto = $paquete->tarifaValor2023()->where('id_concepto', '=', $concepto->id_concepto)->get();
									if(count($valorConcepto) == 1){
										$valorConcepto = $valorConcepto[0]->{self::$sumaAsegurada.'_'.self::$deducible};
									}
									$htmlTmp .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.(($pdf == true)?utf8_decode($valorConcepto):$valorConcepto).'</td>';
								}*/
								foreach($aseguradora['paquetes'] as $paquete){
									//$valorConcepto = $paquete->tarifaValor2023()->where('id_concepto', '=', $concepto->id_concepto)->get();
									$valorConcepto = Paqueteconceptotarifa2023::where('id_paquete', '=', $paquete['id_paquete'])->where('id_concepto', '=', $concepto->id_concepto)->get();
									if(count($valorConcepto) == 1){
										$valorConcepto = $valorConcepto[0]->{self::$sumaAsegurada.'_'.self::$deducible};
									}
									$htmlTmp .= '<td id="' . $paquete['paquete_campo'] . '-' . $concepto->id_concepto . '" class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>'.(($pdf == true)?utf8_decode($valorConcepto):$valorConcepto).'</td>';
								}
							}
				//		}
				//	}
				}
				$htmlTmp .= '</tr>';
				switch ($concepto->id_concepto) {
					case 7:
					case 8:
						if(self::$mostrarMaternidad == true){
							$html .= $htmlTmp;
						}
					break;
					default:
						$html .= $htmlTmp;
					break;
				}
			}
			$html .= '<tr>
						<td colspan="2" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>Pago de contado</strong></td>';
			
			/*foreach(self::$aseguradoras AS $aseguradora){
				if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
					if(count($aseguradora->paquetes) > 0){
						if($aseguradora->id_aseguradora==2){
							if(self::$cotizacionDatos->nivel_amplio==0){
								foreach($aseguradora->paquetes()->where('activo', '=', 1)->orWhere('id_paquete', '=', 10)->orderBy('orden')->get() AS $paquete){
									$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
										->where("tipo", "=", self::$sumaAsegurada . self::$deducible)
										->where("hospitales", "=", $paquete->paquete_campo)
										->first();
									if($recotizacion)
										$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="contado-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '"><strong>'.number_format($recotizacion->contado, 2).'</strong></td>';
									else
										$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="contado-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '"><strong>-</strong></td>';
								}
							}
							else{
								foreach($aseguradora->paquetes()->where('activo', '=', 1)->orWhere('id_paquete', '=', 10)->orWhere('id_paquete', '=', 11)->orderBy('orden')->get() AS $paquete){
									$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
										->where("tipo", "=", self::$sumaAsegurada . self::$deducible)
										->where("hospitales", "=", $paquete->paquete_campo)
										->first();
									if($recotizacion)
										$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="contado-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '"><strong>'.number_format($recotizacion->contado, 2).'</strong></td>';
									else
										$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="contado-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '"><strong>-</strong></td>';
								}
							}
						}
						else{
							foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
								$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.SistemaFunciones::formatMoney1((int)self::$calculos[$paquete->id_paquete]['total']).'</strong></td>';
							}
						}
					}
				}
			}*/
			foreach($aAseguradoras as $aseguradora){
				foreach($aseguradora['paquetes'] as $paquete){
					if($aseguradora['id']==2){
						$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
							->where("tipo", "=", "sadb")
							->where("hospitales", "=", $paquete['paquete_campo'])
							->first();
						if($recotizacion)
							$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="contado-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete['paquete_campo'] . '"><strong>'.number_format($recotizacion->contado, 0, ".", ",").'</strong></td>';
						else
							$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="contado-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete['paquete_campo'] . '"><strong>-</strong></td>';
					}
					else{
						$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>'.SistemaFunciones::formatMoney1((int)self::$calculos[$paquete['id_paquete']]['total']).'</strong></td>';
					}
				}
			}
			
			$html .= '</tr>';
			if($mostrarContratar == true){
				$html .= '<tr>
							<td colspan="2">&nbsp;</td>';
				
				/*foreach(self::$aseguradoras AS $aseguradora){
					if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
						if(count($aseguradora->paquetes) > 0){
							if($aseguradora->id_aseguradora==2){
								if(self::$cotizacionDatos->nivel_amplio==0){
									foreach($aseguradora->paquetes()->where('activo', '=', 1)->orWhere('id_paquete', '=', 10)->orderBy('orden')->get() AS $paquete){
										$html .= '<td><a href="'.URL::to('/contratarPaquete/'.self::$cotizacionDatos->id_cotizacion.'/'.self::$cotizacionDatos->secret.'/'.$paquete->paquete_campo.'/'.self::$sumaAsegurada.'/'.self::$deducible).'" class="but_phone" style="cursor: pointer;"><i class="fa fa-thumbs-o-up"></i> Contratar</a> </td>';
									}
								}
								else{
									foreach($aseguradora->paquetes()->where('activo', '=', 1)->orWhere('id_paquete', '=', 10)->orWhere('id_paquete', '=', 11)->orderBy('orden')->get() AS $paquete){
										$html .= '<td><a href="'.URL::to('/contratarPaquete/'.self::$cotizacionDatos->id_cotizacion.'/'.self::$cotizacionDatos->secret.'/'.$paquete->paquete_campo.'/'.self::$sumaAsegurada.'/'.self::$deducible).'" class="but_phone" style="cursor: pointer;"><i class="fa fa-thumbs-o-up"></i> Contratar</a> </td>';
									}
								}
							}
							else{
								foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
									$html .= '<td><a href="'.URL::to('/contratarPaquete/'.self::$cotizacionDatos->id_cotizacion.'/'.self::$cotizacionDatos->secret.'/'.$paquete->paquete_campo.'/'.self::$sumaAsegurada.'/'.self::$deducible).'" class="but_phone" style="cursor: pointer;"><i class="fa fa-thumbs-o-up"></i> Contratar</a> </td>';
								}
							}
						}
					}
				}*/
				foreach($aAseguradoras as $aseguradora){
					foreach($aseguradora['paquetes'] as $paquete){
						$html .= '<td><a href="'.URL::to('/contratarPaquete/'.self::$cotizacionDatos->id_cotizacion.'/'.self::$cotizacionDatos->secret.'/'.$paquete->paquete_campo.'/'.self::$sumaAsegurada.'/'.self::$deducible).'" class="but_phone" style="cursor: pointer;"><i class="fa fa-thumbs-o-up"></i> Contratar</a> </td>';
					}
				}
				
				$html .= '</tr>';
				
			}
			if(!$pdf && $admin == false){
				$html .= '<tr>
						<table width="100%" class="table-list">
							<tr>
								<td colspan="'.($numeroPaquetes+2).'"><a href="#" class="mostrarPreciosMensualidades"><strong>Mostrar pagos en mensualidades</strong></a></td>
							</tr>
						</table>
						<table width="100%" class="table-list tabla-precios hide">';
			}
			foreach(self::$pagosDiferidos AS $meses => $texto){
				$html .= '<tr>
							<td rowspan="2" class="alignVerticalMiddle" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'><strong>Pago '.$texto.'</strong></td>
							<td '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>Primer pago</td>';
				//foreach(self::$aseguradoras AS $aseguradora){
				foreach($aAseguradoras as $aseguradora){
					//if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
					//	if(count($aseguradora->paquetes) > 0){
							if($aseguradora['id']==2){
								/*if(self::$cotizacionDatos->nivel_amplio==0){
									foreach($aseguradora->paquetes()->where('activo', '=', 1)->orWhere('id_paquete', '=', 10)->orderBy('orden')->get() AS $paquete){
										$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
											->where("tipo", "=", self::$sumaAsegurada . self::$deducible)
											->where("hospitales", "=", $paquete->paquete_campo)
											->first();
										if($recotizacion){
											switch($meses){
												case 6:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="semestral-1-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ '.$recotizacion->semestral_primer.'</td>';
													break;
												case 3:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="trimestral-1-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ '.$recotizacion->trimestral_primer.'</td>';
													break;
												case 1:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="mensual-1-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ '.$recotizacion->mensual_primer.'</td>';
													break;
											}
										}
										else{
											switch($meses){
												case 6:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="semestral-1-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ -</td>';
													break;
												case 3:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="trimestral-1-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ -</td>';
													break;
												case 1:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="mensual-1-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ -</td>';
													break;
											}
										}
									}
								}
								else{
									foreach($aseguradora->paquetes()->where('activo', '=', 1)->orWhere('id_paquete', '=', 10)->orWhere('id_paquete', '=', 11)->orderBy('orden')->get() AS $paquete){
										$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
											->where("tipo", "=", self::$sumaAsegurada . self::$deducible)
											->where("hospitales", "=", $paquete->paquete_campo)
											->first();
										if($recotizacion){
											switch($meses){
												case 6:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="semestral-1-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ '.$recotizacion->semestral_primer.'</td>';
													break;
												case 3:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="trimestral-1-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ '.$recotizacion->trimestral_primer.'</td>';
													break;
												case 1:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="mensual-1-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ '.$recotizacion->mensual_primer.'</td>';
													break;
											}
										}
										else{
											switch($meses){
												case 6:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="semestral-1-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ -</td>';
													break;
												case 3:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="trimestral-1-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ -</td>';
													break;
												case 1:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="mensual-1-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ -</td>';
													break;
											}
										}
									}
								}*/
								foreach($aseguradora['paquetes'] as $paquete){
									$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
										->where("tipo", "=", "sadb")
										->where("hospitales", "=", $paquete['paquete_campo'])
										->first();
									if($recotizacion){
										switch($meses){
											case 6:
												$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="semestral-1-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete['paquete_campo'] . '">$ '.number_format($recotizacion->semestral_primer, 0, ".", ",").'</td>';
												break;
											case 3:
												$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="trimestral-1-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete['paquete_campo'] . '">$ '.number_format($recotizacion->trimestral_primer, 0, ".", ",").'</td>';
												break;
											case 1:
												$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="mensual-1-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete['paquete_campo'] . '">$ '.number_format($recotizacion->mensual_primer, 0, ".", ",").'</td>';
												break;
										}
									}
									else{
										switch($meses){
											case 6:
												$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="semestral-1-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete['paquete_campo'] . '">$ -</td>';
												break;
											case 3:
												$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="trimestral-1-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete['paquete_campo'] . '">$ -</td>';
												break;
											case 1:
												$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="mensual-1-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete['paquete_campo'] . '">$ -</td>';
												break;
										}
									}
								}
							}
							else{
								//foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
								foreach($aseguradora['paquetes'] as $paquete){
									$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>$ '.SistemaFunciones::formatMoney1((int)self::$calculos[$paquete['id_paquete']]['diferidos'][$meses][1]).'</td>';
								}
							}
				//		}
				//	}
				}
				$html .= '</tr>
						<tr>
							<td '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>Posteriores</td>';
				//foreach(self::$aseguradoras AS $aseguradora){
				foreach($aAseguradoras as $aseguradora){
					//if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
					//	if(count($aseguradora->paquetes) > 0){
							if($aseguradora['id']==2){
								/*if(self::$cotizacionDatos->nivel_amplio==0){
									foreach($aseguradora->paquetes()->where('activo', '=', 1)->orWhere('id_paquete', '=', 10)->orderBy('orden')->get() AS $paquete){
										$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
											->where("tipo", "=", self::$sumaAsegurada . self::$deducible)
											->where("hospitales", "=", $paquete->paquete_campo)
											->first();
										if($recotizacion){
											switch($meses){
												case 6:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="semestral-2-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ '.$recotizacion->semestral_posterior.'</td>';
													break;
												case 3:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="trimestral-2-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ '.$recotizacion->trimestral_posterior.'</td>';
													break;
												case 1:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="mensual-2-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ '.$recotizacion->mensual_posterior.'</td>';
													break;
											}
										}
										else{
											switch($meses){
												case 6:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="semestral-2-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ -</td>';
													break;
												case 3:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="trimestral-2-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ -</td>';
													break;
												case 1:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="mensual-2-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ -</td>';
													break;
											}
										}
									}
								}
								else{
									foreach($aseguradora->paquetes()->where('activo', '=', 1)->orWhere('id_paquete', '=', 10)->orWhere('id_paquete', '=', 11)->orderBy('orden')->get() AS $paquete){
										$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
											->where("tipo", "=", self::$sumaAsegurada . self::$deducible)
											->where("hospitales", "=", $paquete->paquete_campo)
											->first();
										if($recotizacion){
											switch($meses){
												case 6:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="semestral-2-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ '.$recotizacion->semestral_posterior.'</td>';
													break;
												case 3:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="trimestral-2-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ '.$recotizacion->trimestral_posterior.'</td>';
													break;
												case 1:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="mensual-2-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ '.$recotizacion->mensual_posterior.'</td>';
													break;
											}
										}
										else{
											switch($meses){
												case 6:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="semestral-2-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ -</td>';
													break;
												case 3:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="trimestral-2-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ -</td>';
													break;
												case 1:
													$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="mensual-2-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete->paquete_campo . '">$ -</td>';
													break;
											}
										}
									}
								}*/
								foreach($aseguradora['paquetes'] as $paquete){
									$recotizacion = RecotizacionMapfre::where("id_cotizacion", "=", self::$cotizacionDatos->id_cotizacion)
										->where("tipo", "=", "sadb")
										->where("hospitales", "=", $paquete['paquete_campo'])
										->first();
									if($recotizacion){
										switch($meses){
											case 6:
												$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="semestral-2-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete['paquete_campo'] . '">$ '.number_format($recotizacion->semestral_posterior, 0, ".", ",").'</td>';
												break;
											case 3:
												$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="trimestral-2-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete['paquete_campo'] . '">$ '.number_format($recotizacion->trimestral_posterior, 0, ".", ",").'</td>';
												break;
											case 1:
												$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="mensual-2-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete['paquete_campo'] . '">$ '.number_format($recotizacion->mensual_posterior, 0, ".", ",").'</td>';
												break;
										}
									}
									else{
										switch($meses){
											case 6:
												$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="semestral-2-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete['paquete_campo'] . '">$ -</td>';
												break;
											case 3:
												$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="trimestral-2-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete['paquete_campo'] . '">$ -</td>';
												break;
											case 1:
												$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').' id="mensual-2-' . self::$sumaAsegurada . self::$deducible . '-' . $paquete['paquete_campo'] . '">$ -</td>';
												break;
										}
									}
								}
							}
							else{
								//foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
								foreach($aseguradora['paquetes'] as $paquete){
									$html .= '<td class="alignCenter" '.(($pdf == true)?'style="padding: 5px; border: 1px solid #ccc;"':'').'>$ '.SistemaFunciones::formatMoney1((int)self::$calculos[$paquete['id_paquete']]['diferidos'][$meses][2]).'</td>';
								}
							}
				//		}
				//	}
				}
				$html .= '</tr>';
			}
			if(!$pdf && $admin == false){
				$html .= '</tr>
							</table>';
			}
			$html .= '</table>';
		}
		return $html;
	}
	
	public static function paquetesCotizacionWS2023(){
		$paquetes = array();
		foreach(self::$aseguradoras AS $aseguradora){
			if(in_array($aseguradora->id_aseguradora, self::$mostrarAseguradoras)){
				if(count($aseguradora->paquetes) > 0){
					if($aseguradora->id_aseguradora==2){
						if(self::$cotizacionDatos->nivel_amplio==0){
							foreach($aseguradora->paquetes()->where('activo', '=', 1)->orWhere('id_paquete', '=', 10)->orderBy('orden')->get() AS $paquete){
								$paquetes[] = array(
									"id_aseguradora" => $aseguradora->id_aseguradora,
									"aseguradora" => $aseguradora->nombre,
									"id_paquete" => $paquete->id_paquete,
									"paquete" => $paquete->paquete
								);
							}
						}
						else{
							foreach($aseguradora->paquetes()->where('activo', '=', 1)
								->orWhere('id_paquete', '=', 10)
								->orWhere('id_paquete', '=', 11)
								->orderBy('orden')->get() AS $paquete){
								$paquetes[] = array(
									"id_aseguradora" => $aseguradora->id_aseguradora,
									"aseguradora" => $aseguradora->nombre,
									"id_paquete" => $paquete->id_paquete,
									"paquete" => $paquete->paquete
								);
							}
						}
					}
					else{
						foreach($aseguradora->paquetes()->where('activo', '=', 1)->orderBy('orden')->get() AS $paquete){
							$paquetes[] = array(
								"id_aseguradora" => $aseguradora->id_aseguradora,
								"aseguradora" => $aseguradora->nombre,
								"id_paquete" => $paquete->id_paquete,
								"paquete" => $paquete->paquete
							);
						}
					}
				}
			}
		}
		return $paquetes;
	}

	public static function datosTablaPaquete($idCotizacion, $idAseguradora, $paquete){
		$tabla = array();
		$sa = "";
		$deducible = "";
		$coaseguro = "";
		$tope = "";
		if($idAseguradora==2){
			$recotizacion = RecotizacionMapfre::where('id_cotizacion', '=', $idCotizacion)
								->where('tipo', '=', 'sadb')
								->where('hospitales', '=', $paquete)
								->first();
		}
		
		if(self::$mostrarMaternidad){
			$conceptos = Paqueteconcepto2023::where('id_concepto', '<>', 8)
				->where('id_concepto', '<>', 5)
				->where('id_concepto', '<>', 6)
				->orderBy('orden')->get();
		}else{
			$conceptos = Paqueteconcepto2023::where('id_concepto', '<>', 8)
				->where('id_concepto', '<>', 5)
				->where('id_concepto', '<>', 6)
				->where('id_concepto', '<>', 7)
				->orderBy('orden')->get();
		}	
		foreach($conceptos AS $concepto){
			$valor = '';
			if($idAseguradora==2){
				switch($concepto->id_concepto){
					case 1:
						$valor = number_format($recotizacion->sa, 0, ".", ",");
						$sa = $valor;
						break;
					case 2:
						$valor = number_format($recotizacion->deducible, 0, ".", ",");
						$deducible = $valor;
						break;
					case 3:
						$valor = $recotizacion->coaseguro . "%";
						$coaseguro = $valor;
						break;
					case 4:
						$valor = number_format($recotizacion->tope_coaseguro, 0, ".", ",");
						$tope = $valor;
						break;
					case 10:
						switch($paquete){
							case 'esencial':
								$valor = 'C';
								break;
							case 'optima':
								$valor = 'B y C';
								break;
							case 'completa':
								$valor = 'A, B y C';
								break;
							case 'amplia':
								$valor = 'AA, A, B y C';
								break;
						}
						break;
					case 16:
						switch($recotizacion->tabulador){
							case "C":
								$valor = "Básico";
								break;
							case "D":
								$valor = "Normal";
								break;
							case "E":
								$valor = "Medio";
								break;
							case "F":
								$valor = "Alto";
								break;
						}
						break;
					case 9:
						$valor = number_format($recotizacion->emergencia_extranjero, 0, ".", ",");
						break;
					case 7:
						$valor = number_format($recotizacion->sa_maternidad, 0, ".", ",");
						break;
					case 17:
						$valor = 'Sí';
						break;
					case 18:
						$valor = (($recotizacion->reduccion_deducible==1) ? "Sí" : "No");
						break;
					case 19:
						$valor = ((!is_null($recotizacion->dental)) ? (($recotizacion->dental=="plata") ? "Plata" : "Oro") : "No");
						break;
					case 20:
						$valor = (($recotizacion->complicaciones==1) ? "Sí" : "No");
						break;
					case 21:
						$valor = (($recotizacion->vanguardia==1) ? "Sí" : "No");
						break;
					case 22:
						$valor = (($recotizacion->multiregion==1) ? "Sí" : "No");
						break;
					case 23:
						$valor = (($recotizacion->preexistentes==1) ? "Sí" : "No");
						break;
					case 24:
						$valor = (($recotizacion->catastroficas==1) ? "Sí" : "No");
						break;
					case 25:
						$valor = (($recotizacion->funeraria==1) ? "Sí" : "No");
						break;
				}
			}
			else{
				$valorConcepto = $paquete->tarifaValor2023()->where('id_concepto', '=', $concepto->id_concepto)->get();
				if(count($valorConcepto) == 1){
					$valorConcepto = $valorConcepto[0]->{self::$sumaAsegurada.'_'.self::$deducible};
				}
				$valor = $valorConcepto;
			}
			$tabla['coberturas'][] = array(
				'concepto' => $concepto->concepto,
				'valor' => $valor
			);
		}
		if($idAseguradora==2){
			$tabla['costos']['sa'] = '$' . $sa;
			$tabla['costos']['deducible'] = '$' . $deducible;
			$tabla['costos']['coaseguro'] = $coaseguro;
			$tabla['costos']['tope'] = '$' . $tope;
			$tabla['costos']['contado'] = '$' . number_format($recotizacion->contado, 0, '.', ',');
			$tabla['costos']['semestral'] = '$' . number_format($recotizacion->semestral_primer + $recotizacion->semestral_posterior, 0, '.', ',');
			$tabla['costos']['semestral-1'] = '$' . number_format($recotizacion->semestral_primer, 0, '.', ',');
			$tabla['costos']['semestral-2'] = '$' . number_format($recotizacion->semestral_posterior, 0, '.', ',');
			$tabla['costos']['trimestral'] = '$' . number_format($recotizacion->trimestral_primer + ($recotizacion->trimestral_posterior * 3), 0, '.', ',');;
			$tabla['costos']['trimestral-1'] = '$' . number_format($recotizacion->trimestral_primer, 0, '.', ',');
			$tabla['costos']['trimestral-2'] = '$' . number_format($recotizacion->trimestral_posterior, 0, '.', ',');
			$tabla['costos']['mensual'] = '$' . number_format($recotizacion-> mensual_primer + ($recotizacion->mensual_posterior * 11), 0, '.', ',');;;
			$tabla['costos']['mensual-1'] = '$' . number_format($recotizacion->mensual_primer, 0, '.', ',');
			$tabla['costos']['mensual-2'] = '$' . number_format($recotizacion->mensual_posterior, 0, '.', ',');
		}
		else{
			
		}
		
		return $tabla;
	}

	public static function tablaIntegrantesPaquete(){
		$tr = '';
		$integrantesOrden = array();
		$n = 2;
		foreach(self::$integrantes AS $integrante){
			switch($integrante->titulo){
				case 'titular':
					$integrantesOrden[0] = $integrante;
				break;
				case 'conyugue':
					$integrantesOrden[1] = $integrante;
				break;
				default:
					$integrantesOrden[$n] = $integrante;
					$n++;
				break;
			}
		}
		self::$integrantes = $integrantesOrden;
		ksort(self::$integrantes);
		foreach(self::$integrantes AS $integrante){
			$tr .= '<tr>
						<td class="alignLeft">'.htmlspecialchars_decode(ucwords(strtolower($integrante->nombre))).' </td>
						<td class="alignCenter">'.(($integrante->sexo == 'm') ? 'Hombre' : 'Mujer').'</td>
						<td class="alignCenter">'.$integrante->edad.'</td>
						<td class="alignCenter">'.ucfirst($integrante->titulo).'</td>
					</tr>';
		}
		$html = '<div class="one_half last">
					<div class="address-info">
						<h4 class="text-azul mt-0 mb-0">Integrantes</h4>
						<table width="100%" class="table table-condensed">
							<thead>
								<tr>
									<th class="alignCenter">Nombre</th>
									<th class="alignCenter">Sexo</th>
									<th class="alignCenter">Edad</th>
									<th class="alignCenter">Titulo</th>
								</tr>
							</thead>
							<tbody>
								'.$tr.'
							</tbody>
						</table>
					</div>
				</div>';
		return $html;
	}
}
