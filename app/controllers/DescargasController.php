<?php
Class DescargasController extends \BaseController {
	public function download($alias){
		$archivo = Archivo::where("alias", $alias)->first();
		if($archivo){
			$headers = array("Content-Type" => $archivo->tipo);
			return Response::download(public_path() . "/descargas/" . $archivo->archivo, $archivo->descarga, $headers);
		}
		else{
			App::abort(404);
		}
	}
}
