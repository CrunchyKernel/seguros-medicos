<?php

use Knp\Snappy\Pdf;
use Carbon\Carbon;

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
                                    'nombre' => utf8_decode($cotizacionDatos->nombre),
                                    'e_mail' => $cotizacionDatos->e_mail,
                                    'id_cotizacion' => $cotizacionDatos->id_cotizacion,
                                    'secret' => $cotizacionDatos->secret,
                                    'encabezado' => $encabezado->texto_pdf,
                                    'cuerpo' => $cuerpo->texto_pdf,
                                    'pie' =>  $pie->texto_pdf
                                );
                try{
                    /*Mail::send('plantillas.correo.enviarCotizacionCorreo', $datosPlantilla, function($message) use ($cotizacionDatos){
                        $message->from($cotizacionDatos->dominio()->first()->email, $cotizacionDatos->dominio()->first()->sender);
                        $message->to($cotizacionDatos->e_mail, $cotizacionDatos->nombre);
                        $message->cc('info@segurodegastosmedicosmayores.mx', 'Cotizaciones');
                        $message->subject('Cotización de Gastos Médicos Mayores - '.$cotizacionDatos->id_cotizacion);
                        if(file_exists($cotizacionDatos->pdf)){
                            $message->attach($cotizacionDatos->pdf, ['as' => 'cotizacion-'.$cotizacionDatos->id_cotizacion.'.pdf']);
                        }
                    });*/
                    \ALTMailer::mail('plantillas.correo.enviarCotizacionCorreo', $datosPlantilla, $cotizacionDatos, $cotizacionDatos->dominio()->first()->email, $cotizacionDatos->dominio()->first()->sender);
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
			else{
				if($cotizacionDatos->cotizar_para > 0)
					self::generarCotizacionPdfWS2023($cotizacionDatos, true);
				else
					self::generarCotizacionPdfWS($cotizacionDatos, true, $sa, $ded);
			}
		}else{
			return Redirect::to('/cotizador');
		}
	}
	
	public function verCotizacionPdfPaquetes($idCotizacion = -1, $secret = '', $sa = 'sb', $ded = 'db', $paq = ''){
		$cotizacionDatos = Cotizacion::find($idCotizacion);
		if($cotizacionDatos){
			$paquetes = explode(',', $paq);
			$cotizacionDatos->integrantes = json_decode($cotizacionDatos->integrantes);
			if(is_null($cotizacionDatos->mapfre_numero))
				self::generarCotizacionPdf($cotizacionDatos, true, $sa, $ded);
			else{
				if($cotizacionDatos->cotizar_para > 0){
					self::generarCotizacionPdfWS2023Paquetes($cotizacionDatos, true, $sa, $ded, $paquetes);
					
					
					/*$numeroPaquetes = 1;
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
					print_r($aAseguradoras);*/
				}
				else
					self::generarCotizacionPdfWS($cotizacionDatos, true, $sa, $ded);
			}
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
						$cotizacionDatos->movil = ((isset($_COOKIE["isMobile"])) ? (($_COOKIE["isMobile"]=="true") ? 1 : 0) : 0);
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
						$aName = explode(' ', $datos['nombre']);
						if(count($aName)>0)
							$cotizacionDatos->nombre_simple = ucwords($aName[0]);
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
										'nombre' => utf8_decode($cotizacionDatos->nombre),
										'e_mail' => $cotizacionDatos->e_mail,
										'id_cotizacion' => $cotizacionDatos->id_cotizacion,
										'secret' => $cotizacionDatos->secret,
										'encabezado' => $encabezado->texto_pdf,
										'cuerpo' => $cuerpo->texto_pdf,
										'pie' => $pie->texto_pdf
									);
								try{
					                /*Mail::send('plantillas.correo.enviarCotizacionCorreo', $datosPlantilla, function($message) use ($cotizacionDatos){
					                	$message->from($cotizacionDatos->dominio()->first()->email, $cotizacionDatos->dominio()->first()->sender);
					                    $message->to($cotizacionDatos->e_mail, $cotizacionDatos->nombre);
					                    $message->cc('info@segurodegastosmedicosmayores.mx', 'Cotizaciones');
					                    $message->subject('Cotización de Gastos Médicos Mayores - '.$cotizacionDatos->id_cotizacion);
					                    if(file_exists($cotizacionDatos->pdf)){
					                        $message->attach($cotizacionDatos->pdf, ['as' => 'cotizacion-'.$cotizacionDatos->id_cotizacion.'.pdf']);
					                    }
					                });*/
					                \ALTMailer::mail('plantillas.correo.enviarCotizacionCorreo', $datosPlantilla, $cotizacionDatos, $cotizacionDatos->dominio()->first()->email, $cotizacionDatos->dominio()->first()->sender);
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
						$cotizacionDatos->movil = ((isset($_COOKIE["isMobile"])) ? (($_COOKIE["isMobile"]=="true") ? 1 : 0) : 0);
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
						$aName = explode(' ', $datos['nombre']);
						if(count($aName)>0)
							$cotizacionDatos->nombre_simple = ucwords($aName[0]);
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
										'nombre' => utf8_decode($cotizacionDatos->nombre),
										'e_mail' => $cotizacionDatos->e_mail,
										'id_cotizacion' => $cotizacionDatos->id_cotizacion,
										'secret' => $cotizacionDatos->secret,
										'encabezado' => $encabezado->texto_pdf,
										'cuerpo' => $cuerpo->texto_pdf,
										'pie' => $pie->texto_pdf
									);
								try{
					                /*Mail::send('plantillas.correo.enviarCotizacionCorreo', $datosPlantilla, function($message) use ($cotizacionDatos){
					                	//$message->from('info@segurodegastosmedicosmayores.mx', 'Seguro de Gastos Médicos Mayores');
					                	$message->from($cotizacionDatos->dominio()->first()->email, $cotizacionDatos->dominio()->first()->sender);
					                    $message->to($cotizacionDatos->e_mail, $cotizacionDatos->nombre);
					                    $message->cc('info@segurodegastosmedicosmayores.mx', 'Cotizaciones');
					                    $message->subject('Cotización de Gastos Médicos Mayores - '.$cotizacionDatos->id_cotizacion);
					                    if(file_exists($cotizacionDatos->pdf)){
					                        $message->attach($cotizacionDatos->pdf, ['as' => 'Cotizacion_PDF:'.$cotizacionDatos->id_cotizacion.'.pdf', 'mime' => 'pdf']);
					                    }
					                });*/
					                \ALTMailer::mail('plantillas.correo.enviarCotizacionCorreo', $datosPlantilla, $cotizacionDatos, $cotizacionDatos->dominio()->first()->email, $cotizacionDatos->dominio()->first()->sender);
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
						$cotizacionDatos->movil = ((isset($_COOKIE["isMobile"])) ? (($_COOKIE["isMobile"]=="true") ? 1 : 0) : 0);
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
						$aName = explode(' ', $datos['nombre']);
						if(count($aName)>0)
							$cotizacionDatos->nombre_simple = ucwords($aName[0]);
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
										'nombre' => utf8_decode($cotizacionDatos->nombre),
										'e_mail' => $cotizacionDatos->e_mail,
										'id_cotizacion' => $cotizacionDatos->id_cotizacion,
										'secret' => $cotizacionDatos->secret,
										'encabezado' => $encabezado->texto_pdf,
										'cuerpo' => $cuerpo->texto_pdf,
										'pie' => $pie->texto_pdf
									);
								try{
					                /*Mail::send('plantillas.correo.enviarCotizacionCorreo', $datosPlantilla, function($message) use ($cotizacionDatos){
					                	//$message->from('info@segurodegastosmedicosmayores.mx', 'Seguro de Gastos Médicos Mayores');
					                	$message->from($cotizacionDatos->dominio()->first()->email, $cotizacionDatos->dominio()->first()->sender);
					                    $message->to($cotizacionDatos->e_mail, $cotizacionDatos->nombre);
					                    $message->cc('info@segurodegastosmedicosmayores.mx', 'Cotizaciones');
					                    $message->subject('Cotización de Gastos Médicos Mayores - '.$cotizacionDatos->id_cotizacion);
					                    if(file_exists($cotizacionDatos->pdf)){
					                        $message->attach($cotizacionDatos->pdf, ['as' => 'Cotizacion_PDF:'.$cotizacionDatos->id_cotizacion.'.pdf']);
					                    }
					                });*/
					                \ALTMailer::mail('plantillas.correo.enviarCotizacionCorreo', $datosPlantilla, $cotizacionDatos, $cotizacionDatos->dominio()->first()->email, $cotizacionDatos->dominio()->first()->sender);
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
			$idCotizacion = $cotizacionDatos->id_cotizacion;
			$secret = $cotizacionDatos->secret;
			if($cotizacionDatos->cotizar_para==0){
				if(is_null($cotizacionDatos->mapfre_numero)){
					/*$integrantes = json_decode($cotizacionDatos->integrantes);
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
					$cotizacion = new Cotizador($cotizacionDatos, 'sa', 'db');
					$mapfreCotizacion = $cotizacion::mapfreCotizacion($mapfreIntegrantes, $estado);
					$cotizacionDatos->mapfre_numero = $mapfreCotizacion["cotizacion"]["xml"]["num_solicitud"];
					$cotizacionDatos->mapfre_xml = $mapfreCotizacion["xml"];
					$cotizacionDatos->mapfre_respuesta = json_encode($mapfreCotizacion["cotizacion"]);
					$cotizacionDatos->save();*/
					return Redirect::to('/cotizador/' . $uuid);
				}
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
				$cotizadorNuevo = 1;
				$cotizacion = new Cotizador($cotizacionDatos, 'sa', 'db');
				$cotizacion::cotizarWS();
				$datos = $cotizacion::datosTablaWS2023();
				
				$textoCEncabezado = "";
				$textoCAbajode = "";
				$textoCPie = "";
				$encabezado = \DB::table('texto_pdf')->where('texto_seccion', 'cotizacion_encabezado')->where('id_dominio', $cotizacionDatos->id_dominio)->get();
				$abajode = \DB::table('texto_pdf')->where('texto_seccion', 'cotizacion_abajode')->where('id_dominio', $cotizacionDatos->id_dominio)->get();
				$pie = \DB::table('texto_pdf')->where('texto_seccion', 'cotizacion_pie')->where('id_dominio', $cotizacionDatos->id_dominio)->get();
				if($encabezado)
					$textoCEncabezado = $encabezado[0]->texto_pdf;
				if($abajode)
					$textoCAbajode = $abajode[0]->texto_pdf;
				if($pie)
					$textoCPie = $pie[0]->texto_pdf;
				
				View::Share('idCotizacion', $idCotizacion);
				View::Share('secret', $secret);
				View::Share('cotizadorNuevo', $cotizadorNuevo);
				View::Share('datos', $datos);
				View::share('cotizacionEncabezado', $textoCEncabezado);
				View::share('cotizacionAbajode', $textoCAbajode);
				View::share('cotizacionPie', $textoCPie);
				
				return View::make('blog.verPortoCotizacionNuevo');
			}
			
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
		$cotizacionDatos = Cotizacion::find($idCotizacion);
		if($cotizacionDatos){
			if($cotizacionDatos->secret == $secret){
				$integrantes = count(json_decode($cotizacionDatos->integrantes));
				$datos = array(
					"idCotizacion" => $idCotizacion,
					"secret" => $secret,
					"planes" => json_encode($planes),
					"comentarios" => $comentarios,
					"por" => $por,
					"cotizacionDatos" => $cotizacionDatos,
					"integrantes" => $integrantes
				);
				
				$cotizacionDatos->estatus = 1;
				$cotizacionDatos->comentarios = $cotizacionDatos->comentarios . " -- Planes: " . $datos["planes"] . " -- Comentarios: " . $comentarios . " -- Contactar por: " . $por;
				$cotizacionDatos->save();
				 /*Mail::send('plantillas.correo.deseaCotizar', $datos, function($message) use ($cotizacionDatos){
                	 $message->from($cotizacionDatos->dominio()->first()->email, $cotizacionDatos->dominio()->first()->sender);
                    $message->to("ventas1@segurodegastosmedicosmayores.mx");
                    $message->subject('Desea Contratar Gastos Medicos ' . $cotizacionDatos->id_cotizacion);
                });*/
                \ALTMailer::mail(
                	'plantillas.correo.deseaCotizar', 
                	$datos, 
                	$cotizacionDatos, 
                	$cotizacionDatos->dominio()->first()->email, 
                	$cotizacionDatos->dominio()->first()->sender, 
                	['ventas1@segurodegastosmedicosmayores.mx'], 
                	'Desea Contratar Gasto Medicos ' . $cotizacionDatos->id_cotizacion
                );
			}
		}
		
		return "1";
	}

	public function nuevaCotizacionWS(){
		//$datos = Input::except('nombres', 'integrantes', 'sexos', 'edades');
		$datos = Input::except('total', 'titulos', 'nombres', 'sexos', 'edades', 'chkTerminos', 'email');
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
			$datos["e_mail"] = Input::get("email");
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
						$sexoDesc = "Masculino";
						$idSexo = 1;
					}
					else{
						$sexoDesc = "Femenino";
						$idSexo = 0;
					}
					$mapfreIntegrantes[] = array(
						"nombre" => $nombre[$x],
						"id_parentesco" => $parentesco->clave_mapfre,
						"parentesco" => $parentesco->parentesco,
						"id_sexo" => $idSexo,
						"sexo" => $sexoDesc,
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
						$cotizacionDatos->movil = ((isset($_COOKIE["isMobile"])) ? (($_COOKIE["isMobile"]=="true") ? 1 : 0) : 0);
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
						$aName = explode(' ', $datos['nombre']);
						if(count($aName)>0)
							$cotizacionDatos->nombre_simple = ucwords($aName[0]);
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
						"id_parentesco" => $parentesco->clave_mapfre,
						"parentesco" => $parentesco->parentesco,
						"id_sexo" => $idSexo,
						"sexo" => $sexo,
						"edad" => $i->edad
					);
				}
				$cotizacion = new Cotizador($cotizacionDatos, 'sa', 'db');
				$mapfreCotizacion = $cotizacion::mapfreCotizacion($mapfreIntegrantes, $estado);
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
				if(is_null($cotizacionDatos->mapfre_numero)){
					/*$integrantes = json_decode($cotizacionDatos->integrantes);
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
							"id_parentesco" => $parentesco->clave_mapfre,
							"parentesco" => $parentesco->parentesco,
							"id_sexo" => $idSexo,
							"sexo" => $sexo,
							"edad" => $i->edad
						);
					}
					$cotizacion = new Cotizador($cotizacionDatos, 'sa', 'db');
					$mapfreCotizacion = $cotizacion::mapfreCotizacion($mapfreIntegrantes, $estado);
					$cotizacionDatos->mapfre_numero = $mapfreCotizacion["cotizacion"]["xml"]["num_solicitud"];
					$cotizacionDatos->mapfre_xml = $mapfreCotizacion["xml"];
					$cotizacionDatos->mapfre_respuesta = json_encode($mapfreCotizacion["cotizacion"]);
					$cotizacionDatos->save();*/
					if($cotizacionDatos->nueva==0)
						return Redirect::to('/cotizador/' . $cotizacionDatos->id_cotizacion . '/' . $cotizacionDatos->secret);
				}
				if($cotizacionDatos->nueva==1){
					$cotizacionDatos->nueva = 0;
					$cotizacionDatos->save();
				}
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
		set_time_limit(0);
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
							"id_parentesco" => $parentesco->clave_mapfre,
							"parentesco" => $parentesco->parentesco,
							"id_sexo" => $idSexo,
							"sexo" => $sexo,
							"edad" => $i->edad
						);
					}
					
					$cotizacion = json_decode($cotizacionDatos->mapfre_respuesta, true);
					$cotizador = new Cotizador($cotizacionDatos, 'sa', 'db');
					$mapfre = $cotizador::mapfreRecotizacion($mapfreIntegrantes, $estado, $cotizacion, $tipo, $idHospitales);
					$recotizacion = new RecotizacionMapfre();
					$recotizacion->id_cotizacion = $cotizacionDatos->id_cotizacion;
					$recotizacion->tipo = $tipo;
					$recotizacion->hospitales = $hospitales;
					$recotizacion->xml = $mapfre["xml"];
					$recotizacion->respuesta = json_encode($mapfre["cotizacion"]);
					
					$multiPaquetes = true;
					$idPaquete = "207";
					if(isset($mapfre["cotizacion"]["xml"]["ofertaComercial"]["paquetes"]["paquete"]["cod_paquete"])){
						$idPaquete = $mapfre["cotizacion"]["xml"]["ofertaComercial"]["paquetes"]["paquete"]["cod_paquete"];
						$multiPaquetes = false;
					}
					
					if($multiPaquetes){
						foreach($mapfre["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][0]["pagos"]["pago"]["montospaquetes"]["montos"] as $monto){
							if($monto["cod_paquete"]==$idPaquete){
								$recotizacion->contado = $monto["monto"];
							}
						}
					}
					else{
						$recotizacion->contado = $mapfre["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][0]["pagos"]["pago"]["montospaquetes"]["montos"]["monto"];
					}
					$primer = 1;
					foreach($mapfre["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][1]["pagos"]["pago"] as $pago){
						if($multiPaquetes){
							foreach($pago["montospaquetes"]["montos"] as $monto){
								if($monto["cod_paquete"]==$idPaquete){
									if($primer==1)
										$recotizacion->semestral_primer = $monto["monto"];
									else
										$recotizacion->semestral_posterior = $monto["monto"];
									$primer = -1;
								}
							}
						}
						else{
							if($pago["montospaquetes"]["montos"]["cod_paquete"]==$idPaquete){
								if($primer==1)
									$recotizacion->semestral_primer = $pago["montospaquetes"]["montos"]["monto"];
								else
									$recotizacion->semestral_posterior = $pago["montospaquetes"]["montos"]["monto"];
								$primer = -1;
							}
						}
					}
					$primer = 1;
					foreach($mapfre["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][2]["pagos"]["pago"] as $pago){
						if($multiPaquetes){
							foreach($pago["montospaquetes"]["montos"] as $monto){
								if($monto["cod_paquete"]==$idPaquete){
									if($primer==1)
										$recotizacion->trimestral_primer = $monto["monto"];
									else
										$recotizacion->trimestral_posterior = $monto["monto"];
									$primer = -1;
								}
							}
						}
						else{
							if($pago["montospaquetes"]["montos"]["cod_paquete"]==$idPaquete){
								if($primer==1)
									$recotizacion->trimestral_primer = $pago["montospaquetes"]["montos"]["monto"];
								else
									$recotizacion->trimestral_posterior = $pago["montospaquetes"]["montos"]["monto"];
								$primer = -1;
							}
						}
					}
					$primer = 1;
					foreach($mapfre["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][3]["pagos"]["pago"] as $pago){
						if($multiPaquetes){
							foreach($pago["montospaquetes"]["montos"] as $monto){
								if($monto["cod_paquete"]==$idPaquete){
									if($primer==1)
										$recotizacion->mensual_primer = $monto["monto"];
									else
										$recotizacion->mensual_posterior = $monto["monto"];
									$primer = -1;
								}
							}
						}
						else{
							if($pago["montospaquetes"]["montos"]["cod_paquete"]==$idPaquete){
								if($primer==1)
									$recotizacion->mensual_primer = $pago["montospaquetes"]["montos"]["monto"];
								else
									$recotizacion->mensual_posterior = $pago["montospaquetes"]["montos"]["monto"];
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
								$cotizacionDatos->pdf_enviado = 1;
					            $cotizacionDatos->save();
								
								//$originalIntegrantes = $cotizacionDatos->integrantes;
								$cotizacionDatos->integrantes = json_decode($cotizacionDatos->integrantes);
								$pdf = self::generarCotizacionPdfWS($cotizacionDatos, false, $cotizacionDatos->pdf_sa, $cotizacionDatos->pdf_ded);
								
								$encabezado = Cotizaciontexto::where('texto_seccion', '=', 'texto_correo_encabezado')->where('id_dominio', '=', $cotizacionDatos->id_dominio)->first();
								$cuerpo = Cotizaciontexto::where('texto_seccion', '=', 'texto_correo_cuerpo')->where('id_dominio', '=', $cotizacionDatos->id_dominio)->first();
								$pie = Cotizaciontexto::where('texto_seccion', '=', 'texto_correo_pie')->where('id_dominio', '=', $cotizacionDatos->id_dominio)->where('id_usuario', '=', '-1')->first();
								$cotizacionDatos->pdf = $pdf;
								$datosPlantilla = array(
										'nombre' => utf8_decode($cotizacionDatos->nombre),
										'e_mail' => $cotizacionDatos->e_mail,
										'id_cotizacion' => $cotizacionDatos->id_cotizacion,
										'secret' => $cotizacionDatos->secret,
										'encabezado' => str_replace('{{nombre}}', utf8_decode($cotizacionDatos->nombre), $encabezado->texto_pdf),
										'cuerpo' => $cuerpo->texto_pdf,
										'pie' => $pie->texto_pdf
									);
								try{
					                /*Mail::send('plantillas.correo.enviarCotizacionCorreo', $datosPlantilla, function($message) use ($cotizacionDatos){
					                	//$message->from('info@segurodegastosmedicosmayores.mx', 'Seguro de Gastos Médicos Mayores');
					                	$message->from($cotizacionDatos->dominio()->first()->email, $cotizacionDatos->dominio()->first()->sender);
					                    $message->to($cotizacionDatos->e_mail, $cotizacionDatos->nombre);
					                    $message->cc('info@segurodegastosmedicosmayores.mx', 'Cotizaciones');
					                    $message->subject('Cotización de Gastos Médicos Mayores - '.$cotizacionDatos->id_cotizacion);
					                    if(file_exists($cotizacionDatos->pdf)){
					                        $message->attach($cotizacionDatos->pdf, ['as' => 'Cotizacion_PDF:'.$cotizacionDatos->id_cotizacion.'.pdf']);
					                    }
					                });*/
					                \ALTMailer::mail('plantillas.correo.enviarCotizacionCorreo', $datosPlantilla, $cotizacionDatos, $cotizacionDatos->dominio()->first()->email, $cotizacionDatos->dominio()->first()->sender);
					                //unset($cotizacionDatos->pdf);
					                //$cotizacionDatos->integrantes = $originalIntegrantes;
					                
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

	public function poblacionesEstado($idEstado){
		$res = array();
		$poblaciones = Poblacion::where('id_estado', '=', $idEstado)->orderBy('poblacion', 'asc')->get();
		if($poblaciones){
			foreach($poblaciones as $poblacion){
				$res[] = array(
					"id" => $poblacion->id_poblacion,
					"poblacion" => $poblacion->poblacion,
					"mapfre" => $poblacion->clave_mapfre
				);
			}
		}
		return json_encode($res);
	}

	public function nuevaCotizacionWS2023(){
		$total = Input::get('total');
		$email = Input::get('email');
		$phone = Input::get('phone');
		$estado = Estado::where('id_estado', '=', Input::get('estado'))->first();
		$poblacion = Poblacion::where('id_poblacion', '=', Input::get('ciudad'))->first();
		$cotizarPara = Input::get('cotizar');
		$hijos = 0;
		
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
        
		if($total > 0){
			$datos["e_mail"] = Input::get("email");
			$sa = 'sa';
			$ded = 'db';
			$integrantesLista = array();
			$x = 0;
			$mapfreIntegrantes = array();
			
			switch($cotizarPara){
				case "1":
					$integrantesLista[] = array(
						'titular' => 1,
						'titulo' => 'Titular',
						'nombre' => Input::get('nombre1'),
						'sexo' => ((Input::get('sexo1')=='H') ? 'm' : 'f'),
						'edad' => Input::get('edad1')
					);
					$mapfreIntegrantes[] = array(
						"nombre" => Input::get('nombre1'),
						"id_parentesco" => 1,
						"parentesco" => 'Titular',
						"id_sexo" => ((Input::get('sexo1')=='H') ? 1 : 0),
						"sexo" => ((Input::get('sexo1')=='H') ? 'Masculino' : 'Femenino'),
						"edad" => Input::get('edad1')
					);
					if(Input::get('edad1') >= 50){
						$sa = 'sa';
	                	$ded = 'da';
					}
					break;
				case "2":
					$integrantesLista[] = array(
						'titular' => 1,
						'titulo' => 'Titular',
						'nombre' => Input::get('nombre2-1'),
						'sexo' => ((Input::get('sexo2-1')=='H') ? 'm' : 'f'),
						'edad' => Input::get('edad2-1')
					);
					$mapfreIntegrantes[] = array(
						"nombre" => Input::get('nombre2-1'),
						"id_parentesco" => 1,
						"parentesco" => 'Titular',
						"id_sexo" => ((Input::get('sexo2-1')=='H') ? 1 : 0),
						"sexo" => ((Input::get('sexo2-1')=='H') ? 'Masculino' : 'Femenino'),
						"edad" => Input::get('edad2-1')
					);
					if(Input::get('edad2-1') >= 50){
						$sa = 'sa';
	                	$ded = 'da';
					}
					
					$integrantesLista[] = array(
						'titular' => 1,
						'titulo' => 'Conyugue',
						'nombre' => Input::get('nombre2-2'),
						'sexo' => ((Input::get('sexo2-2')=='H') ? 'm' : 'f'),
						'edad' => Input::get('edad2-2')
					);
					$mapfreIntegrantes[] = array(
						"nombre" => Input::get('nombre2-2'),
						"id_parentesco" => 2,
						"parentesco" => 'Conyugue',
						"id_sexo" => ((Input::get('sexo2-2')=='H') ? 1 : 0),
						"sexo" => ((Input::get('sexo2-2')=='H') ? 'Masculino' : 'Femenino'),
						"edad" => Input::get('edad2-2')
					);
					if(Input::get('edad2-2') >= 50){
						$sa = 'sa';
	                	$ded = 'da';
					}
					break;
				case "3":
					$integrantesLista[] = array(
						'titular' => 1,
						'titulo' => 'Titular',
						'nombre' => Input::get('nombre3-1'),
						'sexo' => ((Input::get('sexo3-1')=='H') ? 'm' : 'f'),
						'edad' => Input::get('edad3-1')
					);
					$mapfreIntegrantes[] = array(
						"nombre" => Input::get('nombre3-1'),
						"id_parentesco" => 1,
						"parentesco" => 'Titular',
						"id_sexo" => ((Input::get('sexo3-1')=='H') ? 1 : 0),
						"sexo" => ((Input::get('sexo3-1')=='H') ? 'Masculino' : 'Femenino'),
						"edad" => Input::get('edad3-1')
					);
					if(Input::get('edad3-1') >= 50){
						$sa = 'sa';
	                	$ded = 'da';
					}
					
					$integrantesLista[] = array(
						'titular' => 1,
						'titulo' => 'Conyugue',
						'nombre' => Input::get('nombre3-2'),
						'sexo' => ((Input::get('sexo3-2')=='H') ? 'm' : 'f'),
						'edad' => Input::get('edad3-2')
					);
					$mapfreIntegrantes[] = array(
						"nombre" => Input::get('nombre3-2'),
						"id_parentesco" => 2,
						"parentesco" => 'Conyugue',
						"id_sexo" => ((Input::get('sexo3-2')=='H') ? 1 : 0),
						"sexo" => ((Input::get('sexo3-2')=='H') ? 'Masculino' : 'Femenino'),
						"edad" => Input::get('edad3-2')
					);
					if(Input::get('edad3-2') >= 50){
						$sa = 'sa';
	                	$ded = 'da';
					}
					
					$hijos = Input::get('hijos1');
					for($x=1;$x<=Input::get('hijos1');$x++){
						$integrantesLista[] = array(
							'titular' => 1,
							'titulo' => 'Hijo(a)',
							'nombre' => Input::get('nombreHijos-' . $x),
							'sexo' => ((Input::get('sexoHijos-' . $x)=='H') ? 'm' : 'f'),
							'edad' => Input::get('edadHijos-' . $x)
						);
						$mapfreIntegrantes[] = array(
							"nombre" => Input::get('nombreHijos-' . $x),
							"id_parentesco" => 3,
							"parentesco" => 'Hijo(a)',
							"id_sexo" => ((Input::get('sexoHijos-' . $x)=='H') ? 1 : 0),
							"sexo" => ((Input::get('sexoHijos-' . $x)=='H') ? 'Masculino' : 'Femenino'),
							"edad" => Input::get('edadHijos-' . $x)
						);
					}
					break;
				case "4":
					$integrantesLista[] = array(
						'titular' => 1,
						'titulo' => 'Titular',
						'nombre' => Input::get('nombre4-2'),
						'sexo' => ((Input::get('sexo4-2')=='H') ? 'm' : 'f'),
						'edad' => Input::get('edad4-2')
					);
					$mapfreIntegrantes[] = array(
						"nombre" => Input::get('nombre4-2'),
						"id_parentesco" => 1,
						"parentesco" => 'Titular',
						"id_sexo" => ((Input::get('sexo4-2')=='H') ? 1 : 0),
						"sexo" => ((Input::get('sexo4-2')=='H') ? 'Masculino' : 'Femenino'),
						"edad" => Input::get('edad4-2')
					);
					if(Input::get('edad4-2') >= 50){
						$sa = 'sa';
	                	$ded = 'da';
					}
					
					$hijos = Input::get('hijos2');
					for($x=1;$x<=Input::get('hijos2');$x++){
						$integrantesLista[] = array(
							'titular' => 1,
							'titulo' => 'Hijo(a)',
							'nombre' => Input::get('nombreHijos-' . $x),
							'sexo' => ((Input::get('sexoHijos-' . $x)=='H') ? 'm' : 'f'),
							'edad' => Input::get('edadHijos-' . $x)
						);
						$mapfreIntegrantes[] = array(
							"nombre" => Input::get('nombreHijos-' . $x),
							"id_parentesco" => 3,
							"parentesco" => 'Hijo(a)',
							"id_sexo" => ((Input::get('sexoHijos-' . $x)=='H') ? 1 : 0),
							"sexo" => ((Input::get('sexoHijos-' . $x)=='H') ? 'Masculino' : 'Femenino'),
							"edad" => Input::get('edadHijos-' . $x)
						);
					}
					break;
				case "5":
					$integrantesLista[] = array(
						'titular' => 1,
						'titulo' => 'Titular',
						'nombre' => Input::get('nombre5-1'),
						'sexo' => ((Input::get('sexo5-1')=='H') ? 'm' : 'f'),
						'edad' => Input::get('edad5-1')
					);
					$mapfreIntegrantes[] = array(
						"nombre" => Input::get('nombre5-1'),
						"id_parentesco" => 1,
						"parentesco" => 'Titular',
						"id_sexo" => ((Input::get('sexo5-1')=='H') ? 1 : 0),
						"sexo" => ((Input::get('sexo5-1')=='H') ? 'Masculino' : 'Femenino'),
						"edad" => Input::get('edad5-1')
					);
					if(Input::get('edad5-1') >= 50){
						$sa = 'sa';
	                	$ded = 'da';
					}
					
					$hijos = Input::get('hijos3');
					for($x=1;$x<=Input::get('hijos3');$x++){
						$integrantesLista[] = array(
							'titular' => 1,
							'titulo' => 'Hijo(a)',
							'nombre' => Input::get('nombreHijos-' . $x),
							'sexo' => ((Input::get('sexoHijos-' . $x)=='H') ? 'm' : 'f'),
							'edad' => Input::get('edadHijos-' . $x)
						);
						$mapfreIntegrantes[] = array(
							"nombre" => Input::get('nombreHijos-' . $x),
							"id_parentesco" => 3,
							"parentesco" => 'Hijo(a)',
							"id_sexo" => ((Input::get('sexoHijos-' . $x)=='H') ? 1 : 0),
							"sexo" => ((Input::get('sexoHijos-' . $x)=='H') ? 'Masculino' : 'Femenino'),
							"edad" => Input::get('edadHijos-' . $x)
						);
					}
					break;
				case "6":
					$hijos = Input::get('hijos4');
					for($x=1;$x<=Input::get('hijos4');$x++){
						$integrantesLista[] = array(
							'titular' => 1,
							'titulo' => 'Hijo(a)',
							'nombre' => Input::get('nombreHijos-' . $x),
							'sexo' => ((Input::get('sexoHijos-' . $x)=='H') ? 'm' : 'f'),
							'edad' => Input::get('edadHijos-' . $x)
						);
						$mapfreIntegrantes[] = array(
							"nombre" => Input::get('nombreHijos-' . $x),
							"id_parentesco" => 3,
							"parentesco" => 'Hijo(a)',
							"id_sexo" => ((Input::get('sexoHijos-' . $x)=='H') ? 1 : 0),
							"sexo" => ((Input::get('sexoHijos-' . $x)=='H') ? 'Masculino' : 'Femenino'),
							"edad" => Input::get('edadHijos-' . $x)
						);
					}
					break;
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
						$cotizacionDatos->movil = ((isset($_COOKIE["isMobile"])) ? (($_COOKIE["isMobile"]=="true") ? 1 : 0) : 0);
						$cotizacionDatos->id_dominio = $dominio->id_dominio;
						$cotizacionDatos->integrantes = json_encode($integrantesLista);
						$cotizacionDatos->secret = str_random(15);
						$cotizacionDatos->forma_ingreso = 2;
						$referer = parse_url($_SERVER["HTTP_REFERER"]);
						if(isset($referer["path"]))
							$cotizacionDatos->ruta = $referer["path"];
						$cotizacionDatos->pdf_sa = $sa;
						$cotizacionDatos->pdf_ded = $ded;
						
						$cotizacionDatos->nombre = Input::get('nombre');
						$cotizacionDatos->e_mail = $email;
						$cotizacionDatos->telefono = $phone;
						$cotizacionDatos->estado = $estado->estado;
						if($poblacion)
							$cotizacionDatos->ciudad = $poblacion->poblacion;
						$cotizacionDatos->comentarios = Input::get('comentarios');
						$cotizacionDatos->nueva = 1;
						$cotizacionDatos->visto = -1;
						$cotizacionDatos->poliza_actual = ((Input::get('poliza')) ? "si" : "no");
						$cotizacionDatos->cotizar_para = $cotizarPara;
						$cotizacionDatos->no_hijos = $hijos;
						$cotizacionDatos->maternidad = ((Input::get('maternidad')) ? 1 : 0);
						$cotizacionDatos->emergencia_extranjero = ((Input::get('emergencia_extranjero')) ? 1 : 0);
						$cotizacionDatos->dental = ((Input::get('dental')) ? 1 : 0);
						$cotizacionDatos->multiregion = ((Input::get('multiregion')) ? 1 : 0);
						$cotizacionDatos->link_cotizacion = Input::get('link_cotizacion');
						$aName = explode(' ', Input::get('nombre'));
						if(count($aName)>0)
							$cotizacionDatos->nombre_simple = ucwords($aName[0]);
						
						if($cotizacionDatos->save()){
							$respuesta["idCotizacion"] = $cotizacionDatos->id_cotizacion;
							$respuesta["secret"] = $cotizacionDatos->secret;
							$respuesta["status"] = "success";
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
	
	public function testCotizacionWS2023(){
		$total = Input::get('total');
		$email = Input::get('email');
		$phone = str_replace('-', '', Input::get('telefono'));
		$estado = Estado::where('id_estado', '=', Input::get('estado'))->first();
		$poblacion = Poblacion::where('id_poblacion', '=', Input::get('ciudad'))->first();
		$cotizarPara = Input::get('cotizar');
		$hijos = 0;
		
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
        
		if($total > 0){
			$datos["e_mail"] = Input::get("email");
			$sa = 'sa';
			$ded = 'db';
			$integrantesLista = array();
			$x = 0;
			$mapfreIntegrantes = array();
			
			switch($cotizarPara){
				case "1":
					if(intval(Input::get('edad'))<18){
						$respuesta["mensaje"] = "El titular tiene que tener por lo menos 18 años";
						return json_encode($respuesta);
					}
					if(intval(Input::get('edad'))>70){
						$respuesta["mensaje"] = "La edad máxima es de 70 años";
						return json_encode($respuesta);
					}
					
					$integrantesLista[] = array(
						'titular' => 1,
						'titulo' => 'Titular',
						'nombre' => Input::get('nombre'),
						'sexo' => ((Input::get('sexo')=='H') ? 'm' : 'f'),
						'edad' => Input::get('edad')
					);
					$mapfreIntegrantes[] = array(
						"nombre" => Input::get('nombre'),
						"id_parentesco" => 1,
						"parentesco" => 'Titular',
						"id_sexo" => ((Input::get('sexo')=='H') ? 1 : 0),
						"sexo" => ((Input::get('sexo')=='H') ? 'Masculino' : 'Femenino'),
						"edad" => Input::get('edad')
					);
					if(Input::get('edad') >= 50){
						$sa = 'sa';
	                	$ded = 'da';
					}
					break;
				case "2":
					if(intval(Input::get('edad'))<18){
						$respuesta["mensaje"] = "El titular tiene que tener por lo menos 18 años";
						return json_encode($respuesta);
					}
					if(intval(Input::get('edad'))>70 || intval(Input::get('edad-2'))>70){
						$respuesta["mensaje"] = "La edad máxima es de 70 años";
						return json_encode($respuesta);
					}
					
					$integrantesLista[] = array(
						'titular' => 1,
						'titulo' => 'Titular',
						'nombre' => Input::get('nombre'),
						'sexo' => ((Input::get('sexo')=='H') ? 'm' : 'f'),
						'edad' => Input::get('edad')
					);
					$mapfreIntegrantes[] = array(
						"nombre" => Input::get('nombre'),
						"id_parentesco" => 1,
						"parentesco" => 'Titular',
						"id_sexo" => ((Input::get('sexo')=='H') ? 1 : 0),
						"sexo" => ((Input::get('sexo')=='H') ? 'Masculino' : 'Femenino'),
						"edad" => Input::get('edad')
					);
					if(Input::get('edad') >= 50){
						$sa = 'sa';
	                	$ded = 'da';
					}
					
					$integrantesLista[] = array(
						'titular' => 1,
						'titulo' => 'Conyugue',
						'nombre' => 'Conyugue',
						'sexo' => ((Input::get('sexo-2')=='H') ? 'm' : 'f'),
						'edad' => Input::get('edad-2')
					);
					$mapfreIntegrantes[] = array(
						"nombre" => 'Conyugue',
						"id_parentesco" => 2,
						"parentesco" => 'Conyugue',
						"id_sexo" => ((Input::get('sexo-2')=='H') ? 1 : 0),
						"sexo" => ((Input::get('sexo-2')=='H') ? 'Masculino' : 'Femenino'),
						"edad" => Input::get('edad-2')
					);
					if(Input::get('edad-2') >= 50){
						$sa = 'sa';
	                	$ded = 'da';
					}
					break;
				case "3":
					if(intval(Input::get('edad'))<18){
						$respuesta["mensaje"] = "El titular tiene que tener por lo menos 18 años";
						return json_encode($respuesta);
					}
					if(intval(Input::get('edad'))>70 || intval(Input::get('edad-2'))>70){
						$respuesta["mensaje"] = "La edad máxima es de 70 años";
						return json_encode($respuesta);
					}
					
					$integrantesLista[] = array(
						'titular' => 1,
						'titulo' => 'Titular',
						'nombre' => Input::get('nombre'),
						'sexo' => ((Input::get('sexo')=='H') ? 'm' : 'f'),
						'edad' => Input::get('edad')
					);
					$mapfreIntegrantes[] = array(
						"nombre" => Input::get('nombre'),
						"id_parentesco" => 1,
						"parentesco" => 'Titular',
						"id_sexo" => ((Input::get('sexo')=='H') ? 1 : 0),
						"sexo" => ((Input::get('sexo')=='H') ? 'Masculino' : 'Femenino'),
						"edad" => Input::get('edad')
					);
					if(Input::get('edad') >= 50){
						$sa = 'sa';
	                	$ded = 'da';
					}
					
					$integrantesLista[] = array(
						'titular' => 1,
						'titulo' => 'Conyugue',
						'nombre' => 'Conyugue',
						'sexo' => ((Input::get('sexo-2')=='H') ? 'm' : 'f'),
						'edad' => Input::get('edad-2')
					);
					$mapfreIntegrantes[] = array(
						"nombre" => 'Conyugue',
						"id_parentesco" => 2,
						"parentesco" => 'Conyugue',
						"id_sexo" => ((Input::get('sexo-2')=='H') ? 1 : 0),
						"sexo" => ((Input::get('sexo-2')=='H') ? 'Masculino' : 'Femenino'),
						"edad" => Input::get('edad-2')
					);
					if(Input::get('edad-2') >= 50){
						$sa = 'sa';
	                	$ded = 'da';
					}
					
					$hijos = Input::get('hijos');
					for($x=1;$x<=Input::get('hijos');$x++){
						if(intval(Input::get('edad-1-' . $x))>70){
							$respuesta["mensaje"] = "La edad máxima es de 70 años";
							return json_encode($respuesta);
						}
						
						$integrantesLista[] = array(
							'titular' => 1,
							'titulo' => 'Hijo(a)',
							'nombre' => 'Hijo(a)',
							'sexo' => ((Input::get('sexo-1-' . $x)=='H') ? 'm' : 'f'),
							'edad' => Input::get('edad-1-' . $x)
						);
						$mapfreIntegrantes[] = array(
							"nombre" => 'Hijo(a)',
							"id_parentesco" => 3,
							"parentesco" => 'Hijo(a)',
							"id_sexo" => ((Input::get('sexo-1-' . $x)=='H') ? 1 : 0),
							"sexo" => ((Input::get('sexo-1-' . $x)=='H') ? 'Masculino' : 'Femenino'),
							"edad" => Input::get('edad-1-' . $x)
						);
					}
					break;
				case "4":
					if(intval(Input::get('edad-2'))<18){
						$respuesta["mensaje"] = "El titular tiene que tener por lo menos 18 años";
						return json_encode($respuesta);
					}
					if(intval(Input::get('edad-2'))>70){
						$respuesta["mensaje"] = "La edad máxima es de 70 años";
						return json_encode($respuesta);
					}
					
					$integrantesLista[] = array(
						'titular' => 1,
						'titulo' => 'Titular',
						'nombre' => 'Titular',
						'sexo' => ((Input::get('sexo-2')=='H') ? 'm' : 'f'),
						'edad' => Input::get('edad-2')
					);
					$mapfreIntegrantes[] = array(
						"nombre" => 'Titular',
						"id_parentesco" => 1,
						"parentesco" => 'Titular',
						"id_sexo" => ((Input::get('sexo-2')=='H') ? 1 : 0),
						"sexo" => ((Input::get('sexo-2')=='H') ? 'Masculino' : 'Femenino'),
						"edad" => Input::get('edad-2')
					);
					if(Input::get('edad-2') >= 50){
						$sa = 'sa';
	                	$ded = 'da';
					}
					
					$hijos = Input::get('hijos');
					for($x=1;$x<=Input::get('hijos');$x++){
						if(intval(Input::get('edad-1-' . $x))>70){
							$respuesta["mensaje"] = "La edad máxima es de 70 años";
							return json_encode($respuesta);
						}
						
						$integrantesLista[] = array(
							'titular' => 1,
							'titulo' => 'Hijo(a)',
							'nombre' => 'Hijo(a)',
							'sexo' => ((Input::get('sexo-1-' . $x)=='H') ? 'm' : 'f'),
							'edad' => Input::get('edad-1-' . $x)
						);
						$mapfreIntegrantes[] = array(
							"nombre" => 'Hijo(a)',
							"id_parentesco" => 3,
							"parentesco" => 'Hijo(a)',
							"id_sexo" => ((Input::get('sexo-1-' . $x)=='H') ? 1 : 0),
							"sexo" => ((Input::get('sexo-1-' . $x)=='H') ? 'Masculino' : 'Femenino'),
							"edad" => Input::get('edad-1-' . $x)
						);
					}
					break;
				case "5":
					if(intval(Input::get('edad'))<18){
						$respuesta["mensaje"] = "El titular tiene que tener por lo menos 18 años";
						return json_encode($respuesta);
					}
					if(intval(Input::get('edad'))>70){
						$respuesta["mensaje"] = "La edad máxima es de 70 años";
						return json_encode($respuesta);
					}
					
					$integrantesLista[] = array(
						'titular' => 1,
						'titulo' => 'Titular',
						'nombre' => Input::get('nombre'),
						'sexo' => ((Input::get('sexo')=='H') ? 'm' : 'f'),
						'edad' => Input::get('edad')
					);
					$mapfreIntegrantes[] = array(
						"nombre" => Input::get('nombre'),
						"id_parentesco" => 1,
						"parentesco" => 'Titular',
						"id_sexo" => ((Input::get('sexo')=='H') ? 1 : 0),
						"sexo" => ((Input::get('sexo')=='H') ? 'Masculino' : 'Femenino'),
						"edad" => Input::get('edad')
					);
					if(Input::get('edad') >= 50){
						$sa = 'sa';
	                	$ded = 'da';
					}
					
					$hijos = Input::get('hijos');
					for($x=1;$x<=Input::get('hijos');$x++){
						if(intval(Input::get('edad-1-' . $x))>70){
							$respuesta["mensaje"] = "La edad máxima es de 70 años";
							return json_encode($respuesta);
						}
						
						$integrantesLista[] = array(
							'titular' => 1,
							'titulo' => 'Hijo(a)',
							'nombre' => 'Hijo(a)',
							'sexo' => ((Input::get('sexo-1-' . $x)=='H') ? 'm' : 'f'),
							'edad' => Input::get('edad-1-' . $x)
						);
						$mapfreIntegrantes[] = array(
							"nombre" => 'Hijo(a)',
							"id_parentesco" => 3,
							"parentesco" => 'Hijo(a)',
							"id_sexo" => ((Input::get('sexo-1-' . $x)=='H') ? 1 : 0),
							"sexo" => ((Input::get('sexo-1-' . $x)=='H') ? 'Masculino' : 'Femenino'),
							"edad" => Input::get('edad-1-' . $x)
						);
					}
					break;
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
						$cotizacionDatos->movil = ((isset($_COOKIE["isMobile"])) ? (($_COOKIE["isMobile"]=="true") ? 1 : 0) : 0);
						$cotizacionDatos->id_dominio = $dominio->id_dominio;
						$cotizacionDatos->integrantes = json_encode($integrantesLista);
						$cotizacionDatos->secret = str_random(15);
						$cotizacionDatos->forma_ingreso = 2;
						$referer = parse_url($_SERVER["HTTP_REFERER"]);
						if(isset($referer["path"]))
							$cotizacionDatos->ruta = $referer["path"];
						$cotizacionDatos->pdf_sa = $sa;
						$cotizacionDatos->pdf_ded = $ded;
						
						$cotizacionDatos->nombre = Input::get('nombre');
						$cotizacionDatos->e_mail = $email;
						$cotizacionDatos->telefono = $phone;
						$cotizacionDatos->estado = $estado->estado;
						if($poblacion)
							$cotizacionDatos->ciudad = $poblacion->poblacion;
						$cotizacionDatos->comentarios = Input::get('observaciones');
						$cotizacionDatos->nueva = 1;
						$cotizacionDatos->visto = -1;
						$cotizacionDatos->poliza_actual = ((Input::get('poliza')) ? "si" : "no");
						$cotizacionDatos->cotizar_para = $cotizarPara;
						$cotizacionDatos->no_hijos = $hijos;
						$cotizacionDatos->maternidad = ((Input::get('maternidad')) ? 1 : 0);
						$cotizacionDatos->emergencia_extranjero = ((Input::get('emergencia_extranjero')) ? 1 : 0);
						$cotizacionDatos->dental = ((Input::get('dental')) ? 1 : 0);
						$cotizacionDatos->multiregion = ((Input::get('multiregion')) ? 1 : 0);
						$cotizacionDatos->link_cotizacion = Input::get('link_cotizacion');
						$aName = explode(' ', Input::get('nombre'));
						if(count($aName)>0)
							$cotizacionDatos->nombre_simple = ucwords($aName[0]);
						
						if($cotizacionDatos->save()){
							$respuesta["idCotizacion"] = $cotizacionDatos->id_cotizacion;
							$respuesta["secret"] = $cotizacionDatos->secret;
							$respuesta["status"] = "success";
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
	
	public function cotizacionWS2023($idCotizacion = -1, $secret = ''){
		// Inicia codigo de verCotizacion
		$cotizacionDatos = Cotizacion::find($idCotizacion);
		if($cotizacionDatos){
			if($secret==''){
				$secret = $cotizacionDatos->secret;
				$base = BaseMapfre::first();
				$recotizaciones = RecotizacionMapfre::where('id_cotizacion', '=', $idCotizacion)
						->where('tipo', '=', 'sadb')
						->get();
				foreach($recotizaciones as $recotizacion){
					if(is_null($recotizacion->sa)){
						$recotizacion->sa = $base->sa;
						$recotizacion->deducible = $base->deducible;
						$recotizacion->coaseguro = $base->coaseguro;
						$recotizacion->tabulador = $base->tabulador;
						$recotizacion->emergencia_extranjero = $base->emergencia_extranjero;
						if($cotizacionDatos->maternidad==1)
							$recotizacion->sa_maternidad = $base->sa_maternidad;
						$recotizacion->reduccion_deducible = $base->reduccion_deducible;
						$recotizacion->dental = $base->dental;
						$recotizacion->complicaciones = $base->complicaciones;
						$recotizacion->vanguardia = $base->vanguardia;
						$recotizacion->multiregion = $base->multiregion;
						$recotizacion->preexistentes = $base->preexistentes;
						$recotizacion->catastroficas = $base->catastroficas;
						$recotizacion->funeraria = $base->funeraria;
						$recotizacion->save();
					}
				}
			}
			if($cotizacionDatos->secret == $secret){
				if(is_null($cotizacionDatos->mapfre_numero)){
					if($cotizacionDatos->nueva==0)
						return Redirect::to('/cotizador/' . $cotizacionDatos->id_cotizacion . '/' . $cotizacionDatos->secret);
				}
				if($cotizacionDatos->nueva==1){
					$cotizacionDatos->nueva = 0;
					$cotizacionDatos->save();
				}
				if($cotizacionDatos->visto == -1){
					$cotizacionDatos->visto = 1;
					$cotizacionDatos->save();
				}
				$cotizacionDatos->integrantes = json_decode($cotizacionDatos->integrantes);
				$sumaAsegurada = 'sb';
				$dedubicle = 'db';
				$tabDefault = 'sa_db';
				foreach($cotizacionDatos->integrantes AS $integrante){
					//if($integrante->edad >= 60 && $integrante->edad <= 64)
					//	$tabDefault = "sb_da";
					//if($integrante->edad >= 50 && $integrante->edad <= 59)
					//	$tabDefault = "sa_da";
					if($integrante->edad >= 50)
						$tabDefault = 'sa_da';
				}
				//View::share('tabDefault', $tabDefault);
				$respuesta["tabDefault"] = $tabDefault;
				//View::share('cotizacionDatos', $cotizacionDatos);
				$respuesta["cotizacionDatos"] = $cotizacionDatos;
				//$cotizacion = new Cotizador($cotizacionDatos, $sumaAsegurada, $dedubicle);
				
				$recotizaciones = RecotizacionMapfre::where('id_cotizacion', '=', $idCotizacion)
						->where('tipo', '=', 'sadb')
						->get();
				$mapfreRecotizaciones = [];
				foreach($recotizaciones as $recotizacion){
					$C = [];
					$C[] = array("id" => 1, "value" => $recotizacion->sa, "format" => "$" . number_format($recotizacion->sa, 0, ".", ","));
					$C[] = array("id" =>2, "value" => $recotizacion->deducible, "format" => "$" . number_format($recotizacion->deducible, 0, ".", ","));
					$C[] = array("id" =>3, "value" => $recotizacion->coaseguro, "format" => $recotizacion->coaseguro . "%");
					$C[] = array("id" =>4, "value" => $recotizacion->tope_coasegurom, "format" => "$" . number_format($recotizacion->tope_coaseguro, 0, ".", ","));
					switch($recotizacion->hospitales){
						case "esencial":
							$C[] = array("id" =>10, "value" => "C", "format" => "C");
							break;
						case "optima":
							$C[] = array("id" =>10, "value" => "B y C", "format" => "B y C");
							break;
						case "completa":
							$C[] = array("id" =>10, "value" => "A, B y C", "format" => "A, B y C");
							break;
						case "amplia":
							$C[] = array("id" =>10, "value" => "AA, A, B y C", "format" => "AA, A, B y C");
							break;
					}
					$tabulador = "Normal";
					switch($recotizacion->tabulador){
						case "C":
							$tabulador = "Básico";
							break;
						case "D":
							$tabulador = "Normal";
							break;
						case "E":
							$tabulador = "Medio";
							break;
						case "F":
							$tabulador = "Alto";
							break;
					}
					$C[] = array("id" =>16, "value" => $recotizacion->tabulador, "format" => $tabulador);
					$C[] = array("id" =>9, "value" => $recotizacion->emergencia_extranjero, "format" => "$" . number_format($recotizacion->emergencia_extranjero, 0, ".", ","));
					$C[] = array("id" =>7, "value" => $recotizacion->sa_maternidad, "format" => "$" . number_format($recotizacion->sa_maternidad, 0, ".", ","));
					$C[] = array("id" =>17, "value" => 1, "format" => "Sí");
					$C[] = array("id" =>18, "value" => $recotizacion->reduccion_deducible, "format" => (($recotizacion->reduccion_deducible==1) ? "Sí" : "No"));
					$C[] = array("id" =>19, "value" => $recotizacion->dental, "format" => ((!is_null($recotizacion->dental)) ? (($recotizacion->dental=="plata") ? "Plata" : "Oro") : "No"));
					$C[] = array("id" =>20, "value" => $recotizacion->complicaciones, "format" => (($recotizacion->complicaciones==1) ? "Sí" : "No"));
					$C[] = array("id" =>21, "value" => $recotizacion->vanguardia, "format" => (($recotizacion->vanguardia==1) ? "Sí" : "No"));
					$C[] = array("id" =>22, "value" => $recotizacion->multiregion, "format" => (($recotizacion->multiregion==1) ? "Sí" : "No"));
					$C[] = array("id" =>23, "value" => $recotizacion->preexistentes, "format" => (($recotizacion->preexistentes==1) ? "Sí" : "No"));
					$C[] = array("id" =>24, "value" => $recotizacion->catastroficas, "format" => (($recotizacion->catastroficas==1) ? "Sí" : "No"));
					$C[] = array("id" =>25, "value" => $recotizacion->funeraria, "format" => (($recotizacion->funeraria==1) ? "Sí" : "No"));
					$mapfreRecotizaciones[] = array("hospitales" => $recotizacion->hospitales, "valores" => $C);
				}
				
				switch($tabDefault){
					case "sa_db":
						$cotizacion = new Cotizador($cotizacionDatos, 'sa', 'db');
						$cotizacion::cotizarWS();
						$tablaDatos['sa_db'] = array('titulo' => 'Plan 1', 'nombre' => 'Suma Asegurada Alta - Deducible Bajo', 'tabla' => $cotizacion::tablaDatos(), 'datos' => $cotizacion::datosTablaWS2023(), 'recotizaciones' => $mapfreRecotizaciones, 's' => 'sa', 'd' => 'db');
						break;
					case "sa_da":
						$cotizacion = new Cotizador($cotizacionDatos, 'sa', 'da');
						$cotizacion::cotizarWS();
						$tablaDatos['sa_db'] = array('titulo' => 'Plan 2', 'nombre' => 'Suma Asegurada Alta - Deducible Alto', 'tabla' => $cotizacion::tablaDatos(), 'datos' => $cotizacion::datosTablaWS2023(), 'recotizaciones' => $mapfreRecotizaciones, 's' => 'sa', 'd' => 'da');
						break;
					case "sb_db":
						$cotizacion = new Cotizador($cotizacionDatos, 'sb', 'db');
						$cotizacion::cotizarWS();
						$tablaDatos['sa_db'] = array('titulo' => 'Plan 3', 'nombre' => 'Suma Asegurada Baja - Deducible Bajo', 'tabla' => $cotizacion::tablaDatos(), 'datos' => $cotizacion::datosTablaWS2023(), 'recotizaciones' => $mapfreRecotizaciones, 's' => 'sb', 'd' => 'db');
						break;
					case "sb_da":
						$cotizacion = new Cotizador($cotizacionDatos, 'sb', 'da');
						$cotizacion::cotizarWS();
						$tablaDatos['sa_db'] = array('titulo' => 'Plan 4', 'nombre' => 'Suma Asegurada Baja - Deducible Alto', 'tabla' => $cotizacion::tablaDatos(), 'datos' => $cotizacion::datosTablaWS2023(), 'recotizaciones' => $mapfreRecotizaciones, 's' => 'sb', 'd' => 'da');
						break;
				}
				
				//View::share('tablaDatos', $tablaDatos);
				$respuesta["tablaDatos"] = $tablaDatos;
				$tablaClienteDatos = $cotizacion::tablaClienteDatos();
				//View::share('tablaClienteDatos', $tablaClienteDatos);
				$respuesta["tablaClienteDatos"] = $tablaClienteDatos;
				$tablaIntegrantes = $cotizacion::tablaIntegrantes();
				//View::share('tablaIntegrantes', $tablaIntegrantes);
				$respuesta["tablaIntegrantes"] =$tablaIntegrantes;
				
				$textoCEncabezado = "";
				$textoCPie = "";
				$encabezado = \DB::table('texto_pdf')->where('texto_seccion', 'cotizacion_encabezado')->where('id_dominio', $cotizacionDatos->id_dominio)->get();
				$pie = \DB::table('texto_pdf')->where('texto_seccion', 'cotizacion_pie')->where('id_dominio', $cotizacionDatos->id_dominio)->get();
				if($encabezado)
					$textoCEncabezado = $encabezado[0]->texto_pdf;
				if($pie)
					$textoCPie = $pie[0]->texto_pdf;
				//View::share('cotizacionEncabezado', $textoCEncabezado);
				//View::share('cotizacionPie', $textoCPie);
				
				//View::Share('idCotizacion', $idCotizacion);
				//View::Share('secret', $secret);
				$respuesta["idCotizacion"] = $idCotizacion;
				$respuesta["secret"] = $secret;
				
				//$this->layout->content = View::make('blog.verPortoCotizacion');
				//return View::make('blog.verPortoCotizacion');
				
				$respuesta["status"] = "success";
				return json_encode($respuesta);
			}
			else{
				return Redirect::to('/');
			}
		}else{
			return Redirect::to('/cotizador');
			//$respuesta["mensaje"] = "Error de redirect a /cotizador";
		}
	}

	public function cotizarWS2023($idCotizacion = -1, $secret = ''){
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
						"id_parentesco" => $parentesco->clave_mapfre,
						"parentesco" => $parentesco->parentesco,
						"id_sexo" => $idSexo,
						"sexo" => $sexo,
						"edad" => $i->edad
					);
				}
				$cotizacion = new Cotizador($cotizacionDatos, 'sa', 'db');
				$mapfreCotizacion = $cotizacion::mapfreCotizacion($mapfreIntegrantes, $estado);
				if(!isset($mapfreCotizacion["cotizacion"]["xml"])){
					$cotizacionDatos->mapfre_respuesta = json_encode($mapfreCotizacion);
					$cotizacionDatos->save();
				}
				$cotizacionDatos->mapfre_numero = $mapfreCotizacion["cotizacion"]["xml"]["num_solicitud"];
				$cotizacionDatos->mapfre_xml = $mapfreCotizacion["xml"];
				$cotizacionDatos->mapfre_respuesta = json_encode($mapfreCotizacion["cotizacion"]);
				$cotizacionDatos->save();
			}
		}
		return json_encode(array());
	}

	public function recotizarWS2023($idCotizacion = -1, $secret = ''){
		set_time_limit(0);
		$respuesta = array(
			"status" => 400,
			"error" => "No existe la cotizacion solicitada",
			"contado" => 0,
			"semestral_1" => 0,
			"semestral_2" => 0,
			"trimestral_1" => 0,
			"trimestral_2" => 0,
			"mensual_1" => 0,
			"mensual_2" => 0,
			"refresh" => 0
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
			case "completa":
				$idHospitales = 3;
				break;
			case "amplia":
				$idHospitales = 4;
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
				//$recotizacion = RecotizacionMapfre::where('id_cotizacion', '=', $idCotizacion)
				//	->where('tipo', '=', $tipo)
				//	->where('hospitales', '=', $hospitales)
				//	->first();
				//if(!$recotizacion){
					
					if(Input::get('nivel_amplio')!=null){
						$cotizacionDatos->nivel_amplio = 1;
						$cotizacionDatos->save();
					}
					
					$recotizacion = RecotizacionMapfre::where('id_cotizacion', '=', $idCotizacion)
						->where('tipo', '=', $tipo)
						->where('hospitales', '=', $hospitales)
						->first();
					if(!$recotizacion){
						$baseMapfre = BaseMapfre::first();
						
						$edad = 0;
						$deducible = 15000;
						$integrantes = json_decode($cotizacionDatos->integrantes, true);
						foreach($integrantes as $i){
							if(intval($i["edad"])>$edad)
								$edad = intval($i["edad"]);
						}
						if($edad<=19)
							$deducible = $baseMapfre->deducible_19;
						elseif($edad > 19 && $edad <= 24)
							$deducible = $baseMapfre->deducible_24;
						elseif($edad > 24 && $edad <= 29)
							$deducible = $baseMapfre->deducible_29;
						elseif($edad > 29 && $edad <= 34)
							$deducible = $baseMapfre->deducible_34;
						elseif($edad > 34 && $edad <= 39)
							$deducible = $baseMapfre->deducible_39;
						elseif($edad > 39 && $edad <= 44)
							$deducible = $baseMapfre->deducible_44;
						elseif($edad > 44 && $edad <= 49)
							$deducible = $baseMapfre->deducible_49;
						elseif($edad > 49 && $edad <= 54)
							$deducible = $baseMapfre->deducible_54;
						elseif($edad > 54 && $edad <= 59)
							$deducible = $baseMapfre->deducible_59;
						elseif($edad > 59 && $edad <= 64)
							$deducible = $baseMapfre->deducible_64;
						elseif($edad > 64 && $edad <= 69)
							$deducible = $baseMapfre->deducible_69;
						
						$recotizacion = new RecotizacionMapfre();
						$recotizacion->id_cotizacion = $cotizacionDatos->id_cotizacion;
						$recotizacion->tipo = $tipo;
						$recotizacion->hospitales = $hospitales;
						$recotizacion->sa = $baseMapfre->sa;
						$recotizacion->deducible = $deducible;
						$recotizacion->coaseguro = $baseMapfre->coaseguro;
						$recotizacion->tope_coaseguro = 40000;
						$recotizacion->tabulador = $baseMapfre->tabulador;
						if($cotizacionDatos->emergencia_extranjero==1)
							$recotizacion->emergencia_extranjero = $baseMapfre->emergencia_extranjero;
						if($cotizacionDatos->maternidad==1)
							$recotizacion->sa_maternidad = $baseMapfre->sa_maternidad;
						$recotizacion->reduccion_deducible = $baseMapfre->reduccion_deducible;
						if($cotizacionDatos->dental==1)
							$recotizacion->dental = $baseMapfre->dental;
						$recotizacion->complicaciones = $baseMapfre->complicaciones;
						$recotizacion->vanguardia = $baseMapfre->vanguardia;
						$recotizacion->multiregion = $cotizacionDatos->multiregion;
						$recotizacion->preexistentes = $baseMapfre->preexistentes;
						$recotizacion->catastroficas = $baseMapfre->catastroficas;
						$recotizacion->funeraria = $baseMapfre->funeraria;
					}
					else{
						if(Input::has('sa'))
							$recotizacion->sa = Input::get('sa');
						if(Input::has('deducible'))
							$recotizacion->deducible = Input::get('deducible');
						if(Input::has('tabulador'))
							$recotizacion->tabulador = Input::get('tabulador');
						$recotizacion->emergencia_extranjero = ((Input::get('emergencia_extranjero')==1) ? 100000 : null);
						if(Input::get('sa_maternidad')!=null){
							if(Input::get('sa_maternidad')!="0")
								$recotizacion->sa_maternidad = Input::get('sa_maternidad');
							else
								$recotizacion->sa_maternidad = null;
						}
						else
							$recotizacion->sa_maternidad = null;
						if(Input::has('reduccion_deducible'))
							$recotizacion->reduccion_deducible = Input::get('reduccion_deducible');
						if(Input::get('dental')!="")
							$recotizacion->dental = Input::get('dental');
						else
							$recotizacion->dental = null;
						if(Input::has('complicaciones'))
							$recotizacion->complicaciones = Input::get('complicaciones');
						if(Input::has('vanguardia'))
							$recotizacion->vanguardia = Input::get('vanguardia');
						if(Input::has('multiregion'))
							$recotizacion->multiregion = Input::get('multiregion');
						if(Input::has('preexistentes'))
							$recotizacion->preexistentes = Input::get('preexistentes');
						if(Input::has('catastroficas'))
							$recotizacion->catastroficas = Input::get('catastroficas');
						if(Input::has('funeraria'))
							$recotizacion->funeraria = Input::get('funeraria');
						$recotizacion->completada = 0;
						$recotizacion->enviada = 0;
					}
					$recotizacion->save();
					
					$cotizador = new Cotizador($cotizacionDatos, 'sa', 'db');
					$mapfre = $cotizador::mapfreRecotizacion2023($cotizacionDatos, $recotizacion, $idHospitales);
					
					// La cotizacion es viejita, volver a cotizar en Mapfre con los mismos datos
					// Modificado por Marcelo Aguilera 2025-09-27
					if(!isset($mapfre["cotizacion"]["xml"])){
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
								"id_parentesco" => $parentesco->clave_mapfre,
								"parentesco" => $parentesco->parentesco,
								"id_sexo" => $idSexo,
								"sexo" => $sexo,
								"edad" => $i->edad
							);
						}
						$cotizacion = new Cotizador($cotizacionDatos, 'sa', 'db');
						$mapfreCotizacion = $cotizacion::mapfreCotizacion($mapfreIntegrantes, $estado);
						if(!isset($mapfreCotizacion["cotizacion"]["xml"])){
							$cotizacionDatos->mapfre_respuesta = json_encode($mapfreCotizacion);
							$cotizacionDatos->save();
						}
						$cotizacionDatos->mapfre_numero = $mapfreCotizacion["cotizacion"]["xml"]["num_solicitud"];
						$cotizacionDatos->mapfre_xml = $mapfreCotizacion["xml"];
						$cotizacionDatos->mapfre_respuesta = json_encode($mapfreCotizacion["cotizacion"]);
						$cotizacionDatos->save();
						
						$cotizador = new Cotizador($cotizacionDatos, 'sa', 'db');
						$mapfre = $cotizador::mapfreRecotizacion2023($cotizacionDatos, $recotizacion, $idHospitales);
						
						// Se vuelven a recotizar todos los paquetes de la cotizacion
						// Modificado por Marcelo Aguilera 2025-10-07
						$recotizaciones = RecotizacionMapfre::where('id_cotizacion', '=', $idCotizacion)
							->where('tipo', '=', $tipo)
							->where('hospitales', '<>', $hospitales)
							->get();
						foreach($recotizaciones as $r){
							switch($r->hospitales){
								case "esencial":
									$idH = 1;
									break;
								case "optima":
									$idH = 2;
									break;
								case "completa":
									$idH = 3;
									break;
								case "amplia":
									$idH = 4;
									break;
							}
							$cotizador::mapfreRecotizacion2023($cotizacionDatos, $r, $idH);
							$r->xml = $mapfre["xml"];
							$r->respuesta = json_encode($mapfre["cotizacion"]);
							$r->completada = 1;
							$r->updated_at = \Carbon\Carbon::now();
							$r->save();
						}
						$respuesta["refresh"] = 1;
					}
					
					$recotizacion->xml = $mapfre["xml"];
					$recotizacion->respuesta = json_encode($mapfre["cotizacion"]);
					$recotizacion->completada = 1;
					$recotizacion->updated_at = \Carbon\Carbon::now();
					$recotizacion->save();
					
					//print_r($mapfre["cotizacion"]);
					
					$multiPaquetes = true;
					$idPaquete = "207";
					if(isset($mapfre["cotizacion"]["xml"]["ofertaComercial"]["paquetes"]["paquete"]["cod_paquete"])){
						$idPaquete = $mapfre["cotizacion"]["xml"]["ofertaComercial"]["paquetes"]["paquete"]["cod_paquete"];
						$multiPaquetes = false;
					}
					
					if($multiPaquetes){
						foreach($mapfre["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][0]["pagos"]["pago"]["montospaquetes"]["montos"] as $monto){
							if($monto["cod_paquete"]==$idPaquete){
								$recotizacion->contado = round($monto["monto"]);
							}
						}
					}
					else{
						$recotizacion->contado = $mapfre["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][0]["pagos"]["pago"]["montospaquetes"]["montos"]["monto"];
					}
					$primer = 1;
					foreach($mapfre["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][1]["pagos"]["pago"] as $pago){
						if($multiPaquetes){
							foreach($pago["montospaquetes"]["montos"] as $monto){
								if($monto["cod_paquete"]==$idPaquete){
									if($primer==1)
										$recotizacion->semestral_primer = round($monto["monto"]);
									else
										$recotizacion->semestral_posterior = round($monto["monto"]);
									$primer = -1;
								}
							}
						}
						else{
							if($pago["montospaquetes"]["montos"]["cod_paquete"]==$idPaquete){
								if($primer==1)
									$recotizacion->semestral_primer = $pago["montospaquetes"]["montos"]["monto"];
								else
									$recotizacion->semestral_posterior = $pago["montospaquetes"]["montos"]["monto"];
								$primer = -1;
							}
						}
					}
					$primer = 1;
					foreach($mapfre["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][2]["pagos"]["pago"] as $pago){
						if($multiPaquetes){
							foreach($pago["montospaquetes"]["montos"] as $monto){
								if($monto["cod_paquete"]==$idPaquete){
									if($primer==1)
										$recotizacion->trimestral_primer = round($monto["monto"]);
									else
										$recotizacion->trimestral_posterior = round($monto["monto"]);
									$primer = -1;
								}
							}
						}
						else{
							if($pago["montospaquetes"]["montos"]["cod_paquete"]==$idPaquete){
								if($primer==1)
									$recotizacion->trimestral_primer = $pago["montospaquetes"]["montos"]["monto"];
								else
									$recotizacion->trimestral_posterior = $pago["montospaquetes"]["montos"]["monto"];
								$primer = -1;
							}
						}
					}
					$primer = 1;
					foreach($mapfre["cotizacion"]["xml"]["ofertaComercial"]["formasPago"]["formaPago"][3]["pagos"]["pago"] as $pago){
						if($multiPaquetes){
							foreach($pago["montospaquetes"]["montos"] as $monto){
								if($monto["cod_paquete"]==$idPaquete){
									if($primer==1)
										$recotizacion->mensual_primer = round($monto["monto"]);
									else
										$recotizacion->mensual_posterior = round($monto["monto"]);
									$primer = -1;
								}
							}
						}
						else{
							if($pago["montospaquetes"]["montos"]["cod_paquete"]==$idPaquete){
								if($primer==1)
									$recotizacion->mensual_primer = $pago["montospaquetes"]["montos"]["monto"];
								else
									$recotizacion->mensual_posterior = $pago["montospaquetes"]["montos"]["monto"];
								$primer = -1;
							}
						}
					}
					$recotizacion->save();
				//}
				
				$respuesta["status"] = 200;
				$respuesta["contado"] = "$" . number_format($recotizacion->contado, 0);
				$respuesta["semestral_1"] = "$" . number_format($recotizacion->semestral_primer, 0);
				$respuesta["semestral_2"] = "$" . number_format($recotizacion->semestral_posterior, 0);
				$respuesta["trimestral_1"] = "$" . number_format($recotizacion->trimestral_primer, 0);
				$respuesta["trimestral_2"] = "$" . number_format($recotizacion->trimestral_posterior, 0);
				$respuesta["mensual_1"] = "$" . number_format($recotizacion->mensual_primer, 0);
				$respuesta["mensual_2"] = "$" . number_format($recotizacion->mensual_posterior, 0);
				
				$doc = new DOMDocument;
				$doc->preserveWhiteSpace = false;
				$doc->loadXML(trim(rtrim($mapfre["xml"], '"'), '"'));
				$xpath = new DOMXPath($doc);
				
				/*$C = array();
				$conceptos = Paqueteconcepto::orderBy('orden')->get();
				foreach($conceptos as $concepto){
					$c = "";
					switch($concepto->id_concepto){
						case 1:
							$nodes = $xpath->query("//xml/cotizar/coberturas/cobertura[cod_cob=1]/suma_aseg");
							foreach($nodes as $node)
								$c = $node->nodeValue;
							break;
						case 2:
							$nodes = $xpath->query("//xml/cotizar/datos_var_cob/imp_deducible_2800");
							foreach($nodes as $node)
								$c = $node->nodeValue;
							break;
						case 3:
							
							break;
						case 4:
							
							break;
						case 5:
							
							break;
						case 6:
							
							break;
						case 7:
							$nodes = $xpath->query("//xml/cotizar/coberturas/cobertura[cod_cob=28]/suma_aseg");
							foreach($nodes as $node)
								$c = $node->nodeValue;
							break;
						case 8:
						
							break;
						case 9:
							$nodes = $xpath->query("//xml/cotizar/coberturas/cobertura[cod_cob=22]/suma_aseg");
							foreach($nodes as $node)
								$c = $node->nodeValue;
							break;
						case 10:
							
							break;
					}
					$C[] = array("id" => $concepto->id_concepto, "concepto" => $c);
				}*/
				
				$C = [];
				$C[] = array("id" => 1, "value" => $recotizacion->sa, "format" => "$" . number_format($recotizacion->sa, 0, ".", ","));
				$C[] = array("id" =>2, "value" => $recotizacion->deducible, "format" => "$" . number_format($recotizacion->deducible, 0, ".", ","));
				$C[] = array("id" =>3, "value" => $recotizacion->coaseguro, "format" => $recotizacion->coaseguro . "%");
				$C[] = array("id" =>4, "value" => $recotizacion->tope_coaseguro, "format" => "$" . number_format($recotizacion->tope_coaseguro, 0, ".", ","));
				switch($recotizacion->hospitales){
					case "esencial":
						$C[] = array("id" =>10, "value" => "C", "format" => "C");
						break;
					case "optima":
						$C[] = array("id" =>10, "value" => "B y C", "format" => "B y C");
						break;
					case "completa":
						$C[] = array("id" =>10, "value" => "A, B y C", "format" => "A, B y C");
						break;
					case "amplia":
						$C[] = array("id" =>10, "value" => "AA, A, B y C", "format" => "AA, A, B y C");
						break;
				}
				switch($recotizacion->tabulador){
					case "C":
						$tabulador = "Básico";
						break;
					case "D":
						$tabulador = "Normal";
						break;
					case "E":
						$tabulador = "Medio";
						break;
					case "F":
						$tabulador = "Alto";
						break;
				}
				$C[] = array("id" =>16, "value" => $recotizacion->tabulador, "format" => $tabulador);
				$C[] = array("id" =>9, "value" => $recotizacion->emergencia_extranjero, "format" => "$" . number_format($recotizacion->emergencia_extranjero, 0, ".", ","));
				$C[] = array("id" =>7, "value" => $recotizacion->sa_maternidad, "format" => "$" . number_format($recotizacion->sa_maternidad, 0, ".", ","));
				$C[] = array("id" =>17, "value" => 1, "format" => "Sí");
				$C[] = array("id" =>18, "value" => $recotizacion->reduccion_deducible, "format" => (($recotizacion->reduccion_deducible==1) ? "Sí" : "No"));
				$C[] = array("id" =>19, "value" => $recotizacion->dental, "format" => ((!is_null($recotizacion->dental)) ? (($recotizacion->dental=="plata") ? "Plata" : "Oro") : "No"));
				$C[] = array("id" =>20, "value" => $recotizacion->complicaciones, "format" => (($recotizacion->complicaciones==1) ? "Sí" : "No"));
				$C[] = array("id" =>21, "value" => $recotizacion->vanguardia, "format" => (($recotizacion->vanguardia==1) ? "Sí" : "No"));
				$C[] = array("id" =>22, "value" => $recotizacion->multiregion, "format" => (($recotizacion->multiregion==1) ? "Sí" : "No"));
				$C[] = array("id" =>23, "value" => $recotizacion->preexistentes, "format" => (($recotizacion->preexistentes==1) ? "Sí" : "No"));
				$C[] = array("id" =>24, "value" => $recotizacion->catastroficas, "format" => (($recotizacion->catastroficas==1) ? "Sí" : "No"));
				$C[] = array("id" =>25, "value" => $recotizacion->funeraria, "format" => (($recotizacion->funeraria==1) ? "Sí" : "No"));
				
				$respuesta["conceptos"] = $C;
				
				if(RecotizacionMapfre::where('id_cotizacion', '=', $cotizacionDatos->id_cotizacion)
					->where('tipo', '=', 'sadb')
					->where('completada', '=', 1)
					->count()==3){
					
					//$cotizacionDatos = Cotizacion::find($idCotizacion);
					//if($cotizacionDatos){
						if($cotizacionDatos->pdf_enviado==0){
							$cotizacionDatos->pdf_enviado = 1;
				            $cotizacionDatos->save();
							
							//$originalIntegrantes = $cotizacionDatos->integrantes;
							$cotizacionDatos->integrantes = json_decode($cotizacionDatos->integrantes);
							$pdf = self::generarCotizacionPdfWS2023($cotizacionDatos, false);
							
							$encabezado = Cotizaciontexto::where('texto_seccion', '=', 'texto_correo_encabezado')->where('id_dominio', '=', $cotizacionDatos->id_dominio)->first();
							$cuerpo = Cotizaciontexto::where('texto_seccion', '=', 'texto_correo_cuerpo')->where('id_dominio', '=', $cotizacionDatos->id_dominio)->first();
							$pie = Cotizaciontexto::where('texto_seccion', '=', 'texto_correo_pie')->where('id_dominio', '=', $cotizacionDatos->id_dominio)->where('id_usuario', '=', '-1')->first();
							$cotizacionDatos->pdf = $pdf;
							$datosPlantilla = array(
									'nombre' => utf8_decode($cotizacionDatos->nombre),
									'e_mail' => $cotizacionDatos->e_mail,
									'id_cotizacion' => $cotizacionDatos->id_cotizacion,
									'secret' => $cotizacionDatos->secret,
									'encabezado' => str_replace('{{nombre}}', utf8_decode($cotizacionDatos->nombre), $encabezado->texto_pdf),
									'cuerpo' => $cuerpo->texto_pdf,
									'pie' => $pie->texto_pdf
								);
							try{
				                /*Mail::send('plantillas.correo.enviarCotizacionCorreo', $datosPlantilla, function($message) use ($cotizacionDatos){
				                	//$message->from('info@segurodegastosmedicosmayores.mx', 'Seguro de Gastos Médicos Mayores');
				                	$message->from($cotizacionDatos->dominio()->first()->email, $cotizacionDatos->dominio()->first()->sender);
				                    $message->to($cotizacionDatos->e_mail, $cotizacionDatos->nombre);
				                    $message->cc('info@segurodegastosmedicosmayores.mx', 'Cotizaciones');
				                    $message->subject('Cotización de Gastos Médicos Mayores - '.$cotizacionDatos->id_cotizacion);
				                    if(file_exists($cotizacionDatos->pdf)){
				                        $message->attach($cotizacionDatos->pdf, ['as' => 'Cotizacion_PDF:'.$cotizacionDatos->id_cotizacion.'.pdf']);
				                    }
				                });*/
				                \ALTMailer::mail('plantillas.correo.enviarCotizacionCorreo', $datosPlantilla, $cotizacionDatos, $cotizacionDatos->dominio()->first()->email, $cotizacionDatos->dominio()->first()->sender);
				                //unset($cotizacionDatos->pdf);
				                //$cotizacionDatos->integrantes = $originalIntegrantes;
				                
				                $recotizaciones = RecotizacionMapfre::where('id_cotizacion', '=', $cotizacionDatos->id_cotizacion)->get();
				                foreach($recotizaciones as $recotizacion){
				                	$recotizacion->enviada = 1;
				                	$recotizacion->save();
				                }
				                
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
					//}
				}
				
				return json_encode($respuesta);
			}
			else
				return json_encode($respuesta);
		}
		else
			return json_encode($respuesta);
	}

	public function actualizaCotizacionWS2023($idCotizacion = -1, $secret = ''){
		$res = array(
			"status" => false
		);
		
		$cotizacionDatos = Cotizacion::find($idCotizacion);
		if($cotizacionDatos){
			if($cotizacionDatos->secret == $secret){
				// Donde te ubicas
				$estado = Estado::where('id_estado', '=', Input::get('estado'))->first();
				$poblacion = Poblacion::where('id_poblacion', '=', Input::get('ciudad'))->first();
				$cotizacionDatos->estado = $estado->estado;
				$cotizacionDatos->ciudad = $poblacion->poblacion;
				// Quiero proteccion para
				switch(Input::get('cotizar')){
					case "1":
						$integrantesLista[] = array(
							'titular' => 1,
							'titulo' => 'Titular',
							'nombre' => Input::get('nombre1'),
							'sexo' => ((Input::get('sexo1')=='H') ? 'm' : 'f'),
							'edad' => Input::get('edad1')
						);
						$mapfreIntegrantes[] = array(
							"nombre" => Input::get('nombre1'),
							"id_parentesco" => 1,
							"parentesco" => 'Titular',
							"id_sexo" => ((Input::get('sexo1')=='H') ? 1 : 0),
							"sexo" => ((Input::get('sexo1')=='H') ? 'Masculino' : 'Femenino'),
							"edad" => Input::get('edad1')
						);
						if(Input::get('edad1') >= 50){
							$sa = 'sa';
		                	$ded = 'da';
						}
						break;
					case "2":
						$integrantesLista[] = array(
							'titular' => 1,
							'titulo' => 'Titular',
							'nombre' => Input::get('nombre2-1'),
							'sexo' => ((Input::get('sexo2-1')=='H') ? 'm' : 'f'),
							'edad' => Input::get('edad2-1')
						);
						$mapfreIntegrantes[] = array(
							"nombre" => Input::get('nombre2-1'),
							"id_parentesco" => 1,
							"parentesco" => 'Titular',
							"id_sexo" => ((Input::get('sexo2-1')=='H') ? 1 : 0),
							"sexo" => ((Input::get('sexo2-1')=='H') ? 'Masculino' : 'Femenino'),
							"edad" => Input::get('edad2-1')
						);
						if(Input::get('edad2-1') >= 50){
							$sa = 'sa';
		                	$ded = 'da';
						}
						
						$integrantesLista[] = array(
							'titular' => 1,
							'titulo' => 'Conyugue',
							'nombre' => Input::get('nombre2-2'),
							'sexo' => ((Input::get('sexo2-2')=='H') ? 'm' : 'f'),
							'edad' => Input::get('edad2-2')
						);
						$mapfreIntegrantes[] = array(
							"nombre" => Input::get('nombre2-2'),
							"id_parentesco" => 2,
							"parentesco" => 'Conyugue',
							"id_sexo" => ((Input::get('sexo2-2')=='H') ? 1 : 0),
							"sexo" => ((Input::get('sexo2-2')=='H') ? 'Masculino' : 'Femenino'),
							"edad" => Input::get('edad2-2')
						);
						if(Input::get('edad2-2') >= 50){
							$sa = 'sa';
		                	$ded = 'da';
						}
						break;
					case "3":
						$integrantesLista[] = array(
							'titular' => 1,
							'titulo' => 'Titular',
							'nombre' => Input::get('nombre3-1'),
							'sexo' => ((Input::get('sexo3-1')=='H') ? 'm' : 'f'),
							'edad' => Input::get('edad3-1')
						);
						$mapfreIntegrantes[] = array(
							"nombre" => Input::get('nombre3-1'),
							"id_parentesco" => 1,
							"parentesco" => 'Titular',
							"id_sexo" => ((Input::get('sexo3-1')=='H') ? 1 : 0),
							"sexo" => ((Input::get('sexo3-1')=='H') ? 'Masculino' : 'Femenino'),
							"edad" => Input::get('edad3-1')
						);
						if(Input::get('edad3-1') >= 50){
							$sa = 'sa';
		                	$ded = 'da';
						}
						
						$integrantesLista[] = array(
							'titular' => 1,
							'titulo' => 'Conyugue',
							'nombre' => Input::get('nombre3-2'),
							'sexo' => ((Input::get('sexo3-2')=='H') ? 'm' : 'f'),
							'edad' => Input::get('edad3-2')
						);
						$mapfreIntegrantes[] = array(
							"nombre" => Input::get('nombre3-2'),
							"id_parentesco" => 2,
							"parentesco" => 'Conyugue',
							"id_sexo" => ((Input::get('sexo3-2')=='H') ? 1 : 0),
							"sexo" => ((Input::get('sexo3-2')=='H') ? 'Masculino' : 'Femenino'),
							"edad" => Input::get('edad3-2')
						);
						if(Input::get('edad3-2') >= 50){
							$sa = 'sa';
		                	$ded = 'da';
						}
						
						$hijos = Input::get('hijos1');
						for($x=1;$x<Input::get('hijos1');$x++){
							$integrantesLista[] = array(
								'titular' => 1,
								'titulo' => 'Hijo(a)',
								'nombre' => Input::get('nombreHijos-' . $x),
								'sexo' => ((Input::get('sexoHijo-' . $x)=='H') ? 'm' : 'f'),
								'edad' => Input::get('edadHijos-' . $x)
							);
							$mapfreIntegrantes[] = array(
								"nombre" => Input::get('nombreHijos-' . $x),
								"id_parentesco" => 3,
								"parentesco" => 'Hijo(a)',
								"id_sexo" => ((Input::get('sexoHijos-' . $x)=='H') ? 1 : 0),
								"sexo" => ((Input::get('sexoHijos-' . $x)=='H') ? 'Masculino' : 'Femenino'),
								"edad" => Input::get('edadHijos-' . $x)
							);
						}
						break;
					case "4":
						$integrantesLista[] = array(
							'titular' => 1,
							'titulo' => 'Titular',
							'nombre' => Input::get('nombre4-2'),
							'sexo' => ((Input::get('sexo4-2')=='H') ? 'm' : 'f'),
							'edad' => Input::get('edad4-2')
						);
						$mapfreIntegrantes[] = array(
							"nombre" => Input::get('nombre4-2'),
							"id_parentesco" => 1,
							"parentesco" => 'Titular',
							"id_sexo" => ((Input::get('sexo4-2')=='H') ? 1 : 0),
							"sexo" => ((Input::get('sexo4-2')=='H') ? 'Masculino' : 'Femenino'),
							"edad" => Input::get('edad4-2')
						);
						if(Input::get('edad4-2') >= 50){
							$sa = 'sa';
		                	$ded = 'da';
						}
						
						$hijos = Input::get('hijos2');
						for($x=1;$x<Input::get('hijos2');$x++){
							$integrantesLista[] = array(
								'titular' => 1,
								'titulo' => 'Hijo(a)',
								'nombre' => Input::get('nombreHijos-' . $x),
								'sexo' => ((Input::get('sexoHijo-' . $x)=='H') ? 'm' : 'f'),
								'edad' => Input::get('edadHijos-' . $x)
							);
							$mapfreIntegrantes[] = array(
								"nombre" => Input::get('nombreHijos-' . $x),
								"id_parentesco" => 3,
								"parentesco" => 'Hijo(a)',
								"id_sexo" => ((Input::get('sexoHijos-' . $x)=='H') ? 1 : 0),
								"sexo" => ((Input::get('sexoHijos-' . $x)=='H') ? 'Masculino' : 'Femenino'),
								"edad" => Input::get('edadHijos-' . $x)
							);
						}
						break;
					case "5":
						$integrantesLista[] = array(
							'titular' => 1,
							'titulo' => 'Titular',
							'nombre' => Input::get('nombre5-1'),
							'sexo' => ((Input::get('sexo5-1')=='H') ? 'm' : 'f'),
							'edad' => Input::get('edad5-1')
						);
						$mapfreIntegrantes[] = array(
							"nombre" => Input::get('nombre5-1'),
							"id_parentesco" => 1,
							"parentesco" => 'Titular',
							"id_sexo" => ((Input::get('sexo5-1')=='H') ? 1 : 0),
							"sexo" => ((Input::get('sexo5-1')=='H') ? 'Masculino' : 'Femenino'),
							"edad" => Input::get('edad5-1')
						);
						if(Input::get('edad5-1') >= 50){
							$sa = 'sa';
		                	$ded = 'da';
						}
						
						$hijos = Input::get('hijos3');
						for($x=1;$x<Input::get('hijos3');$x++){
							$integrantesLista[] = array(
								'titular' => 1,
								'titulo' => 'Hijo(a)',
								'nombre' => Input::get('nombreHijos-' . $x),
								'sexo' => ((Input::get('sexoHijo-' . $x)=='H') ? 'm' : 'f'),
								'edad' => Input::get('edadHijos-' . $x)
							);
							$mapfreIntegrantes[] = array(
								"nombre" => Input::get('nombreHijos-' . $x),
								"id_parentesco" => 3,
								"parentesco" => 'Hijo(a)',
								"id_sexo" => ((Input::get('sexoHijos-' . $x)=='H') ? 1 : 0),
								"sexo" => ((Input::get('sexoHijos-' . $x)=='H') ? 'Masculino' : 'Femenino'),
								"edad" => Input::get('edadHijos-' . $x)
							);
						}
						break;
					case "6":
						$hijos = Input::get('hijos4');
						for($x=1;$x<Input::get('hijos4');$x++){
							$integrantesLista[] = array(
								'titular' => 1,
								'titulo' => 'Hijo(a)',
								'nombre' => Input::get('nombreHijos-' . $x),
								'sexo' => ((Input::get('sexoHijo-' . $x)=='H') ? 'm' : 'f'),
								'edad' => Input::get('edadHijos-' . $x)
							);
							$mapfreIntegrantes[] = array(
								"nombre" => Input::get('nombreHijos-' . $x),
								"id_parentesco" => 3,
								"parentesco" => 'Hijo(a)',
								"id_sexo" => ((Input::get('sexoHijos-' . $x)=='H') ? 1 : 0),
								"sexo" => ((Input::get('sexoHijos-' . $x)=='H') ? 'Masculino' : 'Femenino'),
								"edad" => Input::get('edadHijos-' . $x)
							);
						}
						break;
				}
				// Opciones adicionales
				$cotizacionDatos->poliza_actual = ((Input::get('poliza')) ? "si" : "no");
				if(Input::get('maternidad')){
					$cotizacionDatos->maternidad = 1;
					$cotizacionDatos->sa_maternidad = Input::get('sa-maternidad');
				}
				else{
					$cotizacionDatos->maternidad = 0;
					$cotizacionDatos->sa_maternidad = null;
				}
				$cotizacionDatos->viajes = ((Input::get('viajes')) ? 1 : 0);
				if(Input::get('dental')){
					$cotizacionDatos->dental = 1;
					$cotizacionDatos->sa_dental = Input::get('sa-dental');
				}
				else{
					$cotizacionDatos->dental = 0;
					$cotizacionDatos->sa_dental = null;
				}
				$cotizacionDatos->otros_estados = ((Input::get('cambio-estado')) ? 1 : 0);
				$cotizacionDatos->reduccion_deducible = ((Input::get('reduccion')) ? 1 : 0);
				if(Input::get('tabulador')){
					$cotizacionDatos->tabulador = Input::get('sa-tabulador');
				}
				else{
					$cotizacionDatos->tabulador = null;
				}
				if(Input::get('sa')){
					$cotizacionDatos->suma_asegurada = Input::get('sa-suma');
					$cotizacionDatos->deducible = Input::get('sa-deducible');
				}
				else{
					$cotizacionDatos->suma_asegurada = null;
					$cotizacionDatos->deducible = null;
				}
				if(Input::get('amplio'))
					$cotizacionDatos->nivel_amplio = 1;
				else
					$cotizacionDatos->nivel_amplio = 0;
				// Datos de contacto
				$cotizacionDatos->nombre = Input::get('nombre');
				$cotizacionDatos->e_mail = Input::get('email');
				$cotizacionDatos->telefono = Input::get('phone');
				$cotizacionDatos->comentarios = Input::get('comentarios');
				$cotizacionDatos->save();
				
				$cotizacion = new Cotizador($cotizacionDatos, 'sa', 'db');
				$tablaDatos = array();
				$tablaDatos['sa_db'] = array(
					'titulo' => 'Plan 4', 
					'nombre' => 'Suma Asegurada Baja - Deducible Alto', 
					'tabla' => '', 
					'datos' => $cotizacion::datosTablaWSMapfre(true), 
					's' => 'sa', 
					'd' => 'db'
				);
				$res["tablaDatos"] = $tablaDatos;
				$res["status"] = "success";
			}
		}
		return json_encode($res);
	}

	public function cotizacionWS2023Local($idCotizacion = -1, $secret = ''){
		$cotizadorNuevo = 1;
		
		$cotizacionDatos = Cotizacion::find($idCotizacion);
		
		$cotizacion = new Cotizador($cotizacionDatos, 'sa', 'db');
		$cotizacion::cotizarWS();
		$datos = $cotizacion::datosTablaWS2023();
		
		$textoCEncabezado = "";
		$textoCAbajode = "";
		$textoCPie = "";
		$encabezado = \DB::table('texto_pdf')->where('texto_seccion', 'cotizacion_encabezado')->where('id_dominio', $cotizacionDatos->id_dominio)->get();
		$abajode = \DB::table('texto_pdf')->where('texto_seccion', 'cotizacion_abajode')->where('id_dominio', $cotizacionDatos->id_dominio)->get();
		$pie = \DB::table('texto_pdf')->where('texto_seccion', 'cotizacion_pie')->where('id_dominio', $cotizacionDatos->id_dominio)->get();
		if($encabezado)
			$textoCEncabezado = $encabezado[0]->texto_pdf;
		if($abajode)
			$textoCAbajode = $abajode[0]->texto_pdf;
		if($pie)
			$textoCPie = $pie[0]->texto_pdf;
		
		View::Share('idCotizacion', $idCotizacion);
		View::Share('secret', $secret);
		View::Share('cotizadorNuevo', $cotizadorNuevo);
		View::Share('datos', $datos);
		View::share('cotizacionEncabezado', $textoCEncabezado);
		View::share('cotizacionAbajode', $textoCAbajode);
		View::share('cotizacionPie', $textoCPie);
		
		return View::make('blog.verPortoCotizacionNuevo');
	}

	private static function generarCotizacionPdfWS2023($cotizacionDatos = array(), $mostrar = true, $sa = 'sa', $ded = 'db', $paquetes = array()){
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
        View::share('paquetes', $paquetes);
        $html = View::make('plantillas.correo.cotizacionPdfWS2023');
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
	
	private static function generarCotizacionPdfWS2023Paquetes($cotizacionDatos = array(), $mostrar = true, $sa = 'sa', $ded = 'db', $paquetes = array()){
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
        View::share('paquetes', $paquetes);
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
		View::share('aAseguradoras', $aAseguradoras);
        $html = View::make('plantillas.correo.cotizacionPdfWS2023');
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
	
	public function correoPlantilla($idCotizacion){
		$ded = "db";
		$cotizacionDatos = Cotizacion::find($idCotizacion);
		
		$integrantes = json_decode($cotizacionDatos->integrantes);
		foreach($integrantes as $i){
			if($i->edad >= 50)
				$ded = $da;
		}
		
		$cotizacion = new Cotizador($cotizacionDatos, "sa", $ded);
        $cotizacion::cotizarWS();
        
        //$mpdf = new mPDF('', 'Letter', '', '', 20,20,20,20,5,5);
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
        /*$html = View::make('plantillas.correo.cotizacionPdfWS2023');
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
        }*/
        return View::make('plantillas.correo.cotizacionPdfWS2023');
	}

	public function cronCotizacionesMailer(){
		$recotizaciones = RecotizacionMapfre::select('id_cotizacion')
							->whereDate('updated_at', '<=', \Carbon\Carbon::now()->subMinutes(60))
							->where(function($query){
								$query->where('enviada', '=', 0)
										->orWhereNull('enviada');
							})
							->distinct()
							->get();
		foreach($recotizaciones as $recotizacion){
			$cotizacionDatos = Cotizacion::find($recotizacion->id_cotizacion);
			$cotizacionDatos->integrantes = json_decode($cotizacionDatos->integrantes);
			
			$encabezado = Cotizaciontexto::where('texto_seccion', '=', 'texto_correo_encabezado')->where('id_dominio', '=', $cotizacionDatos->id_dominio)->first();
			$cuerpo = Cotizaciontexto::where('texto_seccion', '=', 'texto_correo_cuerpo')->where('id_dominio', '=', $cotizacionDatos->id_dominio)->first();
			$pie = Cotizaciontexto::where('texto_seccion', '=', 'texto_correo_pie')->where('id_dominio', '=', $cotizacionDatos->id_dominio)->where('id_usuario', '=', '-1')->first();
			
			$pdf = self::generarCotizacionPdfWS2023($cotizacionDatos, false);
			
			$cotizacionDatos->pdf = $pdf;
			$datosPlantilla = array(
					'nombre' => utf8_decode($cotizacionDatos->nombre),
					'e_mail' => $cotizacionDatos->e_mail,
					'id_cotizacion' => $cotizacionDatos->id_cotizacion,
					'secret' => $cotizacionDatos->secret,
					'encabezado' => str_replace('{{nombre}}', utf8_decode($cotizacionDatos->nombre), $encabezado->texto_pdf),
					'cuerpo' => $cuerpo->texto_pdf,
					'pie' => $pie->texto_pdf
				);
			try{
                /*Mail::send('plantillas.correo.enviarCotizacionCorreo', $datosPlantilla, function($message) use ($cotizacionDatos){
                	//$message->from('info@segurodegastosmedicosmayores.mx', 'Seguro de Gastos Médicos Mayores');
                	$message->from($cotizacionDatos->dominio()->first()->email, $cotizacionDatos->dominio()->first()->sender);
                    $message->to($cotizacionDatos->e_mail, $cotizacionDatos->nombre);
                    $message->cc('info@segurodegastosmedicosmayores.mx', 'Cotizaciones');
                    $message->subject('Cotización de Gastos Médicos Mayores - '.$cotizacionDatos->id_cotizacion);
                    if(file_exists($cotizacionDatos->pdf)){
                        $message->attach($cotizacionDatos->pdf, ['as' => 'Cotizacion_PDF:'.$cotizacionDatos->id_cotizacion.'.pdf']);
                    }
                });*/
                \ALTMailer::mail('plantillas.correo.enviarCotizacionCorreo', $datosPlantilla, $cotizacionDatos, $cotizacionDatos->dominio()->first()->email, $cotizacionDatos->dominio()->first()->sender);
                //unset($cotizacionDatos->pdf);
                //$cotizacionDatos->integrantes = $originalIntegrantes;
                
                $toUpdate = RecotizacionMapfre::where('id_cotizacion', '=', $cotizacionDatos->id_cotizacion)->get();
                foreach($toUpdate as $update){
                	$update->enviada = 1;
                	$update->save();
                }
                
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

	public function meInteresa($idCotizacion, $secret, $paquete){
		$cotizacionDatos = Cotizacion::find($idCotizacion);
		$paq = Paquete::where('paquete_campo', '=', $paquete)->get();
		$aseguradora = Aseguradora::find($paq[0]->id_aseguradora);
		$cotizacion = new Cotizador($cotizacionDatos, 'sa', 'db');
		$cotizacion::cotizarWS();
		$tablaIntegrantes = $cotizacion::tablaIntegrantesPaquete();
		$tabla = $cotizacion::datosTablaPaquete($idCotizacion, $paq[0]->id_aseguradora, $paq[0]->paquete_campo);
		$integrantes = count(json_decode($cotizacionDatos->integrantes));
		
		if($cotizacionDatos->me_interesa != $paquete){
			$datos = array(
				//"idCotizacion" => $idCotizacion,
				//"secret" => $cotizacionDatos->secret,
				"cotizacionDatos" => $cotizacionDatos,
				"planes" => $paquete,
				//"comentarios" => "",
				//"por" => ""
				"integrantes" => $integrantes
			);
			\ALTMailer::mail(
            	//'plantillas.correo.deseaCotizar', 
            	'plantillas.correo.meInteresa', 
            	$datos, 
            	$cotizacionDatos, 
            	$cotizacionDatos->dominio()->first()->email, 
            	$cotizacionDatos->dominio()->first()->sender, 
            	['ventas1@segurodegastosmedicosmayores.mx'], 
            	'Desea Contratar Gastos Medicos ' . $cotizacionDatos->id_cotizacion
            );
		}
		
		$cotizacionDatos->estatus = 1;
		$cotizacionDatos->me_interesa = $paquete;
		$cotizacionDatos->save();
		
		View::share('cotizacionDatos', $cotizacionDatos);
		View::share('tablaIntegrantes', $tablaIntegrantes);
		View::share('tabla', $tabla);
		View::share('paquete', $paq);
		View::share('aseguradora', $aseguradora);
		
		return View::make('blog.verPortoMeInteresa');
	}
	
	public function meInteresaContactanos(){
		$idCotizacion = Input::get('id');
		$secret = Input::get('secret');
		$paquete = Input::get('paquete');
		$comentarios = Input::get('comentarios');
		
		$cotizacionDatos = Cotizacion::find($idCotizacion);
		if($cotizacionDatos){
			if($cotizacionDatos->secret == $secret){
				if(is_null($cotizacionDatos->comentarios))
					$cotizacionDatos->comentarios = "";
				$cotizacionDatos->comentarios .= "\n\n--- Me Interesa " . $paquete . " ---\n";
				$cotizacionDatos->comentarios .= $comentarios;
				$cotizacionDatos->save(); 
			}
		}
		
		return "1";
	}

	public function ipLocation(){
		$ip = $_SERVER["REMOTE_ADDR"];
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://api.ip2location.io/?' . http_build_query([
			'ip' => $ip,
			'key' => 'ADF1B8CA7D9C0BFE22C25ED5A53FDC6B',
			'format' => 'json',
		]));

		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);

		$response = curl_exec($ch);

		//var_dump($response);
		
		return $response;
	}

	public function testPDF(){
		$cotizacionDatos = Cotizacion::find(18324);
		$cotizacionDatos->integrantes = json_decode($cotizacionDatos->integrantes);
		
		$cotizacion = new Cotizador($cotizacionDatos, 'sa', 'db');
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
        View::share('paquetes', $paquetes);
        $html = View::make('plantillas.correo.cotizacionPdfWS2023');
        
        echo $html;
	}

	public function whatsappReceived($phone){
		$cotizaciones = Cotizacion::where('telefono', $phone)
							->where('id_lista_distribucion', '<>', 0)
							->where('pausa_lista_distribucion', 0)
							->get();
		foreach($cotizaciones as $cotizacion){
			$cotizacion->pausa_lista_distribucion = 1;
			$cotizacion->save();
		}
		
		return '1';
	}
}
