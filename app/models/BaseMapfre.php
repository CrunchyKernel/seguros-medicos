<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class BaseMapfre extends Eloquent implements UserInterface, RemindableInterface {
	
	use UserTrait, RemindableTrait;
	
	protected $table = 'base_mapfre';
	
	protected $fillable = array(
		'sa',
		'deducible',
		'coaseguro',
		'tabulador',
		'emergencia_extranjero',
		'sa_maternidad',
		'reduccion_deducible',
		'dental',
		'complicaciones',
		'vanguardia',
		'multiregion',
		'preexistentes',
		'catastroficas',
		'funeraria'
	);
	
	public $timestamps = false;
	
	public function getKeyName(){
	    return "id_base";
	}
	
}