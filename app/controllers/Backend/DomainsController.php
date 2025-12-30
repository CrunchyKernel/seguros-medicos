<?php

namespace Backend;

use App\Models\Backend\Usuario;
use App\Models\Backend\Usuariosesion;

class DomainsController extends \BaseController {
	public $ruta = '';
    public $scripts = array();
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
        $this->ruta = \Request::segment(2).'/'.\Request::segment(3);
        if(file_exists(public_path().'/backend/js/helpers/'.$this->ruta.'.js')){
            $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/helpers/".$this->ruta);
        }
        $this->ruta = str_replace("/", ".", $this->ruta);
    }
    
    public function altaDominio()
    {
    	$this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/x-editable/dist/bootstrap3-editable/js/bootstrap-editable.min");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/js/x-editable/dist/bootstrap3-editable/css/bootstrap-editable"); 
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/css/style.datatables");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/jquery.dataTables.min");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/dataTables/dataTables.bootstrap");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/dataTables/dataTables.responsive");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/jquery.validate.min");
        \View::share('scripts', $this->scripts);
        
        $this->layout->content = \View::make('backend.'.$this->ruta);
    }
    
    public function agregarDominio(){
    	$dominioDatos = \Input::except('activo');
		$dominio = new \Domain($dominioDatos);
		
		$activo = \Input::get('activo');
        if($activo == 'on')
        	$dominio->activo = 1;
        else
        	$dominio->activo = 0;
        
        $respuesta = array(
                        "status" => "invalid",
                        "titulo" => "Alta dominio",
                        "mensaje" => "Ocurrio un error al tratar de registrar la aseguradora",
                        "posicion" => "stack_bar_bottom",
                        "tipo" => "error",
                    );
        try{
			if($dominio->save()){
			 	 $respuesta['mensaje'] = 'Dominio agregado correctamente';
                $respuesta['status'] = 'success';
                $respuesta['tipo'] = 'success';
            }
		}catch(Exception $e){
            $respuesta['mensaje'] = $e->getMessage();
        }
        return json_encode($respuesta);
    } 
    
   	public function actualizarDominio(){
        $dominioDatos = \Input::all();
        $dominio = \Domain::find($dominioDatos["pk"]);
        $dominio->$dominioDatos["campo"] = $dominioDatos["value"];

        try{
            if($dominio->save()){
                return 1;
            }
        }catch(Exception $e){
            return 0;
        }
        return 0;
    } 
    
    public function getConsultaDominios(){
        $dominios = \Domain::where('id_dominio', '>', 0)->get();
        return \Datatable::collection($dominios)
            ->showColumns('id_dominio')
             ->addColumn('nombre', function($dominio)
            {
                 //return $paquete->paquete;
                 return '<a href="#" class="campo" data-campo="nombre" data-value="'.$dominio->nombre.'" data-type="text" data-original-title="Nombre" data-pk="'.$dominio->id_dominio.'"></a>';
            })
            ->addColumn('dominio', function($dominio)
            {
                 //return $paquete->paquete;
                 return '<a href="#" class="campo" data-campo="dominio" data-value="'.$dominio->dominio.'" data-type="text" data-original-title="Dominio" data-pk="'.$dominio->id_dominio.'"></a>';
            })
            ->addColumn('email', function($dominio)
            {
                //return $paquete->paquete_campo;
                return '<a href="#" class="campo" data-campo="email" data-value="'.$dominio->email.'" data-type="email" data-original-title="Email" data-pk="'.$dominio->id_dominio.'"></a>';
            })
            ->addColumn('sender', function($dominio)
            {
                //return $paquete->paquete_campo;
                return '<a href="#" class="campo" data-campo="sender" data-value="'.$dominio->sender.'" data-type="text" data-original-title="Remitente" data-pk="'.$dominio->id_dominio.'"></a>';
            })
            ->addColumn('logo', function($dominio)
            {
                //return $paquete->paquete_campo;
                return '<a href="#" class="campo" data-campo="logo" data-value="'.$dominio->logo.'" data-type="text" data-original-title="Logo" data-pk="'.$dominio->id_dominio.'"></a>';
            })
            ->addColumn('ver_cotizacion', function($dominio)
            {
                //return $paquete->paquete_campo;
                return '<a href="#" class="campo" data-campo="ver_cotizacion" data-value="'.$dominio->ver_cotizacion.'" data-type="text" data-original-title="Ver cotizacion" data-pk="'.$dominio->id_dominio.'"></a>';
            })
            ->addColumn('ver_cotizacion_nuevo', function($dominio)
            {
                //return $paquete->paquete_campo;
                return '<a href="#" class="campo" data-campo="ver_cotizacion_nuevo" data-value="'.$dominio->ver_cotizacion_nuevo.'" data-type="text" data-original-title="Ver cotizacion nuevo" data-pk="'.$dominio->id_dominio.'"></a>';
            })
            ->addColumn('estatus', function($dominio)
            {
                //return (($paquete->activo==1) ? 'Activo' : 'Inactivo');
                return '<a href="#" class="estatus" data-campo="activo" data-value="'.$dominio->activo.'" data-type="select" data-source=\'[ {value: 1, text: "Activo"}, {value: -1, text: "Inactivo"} ]\' data-original-title="Estatus" data-pk="'.$dominio->id_dominio.'"></a>';
            })
            ->searchColumns('id_dominio', 'nombre', 'dominio', 'email', 'remitente', 'estatus')
            ->orderColumns('id_dominio')
            ->make();
    }
    
    public function consultaDominios()
	{
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/x-editable/dist/bootstrap3-editable/js/bootstrap-editable.min");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/js/x-editable/dist/bootstrap3-editable/css/bootstrap-editable");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/css/style.datatables");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/jquery.dataTables.min");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/dataTables/dataTables.bootstrap");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/dataTables/dataTables.responsive");
        \View::share('scripts', $this->scripts);

        $this->layout->content = \View::make('backend.'.$this->ruta);
	}
}
?>