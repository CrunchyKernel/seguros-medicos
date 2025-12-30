<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Emailwhite extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'email_whitelist';

	public $timestamps = false;
	
	protected $fillable = array('e_mail', 'cotizacionesTotales', 'fecha_registro');
	
	public function getKeyName(){
	    return "id_email_whitelist";
	}

	
}
