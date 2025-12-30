<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Cotizadores extends Eloquent implements UserInterface, RemindableInterface {
	
	use UserTrait, RemindableTrait;
	
	protected $table = 'cotizadores';
	
	protected $fillable = array(
		'alias',
		'movil'
	);
	
	//public $timestamps = false;
	
	public function getKeyName(){
	    return "id_cotizador";
	}
	
}