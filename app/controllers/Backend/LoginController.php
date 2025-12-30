<?php

namespace Backend;

use App\Models\Backend\User;
use App\Models\Backend\Usuariosesion;

class LoginController extends \BaseController {
	//protected $layout = 'Backend.layout.master';
	
	public function iniciarSesion(){
		$cuentaDatos = \Input::all();
		
		$respuesta = array(
						"status" => "invalid",
                        "titulo" => "Iniciar sesión",
                        "mensaje" => "Ocurrio un error al tratar de iniciar sesión",
                        "posicion" => "stack_bar_bottom",
                        "tipo" => "danger",
                        "blocked" => 0
                    );
		//$administradorDatos = User::where('e_mail', '=', $cuentaDatos['username'])->where('contrasena', '=', sha1($cuentaDatos['password']))->first();
		$administradorDatos = User::where('e_mail', '=', $cuentaDatos['username'])->first();
		if($administradorDatos){
			if($administradorDatos->contrasena==sha1($cuentaDatos['password'])){
				if($administradorDatos->errores<5){
					\Auth::login($administradorDatos);
					if (\Auth::check() && \Auth::user()->id_usuario > 0){
						Usuariosesion::where('updated_at', '<', date('Y-m-d H:i:s'))->where("id_usuario", \Auth::user()->id_usuario )->delete();
						$usuarioSesion = Usuariosesion::find(\Auth::user()->id_usuario);
						if($usuarioSesion == NULL){
							\Session::set('id_usuario', $administradorDatos->id_usuario);
							$usuarioSesion = new Usuariosesion;
							$usuarioSesion->id_usuario = \Auth::user()->id_usuario;
							$usuarioSesion->ip_usuario = \Request::getClientIp();

							if($usuarioSesion->save()){
								$administradorDatos->errores = 0;
								$administradorDatos->errores_secret = '';
								$administradorDatos->save();
								
								$respuesta["status"] = "success";
								$respuesta["tipo"] = "success";
								$respuesta["mensaje"] = "";
							}
						}
					}
				}
				else{
					$respuesta["blocked"] = 1;
					$administradorDatos->errores_secret = str_random(15);
					\ALTMailer::usuarioBloqueado($cuentaDatos['username'], $administradorDatos->errores_secret);
					$administradorDatos->save();
				}
			}
			else{
				$administradorDatos->errores = $administradorDatos->errores + 1;
				if($administradorDatos->errores>=5){
					$respuesta["blocked"] = 1;
					$administradorDatos->errores_secret = str_random(15);
					\ALTMailer::usuarioBloqueado($cuentaDatos['username'], $administradorDatos->errores_secret);
				}
				$administradorDatos->save();
			}
		}
		return json_encode($respuesta);
	}
	
	public function cerrarSesion(){
		if (\Auth::check()){
			\Cotizacion::where('id_agente', '=', \Auth::user()->id_usuario)->where('estatus', '=', 3)->whereNotNull('paquete')->whereNotNull('sa')->whereNotNull('ded')->update(array('estatus' => 1, 'id_agente' => -1));
			\Cotizacion::where('id_agente', '=', \Auth::user()->id_usuario)->where('estatus', '=', 3)->whereNull('paquete')->whereNull('sa')->whereNull('ded')->update(array('estatus' => 2, 'id_agente' => -1));
			\Cotizacion::where('id_agente', '=', \Auth::user()->id_usuario)->whereIn('estatus', array(4,5))->update(array('id_agente' => -1));
			$sesionRegistrada = Usuariosesion::find(\Auth::user()->id_usuario);
			if($sesionRegistrada){
				$sesionRegistrada->delete();
			}
		}
		\Session::flush();
    	return \Redirect::to('admingm/login');
	}

	public function desbloquearUsuario($secret){
		if($secret!=""){
			$administradorDatos = User::where('errores_secret', '=', $secret)->first();
			if($administradorDatos){
				$administradorDatos->errores = 0;
				$administradorDatos->errores_secret = '';
				$administradorDatos->save();
				
				return \Redirect::to('/admingm');
			}
		}
	}	
}
