<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Paquetedescripcion extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	protected $table = 'paquetes_descripcion';
	
	protected $fillable = array('id_aseguradora', 'paquete', 'paquete_campo', 'configuracion', 'activo');
	
	public $timestamps = false;
	
	public function getKeyName(){
	    return "id_paquete";
	}
	
	public function aseguradora(){
		return $this->hasOne('Aseguradora', 'id_aseguradora', 'id_aseguradora');
	}
}