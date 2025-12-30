<?php

namespace Backend;

use App\Models\Backend\User;
use App\Models\Backend\Usuariosesion;

class ListasJsonController extends \BaseController {
    public $ruta = '';
    public $scripts = array();
	//protected $layout = 'layouts.master';

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

    public function agentesJson(){
        $agentes = array();
        foreach(User::where('activo', '=', 1)->orderBy('nombre')->get() AS $agente)
        {
            $agentes[] = array('value' => $agente->id_usuario, 'text' => ucwords(\Str::lower($agente->nombre.' '.$agente->apellido_paterno) ) );
        }
        return json_encode($agentes);
    }

    public function categoriasBlogJson(){
        $categorias = array();
        foreach(\Blogcategoria::where('id_blog_categoria', '>', 0)->orderBy('categoria')->get() AS $categoria)
        {
            $categoriaPadre = \Blogcategoria::find($categoria->id_padre);
            $categorias[] = array('value' => $categoria->id_blog_categoria, 'text' => ucwords(\Str::lower( (($categoriaPadre->id_blog_categoria > 0) ? $categoriaPadre->categoria.' - ' : '').$categoria->categoria)) );
        }
        return json_encode($categorias);
    }

    public function cotizacionEstatusJson(){
        $estatus = array();
        foreach(\Cotizacionestatus::select('id_estatus','estatus')->orderBy('orden')->get() AS $estatu)
        {
            $estatus[] = array('value' => $estatu->id_estatus, 'text' => $estatu->estatus );
        }
        return json_encode($estatus);
    }

	public function dominiosJson(){
        $dominios = array();
        foreach(\Domain::all() AS $dominio)
        {
            $dominios[] = array('value' => $dominio->id_dominio, 'text' => ucwords(\Str::lower($dominio->nombre) ) );
        }
        return json_encode($dominios);
    }
}
