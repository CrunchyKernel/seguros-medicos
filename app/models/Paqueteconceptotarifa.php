<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Paqueteconceptotarifa extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	protected $table = 'paquete_conceptos_tarifa_valores';
	
	protected $fillable = array('id_paquete', 'id_concepto', 'sa_da', 'sa_db', 'sb_da', 'sb_db');
	
	public $timestamps = false;
	
	public function getKeyName(){
	    return "id_tarifa_valor";
	}

	public function paquete(){
	    return $this->hasOne('Paquete', 'id_paquete');
	}

	public function concepto(){
	    return $this->hasOne('Paqueteconcepto', 'id_concepto');
	}

}
