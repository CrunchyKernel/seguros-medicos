<?php 

namespace Backend;

use App\Models\Backend\User;
use App\Models\Backend\Cotizacion;
use App\Models\Backend\Usuariosesion;
use App\Models\Backend\Cotizacionseguimiento;

class CalendarioController extends \BaseController {

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

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/qtip2/jquery.qtip.min");
		$this->scripts[] = array("tipo" => "css", "archivo" => "backend/js/qtip2/jquery.qtip.min");
		$this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/moment.min");
		$this->scripts[] = array("tipo" => "css", "archivo" => "backend/css/style.calendar");
		$this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/fullcalendar.min");
        \View::share('scripts', $this->scripts);
        $this->layout->content = \View::make('backend.'.$this->ruta);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show()
	{
		$start = \Input::get('start');
		$end = \Input::get('end');
		
		//$seguimientosProgramados = Cotizacionseguimiento::select('id','title','start','end','all_day','color')->where('start','>=',$start)->orWhere('end','<=',$end)->get();
		$cotizaciones = \Cotizacion::select('id_cotizacion')->where('id_agente',\Auth::user()->id_usuario)->get()->toArray();
		$seguimientosProgramados = \Cotizacionseguimiento::select('id_seguimiento','id_cotizacion','fecha_seguimiento AS start','notas AS tooltip','realizado')->whereIn('id_cotizacion',$cotizaciones)->where('realizado',-1)->whereBetween('fecha_seguimiento',[$start,$end])->get();
		foreach($seguimientosProgramados AS $event) {
			$event->color = '#31b0d5';
            $cotizacion = \Cotizacion::find($event->id_cotizacion);
            if($cotizacion){
            	$event->title = $cotizacion->nombre;
            	if($cotizacion->id_agente == \Auth::user()->id_usuario){
            		$event->color = '#449d44';
            		$event->url = \URL::to('admingm/cotizacion/verCotizacion/'.$cotizacion->id_cotizacion.'/'.$cotizacion->secret);
            	}else{
            		$event->url = \URL::to('admingm/cotizacion/verCotizacionRapida/'.$cotizacion->id_cotizacion);
            	}
            	if($cotizacion->prioridad()->where('id_agente',\Auth::user()->id_usuario)->count() == 1){
            		$event->color = '#f0ad4e';
            	}
            }else{
            	$event->title = $cotizacion->id_seguimiento;
            }
        }
        return json_encode($seguimientosProgramados);
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function actualizarSeguimientosCalendario()
	{
		$response = [
            'status' => 'invalid',
            'tipo' => 'error',
            'mensaje' => 'Ocurrio un error al tratar de actualizar el seguimiento',
        ];
		$datos = \Input::all();
		$seguimiento = \Cotizacionseguimiento::find(\Input::get('id_seguimiento'));
		if($seguimiento){
			$seguimiento->fecha_seguimiento = \Input::get('start');
			if($seguimiento->save()){
				$response['status'] = 'success';
				$response['tipo'] = 'success';
				$response['mensaje'] = 'Seguimiento actualizado correctamente';
			}
		}
		return json_encode($response);
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}


}
