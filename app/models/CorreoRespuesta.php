<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class CorreoRespuesta extends Eloquent implements UserInterface, RemindableInterface {
	
	use UserTrait, RemindableTrait;
	
	protected $table = 'correo_respuestas';
	
	protected $fillable = array('titulo', 'contenido', 'id_dominio');
	
	public function dominio(){
		return $hits->hasOne('Domain', 'id_dominio', 'id_dominio');
	}
}
