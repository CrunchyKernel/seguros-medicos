<?php
namespace Backend;

use App\Models\Backend\Dominio;
use Illuminate\Support\Facades\Input;
use phpbrowscap\Exception;

class DominiosController extends \BaseController {
    protected $layout = 'backend.layout.master';
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        $scripts[] = array("tipo" => "js", "archivo" => "backend/js/helpers/dominiosMain");
        \View::share('scripts', $scripts);
        
        $this->layout->content = \View::make('backend.main');
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
		$nuevoDominio = \Input::all();

		try{
			$respuesta = \DB::table('dominios')->insert($nuevoDominio);
		}catch(Exception $e){
			echo $e->getMessage();
		}

		if($respuesta){
			$respuesta = "Se ha almacenado correctamente el dominio ". $nuevoDominio['dominio'];
		}else{
			$respuesta = "Se ha producido un error al agregar el dominio ". $nuevoDominio['dominio'];
		}

		return $respuesta;
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($dominio)
	{
		$retorno = \DB::table('dominios')->where("dominio", $dominio)->get();

		return \Response::json($retorno[0]);
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
	public function update($id)
	{
		$actualizaDominio = \Input::all();

		$respuesta =  \DB::table('dominios')
				->where('dominio',$actualizaDominio['dominio'])
				->update($actualizaDominio);

		if($respuesta > 0){
			$respuesta = "Se ha modificado correctamente";
		}else{
			$respuesta = "Ha ocurrido un error. Verifique los campos y vuelva a interntarlo";
		}
			/*$dominio->dominio = (isset($acualizaDominio['dominio'])) ? $acualizaDominio['dominio']
				: $dominio->dominio ;
			$dominio->host = (isset($acualizaDominio['host'])) ? $acualizaDominio['host']
				: $dominio->host ;
			$dominio->descripcion = (isset($acualizaDominio['descripcion'])) ? $acualizaDominio['descripcion']
				: $dominio->descripcion ;
			$dominio->database = (isset($acualizaDominio['database'])) ? $acualizaDominio['database']
				: $dominio->database ;
			$dominio->username = (isset($acualizaDominio['username'])) ? $acualizaDominio['username']
				: $dominio->username ;
			$dominio->password = (isset($acualizaDominio['password'])) ? $acualizaDominio['password']
				: $dominio->password ;

			if($acualizaDominio->save()){
				$respuesta = "Se ha actualizado el dominio correctamente";
			}else{
				$respuesta = "Existió un error en la actualización. Revise los campos y vuelva a interntarlo";
			}*/
		return $respuesta;
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

	/**
	 * Con ésta función cambiamos las credenciales de la conexión predeterminada
	 * de la base de datos a las requeridas por el usuario desde la pagina
	 * "http://segurodegastosmedicos.app/admingm/main", la cual variará
	 * según el dominio. Los datos para la conexíon se obtendran de la tabla "dominios"
	 */
	public function cambiaDominioDB(){
		$dominio = \Input::all();
		#Se obtiene la url completa, es por eso que se hace un explode con
		# "//"
		$dominio =  explode("//", $dominio['dominio'])[1];

		$datosDominio = \DB::table('dominios')->where("dominio", $dominio)->get()[0];

		//"Seteamos" la configuración para la conexión a la base de datos
		\Config::set( 'local.database.connections.dominio.host', $datosDominio->host );
		\Config::set( 'local.database.connections.dominio.username', $datosDominio->username );
		\Config::set( 'local.database.connections.dominio.password', $datosDominio->password );
		\Config::set( 'local.database.connections.dominio.database', $datosDominio->database );
		\DB::purge('mysql');
		//\DB::connection('mysql');
		\DB::reconnect( 'dominio' );
		dd(\DB::connection( 'dominio' ));
		/*\DB::purge('mysql');
		dd(\DB::connection( 'dominio' ));
		\Config::set( 'database.default', 'dominio' );
		//dd( \DB::connection( 'dominio' ) );
		*/
	}


}
