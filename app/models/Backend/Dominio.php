<?php

namespace App\Models\Backend;

class Dominio extends \Eloquent  {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'dominios';
    protected $fillable = array('dominio', 'host', 'descripcion', 'database', 'username', 'password');
    public $timestamps = false;

}