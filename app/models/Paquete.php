<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Paquete extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	protected $table = 'paquetes';
	
	//protected $fillable = array('id_aseguradora', 'paquete', 'paquete_campo', 'derecho_poliza', 'configuracion', 'activo');
	protected $fillable = array('id_aseguradora', 'paquete', 'paquete_campo', 'derecho_poliza', 'orden', 'activo', 'descripcion_backend', 'descripcion_me_interesa');
	
	public $timestamps = false;
	
	public function getKeyName(){
	    return "id_paquete";
	}
	
	public function aseguradora(){
		return $this->hasOne('Aseguradora', 'id_aseguradora', 'id_aseguradora');
	}
	
	public function tarifasada(){
		return $this->hasMany('Paquetetarifasada', 'id_paquete', 'id_paquete');
	}
	
	public function tarifasadb(){
		return $this->hasMany('Paquetetarifasadb', 'id_paquete', 'id_paquete');
	}
	
	public function tarifasbda(){
		return $this->hasMany('Paquetetarifasbda', 'id_paquete', 'id_paquete');
	}
	
	public function tarifasbdb(){
		return $this->hasMany('Paquetetarifasbdb', 'id_paquete', 'id_paquete');
	}
	
	public function tarifaValor(){
	    return $this->hasMany('Paqueteconceptotarifa', 'id_paquete');
	}
	
	public function tarifaValor2023(){
	    return $this->hasMany('Paqueteconceptotarifa2023', 'id_paquete');
	}
}