<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Domain extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	protected $table = 'dominios';
	
	protected $fillable = array('nombre', 'dominio', 'email', 'sender', 'logo', 'ver_cotizacion', 'ver_cotizacion_nuevo', 'activo');
	
	public $timestamps = false;
	
	public function getKeyName(){
	    return "id_dominio";
	}
}