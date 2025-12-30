<?php

namespace App\Models\Backend;

use App\Models\Backend\User;

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Modulo extends \Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'modulos';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('fecha_registro', 'activo', 'orden');

	public $timestamps = false;

	public function getKeyName()
	{
	    return 'id_modulo';
	}
	
	public function users()
	{
        return $this->belongsToMany('User', 'modulos_permisos', 'id_usuario', 'id_usuario');
    }
    
    public function moduloshijos()
	{
        return $this->hasMany('App\Models\Backend\Modulo', 'id_padre', 'id_modulo');
    }
	
	static function getModuloIcono($modulo = ''){
    	if(strlen($modulo) > 0){
    		$icono = App\Models\Backend\Modulo::select('controlador_icono')->where('controlador', '=', $modulo)->get();
    		if(count($icono) == 1){
    			return $icono[0]->controlador_icono;
    		}
    	}
    	return '';
    }

}
