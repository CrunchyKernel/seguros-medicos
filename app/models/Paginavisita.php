<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Paginavisita extends Eloquent implements UserInterface, RemindableInterface {
	
	use UserTrait, RemindableTrait;
	
	protected $table = 'paginas_visitadas';
	
	protected $hidden = array();
	
	protected $fillable = array('url', 'isMobile', 'isTablet', 'isDesktop', 'isBot', 'browserFamily', 'browserVersionMajor', 'browserVersionMinor', 'browserVersionPatch', 'osFamily', 'osVersionMajor', 'osVersionMinor', 'osVersionPatch', 'deviceFamily', 'deviceModel', 'mobileGrade', 'cssVersion', 'javaScriptSupport');
	
	public $timestamps = false;
	
	public function getKeyName()
	{
	    return "id_pagina_visita";
	}
	
	public static function hit(){
		$userBrowser = BrowserDetect::detect();
	    $paginaVisita = new Paginavisita;
	    $paginaVisita->url = str_replace(url().'/', '', Request::url());
	    
	    foreach($paginaVisita->getFillable() AS $key) {
	        if($key != 'url'){
	            $paginaVisita->$key = BrowserDetect::$key();
	        }
	    }
	    $paginaVisita->save();
	}
	
}
