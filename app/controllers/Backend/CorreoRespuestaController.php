<?php 

namespace Backend;

class CorreoRespuestaController extends \BaseController {
	protected $layout = 'backend.layout.master';
    public $ruta = '';
    public $scripts = array();
    
    public function __construct()
    {
        $this->beforeFilter(function(){
            if(!\Auth::check()){
                return \Redirect::to('/admingm/login/cerrarSesion');
            }
        });
        $this->ruta = strtolower(\Request::segment(2)).'/'.\Request::segment(3);
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
		$this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/x-editable/dist/bootstrap3-editable/js/bootstrap-editable.min");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/js/x-editable/dist/bootstrap3-editable/css/bootstrap-editable");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/css/style.datatables");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/jquery.dataTables.min");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/dataTables/dataTables.bootstrap");
        //$this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/dataTables/dataTables.responsive");
        \View::share('scripts', $this->scripts);

		$dominios = \Domain::select('id_dominio', 'nombre')->get();
        \View::share('dominios', $dominios);

		$this->layout->content = \View::make('backend.'.strtolower($this->ruta));
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
		$respuesta = array(
                        "status" => "invalid",
                        "mensaje" => "Ocurrio un error al tratar de guardar la respeusta de correo",
                        "posicion" => "stack_bar_bottom",
                        "tipo" => "error",
                    );
		$respuestaCorreo = new \CorreoRespuesta(\Input::all());
		try{
			if($respuestaCorreo->save()){
				$respuesta['status'] = 'success';
				$respuesta['mensaje'] = 'Respuesta guardada correctamente';
				$respuesta['tipo'] = 'success';
			}
		}catch(Exception $e){

		} catch (\Illuminate\Database\QueryException $e) {
			$respuesta['mensaje'] = 'Revise que el título de la respuesta no se encuentre repetida';
		}
		return json_encode($respuesta);
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show()
	{
		$correoRespuestas = \CorreoRespuesta::all();
		return \Datatable::collection($correoRespuestas)
            ->showColumns('id')
            ->addColumn('dominio', function($correoRespuesta)
            {
                return '<a href="#" class="campo" data-campo="id_dominio" data-value="'.$correoRespuesta->id_dominio.'" data-type="select" data-source="'.\URL::to('admingm/listasJson/dominiosJson').'" data-original-title="Dominio" data-pk="'.$correoRespuesta->id.'"></a>';
            })
            ->addColumn('titulo', function($correoRespuesta)
            {
                return '<a href="#" class="campo" data-campo="titulo" data-value="'.$correoRespuesta->titulo.'" data-type="text" data-original-title="Título" data-pk="'.$correoRespuesta->id.'"></a>';
            })
            ->addColumn('contenido', function($correoRespuesta)
            {
                return '<a href="#" class="campo" data-campo="contenido" data-value="'.$correoRespuesta->contenido.'" data-type="textarea" data-original-title="Contenido" data-pk="'.$correoRespuesta->id.'"></a>';
            })
            ->addColumn('opciones', function($correoRespuesta)
            {
                return '<a href="#" data-toggle="tooltip" class="delete-row tooltips eliminarCorreoRespuesta" data-id="'.$correoRespuesta->id.'" data-titulo="'.$correoRespuesta->titulo.'" data-original-title="Eliminar"><i class="fa fa-trash-o"></i></a>';
            })
            ->searchColumns('id', 'titulo', 'contenido')
            ->orderColumns('id', 'titulo', 'contenido')
            ->make();
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update()
	{
		$correoRespuesta = \CorreoRespuesta::find(\Request::input('pk'));
		if($correoRespuesta){
			$correoRespuesta->{\Request::input('campo')} = \Request::input('value');
			if($correoRespuesta->save()){
				return 1;
			}
		}
		return 0;
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$correoRespuesta = \CorreoRespuesta::find($id);
		$respuesta = array(
                        "status" => "invalid",
                        "mensaje" => "Ocurrio un error al tratar de guardar el formulario",
                        "posicion" => "stack_bar_bottom",
                        "tipo" => "error",
                        "titulo" => "Elimiar",
                    );
		if($correoRespuesta && $correoRespuesta->delete()){
			$respuesta['status'] = 'success';
			$respuesta['tipo'] = 'success';
			$respuesta['mensaje'] = 'Respuesta de correo eliminada correctamente';
		}
		return json_encode($respuesta);
	}


}
