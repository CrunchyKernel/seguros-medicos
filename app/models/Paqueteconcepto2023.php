<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Paqueteconcepto2023 extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	protected $table = 'paquete_conceptos_2023';
	
	protected $fillable = array('concepto', 'tooltip', 'orden');
	
	public $timestamps = false;
	
	public function getKeyName(){
	    return "id_concepto";
	}
	
	public function tarifaValor(){
	    return $this->hasMany('Paqueteconceptotarifa2023', 'id_concepto');
	}
	
}
