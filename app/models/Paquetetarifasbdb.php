<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Paquetetarifasbdb extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	protected $table = 'tarifa_sb_db';
	
	protected $fillable = array('id_paquete', 'edad', 'tarifa_m', 'tarifa_f');
	
	public $timestamps = false;
	
	public function getKeyName(){
	    return "id_tarifa";
	}
	
	public function paquete(){
		return $this->hasOne('Paquete', 'id_paquete', 'id_paquete');
	}
	
}