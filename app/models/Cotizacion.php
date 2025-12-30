<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Cotizacion extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	protected $table = 'cotizaciones';
	
	protected $fillable = array('id_agente', 'nombre', 'e_mail', 'telefono', 'estado', 'ciudad', 'integrantes', 'secret', 'estatus', 'visto', 'poliza_actual', 'comentarios', 'id_origen', 'id_dominio');
	
	public $timestamps = false;
	
	public function getKeyName(){
	    return "id_cotizacion";
	}
	public function estatus(){
		return $this->hasOne('Cotizacionestatus', 'id_estatus', 'estatus');
	}
	public function agente(){
		return $this->hasOne('App\Models\Backend\User', 'id_usuario', 'id_agente');
	}
	public function dominio(){
		return $this->hasOne('Domain', 'id_dominio', 'id_dominio');
	}
	public function seguimientos(){
		return $this->hasMany('Cotizacionseguimiento', 'id_cotizacion', 'id_cotizacion');
	}
	public function prioridad(){
		return $this->belongsToMany('App\Models\Backend\User', 'cotizaciones_prioridad', 'id_cotizacion', 'id_agente');
	}
	public static function siguienteCotizacionAgente(){
		//cotizaciónes con paquete seleccionado
		$cotizacionSiguiente = \Cotizacion::where('estatus', '=', 1)->where('paquete', '<>', 'null')->whereNotNull('sa')->whereNotNull('ded')->where('id_agente', '=', -1)->orderBy('estatus')->orderBy('fecha_registro', 'desc')->first();
		if($cotizacionSiguiente){
			return $cotizacionSiguiente->id_cotizacion;
		}
		//cotizaciones en espera
		$cotizacionSiguiente = \Cotizacion::where('estatus', '=', 2)->where('id_agente', '=', -1)->orderBy('estatus')->orderBy('fecha_registro', 'desc')->first();
		if($cotizacionSiguiente){
			return $cotizacionSiguiente->id_cotizacion;
		}
		//cotizaciones en proceso
		$cotizacionSiguiente = \Cotizacion::select('id_cotizacion')->where('id_agente', '=', \Auth::user()->id_usuario)->where('estatus', '=', 3)->orderBy('fecha_registro', 'desc')->first();
		if($cotizacionSiguiente){
			return $cotizacionSiguiente->id_cotizacion;
		}
		//cotizaciónes marcas como prioridad para el agente
		$programadas = \Cotizacionseguimiento::select('id_cotizacion')->where('realizado', '=', -1)->where('fecha_seguimiento', '<=', date('Y-m-d H:i:00'))->groupBy('id_cotizacion')->orderBy('fecha_seguimiento', 'desc')->get()->toArray();
		$cotizacionSiguiente = \Auth::user()->cotizacionPrioridad()->whereIn('cotizaciones_prioridad.id_cotizacion', $programadas)->orderBy('fecha_registro', 'desc')->first();
		if($cotizacionSiguiente){
			return $cotizacionSiguiente->id_cotizacion;
		}
		//cotizaciones programado
		$cotizacionSiguienteProgramada = \Cotizacion::select('id_cotizacion')->where('id_agente', '=', \Auth::user()->id_usuario)->where('estatus', '=', 6)->get()->toArray();
		$cotizacionSeguimientoProgramado = \Cotizacionseguimiento::whereIn('id_cotizacion', $cotizacionSiguienteProgramada)->where('realizado', '=', -1)->where('fecha_seguimiento', '<=', date('Y-m-d H:i:00'))->groupBy('id_cotizacion')->orderBy('fecha_seguimiento', 'desc')->first();
		if($cotizacionSeguimientoProgramado){
			if($cotizacionSeguimientoProgramado->id_cotizacion > 0){
				return $cotizacionSeguimientoProgramado->id_cotizacion;
			}
		}
		//cotizaciones con Segundo intento sin agente
		$cotizacionSiguiente = \Cotizacion::where('estatus', '=', 4)->where('id_agente', '=', -1)->orderBy('estatus')->orderBy('fecha_registro', 'desc')->first();
		if($cotizacionSiguiente){
			return $cotizacionSiguiente->id_cotizacion;
		}
		//cotizaciones Tercer intento sin agente
		$cotizacionSiguiente = \Cotizacion::where('estatus', '=', 5)->where('id_agente', '=', -1)->orderBy('estatus')->orderBy('fecha_registro', 'desc')->first();
		if($cotizacionSiguiente){
			return $cotizacionSiguiente->id_cotizacion;
		}
		return -1;
	}
	
	public function listaDistribucion(){
		return $this->hasOne('ListaDistribucion', 'id_lista', 'id_lista_distribucion');
	}
}
