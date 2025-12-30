<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Estado extends Eloquent implements UserInterface, RemindableInterface {
	
	use UserTrait, RemindableTrait;
	
	protected $table = 'estados';
	
	protected $fillable = array('clave', 'estado', 'clave_mapfre');
	
	public $timestamps = false;
	
	public function getKeyName(){
	    return "id_estado";
	}
	
}