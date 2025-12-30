<?php

class ChatController extends BaseController {
	protected $layout = 'layout.master';

	public function getMensajesChatOnline(){
		$chatSesion = Chatonline::where('id', '=', Session::get('id'))->first();
		$respuesta = array(
                        "status" => 'invalid',
                        "mensajes" => array(),
                    );
		if($chatSesion){
			$mensajes = Chatonlinemensaje::where('para', '=', Session::get('id'))->where('leido', '=', 0)->get();
			if(count($mensajes) > 0){
				$mensajesSesion = json_decode(Session::get('mensajes'));
				foreach($mensajes AS $mensaje){
					$respuesta['mensajes'][strtotime($mensaje->fecha_registro)] = $mensajesSesion[strtotime($mensaje->fecha_registro)] = array(
																					"nombre" => $chatSesion->asesor->nombre.' '.$chatSesion->asesor->apellido_paterno,
																					"mensaje" => $mensaje->mensaje,
																					"owner" => 'customer',
																					"fecha" => $mensaje->fecha_registro,
																				);
					$mensaje->leido = 1;
					$mensaje->read_datetime = strtotime(date('Y-m-d'));
					$mensaje->save();
				}
				Session::set('mensajes', json_encode($mensajesSesion));
			}
		}
		return json_encode($respuesta);
	}
	
	public function iniciarSesionChatOnlinea()
	{
		$respuesta = array(
                        "status" => 'invalid',
                        "mensaje" => "",
                    );
		$usuarioDatos = Input::all();
		Chatonline::eliminarSesiones();

		$usuarioSesion = Chatonline::where('e_mail', '=', $usuarioDatos['e_mail_chat'])->get();
		if(count($usuarioSesion) == 0){
			$sesionChat = new Chatonline;
			$sesionChat->id = uniqid();
			$sesionChat->nombre = $usuarioDatos['nombre_chat'];
			$sesionChat->e_mail = $usuarioDatos['e_mail_chat'];
			$sesionChat->ip_usuario = Request::getClientIp();
			if($sesionChat->save()){
				Session::set('mensajes', json_encode(array()));
				Session::set('id', $sesionChat->id);
				Session::set('nombre', $sesionChat->nombre);
				Session::set('e_mail', $sesionChat->e_mail);
				$respuesta['status'] = 'success';
			}
		}else{
			$respuesta['mensaje'] = 'Alguna sesión que inicio anteriormente no fue cerrada eliminada, espere 5 minutos para que el sistema elimine la sesión por inactividad.';
		}
		return json_encode($respuesta);
	}

}