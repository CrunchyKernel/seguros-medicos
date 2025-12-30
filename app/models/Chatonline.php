<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Chatonline extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	protected $table = 'chat_online_sesiones';
	
	protected $fillable = array('id', 'nombre', 'e_mail', 'id_usuario_atiende', 'ip_usuario', 'fecha_registro');
	
	public $timestamps = false;
	
	public function getKeyName(){
	    return "id_usuario_sesion";
	}

	public function asesor(){
		return $this->hasOne('Asesor', 'id_usuario', 'id_usuario_atiende');
	}
	
	public static function eliminarSesiones(){
		Chatonline::where('fecha_registro', '<', date('Y-m-d H:i', strtotime("-5minute", strtotime(date('Y-m-d H:i')) )) )->delete();
	}

}