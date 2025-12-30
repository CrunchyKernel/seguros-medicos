<?php

class MainController extends BaseController {
	protected $layout = 'layout.master';
	
	public function bienvenido()
	{
		//$this->layout->content = View::make('inicio');
		//Paginavisita::hit();
		
		$contenido = Blog::where('alias', '=', 'main')->where('estatus', 1)->first();
		//$contenido = Blog::where('id_blog', '=', 244)->first();
		$contenido->html = json_decode($contenido->html);
		View::share('metaTitulo', $contenido->titulo);
		View::share('metaKeys', $contenido->metakey);
		View::share('metaDescripcion', str_limit(trim(strip_tags(html_entity_decode($contenido->metadesc))), 155, ''));
		//dd($contenido);
		
		// Para plantilla origginal
		/*if(!Input::get('porto')){
			View::share('contenido', $contenido->html);
			return View::make('inicio');
		}
		else{
			if(Input::get('porto')!=1){
				View::share('contenido', $contenido->html);
				return View::make('inicio');
			}
			else{
				View::share('contenido', $contenido);
				return View::make('blog.verPortoHtml');
			}
		}*/
		View::share('hideTitle', true);
		View::share('contenido', $contenido);
		return View::make('blog.verPortoBlog');
	}

	public function paquetes(){
		$aseguradoras = Aseguradora::where('activa', '=', 1)->get();
		$aseguradorasPaquetes = array();
		$paqueteConceptos = Paqueteconcepto::orderBy('orden')->get();
		
		foreach($aseguradoras AS $aseguradora){
			$paquetes = $aseguradora->Paquetes()->where('activo', '=', 1)->get();
			if(count($paquetes) > 0){
				$aseguradorasPaquetes[$aseguradora->aseguradora] = array();
				foreach($paquetes AS $paquete){
					$aseguradorasPaquetes[$aseguradora->aseguradora][$paquete->paquete_campo]['idPaquete'] = $paquete->id_paquete;
					$aseguradorasPaquetes[$aseguradora->aseguradora][$paquete->paquete_campo]['paqueteNombre'] = $paquete->paquete;
					$aseguradorasPaquetes[$aseguradora->aseguradora][$paquete->paquete_campo]['paqueteCampo'] = $paquete->paquete_campo;
					//$aseguradorasPaquetes[$aseguradora->aseguradora][$paquete->paquete_campo]['configuracion'] = json_decode($paquete->configuracion);
					if(count($paqueteConceptos) > 0){
						foreach($paqueteConceptos AS $paqueteConcepto){
							$conceptoValor = $paqueteConcepto->tarifaValor()->where('id_paquete', '=', $paquete->id_paquete)->get();
							if(count($conceptoValor) == 1){
								$conceptoValor = $conceptoValor[0]->sb_db;
							}else{
								$conceptoValor = '';
							}
							$aseguradorasPaquetes[$aseguradora->aseguradora][$paquete->paquete_campo]['configuracion'][$paqueteConcepto->concepto] = $conceptoValor;
						}
					}
				}
			}
		}
		/*
		echo "<pre>";
		foreach($aseguradorasPaquetes AS $aseguradora => $paquetes){
			foreach($paquetes AS $paquete){
				foreach($paquete["configuracion"] AS $key => $value){
					echo $value;
					exit;
				}
			}
		}
		*/
		//echo "<pre>";
		//print_r($aseguradorasPaquetes);
		//exit;
		View::share('aseguradorasPaquetes', $aseguradorasPaquetes);
		View::share('metaTitulo', 'Paquetes de Gastos médicos');
        View::share('metaDescripcion', 'Selecciona tu paquete y realiza una cotiazión');
		
		// Para plantilla original
		/*if(!\Input::get("porto"))
			$this->layout->content = View::make('paquetes');
		else{
			if(\Input::get("porto")!="1")
				$this->layout->content = View::make('paquetes');
			else
				return View::make('portoPaquetes');
		}*/
		return View::make('portoPaquetes');
		Paginavisita::hit();
	}

	public function enviarContacto(){
		$contactoDatos = Input::all();
		
		$respuesta = array(
                        "status" => "invalid",
                        "mensaje" => "Ocurrio un error al tratar de enviar el formulario",
                    );
        $datosPlantilla = array(
							'nombre' => $contactoDatos['nombre'],
							'e_mail' => $contactoDatos['e_mail'],
							'mensaje' => $contactoDatos['mensaje'],
						);
        //echo View::make('plantillas.correo.contacto');
        //exit;
		try{
			Mail::send('plantillas.correo.contacto', $datosPlantilla, function($message) use ($contactoDatos){
	            $message->from('info@segurodegastosmedicosmayores.mx', 'Seguro de Gastos Medicos Mayores');
	            $message->to('info@segurodegastosmedicosmayores.mx', 'Contacto');
	            $message->cc($contactoDatos['e_mail'], $contactoDatos['nombre']);
	            $message->subject('Formulario de contacto: '.$contactoDatos['nombre']);
	        });
	    }catch(Exception $e){
	    	$respuesta["mensaje"] = $e->getMessage();
	        return json_encode($respuesta);
	    }
	    $respuesta["status"] = "success";
	    $respuesta["mensaje"] = 'Formulario enviado correctamente.';

	    return json_encode($respuesta);
	}

	public function contacto(){
		View::share('metaTitulo', 'Contacto');
        View::share('metaDescripcion', 'Envíanos tus dudas y con gusto te responderemos');
		
		// Para plantilla original
		/*if(!\Input::get("porto"))
			$this->layout->content = View::make('contacto');
		else{
			if(\Input::get("porto")!="1")
				$this->layout->content = View::make('contacto');
			else
				return View::make('portoContacto');
		}*/
		return View::make('portoContacto');
		Paginavisita::hit();
	}

	public function nosotros(){
		View::share('metaTitulo', 'Nosotros');
        View::share('metaDescripcion', 'Somos un grupo de agentes de seguros llamados ProtectoDIEZ, con más de 40 años en el mercado.');
		
		// Para plantilla original
		/*if(!\Input::get("porto"))
			$this->layout->content = View::make('nosotros');
		else{
			if(\Input::get("porto")!="1")
				$this->layout->content = View::make('nosotros');
			else
				return View::make('portoNosotros');
		}*/
		return View::make('portoNosotros');
	}

	public function postContacto(){
		$datos = Input::all();
		Mail::send('plantillas.correo.postContacto', $datos, function($message){
        	 $message->from("contacto@segurodegastosmedicosmayores.mx", "Contacto");
            $message->to("contacto@segurodegastosmedicosmayores.mx");
            $message->subject('Formulario de contacto');
        });
        return "1";
	}
}
