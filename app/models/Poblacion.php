<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Poblacion extends Eloquent implements UserInterface, RemindableInterface {
	
	use UserTrait, RemindableTrait;
	
	protected $table = 'poblaciones';
	
	protected $fillable = array('id_estado', 'poblacion', 'clave_mapfre');
	
	public $timestamps = false;
	
	public function getKeyName(){
	    return "id_poblacion";
	}
	
}