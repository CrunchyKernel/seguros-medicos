<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Cotizacionseguimiento extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	protected $table = 'cotizaciones_seguimientos';
	
	protected $fillable = array('id_cotizacion', 'fecha_seguimiento', 'notas', 'fecha_cierre', 'realizado', 'fecha_registro');
	
	public $timestamps = false;
	
	public function getKeyName(){
	    return "id_seguimiento";
	}
	public function cotizacion(){
		return $this->hasOne('Cotizacion', 'id_cotizacion', 'id_cotizacion');
	}
}