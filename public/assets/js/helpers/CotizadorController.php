<?php

use Knp\Snappy\Pdf;

class CotizadorController extends BaseController {
	protected $layout = 'layout.master';
	// Pruebas: 
	//protected $wsUrl = 'https://negociosuat.mapfre.com.mx/';
	// Produccion:
	protected $wsUrl = 'https://zonaliados.mapfre.com.mx/';
	
	public function gracias(){
		echo "gracias";
	}

	public function cotizacionContratar(){
		$respuesta = array(
                        "status" => "invalid",
                        "titulo" => "Contratar",
                        "mensaje" => "Ocurrio un error al tratar de actualizar su cotización",
                    );
		$datos = Input::all();
		$cotizacionDatos = Cotizacion::where('id_cotizacion', '=', $datos['idCotizacion'])->where('secret', '=', $datos['secret'])->first();
		if($cotizacionDatos){
			$cotizacionDatos->nombre = $datos['nombre'];
			$cotizacionDatos->e_mail = $datos['e_mail'];
		    $cotizacionDatos->telefono = $datos['telefono'];
		    $cotizacionDatos->estado = $datos['estado'];
		    $cotizacionDatos->ciudad = $datos['ciudad'];
		    $cotizacionDatos->contacto = (($datos['contacto'] == 1) ? 'Lo más pronto posible' : 'Cuando venza mi póliza');
		    $cotizacionDatos->fecha_vencimiento_poliza = $datos['fechaPoliza'];
		    $cotizacionDatos->comentario = $datos['comentarios'];
		    $cotizacionDatos->paquete = $datos['paquete'];
		    $cotizacionDatos->sa = $datos['sa'];
		    $cotizacionDatos->ded = $datos['ded'];
		    $cotizacionDatos->forma_ingreso = 2;
		    $cotizacionDatos->poliza_actual = $datos['poliza_actual'];
		    $cotizacionDatos->comentarios = $datos['comentarios'];
		    $cotizacionDatos->estatus = 1;
		    if($cotizacionDatos->save()){
		    	$respuesta['status'] = 'success';
		    }
		}
		return json_encode($respuesta);
	}
	
	public function contratarPaquete($idCotizacion, $secret, $paquete, $sa, $db) {
		$cotizacionDatos = Cotizacion::where('id_cotizacion', '=', $idCotizacion)->where('secret', '=', $secret)->first();
		if($cotizacionDatos){
			$paqueteDatos = Paquete::where('paquete_campo', '=', $paquete)->first();
			if($paqueteDatos){
				$cotizacionDatos->integrantes = json_decode($cotizacionDatos->integrantes);
				View::share('cotizacionDatos', $cotizacionDatos);
				View::share('paqueteDatos', $paqueteDatos);
				$cotizacion = new Cotizador($cotizacionDatos, $sa, $db);
				View::share('tablaIntegrantes', $cotizacion::tablaIntegrantes());
				View::share('paquete', $paquete);
				View::share('sa', $sa);
				View::share('ded', $db);
				$this->layout->content = View::make('cotizacion.contratar');
			}
		}else{
			return Redirect::to('/');
		}
	}

	public function enviarCotizacionEmail(){
		$idCotizacion = Input::get('idCotizacion');
        $secret = Input::get('secret');
        $sa = Input::get('sa');
        $ded = Input::get('ded');
        $cotizacionDatos = Cotizacion::where('id_cotizacion', '=', $idCotizacion)->where('secret', '=', $secret)->first();
        
        $respuesta = array(
                        "status" => "invalid",
                        "titulo" => "Enviar cotiazión por e-mail",
                        "mensaje" => "Ocurrio un error al tratar de enviar la cotiazión por Correo electrónico 1",
                        "posicion" => "stack_bar_bottom",
                        "tipo" => "error",
                    );
        if($cotizacionDatos){
            $cotizacionDatos->integrantes = json_decode($cotizacionDatos->integrantes);
            foreach($cotizacionDatos->integrantes AS $integrante){
                if($integrante->edad >= 50){
                    $sumaAsegurada = 'sa';
                    $dedubicle = 'da';
                }
            }
            $pdf = self::generarCotizacionPdf($cotizacionDatos, false, $sa, $ded);
            
            if(file_exists($pdf)){
                $encabezado = Cotizaciontexto::where('texto_seccion', '=', 'texto_correo_encabezado')->where('id_dominio', '=', 1)->first();
				$cuerpo = Cotizaciontexto::where('texto_seccion', '=', 'texto_correo_cuerpo')->where('id_dominio', '=', 1)->first();
				$pie = Cotizaciontexto::where('texto_seccion', '=', 'texto_correo_pie')->where('id_dominio', '=', 1)->first();
                $cotizacionDatos->pdf = $pdf;
                $datosPlantilla = array(
                                    'nombre' => $cotizacionDatos->nombre,
                                    'e_mail' => $cotizacionDatos->e_mail,
                                    'id_cotizacion' => $cotizacionDatos->id_cotizacion,
                                    'secret' => $cotizacionDatos->secret,
                                    'encabezado' => $encabezado->texto_pdf,
                                    'cuerpo' => $cuerpo->texto_pdf,
                                    'pie' =>  $pie->texto_pdf
                                );
                try{
                    Mail::send('plantillas.correo.enviarCotizacionCorreo', $datosPlantilla, function($message) use ($cotizacionDatos){
                        $message->from($cotizacionDatos->dominio()->first()->email, $cotizacionDatos->dominio()->first()->sender);
                        $message->to($cotizacionDatos->e_mail, $cotizacionDatos->nombre);
                        $message->cc('info@segurodegastosmedicosmayores.mx', 'Cotizaciones');
                        $message->subject('Cotización de Gastos Médicos Mayores - '.$cotizacionDatos->id_cotizacion);
                        if(file_exists($cotizacionDatos->pdf)){
                            $message->attach($cotizacionDatos->pdf, ['as' => 'cotizacion-'.$cotizacionDatos->id_cotizacion.'.pdf']);
                        }
                    });
                }catch(Exception $e){
                    $respuesta["mensaje"] = $e->getMessage();
                    return json_encode($respuesta);
                }
                $respuesta["status"] = "success";
                $respuesta["tipo"] = "success";
                $respuesta["mensaje"] = 'Cotización enviada correctamente por Correo electrónico.';
                @unlink($pdf);
            }
        }
        return json_encode($respuesta);
	}

	private static function generarCotizacionPdf($cotizacionDatos = array(), $mostrar = true, $sa = 'sa', $ded = 'db'){
		$cotizacion = new Cotizador($cotizacionDatos, $sa, $ded);
        $cotizacion::cotizar();
        
        $mpdf = new mPDF('', 'Letter', '', '', 20,20,20,20,5,5);
        $bienvenida = Cotizaciontexto::where('texto_seccion', '=', 'saludo_bienvenida')->where('id_dominio', '=', 1)->first()->texto_pdf;
        View::share('bienvenida', $bienvenida);
        $beneficios = Cotizaciontexto::where('texto_seccion', '=', 'beneficios_protecto')->where('id_dominio', '=', 1)->first()->texto_pdf;
        View::share('beneficios', $beneficios);
        View::share('cotizacionDatos', $cotizacionDatos);
        View::share('cotizacion', $cotizacion);
        $aseguradoras = Aseguradora::where('activa', '=', 1)->orderBy('orden')->get();
        View::share('aseguradoras', $aseguradoras);
        $html = View::make('plantillas.correo.cotizacionPdf');
        $mpdf->WriteHTML($html);
        if($mostrar == true){
            $mpdf->Output($file_name, 'I');
        }else{
            if(!is_dir('tmp')){
                mkdir('tmp');
            }
            $ruta = 'tmp/cotizacion_'.$cotizacionDatos->id_cotizacion.'_'.$sa.'_'.$ded.'.pdf';
            $mpdf->Output($ruta,'F');
            return $ruta;
        }
	}

	public function verCotizacionPdf($idCotizacion = -1, $secret = '', $sa = 'sb', $ded = 'db'){
		$cotizacionDatos = Cotizacion::find($idCotizacion);
		if($cotizacionDatos){
			$cotizacionDatos->integrantes = json_decode($cotizacionDatos->integrantes);
			if(is_null($cotizacionDatos->mapfre_numero))
				self::generarCotizacionPdf($cotizacionDatos, true, $sa, $ded);
			else
				self::generarCotizacionPdfWS($cotizacionDatos, true, $sa, $ded);
		}else{
			return Redirect::to('/cotizador');
		}
	}

	public function verCotizacion($idCotizacion = -1, $secret = ''){
		$cotizacionDatos = Cotizacion::find($idCotizacion);
		if($cotizacionDatos){
			if($cotizacionDatos->visto == -1){
				$cotizacionDatos->visto = 1;
				$cotizacionDatos->save();
			}
			/*
			$conversionAdwords = '';
			if($cotizacionDatos->conversionAdwords == -1){
				$conversionAdwords= '<!-- Google Code for Gastos Medicos 2017 Conversion Page -->
										<script type="text/javascript">
											/* <![CDATA[ *
											var google_conversion_id = 1025142637;
											var google_conversion_language = "en";
											var google_conversion_format = "3";
											var google_conversion_color = "ffffff";
											var google_conversion_label = "Um2KCOLz1G4Q7d7p6AM";
											var google_remarketing_only = false;
											/* ]]> *
										</script>
										<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js"></script>
										<noscript>
											<div style="display:inline;">
												<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/1025142637/?label=Um2KCOLz1G4Q7d7p6AM&amp;guid=ON&amp;script=0"/>
											</div>
										</noscript>';
				$cotizacionDatos->conversionAdwords = 1;
				$cotizacionDatos->save();
			}
			View::share('conversionAdwords', $conversionAdwords);
			*/
			$cotizacionDatos->integrantes = json_decode($cotizacionDatos->integrantes);
			$sumaAsegurada = 'sb';
			$dedubicle = 'db';
			$tabDefault = 'sa_db';
			foreach($cotizacionDatos->integrantes AS $integrante){
				if($integrante->edad >= 50){
					$sumaAsegurada = 'sa';
					$dedubicle = 'da';
					$tabDefault = 'sa_da';
				}
			}
			View::share('tabDefault', $tabDefault);
			View::share('cotizacionDatos', $cotizacionDatos);
			//$cotizacion = new Cotizador($cotizacionDatos, $sumaAsegurada, $dedubicle);
			$cotizacion = new Cotizador($cotizacionDatos, 'sa', 'db');
			$cotizacion::cotizar();
			$tablaDatos['sa_db'] = array('titulo' => 'Plan 1', 'nombre' => 'Suma Asegurada Alta - Deducible Bajo', 'tabla' => $cotizacion::tablaDatos(), 's' => 'sa', 'd' => 'db');
			
			$cotizacion = new Cotizador($cotizacionDatos, 'sa', 'da');
			$cotizacion::cotizar();
			$tablaDatos['sa_da'] = array('titulo' => 'Plan 2', 'nombre' => 'Suma Asegurada Alta - Deducible Alto', 'tabla' => $cotizacion::tablaDatos(), 's' => 'sa', 'd' => 'da');
			
			$cotizacion = new Cotizador($cotizacionDatos, 'sb', 'db');
			$cotizacion::cotizar();
			$tablaDatos['sb_db'] = array('titulo' => 'Plan 3', 'nombre' => 'Suma Asegurada Baja - Deducible Bajo', 'tabla' => $cotizacion::tablaDatos(), 's' => 'sb', 'd' => 'db');
			
			$cotizacion = new Cotizador($cotizacionDatos, 'sb', 'da');
			$cotizacion::cotizar();
			$tablaDatos['sb_da'] = array('titulo' => 'Plan 4', 'nombre' => 'Suma Asegurada Baja - Deducible Alto', 'tabla' => $cotizacion::tablaDatos(), 's' => 'sb', 'd' => 'da');

			View::share('tablaDatos', $tablaDatos);
			$tablaClienteDatos = $cotizacion::tablaClienteDatos();
			View::share('tablaClienteDatos', $tablaClienteDatos);
			$tablaIntegrantes = $cotizacion::tablaIntegrantes();
			View::share('tablaIntegrantes', $tablaIntegrantes);

			$this->layout->content = View::make('cotizacion.verCotizacion');
		}else{
			return Redirect::to('/cotizador');
		}
	}
	//Nuevo pdf 
	public function cotizacion2016($idCotizacion = -1, $secret = ''){
		$cotizacionDatos = Cotizacion::find($idCotizacion);
		if($cotizacionDatos){
			
			if($cotizacionDatos->visto == -1){
				$cotizacionDatos->visto = 1;
				$cotizacionDatos->save();
			}
			$cotizacionDatos->integrantes = json_decode($cotizacionDatos->integrantes);
			$sumaAsegurada = 'sb';
			$dedubicle = 'db';
			foreach($cotizacionDatos->integrantes AS $integrante){
				if($integrante->edad > 54){
					$sumaAsegurada = 'sa';
					$dedubicle = 'da';
				}
			}
			View::share('cotizacionDatos', $cotizacionDatos);
			$cotizacion = new Cotizador($cotizacionDatos, $sumaAsegurada, $dedubicle);
			$cotizacion::cotizar();
			$tablaDatos = $cotizacion::tablaDatos2016();
			View::share('tablaDatos', $tablaDatos);
			$tablaClienteDatos = $cotizacion::tablaClienteDatos2016();
			View::share('tablaClienteDatos', $tablaClienteDatos);
			$tablaIntegrantes = $cotizacion::tablaIntegrantes2016();
			View::share('tablaIntegrantes', $tablaIntegrantes);
			$textoProtecto = \DB::table('texto_pdf')->where('id_texto_pdf', 1)->get()[0];
			View::share('textoProtecto', $textoProtecto->texto_pdf);
			//dd($cotizacionDatos->fecha_registro);
			View::share('fechaRegistro', $cotizacionDatos->fecha_registro);
			//Traemos los textos descriptivos de cada plan para agregarlos al pdf.
			//Se seleciconan sólo los de las aseguradoras activas y plan activo
			$textos_aseguradora_activa = \DB::table('aseguradoras')
					->join('paquetes', 'aseguradoras.id_aseguradora', '=','paquetes.id_aseguradora')
					->where('aseguradoras.activa', 1) //1 = activo
					->where('paquetes.activo', 1)
					->select('paquetes.descripcion_backend', 'paquetes.paquete', 'aseguradoras.nombre')
					->orderBy('paquetes.id_aseguradora', 'DESC')
					->get();
					dd($textos_aseguradora_activa);
			View::share('textos_plan_activo', $textos_aseguradora_activa);

			View::share('PDF', 1);
			$this->layout->content = View::make('cotizacion.cotizacionPDF');
		}else{
			return Redirect::to('/cotizador');
		}
	}
	
	public function nuevaCotizacion()
	{
		$datos = Input::except('nombres', 'integrantes', 'sexos', 'edades');
		$integrantes = Input::get('integrantes');
		$integrantes[] = 1;
		
		$respuesta = array(
                        "status" => "invalid",
                        "mensaje" => "Ocurrio un error al tratar de guardar el formulario",
                        "idCotizacion" => -1,
                        "secret" => ""
                    );
		if(count($integrantes) > 0){
			$nombre = Input::get('nombres');
			$sexo = Input::get('sexos');
			$edad = Input::get('edades');
			$sa = 'sa';
			$ded = 'db';
			$integrantesLista = array();
			foreach($integrantes AS $integrante){
				$integrantesLista[] = array(
									'titular' => (($integrante == 1) ? 1 : 0),
									'titulo' => (($integrante == 1) ? 'titular' : (($integrante == 2) ? 'conyugue' : 'hijo') ),
									'nombre' => $nombre[$integrante - 1],
									'sexo' => $sexo[$integrante - 1],
									'edad' => $edad[$integrante - 1],
								);
				if($edad[$integrante - 1] >= 50){
					$sa = 'sa';
                    $ded = 'da';
				}
			}
			if(count($integrantesLista) > 0){
				
				if(Emailblack::where('e_mail', '=', $datos['e_mail'])->get()->count() == 0){
					$fechaUltima = date('Y-m-d', strtotime('-6month', strtotime(date('Y-m-d'))));
	                $cotizar = false;
	                $totalCotizaciones = Cotizacion::where('e_mail', '=', $datos['e_mail'])->where('fecha_registro', '>=', $fechaUltima)->get()->count();
	                
	                $Emailwhite = Emailwhite::where('e_mail', '=', $datos['e_mail'])->first();
	                if($totalCotizaciones < 6){
	                    $cotizar = true;
	                }
	                if($Emailwhite){
	                    if($totalCotizaciones < $Emailwhite->cotizacionesTotales){
	                        $cotizar = true;
	                    }
	                }
	                if($cotizar == true){
						
						$cotizacionDatos = new Cotizacion();
						$cotizacionDatos->id_dominio = 1;
						foreach($datos AS $key => $value){
							$cotizacionDatos->$key = $value;
						}
						$cotizacionDatos->integrantes = json_encode($integrantesLista);
						$cotizacionDatos->secret = str_random(15);
						$cotizacionDatos->forma_ingreso = 2;
						$referer = parse_url($_SERVER["HTTP_REFERER"]);
						if(isset($referer["path"]))
							$cotizacionDatos->ruta = $referer["path"];
						if($cotizacionDatos->save()){
							$respuesta["status"] = "success";
							$respuesta["idCotizacion"] = $cotizacionDatos->id_cotizacion;
							$respuesta["secret"] = $cotizacionDatos->secret;
							$cotizacionDatos->integrantes = json_decode($cotizacionDatos->integrantes);
							$pdf = self::generarCotizacionPdf($cotizacionDatos, false, $sa, $ded);
							
							//if(file_exists($pdf)){
								$encabezado = Cotizaciontexto::where('texto_seccion', '=', 'texto_correo_encabezado')->where('id_dominio', '=', 1)->first();
								$cuerpo = Cotizaciontexto::where('texto_seccion', '=', 'texto_correo_cuerpo')->where('id_dominio', '=', 1)->first();
								$pie = Cotizaciontexto::where('texto_seccion', '=', 'texto_correo_pie')->where('id_usuario', '=', '-1')->where('id_dominio', '=', 1)->first();
								$cotizacionDatos->pdf = $pdf;
								$datosPlantilla = array(
										'nombre' => $cotizacionDatos->nombre,
										'e_mail' => $cotizacionDatos->e_mail,
										'id_cotizacion' => $cotizacionDatos->id_cotizacion,
										'secret' => $cotizacionDatos->secret,
										'encabezado' => $encabezado->texto_pdf,
										'cuerpo' => $cuerpo->texto_pdf,
										'pie' => $pie->texto_pdf
									);
								try{
					                Mail::send('plantillas.correo.enviarCotizacionCorreo', $datosPlantilla, function($message) use ($cotizacionDatos){
					                	$message->from($cotizacionDatos->dominio()->first()->email, $cotizacionDatos->dominio()->first()->sender);
					                    $message->to($cotizacionDatos->e_mail, $cotizacionDatos->nombre);
					                    $message->cc('info@segurodegastosmedicosmayores.mx', 'Cotizaciones');
					                    $message->subject('Cotización de Gastos Médicos Mayores - '.$cotizacionDatos->id_cotizacion);
					                    if(file_exists($cotizacionDatos->pdf)){
					                        $message->attach($cotizacionDatos->pdf, ['as' => 'cotizacion-'.$cotizacionDatos->id_cotizacion.'.pdf']);
					                    }
					                });
					                $log = new Logsistema;
					                $log->cotizacion_id = $cotizacionDatos->id_cotizacion;
					                $log->error = 'Cotizacion enviada: '.$cotizacionDatos->id_cotizacion;
					                $log->tipo = 'correo_enviado';
					                $log->controlador = 'CotizadorController';
					                $log->metodo = 'nuevaCotizacion';
					                $log->save();
					            }catch(Exception $e){
					            	$respuesta["status"] = "warning";
									//dd($e->getMessage());
					            	$respuesta["mensaje"] .= 'Ocurrio un error al tratar de enviar la cotización a su Correo';
					            	$log = new Logsistema;
					            	$log->cotizacion_id = $cotizacionDatos->id_cotizacion;
					            	$log->error = $e->getMessage();
					            	$log->tipo = 'enviar_correo';
					            	$log->controlador = 'CotizadorController';
					            	$log->metodo = 'nuevaCotizacion';
					            	$log->save();
					            }
					            $respuesta["mensaje"] .= 'Cotización enviada correctamente a su Correo.';
								@unlink($pdf);
							//}
						}
						
					}
					else{
						$respuesta['mensaje'] = 'Lo sentimos, su cuenta de correo electrónico ha superado el número de cotizaciones al semestre. Si necesita una cotización en este momento comuníquese con nosotros a los teléfonos en Guadalajara: (33) 200-201-70 y con gusto le atenderemos. Si eres empresa comunícate con nosotros para darte un precio especial.';
					}
				}
				else{
					$respuesta['mensaje'] = 'Lo sentimos, su cuenta de correo electrónico se encuentra bloqueado. En caso de no serlo, por favor comuníquese con nosotros a los teléfonos: (33) 200-201-70 para realizar la cotización.';
				}
				
			}
		}
		return json_encode($respuesta);
	}

	public function cotizador()
	{
		// Para plantilla original
		//$cotizadorPagina = Blog::where('alias', '=', 'cotizador')->where('tipo', '=', 2)->where('estatus', '=', 1)->first();
		$cotizadorPagina = Blog::where('alias', '=', 'cotizador')->where('tipo', '=', 1)->where('estatus', '=', 1)->first();
		if($cotizadorPagina){
			$cotizadorPagina->html = json_decode($cotizadorPagina->html);
			View::share('cotizadorPagina', $cotizadorPagina->html);
			View::share('metaTitulo', $cotizadorPagina->titulo);
			View::share('metaDescripcion', $cotizadorPagina->metadesc);
		}
		$this->layout->content = View::make('cotizacion.cotizador');
		//Paginavisita::hit();
	}
	
	public function miniNuevaCotizacion(){
		$datos = Input::except('nombres', 'integrantes', 'sexos', 'edades');
		$integrantes = Input::get('integrantes');
		$integrantes[] = 1;
		
		$respuesta = array(
                        "status" => "invalid",
                        "mensaje" => "Ocurrio un error al tratar de guardar el formulario",
                        "idCotizacion" => -1,
                        "secret" => "",
                        "tabDefault" => "",
                        "cotizacionDatos" => "",
                        "tablaDatos" => "",
                        "tablaClienteDatos" => "",
                        "tablaIntegrantes" => ""
                    );
                    
		if(count($integrantes) > 0){
			$nombre = Input::get('nombres');
			$sexo = Input::get('sexos');
			$edad = Input::get('edades');
			$sa = 'sa';
			$ded = 'db';
			$integrantesLista = array();
			foreach($integrantes AS $integrante){
				$integrantesLista[] = array(
									'titular' => (($integrante == 1) ? 1 : 0),
									'titulo' => (($integrante == 1) ? 'titular' : (($integrante == 2) ? 'conyugue' : 'hijo') ),
									'nombre' => $nombre[$integrante - 1],
									'sexo' => $sexo[$integrante - 1],
									'edad' => $edad[$integrante - 1],
								);
				if($edad[$integrante - 1] >= 50){
					$sa = 'sa';
                    $ded = 'da';
				}
			}
			if(count($integrantesLista) > 0){
				
				if(Emailblack::where('e_mail', '=', $datos['e_mail'])->get()->count() == 0){
					$fechaUltima = date('Y-m-d', strtotime('-6month', strtotime(date('Y-m-d'))));
	                $cotizar = false;
	                $totalCotizaciones = Cotizacion::where('e_mail', '=', $datos['e_mail'])->where('fecha_registro', '>=', $fechaUltima)->get()->count();
	                
	                $Emailwhite = Emailwhite::where('e_mail', '=', $datos['e_mail'])->first();
	                if($totalCotizaciones < 6){
	                    $cotizar = true;
	                }
	                if($Emailwhite){
	                    if($totalCotizaciones < $Emailwhite->cotizacionesTotales){
	                        $cotizar = true;
	                    }
	                }
	                if($cotizar == true){
						
						$cotizacionDatos = new Cotizacion();
						$cotizacionDatos->id_dominio = 1;
						foreach($datos AS $key => $value){
							$cotizacionDatos->$key = $value;
						}
						$cotizacionDatos->integrantes = json_encode($integrantesLista);
						$cotizacionDatos->secret = str_random(15);
						$cotizacionDatos->forma_ingreso = 2;
						$referer = parse_url($_SERVER["HTTP_REFERER"]);
						if(isset($referer["path"]))
							$cotizacionDatos->ruta = $referer["path"];
						if($cotizacionDatos->save()){
							//$respuesta["status"] = "success";
							$respuesta["idCotizacion"] = $cotizacionDatos->id_cotizacion;
							$respuesta["secret"] = $cotizacionDatos->secret;
							$cotizacionDatos->integrantes = json_decode($cotizacionDatos->integrantes);
							$pdf = self::generarCotizacionPdf($cotizacionDatos, false, $sa, $ded);
							
							//if(file_exists($pdf)){
								$encabezado = Cotizaciontexto::where('texto_seccion', '=', 'texto_correo_encabezado')->first();
								$cuerpo = Cotizaciontexto::where('texto_seccion', '=', 'texto_correo_cuerpo')->first();
								$pie = Cotizaciontexto::where('texto_seccion', '=', 'texto_correo_pie')->where('id_usuario', '=', '-1')->first();
								$cotizacionDatos->pdf = $pdf;
								$datosPlantilla = array(
										'nombre' => $cotizacionDatos->nombre,
										'e_mail' => $cotizacionDatos->e_mail,
										'id_cotizacion' => $cotizacionDatos->id_cotizacion,
										'secret' => $cotizacionDatos->secret,
										'encabezado' => $encabezado->texto_pdf,
										'cuerpo' => $cuerpo->texto_pdf,
										'pie' => $pie->texto_pdf
									);
								try{
					                Mail::send('plantillas.correo.enviarCotizacionCorreo', $datosPlantilla, function($message) use ($cotizacionDatos){
					                	//$message->from('info@segurodegastosmedicosmayores.mx', 'Seguro de Gastos Médicos Mayores');
					                	$message->from($cotizacionDatos->dominio()->first()->email, $cotizacionDatos->dominio()->first()->sender);
					                    $message->to($cotizacionDatos->e_mail, $cotizacionDatos->nombre);
					                    $message->cc('info@segurodegastosmedicosmayores.mx', 'Cotizaciones');
					                    $message->subject('Cotización de Gastos Médicos Mayores - '.$cotizacionDatos->id_cotizacion);
					                    if(file_exists($cotizacionDatos->pdf)){
					                        $message->attach($cotizacionDatos->pdf, ['as' => 'Cotizacion_PDF:'.$cotizacionDatos->id_cotizacion.'.pdf', 'mime' => 'pdf']);
					                    }
					                });
					                $log = new Logsistema;
					                $log->cotizacion_id = $cotizacionDatos->id_cotizacion;
					                $log->error = 'Cotizacion enviada: '.$cotizacionDatos->id_cotizacion;
					                $log->tipo = 'correo_enviado';
					                $log->controlador = 'CotizadorController';
					                $log->metodo = 'nuevaCotizacion';
					                $log->save();
					            }catch(Exception $e){
					            	$respuesta["status"] = "waring";
									//dd($e->getMessage());
					            	$respuesta["mensaje"] .= 'Ocurrio un error al tratar de enviar la cotización a su Correo';
					            	$log = new Logsistema;
					            	$log->cotizacion_id = $cotizacionDatos->id_cotizacion;
					            	$log->error = $e->getMessage();
					            	$log->tipo = 'enviar_correo';
					            	$log->controlador = 'CotizadorController';
					            	$log->metodo = 'nuevaCotizacion';
					            	$log->save();
					            }
					            $respuesta["mensaje"] .= 'Cotización enviada correctamente a su Correo.';
								@unlink($pdf);
							//}
							
							// Inicia codigo de verCotizacion
							$cotizacionDatos = Cotizacion::find($cotizacionDatos->id_cotizacion);
							if($cotizacionDatos){
								if($cotizacionDatos->visto == -1){
									$cotizacionDatos->visto = 1;
									$cotizacionDatos->save();
								}
								/*
								$conversionAdwords = '';
								if($cotizacionDatos->conversionAdwords == -1){
									$conversionAdwords= '<!-- Google Code for Gastos Medicos 2017 Conversion Page -->
															<script type="text/javascript">
																/* <![CDATA[ *
																var google_conversion_id = 1025142637;
																var google_conversion_language = "en";
																var google_conversion_format = "3";
																var google_conversion_color = "ffffff";
																var google_conversion_label = "Um2KCOLz1G4Q7d7p6AM";
																var google_remarketing_only = false;
																/* ]]> *
															</script>
															<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js"></script>
															<noscript>
																<div style="display:inline;">
																	<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/1025142637/?label=Um2KCOLz1G4Q7d7p6AM&amp;guid=ON&amp;script=0"/>
																</div>
															</noscript>';
									$cotizacionDatos->conversionAdwords = 1;
									$cotizacionDatos->save();
								}
								View::share('conversionAdwords', $conversionAdwords);
								*/
								$cotizacionDatos->integrantes = json_decode($cotizacionDatos->integrantes);
								$sumaAsegurada = 'sb';
								$dedubicle = 'db';
								$tabDefault = 'sa_db';
								foreach($cotizacionDatos->integrantes AS $integrante){
									if($integrante->edad >= 50){
										$sumaAsegurada = 'sa';
										$dedubicle = 'da';
										$tabDefault = 'sa_da';
									}
								}
								//View::share('tabDefault', $tabDefault);
								$respuesta["tabDefault"] = $tabDefault;
								//View::share('cotizacionDatos', $cotizacionDatos);
								$respuesta["cotizacionDatos"] = $cotizacionDatos;
								//$cotizacion = new Cotizador($cotizacionDatos, $sumaAsegurada, $dedubicle);
								$cotizacion = new Cotizador($cotizacionDatos, 'sa', 'db');
								$cotizacion::cotizar();
								$tablaDatos['sa_db'] = array('titulo' => 'Plan 1', 'nombre' => 'Suma Asegurada Alta - Deducible Bajo', 'tabla' => $cotizacion::tablaDatos(), 's' => 'sa', 'd' => 'db');
								
								$cotizacion = new Cotizador($cotizacionDatos, 'sa', 'da');
								$cotizacion::cotizar();
								$tablaDatos['sa_da'] = array('titulo' => 'Plan 2', 'nombre' => 'Suma Asegurada Alta - Deducible Alto', 'tabla' => $cotizacion::tablaDatos(), 's' => 'sa', 'd' => 'da');
								
								$cotizacion = new Cotizador($cotizacionDatos, 'sb', 'db');
								$cotizacion::cotizar();
								$tablaDatos['sb_db'] = array('titulo' => 'Plan 3', 'nombre' => 'Suma Asegurada Baja - Deducible Bajo', 'tabla' => $cotizacion::tablaDatos(), 's' => 'sb', 'd' => 'db');
								
								$cotizacion = new Cotizador($cotizacionDatos, 'sb', 'da');
								$cotizacion::cotizar();
								$tablaDatos['sb_da'] = array('titulo' => 'Plan 4', 'nombre' => 'Suma Asegurada Baja - Deducible Alto', 'tabla' => $cotizacion::tablaDatos(), 's' => 'sb', 'd' => 'da');

								//View::share('tablaDatos', $tablaDatos);
								$respuesta["tablaDatos"] = $tablaDatos;
								$tablaClienteDatos = $cotizacion::tablaClienteDatos();
								//View::share('tablaClienteDatos', $tablaClienteDatos);
								$respuesta["tablaClienteDatos"] = $tablaClienteDatos;
								$tablaIntegrantes = $cotizacion::tablaIntegrantes();
								//View::share('tablaIntegrantes', $tablaIntegrantes);
								$respuesta["tablaIntegrantes"] =$tablaIntegrantes;

								//$this->layout->content = View::make('cotizacion.verCotizacion');
								
								$respuesta["status"] = "success";
							}else{
								//return Redirect::to('/cotizador');
								$respuesta["mensaje"] = "Error de redirect a /cotizador";
							}
							// Termina codigo de verCotizacion
						}
						
					}
					else{
						$respuesta['mensaje'] = 'Lo sentimos, su cuenta de correo electrónico ha superado el número de cotizaciones al semestre. Si necesita una cotización en este momento comuníquese con nosotros a los teléfonos en Guadalajara: (33) 200-201-70 y con gusto le atenderemos. Si eres empresa comunícate con nosotros para darte un precio especial.';
					}
				}
				else{
					$respuesta['mensaje'] = 'Lo sentimos, su cuenta de correo electrónico se encuentra bloqueado. En caso de no serlo, por favor comuníquese con nosotros a los teléfonos: (33) 200-201-70 para realizar la cotización.';
				}
				
			}
		}
		return json_encode($respuesta);
	}

	public function miniNuevaCotizacionOrigen(){
		//$datos = Input::except('nombres', 'integrantes', 'sexos', 'edades');
		$datos = Input::except('total', 'titulos', 'nombres', 'sexos', 'edades', 'chkTerminos');
		//$integrantes = Input::get('integrantes');
		//$integrantes[] = 1;
		$total = Input::get('total');
		
		$respuesta = array(
                        "status" => "invalid",
                        "mensaje" => "Ocurrio un error al tratar de guardar el formulario",
                        "idCotizacion" => -1,
                        "secret" => "",
                        "tabDefault" => "",
                        "cotizacionDatos" => "",
                        "tablaDatos" => "",
                        "tablaClienteDatos" => "",
                        "tablaIntegrantes" => ""
                    );
        
        $origen = Request::server('HTTP_ORIGIN');
        $dominio = Domain::where('dominio', '=', $origen)->where('activo', true)->first();
        if(!$dominio){
        	$respuesta["mensaje"] = "No se pueden generar cotizaciones desde este dominio: " . $origen;
			return json_encode($respuesta);
		}
        
		//if(count($integrantes) > 0){
		if($total > 0){
			$titulo = Input::get('titulos');
			$nombre = Input::get('nombres');
			$sexo = Input::get('sexos');
			$edad = Input::get('edades');
			$sa = 'sa';
			$ded = 'db';
			$integrantesLista = array();
			//foreach($integrantes AS $integrante){
			$x = 0;
			foreach($titulo AS $t){
				if($t!=""){
					$integrantesLista[] = array(
										//'titular' => (($integrante == 1) ? 1 : 0),
										'titular' => (($t == "Titular") ? 1 : 0),
										//'titulo' => (($integrante == 1) ? 'titular' : (($integrante == 2) ? 'conyugue' : 'hijo') ),
										'titulo' => $t,
										//'nombre' => $nombre[$integrante - 1],
										'nombre' => $nombre[$x],
										//'sexo' => $sexo[$integrante - 1],
										'sexo' => $sexo[$x],
										//'edad' => $edad[$integrante - 1],
										'edad' => $edad[$x],
									);
					//if($edad[$integrante - 1] >= 50){
					if($edad[$x] >= 50){
						$sa = 'sa';
	                	$ded = 'da';
					}
				}
				$x++;
			}
			if(count($integrantesLista) > 0){
				
				if(Emailblack::where('e_mail', '=', $datos['e_mail'])->get()->count() == 0){
					$fechaUltima = date('Y-m-d', strtotime('-6month', strtotime(date('Y-m-d'))));
	                $cotizar = false;
	                $totalCotizaciones = Cotizacion::where('e_mail', '=', $datos['e_mail'])->where('fecha_registro', '>=', $fechaUltima)->get()->count();
	                
	                $Emailwhite = Emailwhite::where('e_mail', '=', $datos['e_mail'])->first();
	                if($totalCotizaciones < 6){
	                    $cotizar = true;
	                }
	                if($Emailwhite){
	                    if($totalCotizaciones < $Emailwhite->cotizacionesTotales){
	                        $cotizar = true;
	                    }
	                }
	                if($cotizar == true){
						
						$cotizacionDatos = new Cotizacion();
						$cotizacionDatos->id_dominio = $dominio->id_dominio;
						foreach($datos AS $key => $value){
							$cotizacionDatos->$key = $value;
						}
						$cotizacionDatos->integrantes = json_encode($integrantesLista);
						$cotizacionDatos->secret = str_random(15);
						$cotizacionDatos->forma_ingreso = 2;
						$referer = parse_url($_SERVER["HTTP_REFERER"]);
						if(isset($referer["path"]))
							$cotizacionDatos->ruta = $referer["path"];
						if($cotizacionDatos->save()){
							//$respuesta["status"] = "success";
							$respuesta["idCotizacion"] = $cotizacionDatos->id_cotizacion;
							$respuesta["secret"] = $cotizacionDatos->secret;
							$cotizacionDatos->integrantes = json_decode($cotizacionDatos->integrantes);
							$pdf = self::generarCotizacionPdfOrigen($cotizacionDatos, false, $sa, $ded);
							
							//if(file_exists($pdf)){
								$encabezado = Cotizaciontexto::where('texto_seccion', '=', 'texto_correo_encabezado')->where('id_dominio', '=', $cotizacionDatos->id_dominio)->first();
								$cuerpo = Cotizaciontexto::where('texto_seccion', '=', 'texto_correo_cuerpo')->where('id_dominio', '=', $cotizacionDatos->id_dominio)->first();
								$pie = Cotizaciontexto::where('texto_seccion', '=', 'texto_correo_pie')->where('id_dominio', '=', $cotizacionDatos->id_dominio)->where('id_usuario', '=', '-1')->first();
								$cotizacionDatos->pdf = $pdf;
								$datosPlantilla = array(
										'nombre' => $cotizacionDatos->nombre,
										'e_mail' => $cotizacionDatos->e_mail,
										'id_cotizacion' => $cotizacionDatos->id_cotizacion,
										'secret' => $cotizacionDatos->secret,
										'encabezado' => $encabezado->texto_pdf,
										'cuerpo' => $cuerpo->texto_pdf,
										'pie' => $pie->texto_pdf
									);
								try{
					                Mail::send('plantillas.correo.enviarCotizacionCorreo', $datosPlantilla, function($message) use ($cotizacionDatos){
					                	//$message->from('info@segurodegastosmedicosmayores.mx', 'Seguro de Gastos Médicos Mayores');
					                	$message->from($cotizacionDatos->dominio()->first()->email, $cotizacionDatos->dominio()->first()->sender);
					                    $message->to($cotizacionDatos->e_mail, $cotizacionDatos->nombre);
					                    $message->cc('info@segurodegastosmedicosmayores.mx', 'Cotizaciones');
					                    $message->subject('Cotización de Gastos Médicos Mayores - '.$cotizacionDatos->id_cotizacion);
					                    if(file_exists($cotizacionDatos->pdf)){
					                        $message->attach($cotizacionDatos->pdf, ['as' => 'Cotizacion_PDF:'.$cotizacionDatos->id_cotizacion.'.pdf']);
					                    }
					                });
					                $log = new Logsistema;
					                $log->cotizacion_id = $cotizacionDatos->id_cotizacion;
					                $log->error = 'Cotizacion enviada: '.$cotizacionDatos->id_cotizacion;
					                $log->tipo = 'correo_enviado';
					                $log->controlador = 'CotizadorController';
					                $log->metodo = 'miniNuevaCotizacionOrigen';
					                $log->save();
					            }catch(Exception $e){
					            	$respuesta["status"] = "warning";
									//dd($e->getMessage());
					            	$respuesta["mensaje"] .= 'Ocurrio un error al tratar de enviar la cotización a su Correo';
					            	$log = new Logsistema;
					            	$log->cotizacion_id = $cotizacionDatos->id_cotizacion;
					            	$log->error = $e->getMessage();
					            	$log->tipo = 'enviar_correo';
					            	$log->controlador = 'CotizadorController';
					            	$log->metodo = 'miniNuevaCotizacionOrigen';
					            	$log->save();
					            }
					            $respuesta["mensaje"] .= 'Cotización enviada correctamente a su Correo.';
								@unlink($pdf);
							//}
							
							// Inicia codigo de verCotizacion
							$cotizacionDatos = Cotizacion::find($cotizacionDatos->id_cotizacion);
							if($cotizacionDatos){
								if($cotizacionDatos->visto == -1){
									$cotizacionDatos->visto = 1;
									$cotizacionDatos->save();
								}
								$cotizacionDatos->integrantes = json_decode($cotizacionDatos->integrantes);
								$sumaAsegurada = 'sb';
								$dedubicle = 'db';
								$tabDefault = 'sa_db';
								foreach($cotizacionDatos->integrantes AS $integrante){
									if($integrante->edad >= 50){
										$sumaAsegurada = 'sa';
										$dedubicle = 'da';
										$tabDefault = 'sa_da';
									}
								}
								//View::share('tabDefault', $tabDefault);
								$respuesta["tabDefault"] = $tabDefault;
								//View::share('cotizacionDatos', $cotizacionDatos);
								$respuesta["cotizacionDatos"] = $cotizacionDatos;
								//$cotizacion = new Cotizador($cotizacionDatos, $sumaAsegurada, $dedubicle);
								$cotizacion = new Cotizador($cotizacionDatos, 'sa', 'db');
								$cotizacion::cotizar();
								$tablaDatos['sa_db'] = array('titulo' => 'Plan 1', 'nombre' => 'Suma Asegurada Alta - Deducible Bajo', 'tabla' => $cotizacion::tablaDatos(), 'datos' => $cotizacion::datosTabla(), 's' => 'sa', 'd' => 'db');
								
								$cotizacion = new Cotizador($cotizacionDatos, 'sa', 'da');
								$cotizacion::cotizar();
								$tablaDatos['sa_da'] = array('titulo' => 'Plan 2', 'nombre' => 'Suma Asegurada Alta - Deducible Alto', 'tabla' => $cotizacion::tablaDatos(), 'datos' => $cotizacion::datosTabla(), 's' => 'sa', 'd' => 'da');
								
								$cotizacion = new Cotizador($cotizacionDatos, 'sb', 'db');
								$cotizacion::cotizar();
								$tablaDatos['sb_db'] = array('titulo' => 'Plan 3', 'nombre' => 'Suma Asegurada Baja - Deducible Bajo', 'tabla' => $cotizacion::tablaDatos(), 'datos' => $cotizacion::datosTabla(), 's' => 'sb', 'd' => 'db');
								
								$cotizacion = new Cotizador($cotizacionDatos, 'sb', 'da');
								$cotizacion::cotizar();
								$tablaDatos['sb_da'] = array('titulo' => 'Plan 4', 'nombre' => 'Suma Asegurada Baja - Deducible Alto', 'tabla' => $cotizacion::tablaDatos(), 'datos' => $cotizacion::datosTabla(), 's' => 'sb', 'd' => 'da');

								//View::share('tablaDatos', $tablaDatos);
								$respuesta["tablaDatos"] = $tablaDatos;
								$tablaClienteDatos = $cotizacion::tablaClienteDatos();
								//View::share('tablaClienteDatos', $tablaClienteDatos);
								$respuesta["tablaClienteDatos"] = $tablaClienteDatos;
								$tablaIntegrantes = $cotizacion::tablaIntegrantes();
								//View::share('tablaIntegrantes', $tablaIntegrantes);
								$respuesta["tablaIntegrantes"] =$tablaIntegrantes;

								//$this->layout->content = View::make('cotizacion.verCotizacion');
								
								$respuesta["status"] = "success";
							}else{
								//return Redirect::to('/cotizador');
								$respuesta["mensaje"] = "Error de redirect a /cotizador";
							}
							// Termina codigo de verCotizacion
						}
						
					}
					else{
						$respuesta['mensaje'] = 'Lo sentimos, su cuenta de correo electrónico ha superado el número de cotizaciones al semestre. Si necesita una cotización en este momento comuníquese con nosotros a los teléfonos en Guadalajara: (33) 200-201-70 y con gusto le atenderemos. Si eres empresa comunícate con nosotros para darte un precio especial.';
					}
				}
				else{
					$respuesta['mensaje'] = 'Lo sentimos, su cuenta de correo electrónico se encuentra bloqueado. En caso de no serlo, por favor comuníquese con nosotros a los teléfonos: (33) 200-201-70 para realizar la cotización.';
				}
				
			}
		}
		return json_encode($respuesta);
	}

	private static function generarCotizacionPdfOrigen($cotizacionDatos = array(), $mostrar = true, $sa = 'sa', $ded = 'db'){
		$cotizacion = new Cotizador($cotizacionDatos, $sa, $ded);
        $cotizacion::cotizar();
        
        $mpdf = new mPDF('', 'Letter', '', '', 20,20,20,20,5,5);
        $bienvenida = Cotizaciontexto::where('texto_seccion', '=', 'saludo_bienvenida')->where('id_dominio', '=', $cotizacionDatos->id_dominio)->first()->texto_pdf;
        View::share('bienvenida', $bienvenida);
        $beneficios = Cotizaciontexto::where('texto_seccion', '=', 'beneficios_protecto')->where('id_dominio', '=', $cotizacionDatos->id_dominio)->first()->texto_pdf;
        View::share('beneficios', $beneficios);
        View::share('cotizacionDatos', $cotizacionDatos);
        View::share('cotizacion', $cotizacion);
        $aseguradoras = Aseguradora::where('activa', '=', 1)->orderBy('orden')->get();
        View::share('aseguradoras', $aseguradoras);
        $html = View::make('plantillas.correo.cotizacionPdf');
        $mpdf->WriteHTML($html);
        if($mostrar == true){
            $mpdf->Output($file_name, 'I');
        }else{
            if(!is_dir('tmp')){
                mkdir('tmp');
            }
            $ruta = 'tmp/cotizacion_'.$cotizacionDatos->id_cotizacion.'_'.$sa.'_'.$ded.'.pdf';
            $mpdf->Output($ruta,'F');
            return $ruta;
        }
	}

	public function miniVerCotizacion($idCotizacion = -1, $secret = ''){
		$cotizacionDatos = Cotizacion::find($idCotizacion);
		$respuesta = array(
                        "status" => "invalid",
                        "mensaje" => "La Cotizacion solicitada no existe",
                        "idCotizacion" => -1,
                        "cotizacionDatos" => "",
                        "tablaDatos" => ""
                    );
		if($cotizacionDatos){
			if($cotizacionDatos->visto == -1){
				$cotizacionDatos->visto = 1;
				$cotizacionDatos->save();
			}
			/*
			$conversionAdwords = '';
			if($cotizacionDatos->conversionAdwords == -1){
				$conversionAdwords= '<!-- Google Code for Gastos Medicos 2017 Conversion Page -->
										<script type="text/javascript">
											/* <![CDATA[ *
											var google_conversion_id = 1025142637;
											var google_conversion_language = "en";
											var google_conversion_format = "3";
											var google_conversion_color = "ffffff";
											var google_conversion_label = "Um2KCOLz1G4Q7d7p6AM";
											var google_remarketing_only = false;
											/* ]]> *
										</script>
										<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js"></script>
										<noscript>
											<div style="display:inline;">
												<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/1025142637/?label=Um2KCOLz1G4Q7d7p6AM&amp;guid=ON&amp;script=0"/>
											</div>
										</noscript>';
				$cotizacionDatos->conversionAdwords = 1;
				$cotizacionDatos->save();
			}
			View::share('conversionAdwords', $conversionAdwords);
			*/
			$cotizacionDatos->integrantes = json_decode($cotizacionDatos->integrantes);
			$sumaAsegurada = 'sb';
			$dedubicle = 'db';
			$tabDefault = 'sa_db';
			foreach($cotizacionDatos->integrantes AS $integrante){
				if($integrante->edad >= 50){
					$sumaAsegurada = 'sa';
					$dedubicle = 'da';
					$tabDefault = 'sa_da';
				}
			}
			View::share('tabDefault', $tabDefault);
			View::share('cotizacionDatos', $cotizacionDatos);
			//$cotizacion = new Cotizador($cotizacionDatos, $sumaAsegurada, $dedubicle);
			$cotizacion = new Cotizador($cotizacionDatos, 'sa', 'db');
			$cotizacion::cotizar();
			$tablaDatos['sa_db'] = array('titulo' => 'Plan 1', 'nombre' => 'Suma Asegurada Alta - Deducible Bajo', 'tabla' => $cotizacion::tablaDatos(), 'datos' => $cotizacion::datosTabla(), 's' => 'sa', 'd' => 'db');
			
			$cotizacion = new Cotizador($cotizacionDatos, 'sa', 'da');
			$cotizacion::cotizar();
			$tablaDatos['sa_da'] = array('titulo' => 'Plan 2', 'nombre' => 'Suma Asegurada Alta - Deducible Alto', 'tabla' => $cotizacion::tablaDatos(), 'datos' => $cotizacion::datosTabla(), 's' => 'sa', 'd' => 'da');
			
			$cotizacion = new Cotizador($cotizacionDatos, 'sb', 'db');
			$cotizacion::cotizar();
			$tablaDatos['sb_db'] = array('titulo' => 'Plan 3', 'nombre' => 'Suma Asegurada Baja - Deducible Bajo', 'tabla' => $cotizacion::tablaDatos(), 'datos' => $cotizacion::datosTabla(), 's' => 'sb', 'd' => 'db');
			
			$cotizacion = new Cotizador($cotizacionDatos, 'sb', 'da');
			$cotizacion::cotizar();
			$tablaDatos['sb_da'] = array('titulo' => 'Plan 4', 'nombre' => 'Suma Asegurada Baja - Deducible Alto', 'tabla' => $cotizacion::tablaDatos(), 'datos' => $cotizacion::datosTabla(), 's' => 'sb', 'd' => 'da');

			//View::share('tablaDatos', $tablaDatos);
			//$tablaClienteDatos = $cotizacion::tablaClienteDatos();
			//View::share('tablaClienteDatos', $tablaClienteDatos);
			//$tablaIntegrantes = $cotizacion::tablaIntegrantes();
			//View::share('tablaIntegrantes', $tablaIntegrantes);

			//$this->layout->content = View::make('cotizacion.verCotizacion');
			$respuesta["status"] = "success";
			$respuesta["cotizacionDatos"] = $cotizacionDatos;
			$respuesta["tablaDatos"] = $tablaDatos;
		}
		//else{
		//	return Redirect::to('/cotizador');
		//}
		return json_encode($respuesta);
	}

	public function cotizacion($idCotizacion = -1, $secret = ''){
		// Inicia codigo de verCotizacion
		$cotizacionDatos = Cotizacion::find($idCotizacion);
		if($cotizacionDatos){
			if($cotizacionDatos->secret == $secret){
				if($cotizacionDatos->visto == -1){
					$cotizacionDatos->visto = 1;
					$cotizacionDatos->save();
				}
				$cotizacionDatos->integrantes = json_decode($cotizacionDatos->integrantes);
				$sumaAsegurada = 'sb';
				$dedubicle = 'db';
				$tabDefault = 'sa_db';
				foreach($cotizacionDatos->integrantes AS $integrante){
					if($integrante->edad >= 60 && $integrante->edad <= 64)
						$tabDefault = "sb_da";
					if($integrante->edad >= 50 && $integrante->edad <= 59)
						$tabDefault = "sa_da";
				}
				View::share('tabDefault', $tabDefault);
				//$respuesta["tabDefault"] = $tabDefault;
				View::share('cotizacionDatos', $cotizacionDatos);
				//$respuesta["cotizacionDatos"] = $cotizacionDatos;
				//$cotizacion = new Cotizador($cotizacionDatos, $sumaAsegurada, $dedubicle);
				$cotizacion = new Cotizador($cotizacionDatos, 'sa', 'db');
				$cotizacion::cotizar();
				$tablaDatos['sa_db'] = array('titulo' => 'Plan 1', 'nombre' => 'Suma Asegurada Alta - Deducible Bajo', 'tabla' => $cotizacion::tablaDatos(), 'datos' => $cotizacion::datosTabla(), 's' => 'sa', 'd' => 'db');
				
				$cotizacion = new Cotizador($cotizacionDatos, 'sa', 'da');
				$cotizacion::cotizar();
				$tablaDatos['sa_da'] = array('titulo' => 'Plan 2', 'nombre' => 'Suma Asegurada Alta - Deducible Alto', 'tabla' => $cotizacion::tablaDatos(), 'datos' => $cotizacion::datosTabla(), 's' => 'sa', 'd' => 'da');
				
				$cotizacion = new Cotizador($cotizacionDatos, 'sb', 'db');
				$cotizacion::cotizar();
				$tablaDatos['sb_db'] = array('titulo' => 'Plan 3', 'nombre' => 'Suma Asegurada Baja - Deducible Bajo', 'tabla' => $cotizacion::tablaDatos(), 'datos' => $cotizacion::datosTabla(), 's' => 'sb', 'd' => 'db');
				
				$cotizacion = new Cotizador($cotizacionDatos, 'sb', 'da');
				$cotizacion::cotizar();
				$tablaDatos['sb_da'] = array('titulo' => 'Plan 4', 'nombre' => 'Suma Asegurada Baja - Deducible Alto', 'tabla' => $cotizacion::tablaDatos(), 'datos' => $cotizacion::datosTabla(), 's' => 'sb', 'd' => 'da');

				View::share('tablaDatos', $tablaDatos);
				//$respuesta["tablaDatos"] = $tablaDatos;
				$tablaClienteDatos = $cotizacion::tablaClienteDatos();
				View::share('tablaClienteDatos', $tablaClienteDatos);
				//$respuesta["tablaClienteDatos"] = $tablaClienteDatos;
				$tablaIntegrantes = $cotizacion::tablaIntegrantes();
				View::share('tablaIntegrantes', $tablaIntegrantes);
				//$respuesta["tablaIntegrantes"] =$tablaIntegrantes;
				
				$textoCEncabezado = "";
				$textoCPie = "";
				$encabezado = \DB::table('texto_pdf')->where('texto_seccion', 'cotizacion_encabezado')->where('id_dominio', $cotizacionDatos->id_dominio)->get();
				$pie = \DB::table('texto_pdf')->where('texto_seccion', 'cotizacion_pie')->where('id_dominio', $cotizacionDatos->id_dominio)->get();
				if($encabezado)
					$textoCEncabezado = $encabezado[0]->texto_pdf;
				if($pie)
					$textoCPie = $pie[0]->texto_pdf;
				View::share('cotizacionEncabezado', $textoCEncabezado);
				View::share('cotizacionPie', $textoCPie);
				
				//$this->layout->content = View::make('blog.verPortoCotizacion');
				return View::make('blog.verPortoCotizacion');
				
				//$respuesta["status"] = "success";
			}
			else{
				return Redirect::to('/');
			}
		}else{
			return Redirect::to('/cotizador');
			//$respuesta["mensaje"] = "Error de redirect a /cotizador";
		}
	}
	
	public function cotizacionUUID($uuid = ''){
		// Inicia codigo de verCotizacion
		$cotizacionDatos = Cotizacion::where("link_cotizacion", "=", $uuid)->first();
		if($cotizacionDatos){
			if($cotizacionDatos->visto == -1){
				$cotizacionDatos->visto = 1;
				$cotizacionDatos->save();
			}
			$cotizacionDatos->integrantes = json_decode($cotizacionDatos->integrantes);
			$sumaAsegurada = 'sb';
			$dedubicle = 'db';
			$tabDefault = 'sa_db';
			foreach($cotizacionDatos->integrantes AS $integrante){
				if($integrante->edad >= 60 && $integrante->edad <= 64)
					$tabDefault = "sb_da";
				if($integrante->edad >= 50 && $integrante->edad <= 59)
					$tabDefault = "sa_da";
			}
			View::share('tabDefault', $tabDefault);
			//$respuesta["tabDefault"] = $tabDefault;
			View::share('cotizacionDatos', $cotizacionDatos);
			//$respuesta["cotizacionDatos"] = $cotizacionDatos;
			//$cotizacion = new Cotizador($cotizacionDatos, $sumaAsegurada, $dedubicle);
			$cotizacion = new Cotizador($cotizacionDatos, 'sa', 'db');
			$cotizacion::cotizar();
			$tablaDatos['sa_db'] = array('titulo' => 'Plan 1', 'nombre' => 'Suma Asegurada Alta - Deducible Bajo', 'tabla' => $cotizacion::tablaDatos(), 'datos' => $cotizacion::datosTabla(), 's' => 'sa', 'd' => 'db');
			
			$cotizacion = new Cotizador($cotizacionDatos, 'sa', 'da');
			$cotizacion::cotizar();
			$tablaDatos['sa_da'] = array('titulo' => 'Plan 2', 'nombre' => 'Suma Asegurada Alta - Deducible Alto', 'tabla' => $cotizacion::tablaDatos(), 'datos' => $cotizacion::datosTabla(), 's' => 'sa', 'd' => 'da');
			
			$cotizacion = new Cotizador($cotizacionDatos, 'sb', 'db');
			$cotizacion::cotizar();
			$tablaDatos['sb_db'] = array('titulo' => 'Plan 3', 'nombre' => 'Suma Asegurada Baja - Deducible Bajo', 'tabla' => $cotizacion::tablaDatos(), 'datos' => $cotizacion::datosTabla(), 's' => 'sb', 'd' => 'db');
			
			$cotizacion = new Cotizador($cotizacionDatos, 'sb', 'da');
			$cotizacion::cotizar();
			$tablaDatos['sb_da'] = array('titulo' => 'Plan 4', 'nombre' => 'Suma Asegurada Baja - Deducible Alto', 'tabla' => $cotizacion::tablaDatos(), 'datos' => $cotizacion::datosTabla(), 's' => 'sb', 'd' => 'da');

			View::share('tablaDatos', $tablaDatos);
			//$respuesta["tablaDatos"] = $tablaDatos;
			$tablaClienteDatos = $cotizacion::tablaClienteDatos();
			View::share('tablaClienteDatos', $tablaClienteDatos);
			//$respuesta["tablaClienteDatos"] = $tablaClienteDatos;
			$tablaIntegrantes = $cotizacion::tablaIntegrantes();
			View::share('tablaIntegrantes', $tablaIntegrantes);
			//$respuesta["tablaIntegrantes"] =$tablaIntegrantes;
			
			$textoCEncabezado = "";
			$textoCPie = "";
			$encabezado = \DB::table('texto_pdf')->where('texto_seccion', 'cotizacion_encabezado')->where('id_dominio', $cotizacionDatos->id_dominio)->get();
			$pie = \DB::table('texto_pdf')->where('texto_seccion', 'cotizacion_pie')->where('id_dominio', $cotizacionDatos->id_dominio)->get();
			if($encabezado)
				$textoCEncabezado = $encabezado[0]->texto_pdf;
			if($pie)
				$textoCPie = $pie[0]->texto_pdf;
			View::share('cotizacionEncabezado', $textoCEncabezado);
			View::share('cotizacionPie', $textoCPie);
			
			//$this->layout->content = View::make('blog.verPortoCotizacion');
			return View::make('blog.verPortoCotizacion');
			
			//$respuesta["status"] = "success";
			
		}else{
			return Redirect::to('/cotizador');
			//$respuesta["mensaje"] = "Error de redirect a /cotizador";
		}
	}
	
	public function cuestionario(){
		$idCotizacion = Input::get('id');
		$secret = Input::get('secret');
		$planes = Input::get('planes');
		$comentarios = Input::get('comentarios');
		$por = Input::get('por');
		$datos = array(
			"idCotizacion" => $idCotizacion,
			"secret" => $secret,
			"planes" => json_encode($planes),
			"comentarios" => $comentarios,
			"por" => $por
		);
		$cotizacionDatos = Cotizacion::find($idCotizacion);
		if($cotizacionDatos){
			if($cotizacionDatos->secret == $secret){
				$cotizacionDatos->estatus = 1;
				$cotizacionDatos->comentarios = $cotizacionDatos->comentarios . " -- Planes: " . $datos["planes"] . " -- Comentarios: " . $comentarios . " -- Contactar por: " . $por;
				$cotizacionDatos->save();
				 Mail::send('plantillas.correo.deseaCotizar', $datos, function($message) use ($cotizacionDatos){
                	 $message->from($cotizacionDatos->dominio()->first()->email, $cotizacionDatos->dominio()->first()->sender);
                    $message->to("ventas1@segurodegastosmedicosmayores.mx");
                    $message->subject('Desea Contratar Gastos Medicos ' . $cotizacionDatos->id_cotizacion);
                });
			}
		}
		
		return "1";
	}

	public function nuevaCotizacionWS(){
		//$datos = Input::except('nombres', 'integrantes', 'sexos', 'edades');
		$datos = Input::except('total', 'titulos', 'nombres', 'sexos', 'edades', 'chkTerminos');
		//$integrantes = Input::get('integrantes');
		//$integrantes[] = 1;
		$total = Input::get('total');
		
		$respuesta = array(
                        "status" => "invalid",
                        "mensaje" => "Ocurrio un error al tratar de guardar el formulario",
                        "idCotizacion" => -1,
                        "secret" => "",
                        "tabDefault" => "",
                        "cotizacionDatos" => "",
                        "tablaDatos" => "",
                        "tablaClienteDatos" => "",
                        "tablaIntegrantes" => ""
                    );
        
        $origen = Request::server('HTTP_ORIGIN');
        $dominio = Domain::where('dominio', '=', $origen)->where('activo', true)->first();
        if(!$dominio){
        	$respuesta["mensaje"] = "No se pueden generar cotizaciones desde este dominio: " . $origen;
			return json_encode($respuesta);
		}
        
		//if(count($integrantes) > 0){
		if($total > 0){
			$estado = Estado::where('clave', '=', $datos["estado"])->first();
			$titulo = Input::get('titulos');
			$nombre = Input::get('nombres');
			$sexo = Input::get('sexos');
			$edad = Input::get('edades');
			$sa = 'sa';
			$ded = 'db';
			$integrantesLista = array();
			//foreach($integrantes AS $integrante){
			$x = 0;
			$mapfreIntegrantes = array();;
			foreach($titulo AS $t){
				if($t!=""){
					$integrantesLista[] = array(
										//'titular' => (($integrante == 1) ? 1 : 0),
										'titular' => (($t == "Titular") ? 1 : 0),
										//'titulo' => (($integrante == 1) ? 'titular' : (($integrante == 2) ? 'conyugue' : 'hijo') ),
										'titulo' => $t,
										//'nombre' => $nombre[$integrante - 1],
										'nombre' => $nombre[$x],
										//'sexo' => $sexo[$integrante - 1],
										'sexo' => $sexo[$x],
										//'edad' => $edad[$integrante - 1],
										'edad' => $edad[$x],
									);
					//if($edad[$integrante - 1] >= 50){
					if($edad[$x] >= 50){
						$sa = 'sa';
	                	$ded = 'da';
					}
					$parentesco = Parentesco::where('parentesco', '=', $t)->first();
					if($sexo[$x]=="m"){
						$sexo = "Masculino";
						$idSexo = 1;
					}
					else{
						$sexo = "Femenino";
						$idSexo = 0;
					}
					$mapfreIntegrantes[] = array(
						"nombre" => $nombre[$x],
						"id_parentesco" => $parentesco->id_parentesco,
						"parentesco" => $parentesco->parentesco,
						"id_sexo" => $idSexo,
						"sexo" => $sexo,
						"edad" => $edad[$x]
					);
				}
				$x++;
			}
			if(count($integrantesLista) > 0){
				
				if(Emailblack::where('e_mail', '=', $datos['e_mail'])->get()->count() == 0){
					$fechaUltima = date('Y-m-d', strtotime('-6month', strtotime(date('Y-m-d'))));
	                $cotizar = false;
	                $totalCotizaciones = Cotizacion::where('e_mail', '=', $datos['e_mail'])->where('fecha_registro', '>=', $fechaUltima)->get()->count();
	                
	                $Emailwhite = Emailwhite::where('e_mail', '=', $datos['e_mail'])->first();
	                if($totalCotizaciones < 6){
	                    $cotizar = true;
	                }
	                if($Emailwhite){
	                    if($totalCotizaciones < $Emailwhite->cotizacionesTotales){
	                        $cotizar = true;
	                    }
	                }
	                if($cotizar == true){
						
						$cotizacionDatos = new Cotizacion();
						$cotizacionDatos->id_dominio = $dominio->id_dominio;
						foreach($datos AS $key => $value){
							$cotizacionDatos->$key = $value;
						}
						$cotizacionDatos->integrantes = json_encode($integrantesLista);
						$cotizacionDatos->secret = str_random(15);
						$cotizacionDatos->forma_ingreso = 2;
						$referer = parse_url($_SERVER["HTTP_REFERER"]);
						if(isset($referer["path"]))
							$cotizacionDatos->ruta = $referer["path"];
						$cotizacionDatos->pdf_sa = $sa;
						$cotizacionDatos->pdf_ded = $ded;
						if($cotizacionDatos->save()){
							//$respuesta["status"] = "success";
							$respuesta["idCotizacion"] = $cotizacionDatos->id_cotizacion;
							$respuesta["secret"] = $cotizacionDatos->secret;
							
							
							/* Codigo para cotizar Mapfre en WebService */
							try{
								
								/*$mapfreCotizacion = $this->mapfreCotizacion($mapfreIntegrantes, $estado);
								$cotizacionDatos->mapfre_numero = $mapfreCotizacion["cotizacion"]["xml"]["num_solicitud"];
								$cotizacionDatos->mapfre_xml = $mapfreCotizacion["xml"];
								$cotizacionDatos->mapfre_respuesta = json_encode($mapfreCotizacion["cotizacion"]);
								$cotizacionDatos->save();*/
								
								/*$cotizacionDatos->integrantes = json_decode($cotizacionDatos->integrantes);
								
								$mapfreSADA = $this->mapfreRecotizacion($mapfreIntegrantes, $estado, $mapfreCotizacion["cotizacion"], "sada", 1);
								$recotizacionSADA = new RecotizacionMapfre();
								$recotizacionSADA->id_cotizacion = $cotizacionDatos->id_cotizacion;
								$recotizacionSADA->tipo = "sada";
								$recotizacionSADA->hospitales = "esencial";
								$recotizacionSADA->xml = $mapfreSADA["xml"];
								$recotizacionSADA->respuesta = json_encode($mapfreSADA["cotizacion"]);
								foreach($mapfreSADA["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][0]["pagos"]["pago"]["montospaquetes"]["montos"] as $monto){
									if($monto["cod_paquete"]=="207"){
										$recotizacionSADA->contado = $monto["monto"];
									}
								}
								$primer = 1;
								foreach($mapfreSADA["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][1]["pagos"]["pago"] as $pago){
									foreach($pago["montospaquetes"]["montos"] as $monto){
										if($monto["cod_paquete"]=="207"){
											if($primer==1)
												$recotizacionSADA->semestral_primer = $monto["monto"];
											else
												$recotizacionSADA->semestral_posterior = $monto["monto"];
											$primer = -1;
										}
									}
								}
								$primer = 1;
								foreach($mapfreSADA["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][2]["pagos"]["pago"] as $pago){
									foreach($pago["montospaquetes"]["montos"] as $monto){
										if($monto["cod_paquete"]=="207"){
											if($primer==1)
												$recotizacionSADA->trimestral_primer = $monto["monto"];
											else
												$recotizacionSADA->trimestral_posterior = $monto["monto"];
											$primer = -1;
										}
									}
								}
								$primer = 1;
								foreach($mapfreSADA["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][3]["pagos"]["pago"] as $pago){
									foreach($pago["montospaquetes"]["montos"] as $monto){
										if($monto["cod_paquete"]=="207"){
											if($primer==1)
												$recotizacionSADA->mensual_primer = $monto["monto"];
											else
												$recotizacionSADA->mensual_posterior = $monto["monto"];
											$primer = -1;
										}
									}
								}
								$recotizacionSADA->save();
								
								$mapfreSADA = $this->mapfreRecotizacion($mapfreIntegrantes, $estado, $mapfreCotizacion["cotizacion"], "sada", 2);
								$recotizacionSADA = new RecotizacionMapfre();
								$recotizacionSADA->id_cotizacion = $cotizacionDatos->id_cotizacion;
								$recotizacionSADA->tipo = "sada";
								$recotizacionSADA->hospitales = "optima";
								$recotizacionSADA->xml = $mapfreSADA["xml"];
								$recotizacionSADA->respuesta = json_encode($mapfreSADA["cotizacion"]);
								foreach($mapfreSADA["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][0]["pagos"]["pago"]["montospaquetes"]["montos"] as $monto){
									if($monto["cod_paquete"]=="207"){
										$recotizacionSADA->contado = $monto["monto"];
									}
								}
								$primer = 1;
								foreach($mapfreSADA["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][1]["pagos"]["pago"] as $pago){
									foreach($pago["montospaquetes"]["montos"] as $monto){
										if($monto["cod_paquete"]=="207"){
											if($primer==1)
												$recotizacionSADA->semestral_primer = $monto["monto"];
											else
												$recotizacionSADA->semestral_posterior = $monto["monto"];
											$primer = -1;
										}
									}
								}
								$primer = 1;
								foreach($mapfreSADA["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][2]["pagos"]["pago"] as $pago){
									foreach($pago["montospaquetes"]["montos"] as $monto){
										if($monto["cod_paquete"]=="207"){
											if($primer==1)
												$recotizacionSADA->trimestral_primer = $monto["monto"];
											else
												$recotizacionSADA->trimestral_posterior = $monto["monto"];
											$primer = -1;
										}
									}
								}
								$primer = 1;
								foreach($mapfreSADA["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][3]["pagos"]["pago"] as $pago){
									foreach($pago["montospaquetes"]["montos"] as $monto){
										if($monto["cod_paquete"]=="207"){
											if($primer==1)
												$recotizacionSADA->mensual_primer = $monto["monto"];
											else
												$recotizacionSADA->mensual_posterior = $monto["monto"];
											$primer = -1;
										}
									}
								}
								$recotizacionSADA->save();
								
								$mapfreSADB = $this->mapfreRecotizacion($mapfreIntegrantes, $estado, $mapfreCotizacion["cotizacion"], "sadb", 1);
								$recotizacionSADB = new RecotizacionMapfre();
								$recotizacionSADB->id_cotizacion = $cotizacionDatos->id_cotizacion;
								$recotizacionSADB->tipo = "sadb";
								$recotizacionSADB->hospitales = "esencial";
								$recotizacionSADB->xml = $mapfreSADB["xml"];
								$recotizacionSADB->respuesta = json_encode($mapfreSADB["cotizacion"]);
								foreach($mapfreSADB["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][0]["pagos"]["pago"]["montospaquetes"]["montos"] as $monto){
									if($monto["cod_paquete"]=="207"){
										$recotizacionSADB->contado = $monto["monto"];
									}
								}
								$primer = 1;
								foreach($mapfreSADB["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][1]["pagos"]["pago"] as $pago){
									foreach($pago["montospaquetes"]["montos"] as $monto){
										if($monto["cod_paquete"]=="207"){
											if($primer==1)
												$recotizacionSADB->semestral_primer = $monto["monto"];
											else
												$recotizacionSADB->semestral_posterior = $monto["monto"];
											$primer = -1;
										}
									}
								}
								$primer = 1;
								foreach($mapfreSADB["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][2]["pagos"]["pago"] as $pago){
									foreach($pago["montospaquetes"]["montos"] as $monto){
										if($monto["cod_paquete"]=="207"){
											if($primer==1)
												$recotizacionSADB->trimestral_primer = $monto["monto"];
											else
												$recotizacionSADB->trimestral_posterior = $monto["monto"];
											$primer = -1;
										}
									}
								}
								$primer = 1;
								foreach($mapfreSADB["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][3]["pagos"]["pago"] as $pago){
									foreach($pago["montospaquetes"]["montos"] as $monto){
										if($monto["cod_paquete"]=="207"){
											if($primer==1)
												$recotizacionSADB->mensual_primer = $monto["monto"];
											else
												$recotizacionSADB->mensual_posterior = $monto["monto"];
											$primer = -1;
										}
									}
								}
								$recotizacionSADB->save();
								
								$mapfreSADB = $this->mapfreRecotizacion($mapfreIntegrantes, $estado, $mapfreCotizacion["cotizacion"], "sadb", 2);
								$recotizacionSADB = new RecotizacionMapfre();
								$recotizacionSADB->id_cotizacion = $cotizacionDatos->id_cotizacion;
								$recotizacionSADB->tipo = "sadb";
								$recotizacionSADB->hospitales = "optima";
								$recotizacionSADB->xml = $mapfreSADB["xml"];
								$recotizacionSADB->respuesta = json_encode($mapfreSADB["cotizacion"]);
								foreach($mapfreSADB["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][0]["pagos"]["pago"]["montospaquetes"]["montos"] as $monto){
									if($monto["cod_paquete"]=="207"){
										$recotizacionSADB->contado = $monto["monto"];
									}
								}
								$primer = 1;
								foreach($mapfreSADB["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][1]["pagos"]["pago"] as $pago){
									foreach($pago["montospaquetes"]["montos"] as $monto){
										if($monto["cod_paquete"]=="207"){
											if($primer==1)
												$recotizacionSADB->semestral_primer = $monto["monto"];
											else
												$recotizacionSADB->semestral_posterior = $monto["monto"];
											$primer = -1;
										}
									}
								}
								$primer = 1;
								foreach($mapfreSADB["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][2]["pagos"]["pago"] as $pago){
									foreach($pago["montospaquetes"]["montos"] as $monto){
										if($monto["cod_paquete"]=="207"){
											if($primer==1)
												$recotizacionSADB->trimestral_primer = $monto["monto"];
											else
												$recotizacionSADB->trimestral_posterior = $monto["monto"];
											$primer = -1;
										}
									}
								}
								$primer = 1;
								foreach($mapfreSADB["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][3]["pagos"]["pago"] as $pago){
									foreach($pago["montospaquetes"]["montos"] as $monto){
										if($monto["cod_paquete"]=="207"){
											if($primer==1)
												$recotizacionSADB->mensual_primer = $monto["monto"];
											else
												$recotizacionSADB->mensual_posterior = $monto["monto"];
											$primer = -1;
										}
									}
								}
								$recotizacionSADB->save();
								
								$mapfreSBDA = $this->mapfreRecotizacion($mapfreIntegrantes, $estado, $mapfreCotizacion["cotizacion"], "sbda", 1);
								$recotizacionSBDA = new RecotizacionMapfre();
								$recotizacionSBDA->id_cotizacion = $cotizacionDatos->id_cotizacion;
								$recotizacionSBDA->tipo = "sbda";
								$recotizacionSBDA->hospitales = "esencial";
								$recotizacionSBDA->xml = $mapfreSBDA["xml"];
								$recotizacionSBDA->respuesta = json_encode($mapfreSBDA["cotizacion"]);
								foreach($mapfreSBDA["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][0]["pagos"]["pago"]["montospaquetes"]["montos"] as $monto){
									if($monto["cod_paquete"]=="207"){
										$recotizacionSBDA->contado = $monto["monto"];
									}
								}
								$primer = 1;
								foreach($mapfreSBDA["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][1]["pagos"]["pago"] as $pago){
									foreach($pago["montospaquetes"]["montos"] as $monto){
										if($monto["cod_paquete"]=="207"){
											if($primer==1)
												$recotizacionSBDA->semestral_primer = $monto["monto"];
											else
												$recotizacionSBDA->semestral_posterior = $monto["monto"];
											$primer = -1;
										}
									}
								}
								$primer = 1;
								foreach($mapfreSBDA["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][2]["pagos"]["pago"] as $pago){
									foreach($pago["montospaquetes"]["montos"] as $monto){
										if($monto["cod_paquete"]=="207"){
											if($primer==1)
												$recotizacionSBDA->trimestral_primer = $monto["monto"];
											else
												$recotizacionSBDA->trimestral_posterior = $monto["monto"];
											$primer = -1;
										}
									}
								}
								$primer = 1;
								foreach($mapfreSBDA["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][3]["pagos"]["pago"] as $pago){
									foreach($pago["montospaquetes"]["montos"] as $monto){
										if($monto["cod_paquete"]=="207"){
											if($primer==1)
												$recotizacionSBDA->mensual_primer = $monto["monto"];
											else
												$recotizacionSBDA->mensual_posterior = $monto["monto"];
											$primer = -1;
										}
									}
								}
								$recotizacionSBDA->save();
								
								$mapfreSBDA = $this->mapfreRecotizacion($mapfreIntegrantes, $estado, $mapfreCotizacion["cotizacion"], "sbda", 2);
								$recotizacionSBDA = new RecotizacionMapfre();
								$recotizacionSBDA->id_cotizacion = $cotizacionDatos->id_cotizacion;
								$recotizacionSBDA->tipo = "sbda";
								$recotizacionSBDA->hospitales = "optima";
								$recotizacionSBDA->xml = $mapfreSBDA["xml"];
								$recotizacionSBDA->respuesta = json_encode($mapfreSBDA["cotizacion"]);
								foreach($mapfreSBDA["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][0]["pagos"]["pago"]["montospaquetes"]["montos"] as $monto){
									if($monto["cod_paquete"]=="207"){
										$recotizacionSBDA->contado = $monto["monto"];
									}
								}
								$primer = 1;
								foreach($mapfreSBDA["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][1]["pagos"]["pago"] as $pago){
									foreach($pago["montospaquetes"]["montos"] as $monto){
										if($monto["cod_paquete"]=="207"){
											if($primer==1)
												$recotizacionSBDA->semestral_primer = $monto["monto"];
											else
												$recotizacionSBDA->semestral_posterior = $monto["monto"];
											$primer = -1;
										}
									}
								}
								$primer = 1;
								foreach($mapfreSBDA["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][2]["pagos"]["pago"] as $pago){
									foreach($pago["montospaquetes"]["montos"] as $monto){
										if($monto["cod_paquete"]=="207"){
											if($primer==1)
												$recotizacionSBDA->trimestral_primer = $monto["monto"];
											else
												$recotizacionSBDA->trimestral_posterior = $monto["monto"];
											$primer = -1;
										}
									}
								}
								$primer = 1;
								foreach($mapfreSBDA["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][3]["pagos"]["pago"] as $pago){
									foreach($pago["montospaquetes"]["montos"] as $monto){
										if($monto["cod_paquete"]=="207"){
											if($primer==1)
												$recotizacionSBDA->mensual_primer = $monto["monto"];
											else
												$recotizacionSBDA->mensual_posterior = $monto["monto"];
											$primer = -1;
										}
									}
								}
								$recotizacionSBDA->save();
								
								$mapfreSBDB = $this->mapfreRecotizacion($mapfreIntegrantes, $estado, $mapfreCotizacion["cotizacion"], "sbdb", 1);
								$recotizacionSBDB = new RecotizacionMapfre();
								$recotizacionSBDB->id_cotizacion = $cotizacionDatos->id_cotizacion;
								$recotizacionSBDB->tipo = "sbdb";
								$recotizacionSBDB->hospitales = "esencial";
								$recotizacionSBDB->xml = $mapfreSBDB["xml"];
								$recotizacionSBDB->respuesta = json_encode($mapfreSBDB["cotizacion"]);
								foreach($mapfreSBDB["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][0]["pagos"]["pago"]["montospaquetes"]["montos"] as $monto){
									if($monto["cod_paquete"]=="207"){
										$recotizacionSBDB->contado = $monto["monto"];
									}
								}
								$primer = 1;
								foreach($mapfreSBDB["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][1]["pagos"]["pago"] as $pago){
									foreach($pago["montospaquetes"]["montos"] as $monto){
										if($monto["cod_paquete"]=="207"){
											if($primer==1)
												$recotizacionSBDB->semestral_primer = $monto["monto"];
											else
												$recotizacionSBDB->semestral_posterior = $monto["monto"];
											$primer = -1;
										}
									}
								}
								$primer = 1;
								foreach($mapfreSBDB["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][2]["pagos"]["pago"] as $pago){
									foreach($pago["montospaquetes"]["montos"] as $monto){
										if($monto["cod_paquete"]=="207"){
											if($primer==1)
												$recotizacionSBDB->trimestral_primer = $monto["monto"];
											else
												$recotizacionSBDB->trimestral_posterior = $monto["monto"];
											$primer = -1;
										}
									}
								}
								$primer = 1;
								foreach($mapfreSBDB["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][3]["pagos"]["pago"] as $pago){
									foreach($pago["montospaquetes"]["montos"] as $monto){
										if($monto["cod_paquete"]=="207"){
											if($primer==1)
												$recotizacionSBDB->mensual_primer = $monto["monto"];
											else
												$recotizacionSBDB->mensual_posterior = $monto["monto"];
											$primer = -1;
										}
									}
								}
								$recotizacionSBDB->save();
								
								$mapfreSBDB = $this->mapfreRecotizacion($mapfreIntegrantes, $estado, $mapfreCotizacion["cotizacion"], "sbdb", 2);
								$recotizacionSBDB = new RecotizacionMapfre();
								$recotizacionSBDB->id_cotizacion = $cotizacionDatos->id_cotizacion;
								$recotizacionSBDB->tipo = "sbdb";
								$recotizacionSBDB->hospitales = "optima";
								$recotizacionSBDB->xml = $mapfreSBDB["xml"];
								$recotizacionSBDB->respuesta = json_encode($mapfreSBDB["cotizacion"]);
								foreach($mapfreSBDB["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][0]["pagos"]["pago"]["montospaquetes"]["montos"] as $monto){
									if($monto["cod_paquete"]=="207"){
										$recotizacionSBDB->contado = $monto["monto"];
									}
								}
								$primer = 1;
								foreach($mapfreSBDB["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][1]["pagos"]["pago"] as $pago){
									foreach($pago["montospaquetes"]["montos"] as $monto){
										if($monto["cod_paquete"]=="207"){
											if($primer==1)
												$recotizacionSBDB->semestral_primer = $monto["monto"];
											else
												$recotizacionSBDB->semestral_posterior = $monto["monto"];
											$primer = -1;
										}
									}
								}
								$primer = 1;
								foreach($mapfreSBDB["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][2]["pagos"]["pago"] as $pago){
									foreach($pago["montospaquetes"]["montos"] as $monto){
										if($monto["cod_paquete"]=="207"){
											if($primer==1)
												$recotizacionSBDB->trimestral_primer = $monto["monto"];
											else
												$recotizacionSBDB->trimestral_posterior = $monto["monto"];
											$primer = -1;
										}
									}
								}
								$primer = 1;
								foreach($mapfreSBDB["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][3]["pagos"]["pago"] as $pago){
									foreach($pago["montospaquetes"]["montos"] as $monto){
										if($monto["cod_paquete"]=="207"){
											if($primer==1)
												$recotizacionSBDB->mensual_primer = $monto["monto"];
											else
												$recotizacionSBDB->mensual_posterior = $monto["monto"];
											$primer = -1;
										}
									}
								}
								$recotizacionSBDB->save();*/
							
								$respuesta["status"] = "success";
							}
							catch(Exception $e){
				            	$respuesta["status"] = "invalid";
								//dd($e->getMessage());
				            	$respuesta["mensaje"] = $e->getMessage();
				            }
							/* Termina codigo para cotizar Mapfre en WebService */
							
							/* Se deshabilita temporalmente el envio del correo
							$pdf = self::generarCotizacionPdfOrigen($cotizacionDatos, false, $sa, $ded);
							//if(file_exists($pdf)){
								$encabezado = Cotizaciontexto::where('texto_seccion', '=', 'texto_correo_encabezado')->where('id_dominio', '=', $cotizacionDatos->id_dominio)->first();
								$cuerpo = Cotizaciontexto::where('texto_seccion', '=', 'texto_correo_cuerpo')->where('id_dominio', '=', $cotizacionDatos->id_dominio)->first();
								$pie = Cotizaciontexto::where('texto_seccion', '=', 'texto_correo_pie')->where('id_dominio', '=', $cotizacionDatos->id_dominio)->where('id_usuario', '=', '-1')->first();
								$cotizacionDatos->pdf = $pdf;
								$datosPlantilla = array(
										'nombre' => $cotizacionDatos->nombre,
										'e_mail' => $cotizacionDatos->e_mail,
										'id_cotizacion' => $cotizacionDatos->id_cotizacion,
										'secret' => $cotizacionDatos->secret,
										'encabezado' => $encabezado->texto_pdf,
										'cuerpo' => $cuerpo->texto_pdf,
										'pie' => $pie->texto_pdf
									);
								try{
					                Mail::send('plantillas.correo.enviarCotizacionCorreo', $datosPlantilla, function($message) use ($cotizacionDatos){
					                	//$message->from('info@segurodegastosmedicosmayores.mx', 'Seguro de Gastos Médicos Mayores');
					                	$message->from($cotizacionDatos->dominio()->first()->email, $cotizacionDatos->dominio()->first()->sender);
					                    $message->to($cotizacionDatos->e_mail, $cotizacionDatos->nombre);
					                    $message->cc('info@segurodegastosmedicosmayores.mx', 'Cotizaciones');
					                    $message->subject('Cotización de Gastos Médicos Mayores - '.$cotizacionDatos->id_cotizacion);
					                    if(file_exists($cotizacionDatos->pdf)){
					                        $message->attach($cotizacionDatos->pdf, ['as' => 'Cotizacion_PDF:'.$cotizacionDatos->id_cotizacion.'.pdf']);
					                    }
					                });
					                $log = new Logsistema;
					                $log->cotizacion_id = $cotizacionDatos->id_cotizacion;
					                $log->error = 'Cotizacion enviada: '.$cotizacionDatos->id_cotizacion;
					                $log->tipo = 'correo_enviado';
					                $log->controlador = 'CotizadorController';
					                $log->metodo = 'miniNuevaCotizacionOrigen';
					                $log->save();
					            }catch(Exception $e){
					            	$respuesta["status"] = "warning";
									//dd($e->getMessage());
					            	$respuesta["mensaje"] .= 'Ocurrio un error al tratar de enviar la cotización a su Correo';
					            	$log = new Logsistema;
					            	$log->cotizacion_id = $cotizacionDatos->id_cotizacion;
					            	$log->error = $e->getMessage();
					            	$log->tipo = 'enviar_correo';
					            	$log->controlador = 'CotizadorController';
					            	$log->metodo = 'miniNuevaCotizacionOrigen';
					            	$log->save();
					            }
					            $respuesta["mensaje"] .= 'Cotización enviada correctamente a su Correo.';
								@unlink($pdf);
							//}*/
							
							/* Se cancela este codigo, se reenvia a pagina de ver-cotizacion
							// Inicia codigo de verCotizacion
							$cotizacionDatos = Cotizacion::find($cotizacionDatos->id_cotizacion);
							if($cotizacionDatos){
								if($cotizacionDatos->visto == -1){
									$cotizacionDatos->visto = 1;
									$cotizacionDatos->save();
								}
								$cotizacionDatos->integrantes = json_decode($cotizacionDatos->integrantes);
								$sumaAsegurada = 'sb';
								$dedubicle = 'db';
								$tabDefault = 'sa_db';
								foreach($cotizacionDatos->integrantes AS $integrante){
									if($integrante->edad >= 50){
										$sumaAsegurada = 'sa';
										$dedubicle = 'da';
										$tabDefault = 'sa_da';
									}
								}
								//View::share('tabDefault', $tabDefault);
								$respuesta["tabDefault"] = $tabDefault;
								//View::share('cotizacionDatos', $cotizacionDatos);
								$respuesta["cotizacionDatos"] = $cotizacionDatos;
								//$cotizacion = new Cotizador($cotizacionDatos, $sumaAsegurada, $dedubicle);
								$cotizacion = new Cotizador($cotizacionDatos, 'sa', 'db');
								$cotizacion::cotizar();
								$tablaDatos['sa_db'] = array('titulo' => 'Plan 1', 'nombre' => 'Suma Asegurada Alta - Deducible Bajo', 'tabla' => $cotizacion::tablaDatos(), 'datos' => $cotizacion::datosTabla(), 's' => 'sa', 'd' => 'db');
								
								$cotizacion = new Cotizador($cotizacionDatos, 'sa', 'da');
								$cotizacion::cotizar();
								$tablaDatos['sa_da'] = array('titulo' => 'Plan 2', 'nombre' => 'Suma Asegurada Alta - Deducible Alto', 'tabla' => $cotizacion::tablaDatos(), 'datos' => $cotizacion::datosTabla(), 's' => 'sa', 'd' => 'da');
								
								$cotizacion = new Cotizador($cotizacionDatos, 'sb', 'db');
								$cotizacion::cotizar();
								$tablaDatos['sb_db'] = array('titulo' => 'Plan 3', 'nombre' => 'Suma Asegurada Baja - Deducible Bajo', 'tabla' => $cotizacion::tablaDatos(), 'datos' => $cotizacion::datosTabla(), 's' => 'sb', 'd' => 'db');
								
								$cotizacion = new Cotizador($cotizacionDatos, 'sb', 'da');
								$cotizacion::cotizar();
								$tablaDatos['sb_da'] = array('titulo' => 'Plan 4', 'nombre' => 'Suma Asegurada Baja - Deducible Alto', 'tabla' => $cotizacion::tablaDatos(), 'datos' => $cotizacion::datosTabla(), 's' => 'sb', 'd' => 'da');

								//View::share('tablaDatos', $tablaDatos);
								$respuesta["tablaDatos"] = $tablaDatos;
								$tablaClienteDatos = $cotizacion::tablaClienteDatos();
								//View::share('tablaClienteDatos', $tablaClienteDatos);
								$respuesta["tablaClienteDatos"] = $tablaClienteDatos;
								$tablaIntegrantes = $cotizacion::tablaIntegrantes();
								//View::share('tablaIntegrantes', $tablaIntegrantes);
								$respuesta["tablaIntegrantes"] =$tablaIntegrantes;

								//$this->layout->content = View::make('cotizacion.verCotizacion');
								
								$respuesta["status"] = "success";
							}else{
								//return Redirect::to('/cotizador');
								$respuesta["mensaje"] = "Error de redirect a /cotizador";
							}*/
							// Termina codigo de verCotizacion
						}
						
					}
					else{
						$respuesta['mensaje'] = 'Lo sentimos, su cuenta de correo electrónico ha superado el número de cotizaciones al semestre. Si necesita una cotización en este momento comuníquese con nosotros a los teléfonos en Guadalajara: (33) 200-201-70 y con gusto le atenderemos. Si eres empresa comunícate con nosotros para darte un precio especial.';
					}
				}
				else{
					$respuesta['mensaje'] = 'Lo sentimos, su cuenta de correo electrónico se encuentra bloqueado. En caso de no serlo, por favor comuníquese con nosotros a los teléfonos: (33) 200-201-70 para realizar la cotización.';
				}
				
			}
		}
		return json_encode($respuesta);
	}

	public function cotizarWS($idCotizacion = -1, $secret = ''){
		$cotizacionDatos = Cotizacion::find($idCotizacion);
		if($cotizacionDatos){
			if($cotizacionDatos->secret == $secret){
				$integrantes = json_decode($cotizacionDatos->integrantes);
				$estado = Estado::where('clave', '=', $cotizacionDatos->estado)->first();
				foreach($integrantes as $i){
					$parentesco = Parentesco::where('parentesco', '=', $i->titulo)->first();
					if($i->sexo=="m"){
						$sexo = "Masculino";
						$idSexo = 1;
					}
					else{
						$sexo = "Femenino";
						$idSexo = 0;
					}
					$mapfreIntegrantes[] = array(
						"nombre" => $i->nombre,
						"id_parentesco" => $parentesco->id_parentesco,
						"parentesco" => $parentesco->parentesco,
						"id_sexo" => $idSexo,
						"sexo" => $sexo,
						"edad" => $i->edad
					);
				}
				$mapfreCotizacion = $this->mapfreCotizacion($mapfreIntegrantes, $estado);
				print_r($mapfreCotizacion);
				$cotizacionDatos->mapfre_numero = $mapfreCotizacion["cotizacion"]["xml"]["num_solicitud"];
				$cotizacionDatos->mapfre_xml = $mapfreCotizacion["xml"];
				$cotizacionDatos->mapfre_respuesta = json_encode($mapfreCotizacion["cotizacion"]);
				$cotizacionDatos->save();
			}
		}
		return json_encode(array());
	}
	
	public function cotizacionWS($idCotizacion = -1, $secret = ''){
		// Inicia codigo de verCotizacion
		$cotizacionDatos = Cotizacion::find($idCotizacion);
		if($cotizacionDatos){
			if($cotizacionDatos->secret == $secret){
				if($cotizacionDatos->visto == -1){
					$cotizacionDatos->visto = 1;
					$cotizacionDatos->save();
				}
				$cotizacionDatos->integrantes = json_decode($cotizacionDatos->integrantes);
				$sumaAsegurada = 'sb';
				$dedubicle = 'db';
				$tabDefault = 'sa_db';
				foreach($cotizacionDatos->integrantes AS $integrante){
					if($integrante->edad >= 60 && $integrante->edad <= 64)
						$tabDefault = "sb_da";
					if($integrante->edad >= 50 && $integrante->edad <= 59)
						$tabDefault = "sa_da";
				}
				View::share('tabDefault', $tabDefault);
				//$respuesta["tabDefault"] = $tabDefault;
				View::share('cotizacionDatos', $cotizacionDatos);
				//$respuesta["cotizacionDatos"] = $cotizacionDatos;
				//$cotizacion = new Cotizador($cotizacionDatos, $sumaAsegurada, $dedubicle);
				$cotizacion = new Cotizador($cotizacionDatos, 'sa', 'db');
				$cotizacion::cotizarWS();
				$tablaDatos['sa_db'] = array('titulo' => 'Plan 1', 'nombre' => 'Suma Asegurada Alta - Deducible Bajo', 'tabla' => $cotizacion::tablaDatos(), 'datos' => $cotizacion::datosTablaWS(), 's' => 'sa', 'd' => 'db');
				
				$cotizacion = new Cotizador($cotizacionDatos, 'sa', 'da');
				$cotizacion::cotizarWS();
				$tablaDatos['sa_da'] = array('titulo' => 'Plan 2', 'nombre' => 'Suma Asegurada Alta - Deducible Alto', 'tabla' => $cotizacion::tablaDatos(), 'datos' => $cotizacion::datosTablaWS(), 's' => 'sa', 'd' => 'da');
				
				$cotizacion = new Cotizador($cotizacionDatos, 'sb', 'db');
				$cotizacion::cotizarWS();
				$tablaDatos['sb_db'] = array('titulo' => 'Plan 3', 'nombre' => 'Suma Asegurada Baja - Deducible Bajo', 'tabla' => $cotizacion::tablaDatos(), 'datos' => $cotizacion::datosTablaWS(), 's' => 'sb', 'd' => 'db');
				
				$cotizacion = new Cotizador($cotizacionDatos, 'sb', 'da');
				$cotizacion::cotizarWS();
				$tablaDatos['sb_da'] = array('titulo' => 'Plan 4', 'nombre' => 'Suma Asegurada Baja - Deducible Alto', 'tabla' => $cotizacion::tablaDatos(), 'datos' => $cotizacion::datosTablaWS(), 's' => 'sb', 'd' => 'da');

				View::share('tablaDatos', $tablaDatos);
				//$respuesta["tablaDatos"] = $tablaDatos;
				$tablaClienteDatos = $cotizacion::tablaClienteDatos();
				View::share('tablaClienteDatos', $tablaClienteDatos);
				//$respuesta["tablaClienteDatos"] = $tablaClienteDatos;
				$tablaIntegrantes = $cotizacion::tablaIntegrantes();
				View::share('tablaIntegrantes', $tablaIntegrantes);
				//$respuesta["tablaIntegrantes"] =$tablaIntegrantes;
				
				$textoCEncabezado = "";
				$textoCPie = "";
				$encabezado = \DB::table('texto_pdf')->where('texto_seccion', 'cotizacion_encabezado')->where('id_dominio', $cotizacionDatos->id_dominio)->get();
				$pie = \DB::table('texto_pdf')->where('texto_seccion', 'cotizacion_pie')->where('id_dominio', $cotizacionDatos->id_dominio)->get();
				if($encabezado)
					$textoCEncabezado = $encabezado[0]->texto_pdf;
				if($pie)
					$textoCPie = $pie[0]->texto_pdf;
				View::share('cotizacionEncabezado', $textoCEncabezado);
				View::share('cotizacionPie', $textoCPie);
				
				View::Share('idCotizacion', $idCotizacion);
				View::Share('secret', $secret);
				
				//$this->layout->content = View::make('blog.verPortoCotizacion');
				return View::make('blog.verPortoCotizacion');
				
				//$respuesta["status"] = "success";
			}
			else{
				return Redirect::to('/');
			}
		}else{
			return Redirect::to('/cotizador');
			//$respuesta["mensaje"] = "Error de redirect a /cotizador";
		}
	}

	public function recotizarWS($idCotizacion = -1, $secret = ''){
		$respuesta = array(
			"status" => 400,
			"error" => "No existe la cotizacion solicitada",
			"contado" => 0,
			"semestral_1" => 0,
			"semestral_2" => 0,
			"trimestral_1" => 0,
			"trimestral_2" => 0,
			"mensual_1" => 0,
			"mensual_2" => 0
		);
		$tipo = Input::get('tipo');
		$hospitales = Input::get('hospitales');
		switch($hospitales){
			case "esencial":
				$idHospitales = 1;
				break;
			case "optima":
				$idHospitales = 2;
				break;
		}
		$cotizacionDatos = Cotizacion::find($idCotizacion);
		if($cotizacionDatos){
			if($cotizacionDatos->secret == $secret){
				while(true){
					if(is_null($cotizacionDatos->mapfre_numero)){
						sleep(5);
						$cotizacionDatos = Cotizacion::find($idCotizacion);
					}
					else
						break;
				}
				$recotizacion = RecotizacionMapfre::where('id_cotizacion', '=', $idCotizacion)
					->where('tipo', '=', $tipo)
					->where('hospitales', '=', $hospitales)
					->first();
				if(!$recotizacion){
					$integrantes = json_decode($cotizacionDatos->integrantes);
					$estado = Estado::where('clave', '=', $cotizacionDatos->estado)->first();
					foreach($integrantes as $i){
						$parentesco = Parentesco::where('parentesco', '=', $i->titulo)->first();
						if($i->sexo=="m"){
							$sexo = "Masculino";
							$idSexo = 1;
						}
						else{
							$sexo = "Femenino";
							$idSexo = 0;
						}
						$mapfreIntegrantes[] = array(
							"nombre" => $i->nombre,
							"id_parentesco" => $parentesco->id_parentesco,
							"parentesco" => $parentesco->parentesco,
							"id_sexo" => $idSexo,
							"sexo" => $sexo,
							"edad" => $i->edad
						);
					}
					
					$cotizacion = json_decode($cotizacionDatos->mapfre_respuesta, true);
					$mapfre = $this->mapfreRecotizacion($mapfreIntegrantes, $estado, $cotizacion, $tipo, $idHospitales);
					$recotizacion = new RecotizacionMapfre();
					$recotizacion->id_cotizacion = $cotizacionDatos->id_cotizacion;
					$recotizacion->tipo = $tipo;
					$recotizacion->hospitales = $hospitales;
					$recotizacion->xml = $mapfre["xml"];
					$recotizacion->respuesta = json_encode($mapfre["cotizacion"]);
					foreach($mapfre["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][0]["pagos"]["pago"]["montospaquetes"]["montos"] as $monto){
						if($monto["cod_paquete"]=="207"){
							$recotizacion->contado = $monto["monto"];
						}
					}
					$primer = 1;
					foreach($mapfre["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][1]["pagos"]["pago"] as $pago){
						foreach($pago["montospaquetes"]["montos"] as $monto){
							if($monto["cod_paquete"]=="207"){
								if($primer==1)
									$recotizacion->semestral_primer = $monto["monto"];
								else
									$recotizacion->semestral_posterior = $monto["monto"];
								$primer = -1;
							}
						}
					}
					$primer = 1;
					foreach($mapfre["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][2]["pagos"]["pago"] as $pago){
						foreach($pago["montospaquetes"]["montos"] as $monto){
							if($monto["cod_paquete"]=="207"){
								if($primer==1)
									$recotizacion->trimestral_primer = $monto["monto"];
								else
									$recotizacion->trimestral_posterior = $monto["monto"];
								$primer = -1;
							}
						}
					}
					$primer = 1;
					foreach($mapfre["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][3]["pagos"]["pago"] as $pago){
						foreach($pago["montospaquetes"]["montos"] as $monto){
							if($monto["cod_paquete"]=="207"){
								if($primer==1)
									$recotizacion->mensual_primer = $monto["monto"];
								else
									$recotizacion->mensual_posterior = $monto["monto"];
								$primer = -1;
							}
						}
					}
					$recotizacion->save();
					
					if(RecotizacionMapfre::where('id_cotizacion', '=', $cotizacionDatos->id_cotizacion)
						->where('tipo', '=', $cotizacionDatos->pdf_sa . $cotizacionDatos->pdf_ded)
						->count()==2){
						
						$cotizacionDatos = Cotizacion::find($idCotizacion);
						if($cotizacionDatos){
							if($cotizacionDatos->pdf_enviado==0){
								$originalIntegrantes = $cotizacionDatos->integrantes;
								$cotizacionDatos->integrantes = json_decode($cotizacionDatos->integrantes);
								$pdf = self::generarCotizacionPdfWS($cotizacionDatos, false, $cotizacionDatos->pdf_sa, $cotizacionDatos->pdf_ded);
								
								$encabezado = Cotizaciontexto::where('texto_seccion', '=', 'texto_correo_encabezado')->where('id_dominio', '=', $cotizacionDatos->id_dominio)->first();
								$cuerpo = Cotizaciontexto::where('texto_seccion', '=', 'texto_correo_cuerpo')->where('id_dominio', '=', $cotizacionDatos->id_dominio)->first();
								$pie = Cotizaciontexto::where('texto_seccion', '=', 'texto_correo_pie')->where('id_dominio', '=', $cotizacionDatos->id_dominio)->where('id_usuario', '=', '-1')->first();
								$cotizacionDatos->pdf = $pdf;
								$datosPlantilla = array(
										'nombre' => $cotizacionDatos->nombre,
										'e_mail' => $cotizacionDatos->e_mail,
										'id_cotizacion' => $cotizacionDatos->id_cotizacion,
										'secret' => $cotizacionDatos->secret,
										'encabezado' => str_replace('{{nombre}}', $cotizacionDatos->nombre, $encabezado->texto_pdf),
										'cuerpo' => $cuerpo->texto_pdf,
										'pie' => $pie->texto_pdf
									);
								try{
					                Mail::send('plantillas.correo.enviarCotizacionCorreo', $datosPlantilla, function($message) use ($cotizacionDatos){
					                	//$message->from('info@segurodegastosmedicosmayores.mx', 'Seguro de Gastos Médicos Mayores');
					                	$message->from($cotizacionDatos->dominio()->first()->email, $cotizacionDatos->dominio()->first()->sender);
					                    $message->to($cotizacionDatos->e_mail, $cotizacionDatos->nombre);
					                    $message->cc('info@segurodegastosmedicosmayores.mx', 'Cotizaciones');
					                    $message->subject('Cotización de Gastos Médicos Mayores - '.$cotizacionDatos->id_cotizacion);
					                    if(file_exists($cotizacionDatos->pdf)){
					                        $message->attach($cotizacionDatos->pdf, ['as' => 'Cotizacion_PDF:'.$cotizacionDatos->id_cotizacion.'.pdf']);
					                    }
					                });
					                unset($cotizacionDatos->pdf);
					                $cotizacionDatos->integrantes = $originalIntegrantes;
					                $cotizacionDatos->pdf_enviado = 1;
					                $cotizacionDatos->save();
					                
					                $log = new Logsistema;
					                $log->cotizacion_id = $cotizacionDatos->id_cotizacion;
					                $log->error = 'Cotizacion enviada: '.$cotizacionDatos->id_cotizacion;
					                $log->tipo = 'correo_enviado';
					                $log->controlador = 'CotizadorController';
					                $log->metodo = 'nuevaCotizacionWS';
					                $log->save();
					            }catch(Exception $e){
					            	$respuesta["status"] = "warning";
									//dd($e->getMessage());
					            	$respuesta["mensaje"] .= 'Ocurrio un error al tratar de enviar la cotización a su Correo';
					            	$log = new Logsistema;
					            	$log->cotizacion_id = $cotizacionDatos->id_cotizacion;
					            	$log->error = $e->getMessage();
					            	$log->tipo = 'enviar_correo';
					            	$log->controlador = 'CotizadorController';
					            	$log->metodo = 'nuevaCotizacionWS';
					            	$log->save();
					            }
					            @unlink($pdf);
							}
						}
					}
				}
				
				$respuesta["status"] = 200;
				$respuesta["contado"] = number_format($recotizacion->contado, 2);
				$respuesta["semestral_1"] = number_format($recotizacion->semestral_primer, 2);
				$respuesta["semestral_2"] = number_format($recotizacion->semestral_posterior, 2);
				$respuesta["trimestral_1"] = number_format($recotizacion->trimestral_primer, 2);
				$respuesta["trimestral_2"] = number_format($recotizacion->trimestral_posterior, 2);
				$respuesta["mensual_1"] = number_format($recotizacion->mensual_primer, 2);
				$respuesta["mensual_2"] = number_format($recotizacion->mensual_posterior, 2);
				
				return json_encode($respuesta);
			}
			else
				return json_encode($respuesta);
		}
		else
			return json_encode($respuesta);
	}

	private function mapfreCotizacion($integrantes, $estado){
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
			$dependiente = $doc->createElement("dependiente");
			$el = $doc->createElement("num_riesgo", $x);
			$dependiente->appendChild($el);
			$el = $doc->createElement("id_parentesco", $i["id_parentesco"]);
			$dependiente->appendChild($el);
			$el = $doc->createElement("nombre", $i["nombre"]);
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
		
		$ch = curl_init($this->wsUrl . "Zonaliados.Negocio/Api/_AYESalud/Cotiza?ramo=288");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		$res = curl_exec($ch);

		$cotizacion = json_decode($res, true);
		return array("cotizacion" => $cotizacion, "xml" => $xml);
	}

	private function mapfreRecotizacion($integrantes, $estado, $cotizacion, $tipo, $hospitales){
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
					default:
						$suma_aseg = $doc->createElement("suma_aseg", 1);
						break;
				}
				$paquetes = $doc->createElement("paquetes");
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
			$dependiente = $doc->createElement("dependiente");
			$el = $doc->createElement("num_riesgo", $x);
			$dependiente->appendChild($el);
			$el = $doc->createElement("id_parentesco", $i["id_parentesco"]);
			$dependiente->appendChild($el);
			$el = $doc->createElement("nombre", $i["nombre"]);
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
		
		$ch = curl_init($this->wsUrl . "WebApiAARCO/api/recotiza/" . $cotizacion["xml"]["num_solicitud"]);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		$res = curl_exec($ch);
		
		$recotizacion = json_decode($res, true);
		return array("cotizacion" => $recotizacion, "xml" => $xml);
	}

	private static function generarCotizacionPdfWS($cotizacionDatos = array(), $mostrar = true, $sa = 'sa', $ded = 'db'){
		$cotizacion = new Cotizador($cotizacionDatos, $sa, $ded);
        $cotizacion::cotizarWS();
        
        $mpdf = new mPDF('', 'Letter', '', '', 20,20,20,20,5,5);
        $bienvenida = Cotizaciontexto::where('texto_seccion', '=', 'saludo_bienvenida')->where('id_dominio', '=', $cotizacionDatos->id_dominio)->first()->texto_pdf;
        View::share('bienvenida', $bienvenida);
        $beneficios = Cotizaciontexto::where('texto_seccion', '=', 'beneficios_protecto')->where('id_dominio', '=', $cotizacionDatos->id_dominio)->first()->texto_pdf;
        View::share('beneficios', $beneficios);
        View::share('cotizacionDatos', $cotizacionDatos);
        View::share('cotizacion', $cotizacion);
        if($cotizacionDatos->estado=="Jalisco")
        	$aseguradoras = Aseguradora::where('activa', '=', 1)->orderBy('orden')->get();
        else
        	$aseguradoras = Aseguradora::where('activa', '=', 1)->where('aseguradora', '=', 'mapfre')->orderBy('orden')->get();
        View::share('aseguradoras', $aseguradoras);
        $html = View::make('plantillas.correo.cotizacionPdfWS');
        $mpdf->WriteHTML($html);
        if($mostrar == true){
            $mpdf->Output($file_name, 'I');
        }else{
            if(!is_dir('tmp')){
                mkdir('tmp');
            }
            $ruta = 'tmp/cotizacion_'.$cotizacionDatos->id_cotizacion.'_'.$sa.'_'.$ded.'.pdf';
            $mpdf->Output($ruta,'F');
            return $ruta;
        }
	}
}
