<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class RecotizacionMapfre extends Eloquent implements UserInterface, RemindableInterface {
	
	use UserTrait, RemindableTrait;
	
	protected $table = 'recotizaciones_mapfre';
	
	protected $fillable = array(
		'id_cotizacion', 
		'tipo', 
		'hospitales', 
		'xml', 
		'respuesta', 
		'contado', 
		'semestral_primer', 
		'semestral_posterior', 
		'trimestral_primer', 
		'trimestral_posterior', 
		'mensual_primer', 
		'mensual_posterior',
		'sa',
		'deducible',
		'coaseguro',
		'tope_coaseguro',
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
	    return "id_recotizacion";
	}
	
}