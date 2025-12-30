<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Faq extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	protected $table = 'faq';
	
	protected $fillable = array('pregunta', 'respuesta', 'icono', 'activo');
	
	public $timestamps = false;
	
	public function getKeyName(){
	    return "id_faq";
	}

	
}