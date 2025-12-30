<?php

namespace App\Models\Backend;

use App\Models\Backend\User;

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class ModuloPermiso extends \Eloquent implements UserInterface, RemindableInterface {
	
	use UserTrait, RemindableTrait;
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'modulos_permisos';
	
	protected $fillable = array('id_modulo', 'id_usuario', 'acceso');

	protected $hidden = array('');

	public $timestamps = false;

	public function getKeyName()
	{
	    return 'id_modulo';
	}
	
}
