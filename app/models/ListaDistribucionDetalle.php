<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class ListaDistribucionDetalle extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	protected $table = 'listas_distribucion_detalle';
	
	protected $fillable = array('id_lista', 'plantilla', 'orden', 'tipo', 'hora', 'ignorar_0', 'ignorar_1', 'ignorar_2', 'ignorar_3', 'ignorar_4', 'ignorar_5', 'ignorar_6', 'dias', 'envio_0', 'envio_1', 'envio_2', 'envio_3', 'envio_4', 'envio_5', 'envio_6');
	
	public $timestamps = false;
	
	
	public function lista(){
		return $this->hasOne('ListaDistribucion', 'id_lista', 'id_lista');
	}
}