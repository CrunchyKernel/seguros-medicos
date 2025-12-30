<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Chatonlinemensaje extends Eloquent implements UserInterface, RemindableInterface {
	
	use UserTrait, RemindableTrait;
	
	protected $table = 'chat_mensajes';
	
	protected $fillable = array('de', 'de_tipo', 'para', 'para_tipo', 'mensaje', 'leido', 'read_datetime');
	
	public $timestamps = false;
	
	public function getKeyName(){
	    return "id_chat";
	}
	
}