<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Gmsitiomenu extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'menus';
	
	public $timestamps = false;
	
	//protected $fillable = array('id_padre', 'nombre', 'url_amigable', 'titulo', 'url_tipo', 'url_target', 'visible', 'orden');
	protected $fillable = array('');
	
	public function getKeyName(){
	    return "id_sitio_menu";
	}
	
	public function menuHijos(){
		return $this->hasMany('Gmsitiomenu', 'id_padre', 'id_sitio_menu');
	}

}
