<?php

namespace Backend;

use App\Models\Backend\User;
use App\Models\Backend\Usuariosesion;

class MainController extends \BaseController {
	protected $layout = 'backend.layout.master';

	public function __construct()
    {
        $this->beforeFilter(function(){
            if(!\Auth::check()){
                return \Redirect::to('/admingm/login/cerrarSesion');
            }
            $sesionRegistrada = Usuariosesion::where('id_usuario', '=', \Auth::user()->id_usuario)->get();
            if(count($sesionRegistrada) == 0){
                return \Redirect::to('/admingm/login/cerrarSesion');
            }
            Usuariosesion::actualizarSesionTiempo();
        });
    }
	
	public function main()
	{
        
        
        $this->layout->content = \View::make('backend.main');
	}
}