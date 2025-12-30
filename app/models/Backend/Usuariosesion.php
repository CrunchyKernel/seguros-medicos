<?php

namespace App\Models\Backend;

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Usuariosesion extends \Eloquent implements UserInterface, RemindableInterface {
	use UserTrait, RemindableTrait;
	
	protected $table = 'usuarios_sesiones';
	
	protected $hidden = array();
	
	protected $fillable = array('estatus', 'ip_usuario', 'created_at', 'updaed_at');
	
	public function getKeyName()
	{
	    return 'id_usuario';
	}
	
	public static function actualizarSesionTiempo(){
		$sesionDatos = Usuariosesion::where('id_usuario', '=', \Auth::user()->id_usuario)->get();
		if(count($sesionDatos) == 1){
			$sesionDatos[0]->updated_at = date('Y-m-d H:i:s');
			$sesionDatos[0]->save();
		}
	}

}