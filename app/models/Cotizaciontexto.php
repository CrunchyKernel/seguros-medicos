<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Cotizaciontexto extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'texto_pdf';
	
	protected $fillable = array('texto_pdf', 'texto_seccion', 'id_usuario', 'id_dominio');
	
	public function getKeyName(){
	    return "id_texto_pdf";
	}

	
}
