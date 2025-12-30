<?php

namespace Backend;

use App\Models\Backend\Cotizaion;
use App\Models\Backend\Cotizacionseguimiento;
use App\Models\Backend\User;
use App\Models\Backend\Usuariosesion;

class SeguimientoController extends \BaseController {
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

    public function getConsultaSeguimientos(){
    	$seguimientos = \Cotizacionseguimiento::all();
    	return \Datatable::collection($seguimientos)
            ->showColumns('id_seguimiento')
            ->addColumn('notas', function($seguimiento)
            {
                return $seguimiento->notas;
            })
            ->addColumn('fecha_seguimiento', function($seguimiento)
            {
                return $seguimiento->fecha_seguimiento;
            })
            ->addColumn('cliente', function($seguimiento)
            {
                return $seguimiento->cotizacion->nombre;
            })
            ->addColumn('agente', function($seguimiento)
            {
                return $seguimiento->cotizacion->agente->nombre;
            })
            ->addColumn('realizado', function($seguimiento)
            {
                return $seguimiento->realizado;
            })
            ->addColumn('opciones', function($seguimiento)
            {
                return '<a href="'.\URL::to('/admingm/cotizacion/verCotizacion/'.$seguimiento->cotizacion->id_cotizacion.'/'.$seguimiento->cotizacion->secret).'" data-toggle="tooltip" class="delete-row tooltips" data-original-title="Ver cotización"><i class="fa fa-eye"></i></a> 
                        <a href="#" data-toggle="tooltip" class="delete-row tooltips eliminarSeguimiento" data-idCotizacion="'.$seguimiento->id_seguimiento.'" data-original-title="Eliminar"><i class="fa fa-trash-o"></i></a>';
            })
            ->searchColumns('id_seguimiento', 'notas', 'fecha_seguimiento', 'cliente', 'agente', 'realizado')
            ->orderColumns('id_seguimiento', 'notas', 'fecha_seguimiento', 'cliente', 'agente', 'realizado')
            ->make();
    }
	
	public function consultaSeguimientos()
	{
		$this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/x-editable/dist/bootstrap3-editable/js/bootstrap-editable.min");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/js/x-editable/dist/bootstrap3-editable/css/bootstrap-editable");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/css/style.datatables");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/jquery.dataTables.min");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/dataTables/dataTables.bootstrap");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/dataTables/dataTables.responsive");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/select2.min");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/css/select2");
		\View::share('scripts', $this->scripts);

        $this->layout->content = \View::make('backend.'.$this->ruta);
	}
}