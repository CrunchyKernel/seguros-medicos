<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class CotizacionWhatsapp extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	protected $table = 'cotizaciones_whatsapp';
	
	protected $fillable = array('id_cotizacion', 'id_lista_distribucion', 'plantilla', 'fecha_envio', 'ch_result', 'ch_error');
	
	public $timestamps = false;
	
	public $incrementing = false;
}