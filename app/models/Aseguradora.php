<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Aseguradora extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	protected $table = 'aseguradoras';
	
	protected $fillable = array('nombre', 'aseguradora', 'configuracion', 'activa');
	
	public $timestamps = false;
	
	public function getKeyName(){
	    return "id_aseguradora";
	}
	
	public function Paquetes(){
	    return $this->hasMany('Paquete', 'id_aseguradora');
	}
}