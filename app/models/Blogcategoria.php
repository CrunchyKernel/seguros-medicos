<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Blogcategoria extends Eloquent implements UserInterface, RemindableInterface {
	
	use UserTrait, RemindableTrait;
	
	protected $table = 'blog_categorias';
	
	protected $hidden = array();
	
	protected $fillable = array('categoria', 'categoria_alias', 'metakey', 'metadesc', 'orden', 'estatus');
	
	public $timestamps = false;
	
	public function getKeyName()
	{
	    return "id_blog_categoria";
	}

	public function contenidos(){
		return $this->belongsTo('Blog', 'id_blog_categoria', 'id_blog_categoria');
	}
	
	public function categoriaHijos(){
		return $this->hasMany('Blogcategoria', 'id_padre', 'id_blog_categoria');
	}
	
}
