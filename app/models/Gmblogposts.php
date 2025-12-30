<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Gmblogposts extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'sitio_blog_posts';

	public $timestamps = false;
	
	//protected $fillable = array('titulo', 'url_amigable', 'introtext', 'metakeys', 'metadesc', 'publicado');
	protected $fillable = array('');
	
	public function getKeyName(){
	    return "id_blog_post";
	}

	
}