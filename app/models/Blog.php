<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Blog extends Eloquent implements UserInterface, RemindableInterface {
	
	use UserTrait, RemindableTrait;
	
	protected $table = 'blog';
	
	protected $hidden = array();
	
	protected $fillable = array('alias', 'titulo', 'introtext', 'contenido', 'id_blog_categoria', 'metakey', 'metadesc', 'imagen_full', 'imagen_medium', 'imagen_small', 'html', 'builder', 'tipo', 'orden', 'fuentes', 'estatus', 'fecha_publicacion', 'fecha_registro', 'incluir_cotizador');
	
	public $timestamps = false;
	
	public function getKeyName()
	{
	    return "id_blog";
	}
	
	public function categoria(){
		return $this->hasOne('Blogcategoria', 'id_blog_categoria', 'id_blog_categoria');
	}
	
	public function visitas(){
		return $this->hasMany('Blogvisita', 'id_blog', 'id_blog');
	}
	
	public static function hitVisita($id_blog = -1){
		if($id_blog > 0){
			$request = Request::instance();
	        $request->setTrustedProxies(array('127.0.0.1'));
	        $ip = $request->getClientIp();
	        
	        $Blogvisita = Blogvisita::firstOrNew(array('id_blog' => $id_blog, 'ip' => $ip, 'fecha_visita' => date('Y-m-d') ));
	        $Blogvisita->save();
		}
	}

}
