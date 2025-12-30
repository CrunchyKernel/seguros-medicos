<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Paqueteconcepto extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	protected $table = 'paquete_conceptos';
	
	protected $fillable = array('concepto', 'nombre', 'orden');
	
	public $timestamps = false;
	
	public function getKeyName(){
	    return "id_concepto";
	}
	
	public function tarifaValor(){
	    return $this->hasMany('Paqueteconceptotarifa', 'id_concepto');
	}
	
}
