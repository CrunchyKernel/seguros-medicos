<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class ListaDistribucion extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	protected $table = 'listas_distribucion';
	
	protected $fillable = array('nombre');
	
	public $timestamps = false;
	
	public function getKeyName(){
	    return "id_lista";
	}
	
	public function Detalles(){
	    return $this->hasMany('ListaDistribucionDetalle', 'id_lista');
	}
}