<?php

namespace Backend;

use App\Models\Backend\User;
use App\Models\Backend\Usuariosesion;
use App\Models\Backend\Modulo;
use App\Models\Backend\ModuloPermiso;


class AseguradoraController extends \BaseController {
	public $ruta = '';
    public $scripts = array();
	protected $layout = 'backend.layout.master';

	public function __construct()
    {
        $this->beforeFilter(function(){
            if(!\Auth::check()){
                return \Redirect::to('/admingm/login/cerrarSesion');
            }
            $sesionRegistrada = Usuariosesion::where('id_usuario', '=', \Auth::user()->id_usuario)->get();
            if(count($sesionRegistrada) == 0){
                return \Redirect::to('/admingm/login/cerrarSesion');
            }
            Usuariosesion::actualizarSesionTiempo();
        });
        $this->ruta = \Request::segment(2).'/'.\Request::segment(3);
        if(file_exists(public_path().'/backend/js/helpers/'.$this->ruta.'.js')){
        	$this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/helpers/".$this->ruta);
        }
        $this->ruta = str_replace("/", ".", $this->ruta);
    }

    public function guardarPaqueteDeducibles(){
        $aseguradora = \Input::get('aseguradora');
        $aseguradoraDatos = \Aseguradora::where('aseguradora', '=', $aseguradora)->first();
        $paquete = \Input::get('paquete');

        $deducibles["SADA"] = \Input::get('SADA');
        $deducibles["SADB"] = \Input::get('SADB');
        $deducibles["SBDA"] = \Input::get('SBDA');
        $deducibles["SBDB"] = \Input::get('SBDB');

        $respuesta = array(
                        "status" => "invalid",
                        "titulo" => "Actualizar paquetes",
                        "mensaje" => "Ocurrio un erro al tratar de actualizar los paquetes",
                        "posicion" => "stack_bottom_left",
                        "tipo" => "error",
                    );
        if(count($deducibles["SADA"]) > 0){
            foreach($deducibles["SADA"] AS $deducible_SADA){
                $aseguradoraDatos->Paquetes()->where('paquete_campo', '=', $paquete)->first()->tarifasada()->where('edad', '=', $deducible_SADA[0])->update(array("tarifa_m" => $deducible_SADA[1], "tarifa_f" => $deducible_SADA[2]));
            }
        }
        if(count($deducibles["SADB"]) > 0){
            foreach($deducibles["SADB"] AS $deducible_SADB){
                $aseguradoraDatos->Paquetes()->where('paquete_campo', '=', $paquete)->first()->tarifasadb()->where('edad', '=', $deducible_SADB[0])->update(array("tarifa_m" => $deducible_SADB[1], "tarifa_f" => $deducible_SADB[2]));
            }
        }
        if(count($deducibles["SBDA"]) > 0){
            foreach($deducibles["SBDA"] AS $deducible_SBDA){
                $aseguradoraDatos->Paquetes()->where('paquete_campo', '=', $paquete)->first()->tarifasbda()->where('edad', '=', $deducible_SBDA[0])->update(array("tarifa_m" => $deducible_SBDA[1], "tarifa_f" => $deducible_SBDA[2]));
            }
        }
        if(count($deducibles["SBDB"]) > 0){
            foreach($deducibles["SBDB"] AS $deducible_SBDB){
                $aseguradoraDatos->Paquetes()->where('paquete_campo', '=', $paquete)->first()->tarifasbdb()->where('edad', '=', $deducible_SBDB[0])->update(array("tarifa_m" => $deducible_SBDB[1], "tarifa_f" => $deducible_SBDB[2]));
            }
        }
        $respuesta["mensaje"] = "Los deducibles han sido actualizados correctamente";
        $respuesta["status"] = "success";
        $respuesta["tipo"] = "success";
        return json_encode($respuesta);
    }

    public function cargarPaqueteDeducibles(){
        $aseguradora = \Input::get('aseguradora');
        $paquete = \Input::get('paquete');
        $aseguradoraDatos = \Aseguradora::where('aseguradora', '=', $aseguradora)->first();
        
        $respuesta = array(
                        "status" => "invalid",
                        "titulo" => "Agregar administrador",
                        "mensaje" => "Ocurrio un error al tratar de agregar al hijo",
                        "posicion" => "stack_bottom_left",
                        "tipo" => "error",
                    );
        $respuesta["paquetes"]["SADA"] = array();
        foreach($aseguradoraDatos->Paquetes()->where('paquete_campo', '=', $paquete)->first()->tarifasada()->select('edad', 'tarifa_m', 'tarifa_f')->orderBy('edad')->get() AS $tarifasada){
            $respuesta["paquetes"]["SADA"][] = array($tarifasada->edad, $tarifasada->tarifa_m, $tarifasada->tarifa_f);
        }
        $respuesta["paquetes"]["SADB"] = array();
        foreach($aseguradoraDatos->Paquetes()->where('paquete_campo', '=', $paquete)->first()->tarifasadb()->select('edad', 'tarifa_m', 'tarifa_f')->orderBy('edad')->get() AS $tarifasadb){
            $respuesta["paquetes"]["SADB"][] = array($tarifasadb->edad, $tarifasadb->tarifa_m, $tarifasadb->tarifa_f);
        }
        $respuesta["paquetes"]["SBDA"] = array();
        foreach($aseguradoraDatos->Paquetes()->where('paquete_campo', '=', $paquete)->first()->tarifasbda()->select('edad', 'tarifa_m', 'tarifa_f')->orderBy('edad')->get() AS $tarifasbda){
            $respuesta["paquetes"]["SBDA"][] = array($tarifasbda->edad, $tarifasbda->tarifa_m, $tarifasbda->tarifa_f);
        }
        $respuesta["paquetes"]["SBDB"] = array();
        foreach($aseguradoraDatos->Paquetes()->where('paquete_campo', '=', $paquete)->first()->tarifasbdb()->select('edad', 'tarifa_m', 'tarifa_f')->orderBy('edad')->get() AS $tarifasbdb){
            $respuesta["paquetes"]["SBDB"][] = array($tarifasbdb->edad, $tarifasbdb->tarifa_m, $tarifasbdb->tarifa_f);
        }
        return json_encode($respuesta);
    }

    public function cargarPaquetesAseguradora(){
        $aseguradora = \Input::get('aseguradora');
        $aseguradoraDatos = \Aseguradora::where('aseguradora', '=', $aseguradora)->first();

        $respuesta = array(
                        "status" => "invalid",
                        "titulo" => "Paquetes",
                        "mensaje" => "Ocurrio un error al tratar de agregar al hijo",
                        "posicion" => "stack_bottom_left",
                        "tipo" => "error",
                        "paquetes" => array()
                    );
        if($aseguradoraDatos){
            $paquetes = \Paquete::where('id_aseguradora', '=', $aseguradoraDatos->id_aseguradora)->get();
            if(count($paquetes) > 0){
                foreach($paquetes AS $paquete){
                    $respuesta["paquetes"][] = array("nombre" => $paquete->paquete, "valor" => $paquete->paquete_campo);
                }
            }
        }
        return json_encode($respuesta);
    }

    public function deducibles(){
        $aseguradoras = \Aseguradora::all();
        \View::share('aseguradoras', $aseguradoras);

        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/jquery.slimscroll.min");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/select2.min");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/css/select2");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/handsontable/dist/handsontable.full");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/js/handsontable/dist/handsontable.full");
        \View::share('scripts', $this->scripts);
        $this->layout->content = \View::make('backend.'.$this->ruta);
    }

    public function actualizarPaquete(){
        $paqueteDatos = \Input::all();
        
        $paquete = \Paquete::find($paqueteDatos['pk']);
        if($paquete){
	    $campo = $paqueteDatos['campo'];
            $paquete->$campo = $paqueteDatos['value'];
            if($paquete->save()){
                return 1;
            }
        }
        return 0;
    }

    public function actualizarSumaAsegurada(){
        
    	$campoDatos = \Input::all();
    	$conceptoTarifa = \Paqueteconceptotarifa::firstOrNew(array('id_paquete' => $campoDatos['idPaquete'], 'id_concepto' => $campoDatos['idConcepto']));
    	$conceptoTarifa->$campoDatos['campo'] = $campoDatos['value'];

    	if($conceptoTarifa->save()){
    		return 1;
    	}
    	return 0;
    }
	
	public function sumasAseguradas(){
		$aseguradoras = \Aseguradora::where('id_aseguradora', '>', 0)->orderBy('nombre')->get();
		\View::share('aseguradoras', $aseguradoras);

		$conceptos = \Paqueteconcepto::orderBy('orden')->get();
		\View::share('conceptos', $conceptos);

		$this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/x-editable/dist/bootstrap3-editable/js/bootstrap-editable.min");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/js/x-editable/dist/bootstrap3-editable/css/bootstrap-editable");
        \View::share('scripts', $this->scripts);

		$this->layout->content = \View::make('backend.'.$this->ruta);
	}
    
    public function recargos(){
        
    }
    
    public function notasPlanes(){
        
        //$this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/ckeditor/ckeditor");
       // $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/helpers/aseguradora/notasPlanes");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/ckeditor/ckeditor");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/ckeditor/adapters/jquery");
        \View::share('scripts', $this->scripts);
        \View::share('paquetes', \DB::table('paquetes')->where('id_paquete', '>', '0')
        ->get() );
       // $variablex = 1;
        //\View::share('variablex', $variablex );
        $aseguradoras = \Aseguradora::where('id_aseguradora', '>', '0')->get();
        \View::share('aseguradoras', $aseguradoras);

        $this->layout->content = \View::make('backend.'.$this->ruta);
    }
    
    public function guardarNota(){
        $descripcion = \Input::All(); 
        return \DB::table('paquetes')->where('id_paquete', $descripcion['id'])->update(array('descripcion_backend' => $descripcion['descripcion']));
    }
    
    public function onOffPlan(){
        $accion = \Input::all();
        if($accion['opcion'] == 'true'){
          $retorno =  \DB::table('paquetes')->where('id_paquete', $accion['id_plan'])->update(array('activo' => 1));
          if($retorno > 0){
              $retorno = "Plan activado";
          }else{
              $retorno = "Ocurrio un error";
          }    
        }else{
          $retorno =  \DB::table('paquetes')->where('id_paquete', $accion['id_plan'])->update(array('activo' => 0));
          if($retorno == 1){
              $retorno = "Plan desactivado";   
           }else{
               $retorno = "Ocurrió un error";
           }
        }
                
        return $retorno;
    }
    
    public function onOffAseguradora(){
        $accion = \Input::all();
        if($accion['opcion'] == 'true'){
          $retorno =  \DB::table('aseguradoras')->where('id_aseguradora', $accion['id_aseguradora'])->update(array('activa' => 1));
          if($retorno > 0){
              $retorno = "Aseguradora activada";
          }else{
              $retorno = "Ocurrio un error";
          }        
        }else{
          $retorno =  \DB::table('aseguradoras')->where('id_aseguradora', $accion['id_aseguradora'])->update(array('activa' => 0));
          if($retorno == 1){
              $retorno = "Aseguradora desactivada" ;   
           }else{
              $retorno = "Ocurrió un error" ;
           }
        }
                
        return $retorno;
    }

    public function imprimibles(){
        //$this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/helpers/aseguradora/imprimibles");
       // $variablex = 1;
        //\View::share('variablex', $variablex);
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/ckeditor/ckeditor");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/ckeditor/adapters/jquery");
        \View::share('scripts', $this->scripts);
        \View::share('textoProtecto',  \DB::table('texto_pdf')->where('texto_seccion', 'beneficios_protecto')->where('id_dominio', 1)->get()[0]);
        \View::share('textoSaludo',  \DB::table('texto_pdf')->where('texto_seccion', 'saludo_bienvenida')->where('id_dominio', 1)->get()[0]);
        \View::share('textoCEncabezado',  \DB::table('texto_pdf')->where('texto_seccion', 'cotizacion_encabezado')->where('id_dominio', 1)->get()[0]);
        $textoCAbajode = "";
        $abajode = \DB::table('texto_pdf')->where('texto_seccion', 'cotizacion_abajode')->where('id_dominio', 1)->get();
        if($abajode)
			$textoCAbajode = $abajode[0]->texto_pdf;
        \View::share('textoCAbajode',  $textoCAbajode);
        \View::share('textoCPie',  \DB::table('texto_pdf')->where('texto_seccion', 'cotizacion_pie')->where('id_dominio', 1)->get()[0]);

		$dominios = \Domain::select('id_dominio', 'nombre')->orderBy('id_dominio')->get();
        \View::share('dominios', $dominios);
		
        $this->layout->content = \View::make('backend.'.$this->ruta);

    }

    public function guardaTextoProtecto(){
        $textoPT = \Input::all();
        $protecto = \DB::table('texto_pdf')->where('texto_seccion', 'beneficios_protecto')->where('id_dominio', $textoPT['id_dominio'])->get();
        if($protecto) 
        	\DB::table('texto_pdf')->where('texto_seccion', 'beneficios_protecto')->where('id_dominio', $textoPT['id_dominio'])->update(array('texto_pdf' => $textoPT['textoPT']));
        else{
			$protecto = new \Cotizaciontexto();
			$protecto->texto_pdf = $textoPT['textoPT'];
			$protecto->texto_seccion = 'beneficios_protecto';
			$protecto->id_usuario = -1;
			$protecto->id_dominio = $textoPT['id_dominio'];
			$protecto->save();
		}
        return 1;
    }

    public function guardaTextoSaludo(){
        $textoPT = \Input::all();
        $saludo = \DB::table('texto_pdf')->where('texto_seccion', 'saludo_bienvenida')->where('id_dominio', $textoPT['id_dominio'])->get();
        if($saludo)
        	\DB::table('texto_pdf')->where('texto_seccion', 'saludo_bienvenida')->where('id_dominio', $textoPT['id_dominio'])->update(array('texto_pdf' => $textoPT['textoPT']));
        else{
			$saludo = new \Cotizaciontexto();
			$saludo->texto_pdf = $textoPT['textoPT'];
			$saludo->texto_seccion = 'saludo_bienvenida';
			$saludo->id_usuario = -1;
			$saludo->id_dominio = $textoPT['id_dominio'];
			$saludo->save();
		}
		return 1;
    }

	public function guardarCotizacionEncabezado(){
        $textoPT = \Input::all();
        $saludo = \DB::table('texto_pdf')->where('texto_seccion', 'cotizacion_encabezado')->where('id_dominio', $textoPT['id_dominio'])->get();
        if($saludo)
        	\DB::table('texto_pdf')->where('texto_seccion', 'cotizacion_encabezado')->where('id_dominio', $textoPT['id_dominio'])->update(array('texto_pdf' => $textoPT['textoPT']));
        else{
			$saludo = new \Cotizaciontexto();
			$saludo->texto_pdf = $textoPT['textoPT'];
			$saludo->texto_seccion = 'cotizacion_encabezado';
			$saludo->id_usuario = -1;
			$saludo->id_dominio = $textoPT['id_dominio'];
			$saludo->save();
		}
		return 1;
    }
    
    public function guardarCotizacionAbajode(){
        $textoPT = \Input::all();
        $saludo = \DB::table('texto_pdf')->where('texto_seccion', 'cotizacion_abajode')->where('id_dominio', $textoPT['id_dominio'])->get();
        if($saludo)
        	\DB::table('texto_pdf')->where('texto_seccion', 'cotizacion_abajode')->where('id_dominio', $textoPT['id_dominio'])->update(array('texto_pdf' => $textoPT['textoPT']));
        else{
			$saludo = new \Cotizaciontexto();
			$saludo->texto_pdf = $textoPT['textoPT'];
			$saludo->texto_seccion = 'cotizacion_abajode';
			$saludo->id_usuario = -1;
			$saludo->id_dominio = $textoPT['id_dominio'];
			$saludo->save();
		}
		return 1;
    }
    
    public function guardarCotizacionPie(){
        $textoPT = \Input::all();
        $saludo = \DB::table('texto_pdf')->where('texto_seccion', 'cotizacion_pie')->where('id_dominio', $textoPT['id_dominio'])->get();
        if($saludo)
        	\DB::table('texto_pdf')->where('texto_seccion', 'cotizacion_pie')->where('id_dominio', $textoPT['id_dominio'])->update(array('texto_pdf' => $textoPT['textoPT']));
        else{
			$saludo = new \Cotizaciontexto();
			$saludo->texto_pdf = $textoPT['textoPT'];
			$saludo->texto_seccion = 'cotizacion_pie';
			$saludo->id_usuario = -1;
			$saludo->id_dominio = $textoPT['id_dominio'];
			$saludo->save();
		}
		return 1;
    }

	public function consultaImprimibles($idDominio){
		$textoProtecto = "Aqui va el contenido de los beneficios";
		$textoSaludo = "Aqui va el contenido del saludo";
		$textoCEncabezado = "Encabezado de la cotizacion";
		$textoCPie = "Pie de pagina de la cotizacion";
		$protecto = \DB::table('texto_pdf')->where('texto_seccion', 'beneficios_protecto')->where('id_dominio', $idDominio)->get();
		$saludo = \DB::table('texto_pdf')->where('texto_seccion', 'saludo_bienvenida')->where('id_dominio', $idDominio)->get();
		$encabezado = \DB::table('texto_pdf')->where('texto_seccion', 'cotizacion_encabezado')->where('id_dominio', $idDominio)->get();
		$pie = \DB::table('texto_pdf')->where('texto_seccion', 'cotizacion_pie')->where('id_dominio', $idDominio)->get();
		if($protecto)
			$textoProtecto = $protecto[0]->texto_pdf;
		if($saludo)
			$textoSaludo = $saludo[0]->texto_pdf;
		if($encabezado)
			$textoCEncabezado = $encabezado[0]->texto_pdf;
		if($pie)
			$textoCPie = $pie[0]->texto_pdf;
		$res = array("textoProtecto" => utf8_encode($textoProtecto), "textoSaludo" => utf8_encode($textoSaludo), "textoCEncabezado" => utf8_encode($textoCEncabezado), "txtCPie" => utf8_encode($textoCPie));
		return json_encode($res);
	}

	public function baseMapfre(){
		$baseMapfre = \BaseMapfre::first();
		\View::share('baseMapfre', $baseMapfre);
		\View::share('scripts', $this->scripts);
		
		$this->layout->content = \View::make('backend.' . $this->ruta);
	}
	
	public function baseMapfreUpdate(){
		$baseMapfre = \BaseMapfre::first();
		
		$baseMapfre->sa = \Input::get('sa');
		$baseMapfre->deducible_19 = \Input::get('deducible_19');
		$baseMapfre->deducible_24 = \Input::get('deducible_24');
		$baseMapfre->deducible_29 = \Input::get('deducible_29');
		$baseMapfre->deducible_34 = \Input::get('deducible_34');
		$baseMapfre->deducible_39 = \Input::get('deducible_39');
		$baseMapfre->deducible_44 = \Input::get('deducible_44');
		$baseMapfre->deducible_49 = \Input::get('deducible_49');
		$baseMapfre->deducible_54 = \Input::get('deducible_54');
		$baseMapfre->deducible_59 = \Input::get('deducible_59');
		$baseMapfre->deducible_64 = \Input::get('deducible_64');
		$baseMapfre->deducible_69 = \Input::get('deducible_69');
		$baseMapfre->coaseguro = \Input::get('coaseguro');
		$baseMapfre->tabulador = \Input::get('tabulador');
		$baseMapfre->emergencia_extranjero = \Input::get('emergencia-extranjero');
		$baseMapfre->sa_maternidad = \Input::get('sa-maternidad');
		
		if(!is_null(\Input::get('reduccion-deducible')))
			$baseMapfre->reduccion_deducible = 1;
		else
			$baseMapfre->reduccion_deducible = 0;
		
		if(\Input::get('dental')!="")
			$baseMapfre->dental = \Input::get('dental');
		else
			$baseMapfre->dental = null;
		
		if(!is_null(\Input::get('complicaciones')))
			$baseMapfre->complicaciones = 1;
		else
			$baseMapfre->complicaciones = 0;
		
		if(!is_null(\Input::get('vanguardia')))
			$baseMapfre->vanguardia = 1;
		else
			$baseMapfre->vanguardia = 0;
		
		if(!is_null(\Input::get('multiregion')))
			$baseMapfre->multiregion = 1;
		else
			$baseMapfre->multiregion = 0;
		
		if(!is_null(\Input::get('preexistentes')))
			$baseMapfre->preexistentes = 1;
		else
			$baseMapfre->preexistentes = 0;
		
		if(!is_null(\Input::get('catastroficas')))
			$baseMapfre->catastroficas = 1;
		else
			$baseMapfre->catastroficas = 0;
		
		if(!is_null(\Input::get('funeraria')))
			$baseMapfre->funeraria = 1;
		else
			$baseMapfre->funeraria = 0;
		
		$baseMapfre->save();
		
		return 1;
	}

	public function sumasAseguradas2023(){
		$aseguradoras = \Aseguradora::where('id_aseguradora', '>', 0)->orderBy('nombre')->get();
		\View::share('aseguradoras', $aseguradoras);

		$conceptos = \Paqueteconcepto2023::orderBy('orden')->get();
		\View::share('conceptos', $conceptos);

		$this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/x-editable/dist/bootstrap3-editable/js/bootstrap-editable.min");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/js/x-editable/dist/bootstrap3-editable/css/bootstrap-editable");
        \View::share('scripts', $this->scripts);

		$this->layout->content = \View::make('backend.'.$this->ruta);
	}

	public function actualizarSumaAsegurada2023(){
        
    	$campoDatos = \Input::all();
    	$conceptoTarifa = \Paqueteconceptotarifa2023::firstOrNew(array('id_paquete' => $campoDatos['idPaquete'], 'id_concepto' => $campoDatos['idConcepto']));
    	$conceptoTarifa->$campoDatos['campo'] = $campoDatos['value'];

    	if($conceptoTarifa->save()){
    		return 1;
    	}
    	return 0;
    }
    
    public function actualizarPaquete2023(){
        $paqueteDatos = \Input::all();
        
        $paquete = \Paquete::find($paqueteDatos['pk']);
        if($paquete){
	    $campo = $paqueteDatos['campo'];
            $paquete->$campo = $paqueteDatos['value'];
            if($paquete->save()){
                return 1;
            }
        }
        return 0;
    }

	public function estadisticas(){
		\View::share('scripts', $this->scripts);
		
		$this->layout->content = \View::make('backend.' . $this->ruta);
	}
	
	public function estadisticasReporte(){
		$res = array();
		$desde = \Input::get('desde') . ' 00:00:00';
		$hasta = \Input::get('hasta') . ' 23:59:59';
		
		$dia = array();
		$rows = \DB::select(\DB::raw("select dayofweek(a.created_at) dia, count(*) total, 
			ifnull((select count(*) from gm_cotizaciones where fecha_registro between '" . $desde . "' and '" . $hasta . "' and dayofweek(fecha_registro) = dayofweek(a.created_at)), 0) cotizaciones
			from gm_cotizadores a 
		    where a.created_at between '" . $desde . "' and '" . $hasta . "'
		    group by dayofweek(a.created_at)
		    order by 1"));
		foreach($rows as $row){
			$d = "";
			switch($row->dia){
				case 1:
					$d = "Domingo";
					break;
				case 2:
					$d = "Lunes";
					break;
				case 3:
					$d = "Martes";
					break;
				case 4:
					$d = "Miercoles";
					break;
				case 5:
					$d = "Jueves";
					break;
				case 6:
					$d = "Viernes";
					break;
				case 7:
					$d = "Sabado";
					break;
			}
			$dia[] = array(
				"dia" => $d,
				"total" => number_format($row->total, 0, "", ","),
				"cotizaciones" => number_format($row->cotizaciones, 0, "", ","),
				"porcentaje" => (($row->total > 0) ? number_format(($row->cotizaciones / $row->total) * 100, 2, ".", "") . " %" : "0 %")
			);
		}
		$res["dia"] = $dia;
		
		$dispositivo = array();
		$rows = \DB::select(\DB::raw("select a.movil, count(*) total, 
			ifnull((select count(*) from gm_cotizaciones where fecha_registro between '" . $desde . "' and '" . $hasta . "' and movil = a.movil), 0) cotizaciones
			from gm_cotizadores a
		    where a.created_at between '" . $desde . "' and '" . $hasta . "'
		    group by a.movil"));
		foreach($rows as $row){
			$dispositivo[] = array(
				"dispositivo" => (($row->movil==0) ? "PC" : "Movil"),
				"total" => number_format($row->total, 0, "", ","),
				"cotizaciones" => number_format($row->cotizaciones, 0, "", ","),
				"porcentaje" => (($row->total > 0) ? number_format(($row->cotizaciones / $row->total) * 100, 2, ".", "") . " %" : "0 %")
			);
		}
		$res["dispositivo"] = $dispositivo;
		    
		$rutaPC = array();
		$rows = \DB::select(\DB::raw("select a.alias, count(*) total 
			, ifnull((select count(*) from gm_cotizaciones where fecha_registro between '" . $desde . "' and '" . $hasta . "' and ruta = concat('/', a.alias) and movil = a.movil), 0) cotizaciones
			from gm_cotizadores a
		    where a.created_at between '" . $desde . "' and '" . $hasta . "' and a.movil = 0
		    group by a.alias
		    order by 2 desc"));
		foreach($rows as $row){
			$rutaPC[] = array(
				"alias" => $row->alias,
				"total" => number_format($row->total, 0, "", ","),
				"cotizaciones" => number_format($row->cotizaciones, 0, "", ","),
				"porcentaje" => (($row->total > 0) ? number_format(($row->cotizaciones / $row->total) * 100, 2, ".", "") . " %" : "0 %")
			);
		}
		$res["rutaPC"] = $rutaPC;
		
		$rutaMovil = array();
		$rows = \DB::select(\DB::raw("select a.alias, count(*) total 
			, ifnull((select count(*) from gm_cotizaciones where fecha_registro between '" . $desde . "' and '" . $hasta . "' and ruta = concat('/', a.alias) and movil = a.movil), 0) cotizaciones
			from gm_cotizadores a
		    where a.created_at between '" . $desde . "' and '" . $hasta . "' and a.movil = 1
		    group by a.alias
		    order by 2 desc"));
		foreach($rows as $row){
			$rutaMovil[] = array(
				"alias" => $row->alias,
				"total" => number_format($row->total, 0, "", ","),
				"cotizaciones" => number_format($row->cotizaciones, 0, "", ","),
				"porcentaje" => (($row->total > 0) ? number_format(($row->cotizaciones / $row->total) * 100, 2, ".", "") . " %" : "0 %")
			);
		}
		$res["rutaMovil"] = $rutaMovil;
		
		return \Response::json($res);
	}

	public function conceptos2023()
	{
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/x-editable/dist/bootstrap3-editable/js/bootstrap-editable.min");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/js/x-editable/dist/bootstrap3-editable/css/bootstrap-editable");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/css/style.datatables");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/jquery.dataTables.min");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/dataTables/dataTables.bootstrap");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/dataTables/dataTables.responsive");
        \View::share('scripts', $this->scripts);

        $this->layout->content = \View::make('backend.'.$this->ruta);
	}
	
	public function getConceptos2023(){
        $conceptos = \Paqueteconcepto2023::where('id_concepto', '>', 0)->get();
        return \Datatable::collection($conceptos)
            ->showColumns('id_concepto')
             ->addColumn('concepto', function($concepto)
            {
                 return $concepto->concepto;
                 //return '<a href="#" class="campo" data-campo="nombre" data-value="'.$dominio->nombre.'" data-type="text" data-original-title="Nombre" data-pk="'.$dominio->id_dominio.'"></a>';
            })
            ->addColumn('tooltip', function($concepto)
            {
                 //return $paquete->paquete;
                 return '<a href="#" class="campo" data-campo="tooltip" data-value="'.$concepto->tooltip.'" data-type="text" data-original-title="Tooltip" data-pk="'.$concepto->id_concepto.'"></a>';
            })
            ->addColumn('orden', function($concepto)
            {
                return $concepto->orden;
                //return '<a href="#" class="campo" data-campo="email" data-value="'.$dominio->email.'" data-type="email" data-original-title="Email" data-pk="'.$dominio->id_dominio.'"></a>';
            })
            ->searchColumns('id_concepto', 'concepto', 'tooltip', 'orden')
            ->orderColumns('orden')
            ->make();
    }
    
    public function actualizaConceptos2023(){
        $datos = \Input::all();
        $concepto = \Paqueteconcepto2023::find($datos["pk"]);
        $concepto->$datos["campo"] = $datos["value"];

        try{
            if($concepto->save()){
                return 1;
            }
        }catch(Exception $e){
            return 0;
        }
        return 0;
    } 

	public function meInteresa(){
        
        //$this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/ckeditor/ckeditor");
       // $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/helpers/aseguradora/notasPlanes");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/ckeditor/ckeditor");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/ckeditor/adapters/jquery");
        \View::share('scripts', $this->scripts);
        \View::share('paquetes', \DB::table('paquetes')->where('id_paquete', '>', '0')
        ->get() );
       // $variablex = 1;
        //\View::share('variablex', $variablex );
        $aseguradoras = \Aseguradora::where('id_aseguradora', '>', '0')->get();
        \View::share('aseguradoras', $aseguradoras);

        $this->layout->content = \View::make('backend.'.$this->ruta);
    }
    
    public function guardarMeInteresa(){
        $descripcion = \Input::All(); 
        return \DB::table('paquetes')->where('id_paquete', $descripcion['id'])->update(array('descripcion_me_interesa' => $descripcion['descripcion']));
    }

	public function listasDistribucion(){
		$this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/x-editable/dist/bootstrap3-editable/js/bootstrap-editable.min");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/js/x-editable/dist/bootstrap3-editable/css/bootstrap-editable");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/css/style.datatables");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/jquery.dataTables.min");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/dataTables/dataTables.bootstrap");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/dataTables/dataTables.responsive");
        \View::share('scripts', $this->scripts);

        $this->layout->content = \View::make('backend.'.$this->ruta);
	}
	
	public function getConsultaListasDistribucion(){
        $listas = \ListaDistribucion::where('id_lista', '>', 0)->get();
        return \Datatable::collection($listas)
            ->showColumns('id_lista')
            ->addColumn('nombre', function($lista)
            {
                //return $administrador->nombre.' '.$administrador->apellido_paterno.' '.$administrador->apellido_materno;
                return '<a href="'.\URL::to('admingm/aseguradora/altaListaDistribucion/'.$lista->id_lista).'">'.$lista->nombre.'</a>';
            })
            ->searchColumns('id_lista', 'nombre')
            ->orderColumns('id_lista')
            ->make();
    }

	public function altaListaDistribucion($idLista = -1)
    {
    	if($idLista > 0){
            $listaDatos = \ListaDistribucion::find($idLista);
            if($listaDatos){
            	\View::share('listaDatos', $listaDatos);
            }
        }
        
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/ckeditor/ckeditor");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/ckeditor/adapters/jquery");
       	$this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/x-editable/dist/bootstrap3-editable/js/bootstrap-editable.min");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/js/x-editable/dist/bootstrap3-editable/css/bootstrap-editable"); 
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/css/style.datatables");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/jquery.dataTables.min");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/dataTables/dataTables.bootstrap");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/dataTables/dataTables.responsive");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/jquery.validate.min");
        \View::share('scripts', $this->scripts);
        
        $this->layout->content = \View::make('backend.'.$this->ruta);
    }

	public function guardarListasDistribucion(){
		$idLista = \Input::get('id_lista');
        if($idLista>0){
			$lista = \ListaDistribucion::find($idLista);
			$lista->nombre = \Input::get('nombre');
		}
		else{
			$listaDatos = \Input::all();
			$lista = new \ListaDistribucion($listaDatos);
		}
		
		$respuesta = array(
                        "status" => "invalid",
                        "titulo" => "Alta lista de distribución",
                        "mensaje" => "Ocurrio un error al tratar de registrar la lista",
                        "posicion" => "stack_bar_bottom",
                        "tipo" => "error",
                        "idLista" => -1,
                    );
        
        try{
			if($lista->save()){
				if($idLista > 0){
                    $respuesta['mensaje'] = 'Lista de distribución actualizada correctamente';
                }else{
                	$idLista = $lista->id_lista;
                    $respuesta['mensaje'] = 'Lista de distribución agregada correctamente';
                }
                $respuesta['status'] = 'success';
                $respuesta['tipo'] = 'success';
                $respuesta['idLista'] = $idLista;
			}
		}catch(Exception $e){
            $respuesta['mensaje'] = $e->getMessage();
        }
        return json_encode($respuesta);
	}
	
	public function getConsultaListaDistribucionesPlantillas($idLista){
		$plantillas = \ListaDistribucionDetalle::where('id_lista', '=', $idLista)->orderBy("orden", "asc")->get();
        return \Datatable::collection($plantillas)
            ->showColumns('plantilla', 'orden')
            //->addColumn('id_lista', function($plantilla)
            //{
            //    return '<a href="#" class="delete"><i class="fa fa-trash" data-nombre="' + $plantilla->plantilla + '"></i></a>';
            //})
            ->searchColumns('plantilla')
            ->orderColumns('orden')
            ->make();
	}
	
	public function agregarPlantilla(){
		$respuesta = array(
                        "status" => "invalid",
                        "titulo" => "Alta de plantilla",
                        "mensaje" => "Ocurrio un error al tratar de registrar la plantilla",
                        "posicion" => "stack_bar_bottom",
                        "tipo" => "error"
                    );
        $iLunes = 0;
        if(\Input::get('chkILunes')==1)
        	$iLunes = 1;
        $iMartes = 0;
        if(\Input::get('chkIMartes')==1)
        	$iMartes = 1;
        $iMiercoles = 0;
        if(\Input::get('chkIMiercoles')==1)
        	$iMiercoles = 1;
        $iJueves = 0;
        if(\Input::get('chkIJueves')==1)
        	$iJueves = 1;
        $iViernes = 0;
        if(\Input::get('chkIViernes')==1)
        	$iViernes = 1;
        $iSabado = 0;
        if(\Input::get('chkISabado')==1)
        	$iSabado = 1;
        $iDomingo = 0;
        if(\Input::get('chkIDomingo')==1)
        	$iDomingo = 1;
        $dias = 0;
        if(\Input::get('dias')!="")
        	$dias = \Input::get('dias');
        $lunes = 0;
        if(\Input::get('chkLunes')==1)
        	$lunes = 1;
        $martes = 0;
        if(\Input::get('chkMartes')==1)
        	$martes = 1;
        $miercoles = 0;
        if(\Input::get('chkMiercoles')==1)
        	$miercoles = 1;
        $jueves = 0;
        if(\Input::get('chkJueves')==1)
        	$jueves = 1;
        $viernes = 0;
        if(\Input::get('chkViernes')==1)
        	$viernes = 1;
        $sabado = 0;
        if(\Input::get('chkSabado')==1)
        	$sabado = 1;
        $domingo = 0;
        if(\Input::get('chkDomingo')==1)
        	$domingo = 1;
        $plantilla = new \ListaDistribucionDetalle();
        $plantilla->id_lista = \Input::get('id_lista');
        $plantilla->plantilla = \Input::get('plantilla');
        $plantilla->orden = \Input::get('orden');
        $plantilla->tipo = \Input::get('tipo');
        $plantilla->hora = \Input::get('hora');
        $plantilla->ignorar_0 = $iLunes;
        $plantilla->ignorar_1 = $iMartes;
        $plantilla->ignorar_2 = $iMiercoles;
        $plantilla->ignorar_3 = $iJueves;
        $plantilla->ignorar_4 = $iViernes;
        $plantilla->ignorar_5 = $iSabado;
        $plantilla->ignorar_6 = $iDomingo;
        $plantilla->dias = $dias;
        $plantilla->envio_0 = $lunes;
        $plantilla->envio_1 = $martes;
        $plantilla->envio_2 = $miercoles;
        $plantilla->envio_3 = $jueves;
        $plantilla->envio_4 = $viernes;
        $plantilla->envio_5 = $sabado;
        $plantilla->envio_6 = $domingo;
        try{
        	if($plantilla->save()){
                $respuesta['mensaje'] = 'Plantilla agregada correctamente';
                $respuesta['status'] = 'success';
                $respuesta['tipo'] = 'success';
			}
        }
        catch(Exception $e){
            $respuesta['mensaje'] = $e->getMessage();
        }
        
        return json_encode($respuesta);
	}
}
