<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Cotizacionestatus extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'cotizaciones_estatus';

	public $timestamps = false;
	
	protected $fillable = array('estatus', 'texto');
	
	public function getKeyName(){
	    return "id_estatus";
	}

	
}
