<?php

namespace Backend;

use App\Models\Backend\User;
use App\Models\Backend\Usuariosesion;

set_time_limit(-1);

class CotizacionController extends \BaseController {
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

    public function getConsultaCotizacionesPrioridad(){
        $misPrioridades = \Auth::user()->cotizacionPrioridad()->get();
        return \Datatable::collection($misPrioridades)
            ->showColumns('id_cotizacion')
            ->addColumn('nombre', function($cotizacion)
            {
                $fechaProgramada = '';
                if($cotizacion->estatus == 6){
                    $seguimiento = $cotizacion->seguimientos()->where('fecha_seguimiento', '>=', date('Y-m-d h:i:00'))->where('realizado', '=', -1)->orderBy('fecha_seguimiento')->first();
                    if($seguimiento){
                        $fechaProgramada = ' - <strong>Seguimiento programado: '.$seguimiento->fecha_seguimiento.'</strong>';
                    }
                }
                return '<a href="'.\URL::to('/admingm/cotizacion/verCotizacion/'.$cotizacion->id_cotizacion.'/'.$cotizacion->secret).'">'.$cotizacion->nombre.'</a><br>'.date('Y-m-d H:i', strtotime($cotizacion->fecha_registro)).$fechaProgramada;
            })
            ->addColumn('ingreso', function($cotizacion)
            {
                return (($cotizacion->forma_ingreso == -1) ? 'Agente' : 'Web');
            })
            ->addColumn('contacto', function($cotizacion)
            {
                return '<a href="#" class="campo" data-campo="telefono" data-value="'.$cotizacion->telefono.'" data-type="text" data-original-title="Teléfono" data-pk="'.$cotizacion->id_cotizacion.'"></a><br><a href="#" class="campo" data-campo="e_mail" data-value="'.$cotizacion->e_mail.'" data-type="text" data-original-title="Correo electrónico" data-pk="'.$cotizacion->id_cotizacion.'"></a>';
            })
            ->addColumn('ubicacion', function($cotizacion)
            {
                return '<a href="#" class="campo" data-campo="estado" data-value="'.$cotizacion->estado.'" data-type="text" data-original-title="Estado" data-pk="'.$cotizacion->id_cotizacion.'"></a> <a href="#" class="campo" data-campo="ciudad" data-value="'.$cotizacion->ciudad.'" data-type="text" data-original-title="Ciudad" data-pk="'.$cotizacion->id_cotizacion.'"></a>';
            })
            ->addColumn('integrantes', function($cotizacion)
            {
                $cotizacion->integrantes = json_decode($cotizacion->integrantes);
                return count($cotizacion->integrantes);
            })
            ->addColumn('agente', function($cotizacion)
            {
                return '<a href="#" class="campo" data-campo="id_agente" data-value="'.$cotizacion->id_agente.'" data-type="select" data-source="'.\URL::to('admingm/listasJson/agentesJson').'" data-original-title="Agente" data-pk="'.$cotizacion->id_cotizacion.'"></a>';
                $nombre = $cotizacion->agente->nombre;
                if($cotizacion->agente->id_usuario == -1){
                    $nombre .= ' '.$cotizacion->agente->apellido_paterno;
                }
                return $nombre;
            })
            ->addColumn('estatus', function($cotizacion)
            {
                return '<a href="#" class="campo" data-campo="estatus" data-value="'.$cotizacion->estatus.'" data-source="'.\URL::to('admingm/listasJson/cotizacionEstatusJson').'" data-type="select" data-original-title="Estatus" data-pk="'.$cotizacion->id_cotizacion.'"></a>';
            })
            ->addColumn('opciones', function($cotizacion)
            {
                $verCotizacion = '';
                if($cotizacion->id_agente == \Auth::user()->id_usuario || $cotizacion->id_agente == -1){
                    $verCotizacion = '<a href="'.\URL::to('/admingm/cotizacion/verCotizacion/'.$cotizacion->id_cotizacion.'/'.$cotizacion->secret).'" data-toggle="tooltip" class="delete-row tooltips" data-original-title="Ver cotización"><i class="fa fa-eye"></i></a> ';
                }else{
                    $verCotizacion = '<a href="'.\URL::to('/admingm/cotizacion/verCotizacionRapida/'.$cotizacion->id_cotizacion).'" data-toggle="tooltip" class="delete-row tooltips" data-original-title="Ver cotización"><i class="fa fa-eye"></i></a> ';
                }
                return $verCotizacion.'<a href="'.\URL::to('/admingm/cotizacion/altaCotizacion/'.$cotizacion->id_cotizacion).'" data-toggle="tooltip" class="delete-row tooltips" data-original-title="Editar"><i class="fa fa-edit"></i></a> 
                        <a href="#" data-toggle="tooltip" class="delete-row tooltips eliminarCotizacion" data-idCotizacion="'.$cotizacion->id_cotizacion.'" data-estatus="'.$cotizacion->estatus.'" data-original-title="Eliminar"><i class="fa fa-trash-o"></i></a>';
            })
            ->searchColumns('id_cotizacion', 'nombre', 'contacto', 'ubicacion', 'integrantes')
            ->orderColumns('id_cotizacion', 'nombre', 'contacto', 'ubicacion', 'integrantes')
            ->make();
    }

    public function misPrioridades(){
        $misPrioridades = \Auth::user()->cotizacionPrioridad;
        \View::share('misPrioridades', $misPrioridades);

        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/x-editable/dist/bootstrap3-editable/js/bootstrap-editable.min");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/js/x-editable/dist/bootstrap3-editable/css/bootstrap-editable");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/css/style.datatables");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/jquery.dataTables.min");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/dataTables/dataTables.bootstrap");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/dataTables/dataTables.responsive");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/select2.min");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/css/select2");
        \View::share('scripts', $this->scripts);
        
        $this->layout->content = \View::make('backend.'.$this->ruta);
    }

    public function prioridadCotizacion(){
        $respuesta = array(
                        "status" => "success",
                        "titulo" => "Cotización",
                        "mensaje" => "Ocurrio un error al tratar de obtener la siguiente cotización",
                        "posicion" => "stack_bar_bottom",
                        "tipo" => "success",
                        "idCotizacionSiguiente" => -1
                    );
        $idCotizacion = \Input::get('idCotizacion');
        if($idCotizacion > 0){
            $cotizacionDatos = \Cotizacion::find($idCotizacion);
            if($cotizacionDatos){
                if(\Input::get('prioridad') == 'true'){
                    $cotizacionDatos->prioridad()->attach(\Auth::user()->id_usuario);
                    $respuesta['mensaje'] = 'Cotización marcada de prioridad';
                }else{
                    $cotizacionDatos->prioridad()->detach(\Auth::user()->id_usuario);
                    $respuesta['mensaje'] = 'Cotización desmarcada de prioridad';
                    /*
                    $idCotizacionSiguiente = \Cotizacion::siguienteCotizacionAgente();
                    if($idCotizacionSiguiente > 0){
                        $respuesta['idCotizacionSiguiente'] = $idCotizacionSiguiente;
                    }
                    */
                }
            }
        }
        return json_encode($respuesta);
    }

    public function actualizarIntegrantes(){
        $idCotizacion = \Input::get('idCotizacion');
        $integrantes = \Input::get('integrantes');
        $integrantes[] = 1;

        $respuesta = array(
                        "status" => "invalid",
                        "mensaje" => "Ocurrio un error al tratar de guardar el formulario",
                        "posicion" => "stack_bar_bottom",
                        "tipo" => "error",
                        "idCotizacion" => -1,
                        "secret" => "",
                    );
        $cotizacion = \Cotizacion::find($idCotizacion);
        if($cotizacion && count($integrantes) > 0){
            $nombre = \Input::get('nombres');
            $sexo = \Input::get('sexos');
            $edad = \Input::get('edades');
            $integrantesLista = array();
            $mapfreIntegrantes = array();
            for($x=1;$x<=10;$x++){
	            foreach($integrantes AS $integrante){
	            	if($integrante==$x){
	            		$titulo = (($integrante == 1) ? 'Titular' : (($integrante == 2) ? 'Conyugue' : 'Hijo(a)'));
	                	$integrantesLista[] = array(
	                                    'titular' => (($integrante == 1) ? 1 : 0),
	                                    'titulo' => $titulo,
	                                    'nombre' => $nombre[$integrante - 1],
	                                    'sexo' => $sexo[$integrante - 1],
	                                    'edad' => $edad[$integrante - 1],
	                                );
	                	
	                	$estado = \Estado::where('clave', '=', $cotizacion->estado)->first();
	                	$parentesco = \Parentesco::where('parentesco', '=', $titulo)->first();
						if($sexo[$integrante - 1]=="m"){
							$sex = "Masculino";
							$idSexo = 1;
						}
						else{
							$sex = "Femenino";
							$idSexo = 0;
						}
	                	$mapfreIntegrantes[] = array(
	                		"nombre" => $nombre[$integrante - 1],
							"id_parentesco" => $parentesco->clave_mapfre,
							"parentesco" => $parentesco->parentesco,
							"id_sexo" => $idSexo,
							"sexo" => $sex,
							"edad" => $edad[$integrante - 1]
	                	);
					}
	            }
			}
            if(count($integrantesLista) > 0){
                $cotizacion->integrantes = json_encode($integrantesLista);
                if($cotizacion->save()){
                	\DB::table('recotizaciones_mapfre')
                		->where('id_cotizacion', '=', $idCotizacion)
                		->delete();
                	
                	$cotizador = new \Cotizador($cotizacion, 'sa', 'db');
					$mapfreCotizacion = $cotizador::mapfreCotizacion($mapfreIntegrantes, $estado);
					$cotizacion->mapfre_xml = $mapfreCotizacion["xml"];
					$cotizacion->mapfre_numero = $mapfreCotizacion["cotizacion"]["xml"]["num_solicitud"];
					$cotizacion->mapfre_respuesta = json_encode($mapfreCotizacion["cotizacion"]);
					$cotizacion->save();
                	
                    $respuesta["status"] = "success";
                    $respuesta["tipo"] = "success";
                    $respuesta["mensaje"] = "Integrantes actualizados correctamente";
                }
            }
        }
        return json_encode($respuesta);
    }

    public function seguimientoRealizado(){
        $respuesta = array(
                        "status" => "success",
                        "titulo" => "Seguimiento",
                        "mensaje" => "Ocurrio un error al tratar de asignar el estatus del seguimiento",
                        "posicion" => "stack_bar_bottom",
                        "tipo" => "success",
                        "estatus" => -1,
                    );
        $idSeguimiento = \Input::get('idSeguimiento');
        $estatus = \Input::get('estatus');
        if($idSeguimiento > 0){
            $seguimientoDatos = \Cotizacionseguimiento::find($idSeguimiento);
            if($seguimientoDatos){
                $seguimientoDatos->realizado = $estatus;
                if($seguimientoDatos->save()){
                    $respuesta['status'] = 'success';
                    $respuesta['tipo'] = 'success';
                    $respuesta['estatus'] = $seguimientoDatos->realizado;
                }
            }
        }
        return json_encode($respuesta);
    }
    
    public function siguienteCotizacion(){
        $respuesta = array(
                        "status" => "success",
                        "titulo" => "Cotización",
                        "mensaje" => "Ocurrio un error al tratar de obtener la siguiente cotización",
                        "posicion" => "stack_bar_bottom",
                        "tipo" => "success",
                        "idCotizacionSiguiente" => -1
                    );
        $idCotizacionSiguiente = \Cotizacion::siguienteCotizacionAgente();
        if($idCotizacionSiguiente > 0){
            $respuesta['idCotizacionSiguiente'] = $idCotizacionSiguiente;
        }else{
            $respuesta['mensaje'] = 'No hay ninguna cotización pendiente';
        }
        return json_encode($respuesta);
    }
    
    public function enviarCotizacinEmail(){
        $idCotizacion = \Input::get('idCotizacionEmail');
        $secret = \Input::get('secret');
        $sa = \Input::get('sa');
        $ded = \Input::get('ded');
        $mensaje = \Input::get('mensaje');
        
        //$cotizacionDatos = \Cotizacion::where('id_cotizacion', '=', $idCotizacion)->where('secret', '=', $secret)->first();
        
        $respuesta = array(
                        "status" => "invalid",
                        "titulo" => "Enviar cotiazión por e-mail",
                        "mensaje" => "Ocurrio un error al tratar de enviar la cotiazión por Correo electrónico",
                        "posicion" => "stack_bar_bottom",
                        "tipo" => "error",
                    );
        $cotizacionDatos = \Cotizacion::where('id_cotizacion', '=', $idCotizacion)->first();
        if($cotizacionDatos){
            $cotizacionDatos->integrantes = json_decode($cotizacionDatos->integrantes);
            $pdf = self::generarCotizacionPdf($cotizacionDatos, false, $sa, $ded);
            /*
            $wkhtmltopdf = \App::make('snappy.pdf');
            $pdf = "tmp/cotizacion_$cotizacionDatos->id_cotizacion.pdf";
            $pdf = str_replace(" ","_" ,$pdf );
            $wkhtmltopdf->generate('http://segurodegastosmedicosmayores.mx/cotizacion/verCotizacionPDF/'.$idCotizacion.'/'.$cotizacionDatos->secret.'/'.$sa.'/'.$ded, $pdf);
            */
            if(file_exists($pdf)){
                $encabezado = \Cotizaciontexto::where('texto_seccion', '=', 'texto_correo_encabezado')->where('id_dominio', '=', $cotizacionDatos->id_dominio)->first();
				$cuerpo = \Cotizaciontexto::where('texto_seccion', '=', 'texto_correo_cuerpo')->where('id_dominio', '=', $cotizacionDatos->id_dominio)->first();
				//$pie = \Cotizaciontexto::where('texto_seccion', '=', 'texto_correo_pie')->first();
				$pie = \Cotizaciontexto::where('texto_seccion', '=', 'texto_correo_pie')
						->whereIn('id_usuario', array(-1, \Auth::user()->id_usuario))
						->where('id_dominio', '=', $cotizacionDatos->id_dominio)
						->orderBy('id_usuario', 'desc')
						->first();
                $cotizacionDatos->pdf = $pdf;
                $cotizacionDatos->para = \Input::get('para');
                $datosPlantilla = array(
                                    'nombre' => $cotizacionDatos->nombre,
                                    'e_mail' => $cotizacionDatos->e_mail,
                                    'id_cotizacion' => $cotizacionDatos->id_cotizacion,
                                    'secret' => $cotizacionDatos->secret,
                                    'encabezado' => str_replace('{{nombre}}', $cotizacionDatos->nombre, $encabezado->texto_pdf),
                                    'cuerpo' => $cuerpo->texto_pdf,
                                    'pie' =>  $pie->texto_pdf,
                                    'mensaje' => $mensaje,
                                    'signature' => '',
                                );
                /*try{
                    //if(\Auth::check() && file_exists(public_path().'/backend/images/signature/'.\Auth::user()->id_usuario.'.jpg')){
                    //    $datosPlantilla['signature'] = \HTML::image('/backend/images/signature/'.\Auth::user()->id_usuario.'.jpg');
                    //}
                    \Mail::send('plantillas.correo.enviarCotizacionCorreo', $datosPlantilla, function($message) use ($cotizacionDatos){
                        //$message->from('info@segurodegastosmedicosmayores.mx', 'Seguro de Gastos Médicos Mayores');
                        $message->from($cotizacionDatos->dominio()->first()->email, $cotizacionDatos->dominio()->first()->sender);
                        //$message->to($cotizacionDatos->e_mail, $cotizacionDatos->nombre);
                        foreach($cotizacionDatos->para AS $para){
                            //$message->to($para, $cotizacionDatos->nombre);
                            $message->to($para);
                        }
                        $message->cc('info@segurodegastosmedicosmayores.mx', 'Cotizaciones');
                        $message->subject('Cotización de Gastos Médicos Mayores - '.$cotizacionDatos->id_cotizacion);
                        if(file_exists($cotizacionDatos->pdf)){
                            $message->attach($cotizacionDatos->pdf, ['as' => 'cotizacion-'.$cotizacionDatos->id_cotizacion.'.pdf']);
                        }
                    });
                }catch(Exception $e){
                    $respuesta["mensaje"] = "22-" . $e->getMessage();
                    return json_encode($respuesta);
                }*/
                
                try{
                	\ALTMailer::mail('plantillas.correo.enviarCotizacionCorreo', $datosPlantilla, $cotizacionDatos, 'info@segurodegastosmedicosmayores.mx', 'Seguro de Gastos Médicos Mayores');
				}
				catch(Exception $e){
                    $respuesta["mensaje"] = "22-" . $e->getMessage();
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
        $cotizacion = new \Cotizador($cotizacionDatos, $sa, $ded);
        if(is_null($cotizacionDatos->mapfre_numero))
        	$cotizacion::cotizar();
        else
        	$cotizacion::cotizarWS();
        
        $mpdf = new \mPDF('', 'Letter', '', '', 20,20,20,20,5,5);
        $bienvenida = \Cotizaciontexto::where('texto_seccion', '=', 'saludo_bienvenida')->where('id_dominio', '=', $cotizacionDatos->id_dominio)->first()->texto_pdf;
        \View::share('bienvenida', $bienvenida);
        $beneficios = \Cotizaciontexto::where('texto_seccion', '=', 'beneficios_protecto')->where('id_dominio', '=', $cotizacionDatos->id_dominio)->first()->texto_pdf;
        \View::share('beneficios', $beneficios);
        \View::share('cotizacionDatos', $cotizacionDatos);
        \View::share('cotizacion', $cotizacion);
        if(is_null($cotizacionDatos->mapfre_numero))
        	$aseguradoras = \Aseguradora::where('activa', '=', 1)->orderBy('orden')->get();
        else{
			if($cotizacionDatos->estado=="Jalisco")
	        	$aseguradoras = \Aseguradora::where('activa', '=', 1)->orderBy('orden')->get();
	        else
	        	$aseguradoras = \Aseguradora::where('activa', '=', 1)->where('aseguradora', '=', 'mapfre')->orderBy('orden')->get();
		}
        \View::share('aseguradoras', $aseguradoras);
        if(is_null($cotizacionDatos->mapfre_numero))
        	$html = \View::make('plantillas.correo.cotizacionPdf');
        else
        	$html = \View::make('plantillas.correo.cotizacionPdfWS');
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

    public function agregarSeguimiento(){
        $datos = \Input::all();
        $cotizacionDatos = \Cotizacion::find($datos['idCotizacion']);
        $respuesta = array(
                        "status" => "success",
                        "titulo" => "Seguimiento",
                        "mensaje" => "Ocurrio un error al tratar de agregar el seguimiento",
                        "posicion" => "stack_bar_bottom",
                        "tipo" => "success",
                        "idSeguimiento" => -1,
                        "idCotizacionSiguiente" => -1,
                        "secret" => ''
                    );
        if($cotizacionDatos){
            $seguimiento = new \Cotizacionseguimiento;
            $seguimiento->id_cotizacion = $cotizacionDatos->id_cotizacion;
            $seguimiento->notas = $datos['notas'];
            $estatusDatos = \Cotizacionestatus::find($datos['cotizacionEstatus']);
            $seguimiento->notas .= ((strlen($seguimiento->notas) > 0) ? ' | ' : '').'Marcado como '.$estatusDatos->estatus.' el día '.date('Y-m-d H:i').' por '.\Auth::user()->nombre;
            $cotizacionDatos->id_agente = -1;
            switch($datos['cotizacionEstatus']){
                case 4:
                case 5:
                    $seguimiento->fecha_seguimiento = $datos['fechaProgramada'];
                break;
                case 6:
                    $seguimiento->fecha_seguimiento = $datos['fechaProgramada'];
                    $cotizacionDatos->id_agente = \Auth::user()->id_usuario;
                break;
                case 8:
                case 10:
                    $cotizacionDatos->id_agente = \Auth::user()->id_usuario;
                    $seguimiento->realizado = 1;
                break;
            }
            $cotizacionDatos->estatus = $datos['cotizacionEstatus'];
            \DB::beginTransaction();
            $cotizacionDatos->save();
            try{
                if($seguimiento->save()){
                    $cotizacionDatos->seguimientos()->where('id_seguimiento','<>',$seguimiento->id_seguimiento)->update(['realizado'=>1]);
                    $respuesta['idSeguimiento'] = $seguimiento->id_seguimiento;
                    $respuesta['status'] = 'success';
                    $respuesta['tipo'] = 'success';
                    $respuesta['mensaje'] = '';
                    $respuesta['idCotizacionSiguiente'] = \Cotizacion::siguienteCotizacionAgente();
                    $respuesta['secret'] = '';

                    \DB::commit();
                    if($cotizacionDatos->estatus > 6){
                        $cotizacionDatos->seguimientos()->update(['realizado'=>1]);
                        $cotizacionDatos->prioridad()->detach(\Auth::user()->id_usuario);
                    }
                }else{
                    \DB::rollback();
                }
            }catch(Exception $e){
                $respuesta['mensaje'] = $e->getMessage();
            }
        }
        return json_encode($respuesta);
    }

    public function verCotizacionRapida($idCotizacion){
        $cotizacionDatos = \Cotizacion::where('id_cotizacion', '=', $idCotizacion)->first();
        if($cotizacionDatos && ($cotizacionDatos->id_agente != -1 || $cotizacionDatos->id_agente != \Auth::user()->id_usuario)){
            $cotizacionDatos->integrantes = json_decode($cotizacionDatos->integrantes);
            \View::share('cotizacionDatos', $cotizacionDatos);

            $cotizacion = new \Cotizador($cotizacionDatos, 'sa', 'da');
            $cotizacion::cotizar();
            \View::share('tablaDatosDASA', $cotizacion::tablaDatos(true, false));

            $cotizacion = new \Cotizador($cotizacionDatos, 'sa', 'db');
            $cotizacion::cotizar();
            \View::share('tablaDatosDASB', $cotizacion::tablaDatos(true, false));

            $cotizacion = new \Cotizador($cotizacionDatos, 'sb', 'da');
            $cotizacion::cotizar();
            \View::share('tablaDatosDBSA', $cotizacion::tablaDatos(true, false));
            
            $cotizacion = new \Cotizador($cotizacionDatos, 'sb', 'db');
            $cotizacion::cotizar();
            \View::share('tablaIntegrantes', $cotizacion::tablaIntegrantes(true));
            \View::share('tablaDatosDBSB', $cotizacion::tablaDatos(true, false));
            
            if(strlen($cotizacionDatos->paquete) > 0){
                $paqueteDatos = \Paquete::where('paquete_campo', '=', $cotizacionDatos->paquete)->first();
                \View::share('paqueteDatos', $paqueteDatos);
            }
            $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/jquery.validate.min");
            $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/x-editable/dist/bootstrap3-editable/js/bootstrap-editable.min");
            $this->scripts[] = array("tipo" => "css", "archivo" => "backend/js/x-editable/dist/bootstrap3-editable/css/bootstrap-editable");
            
            $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/admin-dock/dockmodal");
            $this->scripts[] = array("tipo" => "css", "archivo" => "backend/js/admin-dock/dockmodal");
            $this->scripts[] = array("tipo" => "css", "archivo" => "backend/js/summernote/summernote");
            $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/summernote/summernote.min");
            
            $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/tagsinput/tagsinput.min");
            $this->scripts[] = array("tipo" => "css", "archivo" => "backend/js/tagmanager/tagmanager");
            $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/tagmanager/tagmanager");

            $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/jquery.validate.min");
            $this->scripts[] = array("tipo" => "css", "archivo" => "backend/css/bootstrap-timepicker.min");
            $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/bootstrap-timepicker.min");
            asort($this->scripts);

            \View::share('scripts', $this->scripts);
            $this->layout->content = \View::make('backend.'.$this->ruta);
        }else{
            return \Redirect::to('/admingm/cotizacion/consultaCotizaciones');
        }
    }
    
    public function verCotizacion($idCotizacion, $secret = null){
        //$cotizacionDatos = \Cotizacion::where('id_cotizacion', '=', $idCotizacion)->where('secret', '=', $secret)->first();
        $cotizacionDatos = \Cotizacion::where('id_cotizacion', '=', $idCotizacion)->first();
        //if($cotizacionDatos && ($cotizacionDatos->id_agente == -1 || $cotizacionDatos->id_agente == \Auth::user()->id_usuario)){
        	
        	if($cotizacionDatos->cotizar_para > 0)
        		return \Redirect::to('/admingm/cotizacion/verCotizacionNuevo/' . $idCotizacion . '/' . $secret);
        	
            if($cotizacionDatos->estatus == 1 || $cotizacionDatos->estatus == 2){
                $cotizacionDatos->estatus = 3;
            }
            if($cotizacionDatos->id_agente == -1){
                $cotizacionDatos->id_agente = \Auth::user()->id_usuario;
            }
            $cotizacionDatos->save();
            if(is_null($cotizacionDatos->mapfre_numero)){
            	$integrantes = json_decode($cotizacionDatos->integrantes);
				$estado = \Estado::where('clave', '=', $cotizacionDatos->estado)->first();
				foreach($integrantes as $i){
					$parentesco = \Parentesco::where('parentesco', '=', $i->titulo)->first();
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
				$cotizacion = new \Cotizador($cotizacionDatos, 'sa', 'db');
				$mapfreCotizacion = $cotizacion::mapfreCotizacion($mapfreIntegrantes, $estado);
				$cotizacionDatos->mapfre_xml = $mapfreCotizacion["xml"];
				$cotizacionDatos->mapfre_numero = $mapfreCotizacion["cotizacion"]["xml"]["num_solicitud"];
				$cotizacionDatos->mapfre_respuesta = json_encode($mapfreCotizacion["cotizacion"]);
				$cotizacionDatos->save();
				
				//$this->recotizarWS($cotizacionDatos, $mapfreIntegrantes, $estado, $mapfreCotizacion["cotizacion"], "sada", 1, "esencial");
				//$this->recotizarWS($cotizacionDatos, $mapfreIntegrantes, $estado, $mapfreCotizacion["cotizacion"], "sada", 2, "optima");
				//$this->recotizarWS($cotizacionDatos, $mapfreIntegrantes, $estado, $mapfreCotizacion["cotizacion"], "sadb", 1, "esencial");
				//$this->recotizarWS($cotizacionDatos, $mapfreIntegrantes, $estado, $mapfreCotizacion["cotizacion"], "sadb", 2, "optima");
				//$this->recotizarWS($cotizacionDatos, $mapfreIntegrantes, $estado, $mapfreCotizacion["cotizacion"], "sbda", 1, "esencial");
				//$this->recotizarWS($cotizacionDatos, $mapfreIntegrantes, $estado, $mapfreCotizacion["cotizacion"], "sbda", 2, "optima");
				//$this->recotizarWS($cotizacionDatos, $mapfreIntegrantes, $estado, $mapfreCotizacion["cotizacion"], "sbdb", 1, "esencial");
				//$this->recotizarWS($cotizacionDatos, $mapfreIntegrantes, $estado, $mapfreCotizacion["cotizacion"], "sbdb", 2, "optima");
			}
            $cotizacionDatos->integrantes = json_decode($cotizacionDatos->integrantes);
            \View::share('cotizacionDatos', $cotizacionDatos);

            $cotizacion = new \Cotizador($cotizacionDatos, 'sa', 'da');
            if(is_null($cotizacionDatos->mapfre_numero)){
            	$cotizacion::cotizar();
            	\View::share('tablaDatosDASA', $cotizacion::tablaDatos(true, false));
			}
            else{
            	$cotizacion::cotizarWS();
            	\View::share('tablaDatosDASA', $cotizacion::tablaDatosWS(true, false));
			}
			
            $cotizacion = new \Cotizador($cotizacionDatos, 'sa', 'db');
            if(is_null($cotizacionDatos->mapfre_numero)){
            	$cotizacion::cotizar();
            	\View::share('tablaDatosDASB', $cotizacion::tablaDatos(true, false));
			}
            else{
            	$cotizacion::cotizarWS();
            	\View::share('tablaDatosDASB', $cotizacion::tablaDatosWS(true, false));
			}

            $cotizacion = new \Cotizador($cotizacionDatos, 'sb', 'da');
            if(is_null($cotizacionDatos->mapfre_numero)){
            	$cotizacion::cotizar();
            	\View::share('tablaDatosDBSA', $cotizacion::tablaDatos(true, false));
			}
            else{
				$cotizacion::cotizarWS();
            	\View::share('tablaDatosDBSA', $cotizacion::tablaDatosWS(true, false));
			}
            
            $cotizacion = new \Cotizador($cotizacionDatos, 'sb', 'db');
            if(is_null($cotizacionDatos->mapfre_numero)){
				$cotizacion::cotizar();
            	\View::share('tablaDatosDBSB', $cotizacion::tablaDatos(true, false));
			}
            else{
				$cotizacion::cotizarWS();
            	\View::share('tablaDatosDBSB', $cotizacion::tablaDatosWS(true, false));
			}
            
            \View::share('tablaIntegrantes', $cotizacion::tablaIntegrantes(true));
            $integrantesOrden = array();
            $n = 2;
            foreach($cotizacionDatos->integrantes AS $integrante){
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
            $cotizacionDatos->integrantes = $integrantesOrden;
            $tablaIntegrantesEditar = '';
            for($i=1;$i<=10;$i++){
                $titulo = '';
                switch($i){
                    case 1:
                        $titulo = 'Titular';
                    break;
                    case 2:
                        $titulo = 'Conyugue';
                    break;
                    default:
                        $titulo = 'Hijo(a)';
                    break;
                }
                $tablaIntegrantesEditar .= '<tr>
                                        <td class="alignCenter alignVerticalMiddle">'.$i.'</td>
                                        <td class="alignCenter alignVerticalMiddle">'.$titulo.'</td>
                                        <td class="alignCenter alignVerticalMiddle">
                                            <div class="ckbox ckbox-primary">
                                                <input type="checkbox" data-id="'.$i.'" id="integrantes_'.$i.'" '.(($i == 1) ? 'checked="checked" disabled=""' : ((isset($cotizacionDatos->integrantes[$i-1])) ? 'checked="checked"' : '') ).' name="integrantes[]" value="'.$i.'" />
                                                <label for="integrantes_'.$i.'"></label>
                                            </div>
                                        </td>
                                        <td class="alignVerticalMiddle"> <input type="text" data-id="'.$i.'" id="nombres_'.$i.'" name="nombres[]" placeholder="Nombre del '.$titulo.'" class="form-control input-sm nombres" '.((isset($cotizacionDatos->integrantes[$i-1])) ? 'value="'.$cotizacionDatos->integrantes[$i-1]->nombre.'"' : '').'> </td>
                                        <td class="alignVerticalMiddle">
                                            <select class="input_bg sexos" style="width: 100%;" data-id="'.$i.'" id="sexos_'.$i.'" name="sexos[]">
                                                <option value="-1">Sexo</option>
                                                <option value="m" '.((isset($cotizacionDatos->integrantes[$i-1]) && $cotizacionDatos->integrantes[$i-1]->sexo == 'm') ? 'selected' : '').'>Hombre</option>
                                                <option value="f" '.((isset($cotizacionDatos->integrantes[$i-1]) && $cotizacionDatos->integrantes[$i-1]->sexo == 'f') ? 'selected' : '').'>Mujer</option>
                                            </select>
                                        </td>
                                        <td class="alignVerticalMiddle"> <input type="text" class="edades" id="edades_'.$i.'" name="edades[]" data-id="'.$i.'" step="1" min="'.(($i == 1 || $i == 2) ? 18 : 0 ).'" max="69" placeholder="Edad" '.((isset($cotizacionDatos->integrantes[$i-1])) ? 'value="'.$cotizacionDatos->integrantes[$i-1]->edad.'"' : '').' digits="true" data-msg-digits="Solo números" data-msg-min="Edad mínima '.(($i == 1 || $i == 2) ? 18 : 0 ).' años" data-msg-max="Edad máxima 69 años" /> </td>
                                    </tr>';
            }
            \View::share('tablaIntegrantesEditar', $tablaIntegrantesEditar);
            if(strlen($cotizacionDatos->paquete) > 0){
                $paqueteDatos = \Paquete::where('paquete_campo', '=', $cotizacionDatos->paquete)->first();
                \View::share('paqueteDatos', $paqueteDatos);
            }
            
            // Consulta el permiso "asignarAgente"
            $asignarAgente = \DB::table("modulos_permisos")->select("acceso")->where("id_modulo", "=", 33)->where("id_usuario", "=", $cotizacionDatos->id_agente)->get(); 
            \View::share('asignarAgente', $asignarAgente);
            
            $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/x-editable/dist/bootstrap3-editable/js/bootstrap-editable.min");
            $this->scripts[] = array("tipo" => "css", "archivo" => "backend/js/x-editable/dist/bootstrap3-editable/css/bootstrap-editable");
            
            $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/admin-dock/dockmodal");
            $this->scripts[] = array("tipo" => "css", "archivo" => "backend/js/admin-dock/dockmodal");
            $this->scripts[] = array("tipo" => "css", "archivo" => "backend/js/summernote/summernote");
            $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/summernote/summernote.min");
            
            $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/tagsinput/tagsinput.min");
            $this->scripts[] = array("tipo" => "css", "archivo" => "backend/js/tagmanager/tagmanager");
            $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/tagmanager/tagmanager");

            $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/jquery.validate.min");
            $this->scripts[] = array("tipo" => "css", "archivo" => "backend/css/bootstrap-timepicker.min");
            $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/bootstrap-timepicker.min");
            $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/jquery.mousewheel");
            $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/select2.min");
            $this->scripts[] = array("tipo" => "css", "archivo" => "backend/css/select2");
            asort($this->scripts);

            $textosRespuestaCorreo = \CorreoRespuesta::where('id_dominio', '=', $cotizacionDatos->id_dominio)->orderBy('titulo')->get();
            \View::share('textosRespuestaCorreo', $textosRespuestaCorreo);

            \View::share('scripts', $this->scripts);
            $this->layout->content = \View::make('backend.'.$this->ruta);
        //}else{
        //    return \Redirect::to('/admingm/cotizacion/consultaCotizaciones');
        //}
    }

    public function agregarCotizacion(){
        $cotizacionDatos = \Input::except('nombres', 'integrantes', 'sexos', 'edades', 'idCotizacion');
        $idCotizacion = \Input::get('idCotizacion');
        $integrantes = \Input::get('integrantes');
        $integrantes[] = 1;

        $respuesta = array(
                        "status" => "invalid",
                        "mensaje" => "Ocurrio un error al tratar de guardar el formulario",
                        "posicion" => "stack_bar_bottom",
                        "tipo" => "error",
                        "idCotizacion" => -1,
                        "secret" => "",
                    );
        if(count($integrantes) > 0){
            $nombre = \Input::get('nombres');
            $sexo = \Input::get('sexos');
            $edad = \Input::get('edades');
            $integrantesLista = array();
            for($x=1;$x<=10;$x++){
	            foreach($integrantes AS $integrante){
	            	if($integrante==$x){
	                	$integrantesLista[] = array(
	                                    'titular' => (($integrante == 1) ? 1 : 0),
	                                    'titulo' => (($integrante == 1) ? 'Titular' : (($integrante == 2) ? 'Conyugue' : 'Hijo(a)') ),
	                                    'nombre' => $nombre[$integrante - 1],
	                                    'sexo' => $sexo[$integrante - 1],
	                                    'edad' => $edad[$integrante - 1],
	                                );
					}
	            }
			}
            if(count($integrantesLista) > 0){
                //$cotizacion = new \Cotizacion($cotizacionDatos);
                $cotizacion = \Cotizacion::firstOrNew(array('id_cotizacion' => $idCotizacion));
                $cotizacion->id_dominio = 1;
                foreach ($cotizacionDatos AS $key => $value) {
                    $cotizacion->$key = $value;
                }
                $cotizacion->integrantes = json_encode($integrantesLista);
                if(strlen($cotizacion->secret) == 0){
                    $cotizacion->secret = str_random(15);
                }
                $referer = parse_url($_SERVER["HTTP_REFERER"]);
				if(isset($referer["path"]))
					$cotizacion->ruta = $referer["path"];
                if($cotizacion->save()){
                    $respuesta["status"] = "success";
                    $respuesta["tipo"] = "success";
                    $respuesta["mensaje"] = "Cotización guardada correctamente";
                    $respuesta["idCotizacion"] = $cotizacion->id_cotizacion;
                    $respuesta["secret"] = $cotizacion->secret;
                }
            }
        }
        return json_encode($respuesta);
    }
	
    public function altaCotizacion($idCotizacion = -1)
    {
        if($idCotizacion > 0){
            $cotizacionDatos = \Cotizacion::find($idCotizacion);
            if($cotizacionDatos){
                $cotizacionDatos->integrantes = json_decode($cotizacionDatos->integrantes);
            }
            $integrantesOrden = array();
            $n = 2;
            foreach($cotizacionDatos->integrantes AS $integrante){
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
            $cotizacionDatos->integrantes = $integrantesOrden;
            \View::share('cotizacionDatos', $cotizacionDatos);
        }
        $tablaIntegrantes = '';
        for($i=1;$i<=10;$i++){
            $titulo = '';
            switch($i){
                case 1:
                    $titulo = 'Titular';
                break;
                case 2:
                    $titulo = 'Conyugue';
                break;
                default:
                    $titulo = 'Hijo(a)';
                break;
            }
            $tablaIntegrantes .= '<tr>
                                    <td class="alignCenter alignVerticalMiddle">'.$i.'</td>
                                    <td class="alignCenter alignVerticalMiddle">'.$titulo.'</td>
                                    <td class="alignCenter alignVerticalMiddle">
                                        <div class="ckbox ckbox-primary">
                                            <input type="checkbox" data-id="'.$i.'" id="integrantes_'.$i.'" '.(($i == 1) ? 'checked="checked" disabled=""' : ((isset($cotizacionDatos->integrantes[$i-1])) ? 'checked="checked"' : '') ).' name="integrantes[]" value="'.$i.'" />
                                            <label for="integrantes_'.$i.'"></label>
                                        </div>
                                    </td>
                                    <td class="alignVerticalMiddle"> <input type="text" data-id="'.$i.'" id="nombres_'.$i.'" name="nombres[]" placeholder="Nombre del '.$titulo.'" class="form-control input-sm nombres" '.((isset($cotizacionDatos->integrantes[$i-1])) ? 'value="'.$cotizacionDatos->integrantes[$i-1]->nombre.'"' : '').'> </td>
                                    <td class="alignVerticalMiddle">
                                        <select class="input_bg sexos" style="width: 100%;" data-id="'.$i.'" id="sexos_'.$i.'" name="sexos[]">
                                            <option value="-1">Sexo</option>
                                            <option value="m" '.((isset($cotizacionDatos->integrantes[$i-1]) && $cotizacionDatos->integrantes[$i-1]->sexo == 'm') ? 'selected' : '').'>Hombre</option>
                                            <option value="f" '.((isset($cotizacionDatos->integrantes[$i-1]) && $cotizacionDatos->integrantes[$i-1]->sexo == 'f') ? 'selected' : '').'>Mujer</option>
                                        </select>
                                    </td>
                                    <td class="alignVerticalMiddle"> <input type="text" class="edades" id="edades_'.$i.'" name="edades[]" data-id="'.$i.'" step="1" min="'.(($i == 1 || $i == 2) ? 18 : 0 ).'" max="69" placeholder="Edad" '.((isset($cotizacionDatos->integrantes[$i-1])) ? 'value="'.$cotizacionDatos->integrantes[$i-1]->edad.'"' : '').' digits="true" data-msg-digits="Solo números" data-msg-min="Edad mínima '.(($i == 1 || $i == 2) ? 18 : 0 ).' años" data-msg-max="Edad máxima 69 años" /> </td>
                                </tr>';
        }
        \View::share('tablaIntegrantes', $tablaIntegrantes);
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/jquery.validate.min");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/jquery.mousewheel");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/select2.min");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/css/select2");
        \View::share('scripts', $this->scripts);
        
        $this->layout->content = \View::make('backend.'.$this->ruta);
    }

    public function eliminarCotizacion(){
        $idCotizacion = \Input::get('idCotizacion');
        $cotizacionDatos = \Cotizacion::find($idCotizacion);
        $respuesta = array(
                        "status" => "invalid",
                        "titulo" => "Eliminar cotizacion",
                        "mensaje" => "Ocurrio un error al tratar de eliminar la cotizacion",
                        "posicion" => "stack_bar_bottom",
                        "tipo" => "error",
                    );
        if($cotizacionDatos->delete()){
            $respuesta['mensaje'] = 'Cotizacion eliminada correctamente';
            $respuesta['status'] = 'success';
            $respuesta['tipo'] = 'success';
        }
        return json_encode($respuesta);
    }

    public function actualizarCotizacionCampos(){
        $datos = \Input::all();
        $cotizacionDatos = \Cotizacion::find($datos["pk"]);
        $cotizacionDatos->$datos["campo"] = $datos["value"];
        
        if($cotizacionDatos){
            try{
                if($cotizacionDatos->save()){
                    return 1;
                }
            }catch(Exception $e){
                
            }
        }
        return 0;
    }

    public function getConsultaCotizaciones(){
        $estatus = \Input::get('estatus');
        $cotizaciones = '';
        switch ($estatus) {
            case 1:
                $cotizaciones = \Cotizacion::where('estatus', '=', 1)->where('forma_ingreso', '=', 2)->where('visto', '=', 1)->where('id_agente', '=', -1)->get();
            break;
            case 2:
                $cotizaciones = \Cotizacion::where('estatus', '=', 2)->get();
            break;
            case 5:
                $cotizaciones = \Cotizacion::where('estatus', '=', 5)->get();
            break;
            case 3:
                $cotizaciones = \Cotizacion::where('id_agente', '=', \Auth::user()->id_usuario)->where('estatus', '=', 3)->get();
            break;
            case 6:
                $cotizaciones = \Cotizacion::where('id_agente', '=', \Auth::user()->id_usuario)->where('estatus', '=', 6)->get();
            break;
            case 7:
                $estatusOtros = array(4,7,8,9,10,11);
                $cotizaciones = \Cotizacion::whereIn('estatus', $estatusOtros)->get();
            break;
            default:
                $cotizaciones = Cotizacion::all();
            break;
        }
        return \Datatable::collection($cotizaciones)
            ->showColumns('id_cotizacion')
            ->addColumn('nombre', function($cotizacion)
            {
                $fechaProgramada = '';
                if($cotizacion->estatus == 6){
                    $seguimiento = $cotizacion->seguimientos()->where('fecha_seguimiento', '>=', date('Y-m-d h:i:00'))->where('realizado', '=', -1)->orderBy('fecha_seguimiento')->first();
                    if($seguimiento){
                        $fechaProgramada = ' - <strong>Seguimiento programado: '.$seguimiento->fecha_seguimiento.'</strong>';
                    }
                }
                return '<a href="'.\URL::to('/admingm/cotizacion/verCotizacion/'.$cotizacion->id_cotizacion.'/'.$cotizacion->secret).'">'.$cotizacion->nombre.'</a><br>'.date('Y-m-d H:i', strtotime($cotizacion->fecha_registro)).$fechaProgramada;
            })
            ->addColumn('ingreso', function($cotizacion)
            {
                return (($cotizacion->forma_ingreso == -1) ? 'Agente' : 'Web');
            })
            ->addColumn('contacto', function($cotizacion)
            {
                return '<a href="#" class="campo" data-campo="telefono" data-value="'.$cotizacion->telefono.'" data-type="text" data-original-title="Teléfono" data-pk="'.$cotizacion->id_cotizacion.'"></a><br><a href="#" class="campo" data-campo="e_mail" data-value="'.$cotizacion->e_mail.'" data-type="text" data-original-title="Correo electrónico" data-pk="'.$cotizacion->id_cotizacion.'"></a>';
            })
            ->addColumn('ubicacion', function($cotizacion)
            {
                return '<a href="#" class="campo" data-campo="estado" data-value="'.$cotizacion->estado.'" data-type="text" data-original-title="Estado" data-pk="'.$cotizacion->id_cotizacion.'"></a> <a href="#" class="campo" data-campo="ciudad" data-value="'.$cotizacion->ciudad.'" data-type="text" data-original-title="Ciudad" data-pk="'.$cotizacion->id_cotizacion.'"></a>';
            })
            ->addColumn('integrantes', function($cotizacion)
            {
                $cotizacion->integrantes = json_decode($cotizacion->integrantes);
                return count($cotizacion->integrantes);
            })
            ->addColumn('agente', function($cotizacion)
            {
                return '<a href="#" class="campo" data-campo="id_agente" data-value="'.$cotizacion->id_agente.'" data-type="select" data-source="'.\URL::to('admingm/listasJson/agentesJson').'" data-original-title="Agente" data-pk="'.$cotizacion->id_cotizacion.'"></a>';
                //return $cotizacion->agente->nombre;
            })
            ->addColumn('estatus', function($cotizacion)
            {
                return '<a href="#" class="campo" data-campo="estatus" data-value="'.$cotizacion->estatus.'" data-source="'.\URL::to('admingm/listasJson/cotizacionEstatusJson').'" data-type="select" data-original-title="Estatus" data-pk="'.$cotizacion->id_cotizacion.'"></a>';
            })
            ->addColumn('opciones', function($cotizacion)
            {
                $verCotizacion = '';
                if($cotizacion->id_agente == \Auth::user()->id_usuario || $cotizacion->id_agente == -1){
                    $verCotizacion = '<a href="'.\URL::to('/admingm/cotizacion/verCotizacion/'.$cotizacion->id_cotizacion.'/'.$cotizacion->secret).'" data-toggle="tooltip" class="delete-row tooltips" data-original-title="Ver cotización"><i class="fa fa-eye"></i></a> ';
                }else{
                    $verCotizacion = '<a href="'.\URL::to('/admingm/cotizacion/verCotizacionRapida/'.$cotizacion->id_cotizacion).'" data-toggle="tooltip" class="delete-row tooltips" data-original-title="Ver cotización"><i class="fa fa-eye"></i></a> ';
                }
                //<a href="'.\URL::to('/admingm/cotizacion/altaCotizacion/'.$cotizacion->id_cotizacion).'" data-toggle="tooltip" class="delete-row tooltips" data-original-title="Editar"><i class="fa fa-edit"></i></a> 
                return $verCotizacion.'<a href="#" data-toggle="tooltip" class="delete-row tooltips eliminarCotizacion" data-idCotizacion="'.$cotizacion->id_cotizacion.'" data-estatus="'.$cotizacion->estatus.'" data-original-title="Eliminar"><i class="fa fa-trash-o"></i></a>';
            })
            ->searchColumns('id_cotizacion', 'nombre', 'contacto', 'ubicacion', 'integrantes')
            ->orderColumns('id_cotizacion', 'nombre', 'contacto', 'ubicacion', 'integrantes')
            ->make();
    }

	public function consultaCotizaciones()
    {
        //$this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/tagsinput/tagsinput.min.js");

        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/x-editable/dist/bootstrap3-editable/js/bootstrap-editable.min");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/js/x-editable/dist/bootstrap3-editable/css/bootstrap-editable");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/css/style.datatables");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/jquery.dataTables.min");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/dataTables/dataTables.bootstrap");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/dataTables/dataTables.responsive");
        \View::share('scripts', $this->scripts);
        
        $estatusTable[1] = 'contratar';
        $estatusTable[2] = 'espera';
        $estatusTable[3] = 'proceso';
        $estatusTable[6] = 'programadas';
        $estatusTable[7] = 'otros';
        \View::share('estatusTable', $estatusTable);

        $this->layout->content = \View::make('backend.'.$this->ruta);
    }

    public function getConsultaCotizacionesTodas(){
        $cotizaciones = \Cotizacion::where(function($query){
                                    $estatus = \Input::get('estatus');
                                    if($estatus > 0){
                                        $query->where('estatus', '=', $estatus);
                                    }
                                    $id_agente = \Input::get('id_agente');
                                    if($id_agente > -1){
                                        $query->where('id_agente', '=', $id_agente);
                                    }
                                })
                                ->get();
        return \Datatable::collection($cotizaciones)
            ->showColumns('id_cotizacion')
            ->addColumn('nombre', function($cotizacion)
            {
                $fechaProgramada = '';
                if($cotizacion->estatus == 6){
                    $seguimiento = $cotizacion->seguimientos()->where('fecha_seguimiento', '>=', date('Y-m-d h:i:00'))->where('realizado', '=', -1)->orderBy('fecha_seguimiento')->first();
                    if($seguimiento){
                        $fechaProgramada = ' - <strong>Seguimiento programado: '.$seguimiento->fecha_seguimiento.'</strong>';
                    }
                }
                return '<a href="'.\URL::to('/admingm/cotizacion/verCotizacion/'.$cotizacion->id_cotizacion.'/'.$cotizacion->secret).'">'.$cotizacion->nombre.'</a><br>'.date('Y-m-d H:i', strtotime($cotizacion->fecha_registro)).$fechaProgramada;
            })
            ->addColumn('ingreso', function($cotizacion)
            {
                return (($cotizacion->forma_ingreso == -1) ? 'Agente' : 'Web');
            })
            ->addColumn('contacto', function($cotizacion)
            {
                return '<a href="#" class="campo" data-campo="telefono" data-value="'.$cotizacion->telefono.'" data-type="text" data-original-title="Teléfono" data-pk="'.$cotizacion->id_cotizacion.'"></a><br><a href="#" class="campo" data-campo="e_mail" data-value="'.$cotizacion->e_mail.'" data-type="text" data-original-title="Correo electrónico" data-pk="'.$cotizacion->id_cotizacion.'"></a>';
            })
            ->addColumn('ubicacion', function($cotizacion)
            {
                return '<a href="#" class="campo" data-campo="estado" data-value="'.$cotizacion->estado.'" data-type="text" data-original-title="Estado" data-pk="'.$cotizacion->id_cotizacion.'"></a> <a href="#" class="campo" data-campo="ciudad" data-value="'.$cotizacion->ciudad.'" data-type="text" data-original-title="Ciudad" data-pk="'.$cotizacion->id_cotizacion.'"></a>';
            })
            ->addColumn('integrantes', function($cotizacion)
            {
                $cotizacion->integrantes = json_decode($cotizacion->integrantes);
                return count($cotizacion->integrantes);
            })
            ->addColumn('agente', function($cotizacion)
            {
                return '<a href="#" class="campo" data-campo="id_agente" data-value="'.$cotizacion->id_agente.'" data-type="select" data-source="'.\URL::to('admingm/listasJson/agentesJson').'" data-original-title="Agente" data-pk="'.$cotizacion->id_cotizacion.'"></a>';
                $nombre = $cotizacion->agente->nombre;
                if($cotizacion->agente->id_usuario == -1){
                    $nombre .= ' '.$cotizacion->agente->apellido_paterno;
                }
                return $nombre;
            })
            ->addColumn('estatus', function($cotizacion)
            {
                return '<a href="#" class="campo" data-campo="estatus" data-value="'.$cotizacion->estatus.'" data-source="'.\URL::to('admingm/listasJson/cotizacionEstatusJson').'" data-type="select" data-original-title="Estatus" data-pk="'.$cotizacion->id_cotizacion.'"></a>';
            })
            ->addColumn('opciones', function($cotizacion)
            {
                $verCotizacion = '';
                if($cotizacion->id_agente == \Auth::user()->id_usuario || $cotizacion->id_agente == -1){
                    $verCotizacion = '<a href="'.\URL::to('/admingm/cotizacion/verCotizacion/'.$cotizacion->id_cotizacion.'/'.$cotizacion->secret).'" data-toggle="tooltip" class="delete-row tooltips" data-original-title="Ver cotización"><i class="fa fa-eye"></i></a> ';
                }else{
                    $verCotizacion = '<a href="'.\URL::to('/admingm/cotizacion/verCotizacionRapida/'.$cotizacion->id_cotizacion).'" data-toggle="tooltip" class="delete-row tooltips" data-original-title="Ver cotización"><i class="fa fa-eye"></i></a> ';
                }
                return $verCotizacion.'<a href="'.\URL::to('/admingm/cotizacion/altaCotizacion/'.$cotizacion->id_cotizacion).'" data-toggle="tooltip" class="delete-row tooltips" data-original-title="Editar"><i class="fa fa-edit"></i></a> 
                        <a href="#" data-toggle="tooltip" class="delete-row tooltips eliminarCotizacion" data-idCotizacion="'.$cotizacion->id_cotizacion.'" data-estatus="'.$cotizacion->estatus.'" data-original-title="Eliminar"><i class="fa fa-trash-o"></i></a>';
            })
            ->searchColumns('id_cotizacion', 'nombre', 'contacto', 'ubicacion', 'integrantes')
            ->orderColumns('id_cotizacion', 'nombre', 'contacto', 'ubicacion', 'integrantes')
            ->make();
    }

    public function cotizacionesTodas()
    {
        $cotizacionEstatus = \Cotizacionestatus::all();
        \View::share('cotizacionEstatus', $cotizacionEstatus);

        $agentes = User::select('id_usuario', 'nombre', 'apellido_paterno', 'apellido_materno')->whereIn('id_usuario', \Cotizacion::select('id_agente')->groupBy('id_agente')->get()->toArray())->orderBy('id_usuario')->get();
        \View::share('agentes', $agentes);

        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/x-editable/dist/bootstrap3-editable/js/bootstrap-editable.min");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/js/x-editable/dist/bootstrap3-editable/css/bootstrap-editable");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/css/style.datatables");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/jquery.dataTables.min");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/dataTables/dataTables.bootstrap");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/dataTables/dataTables.responsive");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/select2.min");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/css/select2");
        \View::share('scripts', $this->scripts);
        
        $this->layout->content = \View::make('backend.'.$this->ruta);
    }

	public function getConsultaCotizacionesTodas2($idAgente = -1, $idEstatus = 0, $valor = ""){
		$cotizaciones = \Cotizacion::join('administradores', 'cotizaciones.id_agente', '=', 'administradores.id_usuario')
			->join('cotizaciones_estatus', 'cotizaciones_estatus.id_estatus', '=', 'cotizaciones.estatus')
			->select('cotizaciones.id_cotizacion', 'cotizaciones.nombre', 'cotizaciones.fecha_registro', 
				\DB::raw("case gm_cotizaciones.forma_ingreso when -1 then 'Agente' else 'Web' end as forma_ingreso"),
				'cotizaciones.telefono', 'cotizaciones.e_mail', 'cotizaciones.estado', 'cotizaciones.ciudad', 'cotizaciones.integrantes',
				\DB::raw("concat(gm_administradores.nombre, ' ', gm_administradores.apellido_paterno) as agente"), 
				'cotizaciones_estatus.estatus', 
				\DB::raw("ifnull((select fecha_seguimiento from gm_cotizaciones_seguimientos where id_cotizacion = gm_cotizaciones.id_cotizacion and fecha_seguimiento >= now() and realizado = -1 order by fecha_seguimiento limit 1), '') as fecha_seguimiento"),
				'cotizaciones.secret'
			)
			->where(function($query) use($idAgente, $idEstatus, $valor){
				if($idAgente>-1)
					$query->where("cotizaciones.id_agente", "=", $idAgente);
				if($idEstatus>0)
					$query->where("cotizaciones.estatus", "=", $idEstatus);
				if($valor!=""){
					$query->where(function($q) use($valor){
						$q->orWhere("cotizaciones.id_cotizacion", "=", $valor);
						$q->orWhere("cotizaciones.nombre", "like", "%" . $valor . "%");
						$q->orWhere("cotizaciones.telefono", "like", "%" . $valor . "%");
						$q->orWhere("cotizaciones.e_mail", "like", "%" . $valor . "%");
						$q->orWhere("cotizaciones.estado", "like", "%" . $valor . "%");
						$q->orWhere("cotizaciones.ciudad", "like", "%" . $valor . "%");
					});
				}
			})
			->orderBy('cotizaciones.id_cotizacion', 'desc')
			->take(1000)
			->get();
		$res = array();
		foreach($cotizaciones as $cotizacion){
			$nombre = '<a href="' . \URL::to('/admingm/cotizacion/verCotizacion/' . $cotizacion->id_cotizacion . '/' .$cotizacion->secret) . '">' . $cotizacion->nombre . "</a><br>" . date('Y-m-d H:i', strtotime($cotizacion->fecha_registro));
			if($cotizacion->fecha_seguimiento!="")
				$nombre .= "<br><strong>Seguimiento programado:<br>" . date('Y-m-d H:i', strtotime($cotizacion->fecha_seguimiento)) . "</strong>";
			$contacto = '<a href="#" class="campo" data-campo="telefono" data-value="' . $cotizacion->telefono . '" data-type="text" data-original-title="Teléfono" data-pk="' . $cotizacion->id_cotizacion . '">' . $cotizacion->telefono . '</a><br><a href="#" class="campo" data-campo="e_mail" data-value="' . $cotizacion->e_mail . '" data-type="text" data-original-title="Correo electrónico" data-pk="' . $cotizacion->id_cotizacion . '">' . $cotizacion->e_mail . '</a>';
			$ubicacion = '<a href="#" class="campo" data-campo="estado" data-value="' . $cotizacion->estado . '" data-type="text" data-original-title="Estado" data-pk="' . $cotizacion->id_cotizacion . '">' . $cotizacion->estado . '</a>, <a href="#" class="campo" data-campo="ciudad" data-value="' . $cotizacion->ciudad . '" data-type="text" data-original-title="Ciudad" data-pk="' . $cotizacion->id_cotizacion . '">' . $cotizacion->ciudad . '</a>';
			$integrantes = count(json_decode($cotizacion->integrantes));
			$agente = '<a href="#" class="campo" data-campo="id_agente" data-value="' . $cotizacion->id_agente . '" data-type="select" data-source="' . \URL::to('admingm/listasJson/agentesJson') . '" data-original-title="Agente" data-pk="' . $cotizacion->id_cotizacion . '">' . $cotizacion->agente . '</a>';
 			$estatus = '<a href="#" class="campo" data-campo="estatus" data-value="' . $cotizacion->estatus . '" data-source="' . \URL::to('admingm/listasJson/cotizacionEstatusJson') . '" data-type="select" data-original-title="Estatus" data-pk="' . $cotizacion->id_cotizacion . '">' . $cotizacion->estatus . '</a>';
 			if($cotizacion->id_agente == \Auth::user()->id_usuario || $cotizacion->id_agente == -1){
        		$opciones = '<a href="' . \URL::to('/admingm/cotizacion/verCotizacion/' . $cotizacion->id_cotizacion . '/' . $cotizacion->secret) . '" data-toggle="tooltip" class="delete-row tooltips" data-original-title="Ver cotización"><i class="fa fa-eye"></i></a> ';
        	}else{
            	$opciones = '<a href="' . \URL::to('/admingm/cotizacion/verCotizacionRapida/' . $cotizacion->id_cotizacion) . '" data-toggle="tooltip" class="delete-row tooltips" data-original-title="Ver cotización"><i class="fa fa-eye"></i></a> ';
        	}
        	$opciones .= '<a href="' . \URL::to('/admingm/cotizacion/altaCotizacion/' . $cotizacion->id_cotizacion) . '" data-toggle="tooltip" class="delete-row tooltips" data-original-title="Editar"><i class="fa fa-edit"></i></a>';
        	$opciones .= '<a href="#" data-toggle="tooltip" class="delete-row tooltips eliminarCotizacion" data-idCotizacion="' . $cotizacion->id_cotizacion . '" data-estatus="' . $cotizacion->estatus . '" data-original-title="Eliminar"><i class="fa fa-trash-o"></i></a>';
			$res[] = array($cotizacion->id_cotizacion, $nombre, $cotizacion->forma_ingreso, $contacto, $ubicacion, $integrantes, $agente, $estatus, $opciones);
		};
		return json_encode(array("data" => $res, "idAgente" => $idAgente));
	}

	public function emailBlackList(){
		$this->scripts[] = array("tipo" => "css", "archivo" => "backend/css/style.datatables");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/jquery.dataTables.min");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/dataTables/dataTables.bootstrap");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/dataTables/dataTables.responsive");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/jquery.validate.min");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/vendor/plugins/xeditable/js/bootstrap-editable.min");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/vendor/plugins/xeditable/css/bootstrap-editable");
        \View::share('scripts', $this->scripts);

        //View::share('breadCumb', SistemaFunciones::breadCumb(Request::segment(2)));
        $this->layout->content = \View::make('backend.'.$this->ruta);
    }
    
    public function getEmailBlackList(){
        $emailBlackList = \Emailblack::all();
        return \Datatable::collection($emailBlackList)
            ->showColumns('id_email_blacklist')
            ->addColumn('e_mail',function($email)
            {
                return $email->e_mail;
            })
            ->addColumn('opciones',function($email)
            {
                return '<button type="button" id="eliminarEmailBlack'.$email->id_email_blacklist.'" class="btn btn-danger eliminarEmailBlack" data-idEmailBlacklist="'.$email->id_email_blacklist.'" data-loading-text=\''.\HTML::image('assets/img/ajax-loader.gif').'\'><i class="fa fa-trash"></i></button>';
            })
            ->searchColumns('e_mail', 'cotizacionesTotales')
            ->orderColumns('id_email_blacklist', 'e_mail', 'cotizacionesTotales')
            ->make();
    }
    
    public function guardarEmailBlackList(){
        $datos = \Input::all();
        $emailblack = new \Emailblack($datos);
        
        $respuesta = array(
                        "status" => "error",
                        "titulo" => "Agregar a lista negra",
                        "mensaje" => "Ocurrio un error al tratar de agregar el correo a la lista negra",
                        "posicion" => "stack_bottom_right",
                        "tipo" => "error",
                    );
        try{
            if($emailblack->save()){
                $respuesta['status'] = 'success';
                $respuesta['tipo'] = 'success';
                $respuesta['mensaje'] = 'Correo agregado correctamente';
            }
        }catch(Exception $e){
            $respuesta['mensaje'] = $e->getMessage();
        }
        return json_encode($respuesta);
    }
    
    public function eliminarEmailBlackList(){
        $idEmailBlacklist = \Input::get('idEmailBlacklist');
        $emailBlacklist = \Emailblack::find($idEmailBlacklist);
        $respuesta = array(
                        "status" => "error",
                        "titulo" => "Eliminar de lista negra",
                        "mensaje" => "Ocurrio un error al tratar de eliminar el correo de la lista negra",
                        "posicion" => "stack_bottom_right",
                        "tipo" => "error",
                    );
        if($emailBlacklist){
            if($emailBlacklist->delete()){
                $respuesta['status'] = 'success';
                $respuesta['tipo'] = 'success';
                $respuesta['mensaje'] = 'Correo eliminado correctamente de la lista negra';
            }
        }
        return json_encode($respuesta);
    }
    
    public function actualizarEmailBlackListCampo(){
        $datos = \Input::all();
        $emailBlackList = \Emailblack::find($datos["pk"]);
        $emailBlackList->$datos["campo"] = $datos["value"];

        try{
            if($emailBlackList->save()){
                return 1;
            }
        }catch(Exception $e){
            
        }
        return 0;
    }

	public function emailWhiteList(){
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/css/style.datatables");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/jquery.dataTables.min");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/dataTables/dataTables.bootstrap");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/dataTables/dataTables.responsive");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/jquery.validate.min");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/vendor/plugins/xeditable/js/bootstrap-editable.min");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/vendor/plugins/xeditable/css/bootstrap-editable");
        \View::share('scripts', $this->scripts);

        //View::share('breadCumb', SistemaFunciones::breadCumb(Request::segment(2)));
        $this->layout->content = \View::make('backend.'.$this->ruta);
    }
    
    public function getEmailWhiteList(){
        $emailWhiteList = \Emailwhite::all();
        return \Datatable::collection($emailWhiteList)
            ->showColumns('id_email_whitelist')
            ->addColumn('e_mail',function($email)
            {
                return $email->e_mail;
            })
            ->addColumn('cotizacionesTotales',function($email)
            {
                return '<a href="#" class="campo" data-campo="cotizacionesTotales" data-value="'.$email->cotizacionesTotales.'" data-type="text" data-pk="'.$email->id_email_whitelist.'" data-original-title="Cotizaciones permitidas"></a>';
            })
            ->addColumn('opciones',function($email)
            {
                return '<button type="button" id="eliminarEmailWhite'.$email->id_email_whitelist.'" class="btn btn-danger eliminarEmailWhite" data-idEmailWhitelist="'.$email->id_email_whitelist.'" data-loading-text=\''.\HTML::image('assets/img/ajax-loader.gif').'\'><i class="fa fa-trash"></i></button>';
            })
            ->searchColumns('e_mail', 'cotizacionesTotales')
            ->orderColumns('id_email_whitelist', 'e_mail', 'cotizacionesTotales')
            ->make();
    }
    
    public function guardarEmailWhiteList(){
        $datos = \Input::all();
        $emailwhite = new \Emailwhite($datos);
        
        $respuesta = array(
                        "status" => "error",
                        "titulo" => "Agregar a lista blanca",
                        "mensaje" => "Ocurrio un error al tratar de agregar el correo a la lista blanca",
                        "posicion" => "stack_bottom_right",
                        "tipo" => "error",
                    );
        try{
            if($emailwhite->save()){
                $respuesta['status'] = 'success';
                $respuesta['tipo'] = 'success';
                $respuesta['mensaje'] = 'Correo agregado correctamente';
            }
        }catch(Exception $e){
            $respuesta['mensaje'] = $e->getMessage();
        }
        return json_encode($respuesta);
    }
    
    public function eliminarEmailWhiteList(){
        $idEmailWhitelist = \Input::get('idEmailWhitelist');
        $emailWhitelist = \Emailwhite::find($idEmailWhitelist);
        $respuesta = array(
                        "status" => "error",
                        "titulo" => "Eliminar de lista blanca",
                        "mensaje" => "Ocurrio un error al tratar de eliminar el correo de la lista blanca",
                        "posicion" => "stack_bottom_right",
                        "tipo" => "error",
                    );
        if($emailWhitelist){
            if($emailWhitelist->delete()){
                $respuesta['status'] = 'success';
                $respuesta['tipo'] = 'success';
                $respuesta['mensaje'] = 'Correo eliminado correctamente de la lista blanca';
            }
        }
        return json_encode($respuesta);
    }
    
    public function actualizarEmailWhiteListCampo(){
        $datos = \Input::all();
        $emailWhiteList = \Emailwhite::find($datos["pk"]);
        $emailWhiteList->$datos["campo"] = $datos["value"];

        try{
            if($emailWhiteList->save()){
                return 1;
            }
        }catch(Exception $e){
            
        }
        return 0;
    }

	private function recotizarWS($cotizacionDatos, $mapfreIntegrantes, $estado, $cotizacion, $tipo, $idHospitales, $hospitales){
		$cotizador = new \Cotizador($cotizacionDatos, 'sa', 'db');
		$mapfre = $cotizador::mapfreRecotizacion($mapfreIntegrantes, $estado, $cotizacion, $tipo, $idHospitales);
		
		$recotizacion = \RecotizacionMapfre::where("id_cotizacion", "=", $cotizacionDatos->id_cotizacion)
			->where("tipo", "=", $tipo)
			->where("hospitales", "=", $hospitales)
			->first();
		if(!$recotizacion){
			$recotizacion = new \RecotizacionMapfre();
			$recotizacion->id_cotizacion = $cotizacionDatos->id_cotizacion;
			$recotizacion->tipo = $tipo;
			$recotizacion->hospitales = $hospitales;
		}
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
	}

	public function recotizarMapfre($idCotizacion, $suma, $deducible, $primera = 0){
		$res = array();
		$cotizacionDatos = \Cotizacion::where('id_cotizacion', '=', $idCotizacion)->first();
		if($cotizacionDatos){
			if($primera==1){
				$recotizacion = \RecotizacionMapfre::where("id_cotizacion", "=", $idCotizacion)
					->where("tipo", "=", $suma . $deducible)
					->where("hospitales", "=", "esencial")
					->first();
				if($recotizacion){
					return json_encode($res);
				}
			}
			$integrantes = json_decode($cotizacionDatos->integrantes);
			$estado = \Estado::where('clave', '=', $cotizacionDatos->estado)->first();
			foreach($integrantes as $i){
				$parentesco = \Parentesco::where('parentesco', '=', $i->titulo)->first();
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
			
			
			$this->recotizarWS($cotizacionDatos, $mapfreIntegrantes, $estado, json_decode($cotizacionDatos->mapfre_respuesta, true), $suma . $deducible, 1, "esencial");
			$this->recotizarWS($cotizacionDatos, $mapfreIntegrantes, $estado, json_decode($cotizacionDatos->mapfre_respuesta, true), $suma . $deducible, 2, "optima");
			
			$recotizacion1 = \RecotizacionMapfre::where("id_cotizacion", "=", $idCotizacion)
				->where("tipo", "=", $suma . $deducible)
				->where("hospitales", "=", "esencial")
				->first();
			if($recotizacion1){
				$res["esencial"] = array(
					"contado" => "$ " . number_format($recotizacion1->contado, 2),
					"semestral-1" => "$ " . number_format($recotizacion1->semestral_primer, 2),
					"semestral-2" => "$ " . number_format($recotizacion1->semestral_posterior, 2),
					"trimestral-1" => "$ " . number_format($recotizacion1->trimestral_primer, 2),
					"trimestral-2" => "$ " . number_format($recotizacion1->trimestral_posterior, 2),
					"mensual-1" => "$ " . number_format($recotizacion1->mensual_primer, 2),
					"mensual-2" => "$ " . number_format($recotizacion1->mensual_posterior, 2)
				);
			}
			$recotizacion2 = \RecotizacionMapfre::where("id_cotizacion", "=", $idCotizacion)
				->where("tipo", "=", $suma . $deducible)
				->where("hospitales", "=", "optima")
				->first();
			if($recotizacion2){
				$res["optima"] = array(
					"contado" => "$ " . number_format($recotizacion2->contado, 2),
					"semestral-1" => "$ " . number_format($recotizacion2->semestral_primer, 2),
					"semestral-2" => "$ " . number_format($recotizacion2->semestral_posterior, 2),
					"trimestral-1" => "$ " . number_format($recotizacion2->trimestral_primer, 2),
					"trimestral-2" => "$ " . number_format($recotizacion2->trimestral_posterior, 2),
					"mensual-1" => "$ " . number_format($recotizacion2->mensual_primer, 2),
					"mensual-2" => "$ " . number_format($recotizacion2->mensual_posterior, 2)
				);
			}
		}
		return json_encode($res);
	}

	public function verCotizacionNuevo($idCotizacion, $secret = null){
        //$cotizacionDatos = \Cotizacion::where('id_cotizacion', '=', $idCotizacion)->where('secret', '=', $secret)->first();
        $cotizacionDatos = \Cotizacion::where('id_cotizacion', '=', $idCotizacion)->first();
        //if($cotizacionDatos && ($cotizacionDatos->id_agente == -1 || $cotizacionDatos->id_agente == \Auth::user()->id_usuario)){
            //if($cotizacionDatos->estatus == 1 || $cotizacionDatos->estatus == 2){
            if($cotizacionDatos->estatus == 2){
                $cotizacionDatos->estatus = 3;
            }
            if($cotizacionDatos->id_agente == -1){
                $cotizacionDatos->id_agente = \Auth::user()->id_usuario;
            }
            $cotizacionDatos->save();
            if(is_null($cotizacionDatos->mapfre_numero)){
            	$integrantes = json_decode($cotizacionDatos->integrantes);
				$estado = \Estado::where('clave', '=', $cotizacionDatos->estado)->first();
				foreach($integrantes as $i){
					$parentesco = \Parentesco::where('parentesco', '=', $i->titulo)->first();
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
				$cotizacion = new \Cotizador($cotizacionDatos, 'sa', 'db');
				$mapfreCotizacion = $cotizacion::mapfreCotizacion($mapfreIntegrantes, $estado);
				$cotizacionDatos->mapfre_xml = $mapfreCotizacion["xml"];
				$cotizacionDatos->mapfre_numero = $mapfreCotizacion["cotizacion"]["xml"]["num_solicitud"];
				$cotizacionDatos->mapfre_respuesta = json_encode($mapfreCotizacion["cotizacion"]);
				$cotizacionDatos->save();
				
				//$this->recotizarWS($cotizacionDatos, $mapfreIntegrantes, $estado, $mapfreCotizacion["cotizacion"], "sada", 1, "esencial");
				//$this->recotizarWS($cotizacionDatos, $mapfreIntegrantes, $estado, $mapfreCotizacion["cotizacion"], "sada", 2, "optima");
				//$this->recotizarWS($cotizacionDatos, $mapfreIntegrantes, $estado, $mapfreCotizacion["cotizacion"], "sadb", 1, "esencial");
				//$this->recotizarWS($cotizacionDatos, $mapfreIntegrantes, $estado, $mapfreCotizacion["cotizacion"], "sadb", 2, "optima");
				//$this->recotizarWS($cotizacionDatos, $mapfreIntegrantes, $estado, $mapfreCotizacion["cotizacion"], "sbda", 1, "esencial");
				//$this->recotizarWS($cotizacionDatos, $mapfreIntegrantes, $estado, $mapfreCotizacion["cotizacion"], "sbda", 2, "optima");
				//$this->recotizarWS($cotizacionDatos, $mapfreIntegrantes, $estado, $mapfreCotizacion["cotizacion"], "sbdb", 1, "esencial");
				//$this->recotizarWS($cotizacionDatos, $mapfreIntegrantes, $estado, $mapfreCotizacion["cotizacion"], "sbdb", 2, "optima");
			}
            $cotizacionDatos->integrantes = json_decode($cotizacionDatos->integrantes);
            \View::share('cotizacionDatos', $cotizacionDatos);

            /*$cotizacion = new \Cotizador($cotizacionDatos, 'sa', 'da');
            if(is_null($cotizacionDatos->mapfre_numero)){
            	$cotizacion::cotizar();
            	\View::share('tablaDatosDASA', $cotizacion::tablaDatos(true, false));
			}
            else{
            	$cotizacion::cotizarWS();
            	\View::share('tablaDatosDASA', $cotizacion::tablaDatosWS(true, false));
			}*/
			
            $cotizacion = new \Cotizador($cotizacionDatos, 'sa', 'db');
            if(is_null($cotizacionDatos->mapfre_numero)){
            	$cotizacion::cotizar();
            	\View::share('tablaDatosDBSA', $cotizacion::tablaDatos(true, false));
			}
            else{
            	$cotizacion::cotizarWS();
            	\View::share('tablaDatosDBSA', $cotizacion::tablaDatosWS2023(true, false));
			}

            /*$cotizacion = new \Cotizador($cotizacionDatos, 'sb', 'da');
            if(is_null($cotizacionDatos->mapfre_numero)){
            	$cotizacion::cotizar();
            	\View::share('tablaDatosDBSA', $cotizacion::tablaDatos(true, false));
			}
            else{
				$cotizacion::cotizarWS();
            	\View::share('tablaDatosDBSA', $cotizacion::tablaDatosWS(true, false));
			}*/
            
            /*$cotizacion = new \Cotizador($cotizacionDatos, 'sb', 'db');
            if(is_null($cotizacionDatos->mapfre_numero)){
				$cotizacion::cotizar();
            	\View::share('tablaDatosDBSB', $cotizacion::tablaDatos(true, false));
			}
            else{
				$cotizacion::cotizarWS();
            	\View::share('tablaDatosDBSB', $cotizacion::tablaDatosWS(true, false));
			}*/
            
            \View::share('tablaIntegrantes', $cotizacion::tablaIntegrantes(true));
            $integrantesOrden = array();
            $n = 2;
            foreach($cotizacionDatos->integrantes AS $integrante){
                switch($integrante->titulo){
                    case 'Titular':
                        $integrantesOrden[0] = $integrante;
                    break;
                    case 'Conyugue':
                        $integrantesOrden[1] = $integrante;
                    break;
                    default:
                        $integrantesOrden[$n] = $integrante;
                        $n++;
                    break;
                }
            }
            $cotizacionDatos->integrantes = $integrantesOrden;
            $tablaIntegrantesEditar = '';
            for($i=1;$i<=10;$i++){
                $titulo = '';
                switch($i){
                    case 1:
                        $titulo = 'Titular';
                    break;
                    case 2:
                        $titulo = 'Conyugue';
                    break;
                    default:
                        $titulo = 'Hijo(a)';
                    break;
                }
                $tablaIntegrantesEditar .= '<tr>
                                        <td class="alignCenter alignVerticalMiddle">'.$i.'</td>
                                        <td class="alignCenter alignVerticalMiddle">'.$titulo.'</td>
                                        <td class="alignCenter alignVerticalMiddle">
                                            <div class="ckbox ckbox-primary">
                                                <input type="checkbox" data-id="'.$i.'" id="integrantes_'.$i.'" '.(($i == 1) ? 'checked="checked" disabled=""' : ((isset($cotizacionDatos->integrantes[$i-1])) ? 'checked="checked"' : '') ).' name="integrantes[]" value="'.$i.'" />
                                                <label for="integrantes_'.$i.'"></label>
                                            </div>
                                        </td>
                                        <td class="alignVerticalMiddle"> <input type="text" data-id="'.$i.'" id="nombres_'.$i.'" name="nombres[]" placeholder="Nombre del '.$titulo.'" class="form-control input-sm nombres" '.((isset($cotizacionDatos->integrantes[$i-1])) ? 'value="'.$cotizacionDatos->integrantes[$i-1]->nombre.'"' : '').'> </td>
                                        <td class="alignVerticalMiddle">
                                            <select class="input_bg sexos" style="width: 100%;" data-id="'.$i.'" id="sexos_'.$i.'" name="sexos[]">
                                                <option value="-1">Sexo</option>
                                                <option value="m" '.((isset($cotizacionDatos->integrantes[$i-1]) && $cotizacionDatos->integrantes[$i-1]->sexo == 'm') ? 'selected' : '').'>Hombre</option>
                                                <option value="f" '.((isset($cotizacionDatos->integrantes[$i-1]) && $cotizacionDatos->integrantes[$i-1]->sexo == 'f') ? 'selected' : '').'>Mujer</option>
                                            </select>
                                        </td>
                                        <td class="alignVerticalMiddle"> <input type="text" class="edades" id="edades_'.$i.'" name="edades[]" data-id="'.$i.'" step="1" min="'.(($i == 1 || $i == 2) ? 18 : 0 ).'" max="69" placeholder="Edad" '.((isset($cotizacionDatos->integrantes[$i-1])) ? 'value="'.$cotizacionDatos->integrantes[$i-1]->edad.'"' : '').' digits="true" data-msg-digits="Solo números" data-msg-min="Edad mínima '.(($i == 1 || $i == 2) ? 18 : 0 ).' años" data-msg-max="Edad máxima 69 años" /> </td>
                                    </tr>';
            }
            \View::share('tablaIntegrantesEditar', $tablaIntegrantesEditar);
            if(strlen($cotizacionDatos->paquete) > 0){
                $paqueteDatos = \Paquete::where('paquete_campo', '=', $cotizacionDatos->paquete)->first();
                \View::share('paqueteDatos', $paqueteDatos);
            }
            
            // Consulta el permiso "asignarAgente"
            $asignarAgente = \DB::table("modulos_permisos")->select("acceso")->where("id_modulo", "=", 33)->where("id_usuario", "=", $cotizacionDatos->id_agente)->get(); 
            \View::share('asignarAgente', $asignarAgente);
            
            $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/x-editable/dist/bootstrap3-editable/js/bootstrap-editable.min");
            $this->scripts[] = array("tipo" => "css", "archivo" => "backend/js/x-editable/dist/bootstrap3-editable/css/bootstrap-editable");
            
            $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/admin-dock/dockmodal");
            $this->scripts[] = array("tipo" => "css", "archivo" => "backend/js/admin-dock/dockmodal");
            $this->scripts[] = array("tipo" => "css", "archivo" => "backend/js/summernote/summernote");
            $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/summernote/summernote.min");
            
            $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/tagsinput/tagsinput.min");
            $this->scripts[] = array("tipo" => "css", "archivo" => "backend/js/tagmanager/tagmanager");
            $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/tagmanager/tagmanager");

            $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/jquery.validate.min");
            $this->scripts[] = array("tipo" => "css", "archivo" => "backend/css/bootstrap-timepicker.min");
            $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/bootstrap-timepicker.min");
            $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/jquery.mousewheel");
            $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/select2.min");
            $this->scripts[] = array("tipo" => "css", "archivo" => "backend/css/select2");
            asort($this->scripts);

            $textosRespuestaCorreo = \CorreoRespuesta::where('id_dominio', '=', $cotizacionDatos->id_dominio)->orderBy('titulo')->get();
            \View::share('textosRespuestaCorreo', $textosRespuestaCorreo);
			\View::share('paquetes', $cotizacion::paquetesCotizacionWS2023());
            \View::share('scripts', $this->scripts);
            
            $testing = 0;
            if(\Input::get('testing')!=null)
            	$testing = 1;
            \View::share('testing', $testing);
            $this->layout->content = \View::make('backend.'.$this->ruta);
        //}else{
        //    return \Redirect::to('/admingm/cotizacion/consultaCotizaciones');
        //}
    }

	public function enviarCotizacinEmailNuevo(){
        $idCotizacion = \Input::get('idCotizacionEmail');
        $secret = \Input::get('secret');
        $sa = \Input::get('sa');
        $ded = \Input::get('ded');
        $mensaje = utf8_decode(\Input::get('mensaje'));
        $para = \Input::get('para');
        $paquetes = array();
        if(\Input::get('paquetes')!="")
        	$paquetes = explode(',', \Input::get('paquetes'));
        
        //$cotizacionDatos = \Cotizacion::where('id_cotizacion', '=', $idCotizacion)->where('secret', '=', $secret)->first();
        
        $respuesta = array(
                        "status" => "invalid",
                        "titulo" => "Enviar cotiazión por e-mail",
                        "mensaje" => "Ocurrio un error al tratar de enviar la cotiazión por Correo electrónico",
                        "posicion" => "stack_bar_bottom",
                        "tipo" => "error",
                    );
        $cotizacionDatos = \Cotizacion::where('id_cotizacion', '=', $idCotizacion)->first();
        if($cotizacionDatos){
            $cotizacionDatos->integrantes = json_decode($cotizacionDatos->integrantes);
            $pdf = self::generarCotizacionPdfWS2023($cotizacionDatos, false, $sa, $ded, $paquetes);
            /*
            $wkhtmltopdf = \App::make('snappy.pdf');
            $pdf = "tmp/cotizacion_$cotizacionDatos->id_cotizacion.pdf";
            $pdf = str_replace(" ","_" ,$pdf );
            $wkhtmltopdf->generate('http://segurodegastosmedicosmayores.mx/cotizacion/verCotizacionPDF/'.$idCotizacion.'/'.$cotizacionDatos->secret.'/'.$sa.'/'.$ded, $pdf);
            */
            if(file_exists($pdf)){
                $encabezado = \Cotizaciontexto::where('texto_seccion', '=', 'texto_correo_encabezado')->where('id_dominio', '=', $cotizacionDatos->id_dominio)->first();
				$cuerpo = \Cotizaciontexto::where('texto_seccion', '=', 'texto_correo_cuerpo')->where('id_dominio', '=', $cotizacionDatos->id_dominio)->first();
				//$pie = \Cotizaciontexto::where('texto_seccion', '=', 'texto_correo_pie')->first();
				$pie = \Cotizaciontexto::where('texto_seccion', '=', 'texto_correo_pie')
						->whereIn('id_usuario', array(-1, \Auth::user()->id_usuario))
						->where('id_dominio', '=', $cotizacionDatos->id_dominio)
						->orderBy('id_usuario', 'desc')
						->first();
                $cotizacionDatos->pdf = $pdf;
                $cotizacionDatos->para = \Input::get('para');
                $datosPlantilla = array(
                                    'nombre' => $cotizacionDatos->nombre,
                                    'e_mail' => $cotizacionDatos->e_mail,
                                    'id_cotizacion' => $cotizacionDatos->id_cotizacion,
                                    'secret' => $cotizacionDatos->secret,
                                    'encabezado' => str_replace('{{nombre}}', $cotizacionDatos->nombre, $encabezado->texto_pdf),
                                    'cuerpo' => $cuerpo->texto_pdf,
                                    'pie' =>  $pie->texto_pdf,
                                    'mensaje' => $mensaje,
                                    'signature' => '',
                                );
                /*try{
                    //if(\Auth::check() && file_exists(public_path().'/backend/images/signature/'.\Auth::user()->id_usuario.'.jpg')){
                    //    $datosPlantilla['signature'] = \HTML::image('/backend/images/signature/'.\Auth::user()->id_usuario.'.jpg');
                    //}
                    \Mail::send('plantillas.correo.enviarCotizacionCorreo', $datosPlantilla, function($message) use ($cotizacionDatos){
                        //$message->from('info@segurodegastosmedicosmayores.mx', 'Seguro de Gastos Médicos Mayores');
                        $message->from($cotizacionDatos->dominio()->first()->email, $cotizacionDatos->dominio()->first()->sender);
                        //$message->to($cotizacionDatos->e_mail, $cotizacionDatos->nombre);
                        foreach($cotizacionDatos->para AS $para){
                            //$message->to($para, $cotizacionDatos->nombre);
                            $message->to($para);
                        }
                        $message->cc('info@segurodegastosmedicosmayores.mx', 'Cotizaciones');
                        $message->subject('Cotización de Gastos Médicos Mayores - '.$cotizacionDatos->id_cotizacion);
                        if(file_exists($cotizacionDatos->pdf)){
                            $message->attach($cotizacionDatos->pdf, ['as' => 'cotizacion-'.$cotizacionDatos->id_cotizacion.'.pdf']);
                        }
                    });
                }catch(Exception $e){
                    $respuesta["mensaje"] = "22-" . $e->getMessage();
                    return json_encode($respuesta);
                }*/
                
                try{
                	\ALTMailer::mail(
                		'plantillas.correo.enviarCotizacionCorreo', 
                		$datosPlantilla, 
                		$cotizacionDatos, 
                		'info@segurodegastosmedicosmayores.mx', 
                		'Seguro de Gastos Médicos Mayores',
                		$para
                	);
				}
				catch(Exception $e){
                    $respuesta["mensaje"] = "22-" . $e->getMessage();
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
	
	private static function generarCotizacionPdfWS2023($cotizacionDatos = array(), $mostrar = true, $sa = 'sa', $ded = 'db', $paquetes = array()){
		$cotizacion = new \Cotizador($cotizacionDatos, $sa, $ded);
        $cotizacion::cotizarWS();
        
        $mpdf = new \mPDF('', 'Letter', '', '', 20,20,20,20,5,5);
        $bienvenida = \Cotizaciontexto::where('texto_seccion', '=', 'saludo_bienvenida')->where('id_dominio', '=', $cotizacionDatos->id_dominio)->first()->texto_pdf;
        \View::share('bienvenida', $bienvenida);
        $beneficios = \Cotizaciontexto::where('texto_seccion', '=', 'beneficios_protecto')->where('id_dominio', '=', $cotizacionDatos->id_dominio)->first()->texto_pdf;
        \View::share('beneficios', $beneficios);
        \View::share('cotizacionDatos', $cotizacionDatos);
        \View::share('cotizacion', $cotizacion);
        if($cotizacionDatos->estado=="Jalisco")
        	$aseguradoras = \Aseguradora::where('activa', '=', 1)->orderBy('orden')->get();
        else
        	$aseguradoras = \Aseguradora::where('activa', '=', 1)->where('aseguradora', '=', 'mapfre')->orderBy('orden')->get();
        \View::share('aseguradoras', $aseguradoras);
        \View::share('paquetes', $paquetes);
        $aAseguradoras = array();
		foreach(\DB::table('paquetes')
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
		foreach(\Paquete::whereIn('id_paquete', $paquetes)->orderBy('orden', 'asc')->get() as $p){
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
		\View::share('aAseguradoras', $aAseguradoras);
        $html = \View::make('plantillas.correo.cotizacionPdfWS2023');
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

	public function actualizarIntegrantes2023(){
        $idCotizacion = \Input::get('idCotizacion');
        $integrantes = \Input::get('integrantes');
        $integrantes[] = 1;

        $respuesta = array(
                        "status" => "invalid",
                        "mensaje" => "Ocurrio un error al tratar de guardar el formulario",
                        "posicion" => "stack_bar_bottom",
                        "tipo" => "error",
                        "idCotizacion" => -1,
                        "secret" => "",
                    );
        $cotizacion = \Cotizacion::find($idCotizacion);
        if($cotizacion && count($integrantes) > 0){
            $nombre = \Input::get('nombres');
            $sexo = \Input::get('sexos');
            $edad = \Input::get('edades');
            $integrantesLista = array();
            $mapfreIntegrantes = array();
            for($x=1;$x<=10;$x++){
	            foreach($integrantes AS $integrante){
	            	if($integrante==$x){
	            		$titulo = (($integrante == 1) ? 'Titular' : (($integrante == 2) ? 'Conyugue' : 'Hijo(a)'));
	                	$integrantesLista[] = array(
	                                    'titular' => (($integrante == 1) ? 1 : 0),
	                                    'titulo' => $titulo,
	                                    'nombre' => $nombre[$integrante - 1],
	                                    'sexo' => $sexo[$integrante - 1],
	                                    'edad' => $edad[$integrante - 1],
	                                );
	                	
	                	$estado = \Estado::where('clave', '=', $cotizacion->estado)->first();
	                	$parentesco = \Parentesco::where('parentesco', '=', $titulo)->first();
						if($sexo[$integrante - 1]=="m"){
							$sex = "Masculino";
							$idSexo = 1;
						}
						else{
							$sex = "Femenino";
							$idSexo = 0;
						}
	                	$mapfreIntegrantes[] = array(
	                		"nombre" => $nombre[$integrante - 1],
							"id_parentesco" => $parentesco->clave_mapfre,
							"parentesco" => $parentesco->parentesco,
							"id_sexo" => $idSexo,
							"sexo" => $sex,
							"edad" => $edad[$integrante - 1]
	                	);
					}
	            }
			}
            if(count($integrantesLista) > 0){
                $cotizacion->integrantes = json_encode($integrantesLista);
                if($cotizacion->save()){
                	/*\DB::table('recotizaciones_mapfre')
                		->where('id_cotizacion', '=', $idCotizacion)
                		->delete();*/
                	
                	$cotizador = new \Cotizador($cotizacion, 'sa', 'db');
					$mapfreCotizacion = $cotizador::mapfreCotizacion($mapfreIntegrantes, $estado);
					$cotizacion->mapfre_xml = $mapfreCotizacion["xml"];
					$cotizacion->mapfre_numero = $mapfreCotizacion["cotizacion"]["xml"]["num_solicitud"];
					$cotizacion->mapfre_respuesta = json_encode($mapfreCotizacion["cotizacion"]);
					$cotizacion->save();
                	
                    $respuesta["status"] = "success";
                    $respuesta["tipo"] = "success";
                    $respuesta["mensaje"] = "Integrantes actualizados correctamente";
                }
            }
        }
        return json_encode($respuesta);
    }

	public function recotizarMapfre2023($idCotizacion=-1, $hospitales=""){
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
		$tipo = "sadb";
		$hospitales = $hospitales;
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
		$cotizacionDatos = \Cotizacion::find($idCotizacion);
		if($cotizacionDatos){
			$recotizacion = \RecotizacionMapfre::where('id_cotizacion', '=', $idCotizacion)
				->where('tipo', '=', $tipo)
				->where('hospitales', '=', $hospitales)
				->first();
			if(!$recotizacion){
				$baseMapfre = \BaseMapfre::first();
				
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
				
				$recotizacion = new \RecotizacionMapfre();
				$recotizacion->id_cotizacion = $cotizacionDatos->id_cotizacion;
				$recotizacion->tipo = $tipo;
				$recotizacion->hospitales = $hospitales;
				$recotizacion->sa = $baseMapfre->sa;
				$recotizacion->deducible = $deducible;
				$recotizacion->coaseguro = $baseMapfre->coaseguro;
				$recotizacion->tope_coaseguro = 40000;
				$recotizacion->tabulador = $baseMapfre->tabulador;
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
			/*else{
				$recotizacion->sa = \Input::get('sa');
				$recotizacion->deducible = Input::get('deducible');
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
				$recotizacion->reduccion_deducible = Input::get('reduccion_deducible');
				if(Input::get('dental')!="")
					$recotizacion->dental = Input::get('dental');
				else
					$recotizacion->dental = null;
				$recotizacion->complicaciones = Input::get('complicaciones');
				$recotizacion->vanguardia = Input::get('vanguardia');
				$recotizacion->multiregion = Input::get('multiregion');
				$recotizacion->preexistentes = Input::get('preexistentes');
				$recotizacion->catastroficas = Input::get('catastroficas');
				$recotizacion->funeraria = Input::get('funeraria');
				$recotizacion->completada = 0;
				$recotizacion->enviada = 0;
			}*/
			$recotizacion->save();
			
			$cotizador = new \Cotizador($cotizacionDatos, 'sa', 'db');
			$mapfre = $cotizador::mapfreRecotizacion2023($cotizacionDatos, $recotizacion, $idHospitales);
			
			$recotizacion->xml = $mapfre["xml"];
			$recotizacion->respuesta = json_encode($mapfre["cotizacion"]);
			$recotizacion->completada = 1;
			$recotizacion->updated_at = \Carbon\Carbon::now();
			$recotizacion->save();
			
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
			
			$respuesta["status"] = 200;
			$respuesta["contado"] = number_format($recotizacion->contado, 2);
			$respuesta["semestral_1"] = number_format($recotizacion->semestral_primer, 2);
			$respuesta["semestral_2"] = number_format($recotizacion->semestral_posterior, 2);
			$respuesta["trimestral_1"] = number_format($recotizacion->trimestral_primer, 2);
			$respuesta["trimestral_2"] = number_format($recotizacion->trimestral_posterior, 2);
			$respuesta["mensual_1"] = number_format($recotizacion->mensual_primer, 2);
			$respuesta["mensual_2"] = number_format($recotizacion->mensual_posterior, 2);
			
			/*$doc = new DOMDocument;
			$doc->preserveWhiteSpace = false;
			$doc->loadXML(trim(rtrim($mapfre["xml"], '"'), '"'));
			$xpath = new DOMXPath($doc);
			
			$C = array();
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
			$C[] = array("id" => 1, "value" => $recotizacion->sa, "format" => number_format($recotizacion->sa, 0, ".", ","));
			$C[] = array("id" =>2, "value" => $recotizacion->deducible, "format" => number_format($recotizacion->deducible, 0, ".", ","));
			$C[] = array("id" =>3, "value" => $recotizacion->coaseguro, "format" => $recotizacion->coaseguro . "%");
			$C[] = array("id" =>4, "value" => $recotizacion->tope_coasegurom, "format" => number_format($recotizacion->tope_coaseguro, 0, ".", ","));
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
			$C[] = array("id" =>9, "value" => $recotizacion->emergencia_extranjero, "format" => number_format($recotizacion->emergencia_extranjero, 0, ".", ","));
			$C[] = array("id" =>7, "value" => $recotizacion->sa_maternidad, "format" => number_format($recotizacion->sa_maternidad, 0, ".", ","));
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
			
			return json_encode($respuesta);
		}
		else
			return json_encode($respuesta);
	}

	public function cotizacionesListaDistribucion(){
		$this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/x-editable/dist/bootstrap3-editable/js/bootstrap-editable.min");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/js/x-editable/dist/bootstrap3-editable/css/bootstrap-editable");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/css/style.datatables");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/jquery.dataTables.min");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/dataTables/dataTables.bootstrap");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/dataTables/dataTables.responsive");
        \View::share('scripts', $this->scripts);

        $this->layout->content = \View::make('backend.'.$this->ruta);
	}
	
	public function getCotizacionesListaDistribucion(){
		$cotizaciones = \Cotizacion::whereNotNull('id_lista_distribucion')->get();
        return \Datatable::collection($cotizaciones)
            ->showColumns('id_cotizacion')
            ->addColumn('nombre', function($cotizacion)
            {
                //return $administrador->nombre.' '.$administrador->apellido_paterno.' '.$administrador->apellido_materno;
                return '<a href="'.\URL::to('admingm/cotizacion/cotizacionListaDistribucion/'.$cotizacion->id_cotizacion).'">'.$cotizacion->nombre.'</a>';
            })
            ->addColumn('email', function($cotizacion){
            	return $cotizacion->listaDistribucion->nombre;
            })
            ->addColumn('', function($cotizacion){
            	return 0;
            })
            ->searchColumns('id_cotizacion', 'nombre')
            ->orderColumns('id_cotizacion', 'desc')
            ->make();
	}

	public function cotizacionListaDistribucion($idCotizacion){
		$cotizacionDatos = \Cotizacion::where('id_cotizacion', '=', $idCotizacion)->first();
		$cotizacionDatos->integrantes = json_decode($cotizacionDatos->integrantes);
		\View::share('cotizacionDatos', $cotizacionDatos);
		
		$this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/x-editable/dist/bootstrap3-editable/js/bootstrap-editable.min");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/js/x-editable/dist/bootstrap3-editable/css/bootstrap-editable");
        
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/admin-dock/dockmodal");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/js/admin-dock/dockmodal");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/js/summernote/summernote");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/summernote/summernote.min");
        
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/tagsinput/tagsinput.min");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/js/tagmanager/tagmanager");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/tagmanager/tagmanager");

        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/jquery.validate.min");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/css/bootstrap-timepicker.min");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/bootstrap-timepicker.min");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/jquery.mousewheel");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/select2.min");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/css/select2");
        asort($this->scripts);
		
		\View::share('scripts', $this->scripts);
		$this->layout->content = \View::make('backend.'.$this->ruta);
	}

	public function whatsappMessage(){
		$idCotizacion = \Input::get('idCotizacion');
		$respuesta = array(
                        "status" => "invalid",
                        "titulo" => "Envio de mensaje",
                        "mensaje" => "Ocurrio un error al tratar de enviar el mensaje",
                        "posicion" => "stack_bar_bottom",
                        "tipo" => "error",
                        "idLista" => -1,
                    );
		
		$cotizacionDatos = \Cotizacion::where('id_cotizacion', '=', $idCotizacion)->first();
		$send = Whatsapp::sendTemplate1($cotizacionDatos);
		$respuesta["status"] = "success";
		$respuesta["mensaje"] = $send;
		$respuesta["tipo"] = "success";
		return json_encode($respuesta);
	}

	public function cotizacionToListaDistribucion(){
		$respuesta = array(
                        "status" => "success",
                        "titulo" => "Cotización",
                        "mensaje" => "Ocurrio un error al tratar de obtener la siguiente cotización",
                        "posicion" => "stack_bar_bottom",
                        "tipo" => "success",
                        "idCotizacionSiguiente" => -1
                    );
        $idCotizacion = \Input::get('idCotizacion');
        if($idCotizacion > 0){
            $cotizacionDatos = \Cotizacion::find($idCotizacion);
            if($cotizacionDatos){
                $cotizacionDatos->id_lista_distribucion = 1;
                $cotizacionDatos->pausa_lista_distribucion = 0;
                $respuesta['mensaje'] = 'Se agrego la cotización a la lista de distribución';
                $cotizacionDatos->save();
                
                $token = "EAAxt5HxflOYBO7HMHgEdSZCDN7ReKmRZCVhZA7UZANUnWf2hGxZCvHxr2bmDu8nXZAmKKu7cHlZCtb70suwJ0WRWFppNdyNBiMolQYC9F7Gh1ksbgZCQUsNIvd32ZB7qQHk4v3xH4RqHdiLOFTwu1IwipmAnDjSh6KePjake2wesPtjp286QmD5zwRmxwA7i5VmVs1AZDZD";
                $url = "https://www.segurodegastosmedicosmayores.mx/cotizacion-nuevo/" . $cotizacionDatos->id_cotizacion . "/" . $cotizacionDatos->secret;
                $tipo = "";
				switch($cotizacionDatos->cotizar_para){
					case 1:
						$tipo = "1 persona";
						break;
					case 2:
						$tipo = "tu pareja y tu";
						break;
					case 3:
						$tipo = "tu pareja, tu y tus hijos";
						break;
					case 4:
						$tipo = "tu pareja e hijos";
						break;
					case 5:
						$tipo = "tu y tus hijos";
						break;
				}
				$jsonPars = '{"type":"text", "parameter_name":"nombre", "text":"' . $cotizacionDatos->nombre_simple . '"}';
				$jsonPars .= ', {"type":"text", "parameter_name":"tipo", "text":"' . $tipo . '"}';
				$jsonPars .= ', {"type":"text", "parameter_name":"url", "text":"' . $url . '"}';
				$lang = 'es_MX';
				$message = "¿Que te pareció tu cotización?<br><br>Estimado(a) {{nombre}} hace algunos días recibimos una solicitud para generar una cotización de seguro de gastos médicos mayores para {{tipo}}. Queremos saber si tienes alguna necesidad en especial.<br><br>Te dejamos un enlace directo a tu cotización {{url}}";
				$idCotizacion = $cotizacionDatos->id_cotizacion;
				$idLista = $cotizacionDatos->id_lista_distribucion;
				$plantilla = 'protecto_mensaje_1';
				$json = '{
					      "messaging_product": "whatsapp",
					      "recipient_type": "individual",
					      "to": "52' . $cotizacionDatos->telefono . '",
					      "type": "template",
					      "template": {
					      	"name": "' . $plantilla . '",
					      	"language": {"code": "' . $lang . '"},
					      	"components": [
					      	  {
					      	  	"type": "body",
					      	  	"parameters": [
					      	  	  ' . $jsonPars . '
					      	  	]
					      	  }
					      	]
					      }
					    }';
				
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/v23.0/614394131767163/messages");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					"Authorization: Bearer " . $token,
					"Content-Type: application/json"
				));
				$res = curl_exec($ch);
				$err = curl_error($ch);
				curl_close($ch);
				
				$wamid = '';
				$jsonRes = json_decode($res);
				if(isset($jsonRes->messages[0]->id)){
					$wamid = $jsonRes->messages[0]->id;
					$whatsapp = new \CotizacionWhatsapp;
					$whatsapp->id_cotizacion = $cotizacionDatos->id_cotizacion;
					$whatsapp->id_lista_distribucion = $cotizacionDatos->id_lista_distribucion;
					$whatsapp->plantilla = $plantilla;
					$whatsapp->ch_result = $res;
					$whatsapp->ch_error = $err;
					$whatsapp->save();
				}
				
				$message = str_replace("{{nombre}}", $cotizacionDatos->nombre_simple, $message);
				$message = str_replace("{{tipo}}", $tipo, $message);
				$message = str_replace("{{url}}", $url, $message);
				
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, "https://whatsapp.mas-ti.mx/demo/php/register-message.php");
				curl_setopt($ch, CURLOPT_POST, true);
				$data = "name=" . $cotizacionDatos->nombre . "&phone=" . $cotizacionDatos->telefono . "&id=2004157567026728&wamid=" . $wamid . "&message=" .$message ;
		    	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		    	$res = curl_exec($ch);
            }
        }
        return json_encode($respuesta);
	}
}
