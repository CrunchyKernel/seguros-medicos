<?php

class Archivo extends Eloquent {

	protected $table = 'archivos';
	
	protected $fillable = array('archivo', 'alias', 'descarga', 'tipo');
	
	public $timestamps = false;
	
	public function getKeyName(){
	    return "id";
	}
}