<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Redireccion extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	protected $table = 'redirecciones';
	
	protected $fillable = array('alias', 'redirect_to', 'tipo');
	
	public $timestamps = false;
	
	public function getKeyName(){
	    return "id";
	}
	
}