<?php

namespace App\Models\Backend;

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends \Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'administradores';
	
	protected $fillable = array('nombre', 'apellido_paterno', 'apellido_materno', 'telefono_particular', 'telefono_celular', 'puesto', 'e_mail', 'contrasena', 'activo');
	
	protected $hidden = array('password', 'remember_token');

	public $timestamps = false;
	
	public function getKeyName()
	{
	    return 'id_usuario';
	}

	public function modulos()
	{
        return $this->belongsToMany('App\Models\Backend\Modulo', 'modulos_permisos', 'id_usuario', 'id_modulo');
    }
	
	function sesiones(){
		return $this->hasOne('Login');
	}

	static function comboPermisos($id_modulo = "", $id_usuario = -1){
        $permisos_array[] = array("permiso" => "Ninguno", "valor" => "-1");
        $permisos_array[] = array("permiso" => "Administrador", "valor" => "1");
        $permisos_array[] = array("permiso" => "Usuario", "valor" => "2");
        
        $permisos_cmb = "<select class='permisoUsuario' data-idModulo='$id_modulo' data-idUsuario='$id_usuario' style='width: 150px;'>";
        foreach($permisos_array AS $permiso){
			$valor_selected = -1;
        	$permiso_usuario = \DB::table("modulos_permisos")->select("acceso")->where("id_modulo", "=", $id_modulo)->where("id_usuario", "=", $id_usuario)->get();
        	if(count($permiso_usuario) == 1){
        		$valor_selected = $permiso_usuario[0]->acceso;
        	}
            $permisos_cmb .= '<option value="'.$permiso['valor'].'" '.(($permiso['valor'] == $valor_selected) ? 'selected' : '').'>'.$permiso['permiso'].'</option>';
        }
        $permisos_cmb .= "</select>";
        
        return $permisos_cmb;
    }

    public function cotizacionPrioridad()
	{
        return $this->belongsToMany('Cotizacion', 'cotizaciones_prioridad', 'id_agente', 'id_cotizacion');
    }
    
}
