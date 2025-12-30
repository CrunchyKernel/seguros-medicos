<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Blogvisita extends Eloquent implements UserInterface, RemindableInterface {
	
	use UserTrait, RemindableTrait;
	
	protected $table = 'blog_visitas';
	
	protected $hidden = array();
	
	protected $fillable = array('id_blog', 'ip', 'fecha_visita');
		
	public $timestamps = false;
	
	public function getKeyName()
	{
	    return "id_blog_visita";
	}

	public function contenido(){
		return $this->hasOne('Blog', 'id_blog', 'id_blog');
	}
	
}
