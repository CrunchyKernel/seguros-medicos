<?php

namespace Backend;

class ContenidoCorreoController extends \BaseController{
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
        $this->ruta = \Request::segment(2).'/'.\Request::segment(3);
        if(file_exists(public_path().'/backend/js/helpers/'.$this->ruta.'.js')){
            $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/helpers/".$this->ruta);
        }

        $this->ruta = str_replace("/", ".", $this->ruta);

    }
    
    public function editarContenido()
    {
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/helpers/correo/contenido");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/ckeditor/ckeditor");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/ckeditor/adapters/jquery");

        $encabezado = \DB::table('texto_pdf')
            ->where('texto_seccion', 'texto_correo_encabezado')
            ->where('id_dominio', 1)
            ->first();
        $cuerpo = \DB::table('texto_pdf')
            ->where('texto_seccion', 'texto_correo_cuerpo')
            ->where('id_dominio', 1)
            ->first();
        $pie = \DB::table('texto_pdf')
            ->where('texto_seccion', 'texto_correo_pie')
            ->where('id_dominio', 1)
            ->first();

        //\View::share('', $this->previsualizar());
        \View::share('scripts', $this->scripts);
        \View::share('cuerpo', $cuerpo->texto_pdf);
        \View::share('pie', $pie->texto_pdf);
        \View::share('encabezado', $encabezado->texto_pdf);
        \View::share('idDominio', 1);
        
        $dominios = \Domain::select('id_dominio', 'nombre')->orderBy('id_dominio')->get();
        \View::share('dominios', $dominios); 
        
       $this->layout->content = \View::make('backend.'.strtolower($this->ruta));
    }

    public function guardaEncabezado(){
        $text = \Input::all();
        $pdf = \DB::table('texto_pdf')->where('texto_seccion', 'texto_correo_encabezado')->where('id_dominio', $text['id_dominio'])->get();
        if($pdf){
	        try{
	        	\DB::table('texto_pdf')
	            ->where('texto_seccion', "texto_correo_encabezado")
	            ->where('id_dominio', $text['id_dominio'])
	            ->update(array('texto_pdf' => $text['textoEncabezado']));
	            $r = 1;
			}catch(Exception $e){
	            $r = 0;
	        }
		}
		else{
			$r = 1;
			$pdf = new \Cotizaciontexto();
			$pdf->texto_pdf = $text['textoEncabezado'];
			$pdf->texto_seccion = 'texto_correo_encabezado';
			$pdf->id_usuario = -1;
			$pdf->id_dominio = $text['id_dominio'];
			$pdf->save();
		}
        return $r;
    }

    public function guardaCuerpo(){
        $text = \Input::all();
        $pdf = \DB::table('texto_pdf')->where('texto_seccion', 'texto_correo_cuerpo')->where('id_dominio', $text['id_dominio'])->get();
    	if($pdf){
	        try{
	        	\DB::table('texto_pdf')
	        	->where('texto_seccion', "texto_correo_cuerpo")
	        	->where('id_dominio', $text['id_dominio'])
	            ->update(array('texto_pdf' => $text['textoCuerpo']));
				$r = 1;
			}catch(Exception $e){
	            $r = 0;
	        }
		}
		else{
			$r = 1;
			$pdf = new \Cotizaciontexto();
			$pdf->texto_pdf = $text['textoCuerpo'];
			$pdf->texto_seccion = 'texto_correo_cuerpo';
			$pdf->id_usuario = -1;
			$pdf->id_dominio = $text['id_dominio'];
			$pdf->save();
		}
        return $r;
    }

    public function guardaPie(){
        $text = \Input::all();
        $pdf = \DB::table('texto_pdf')->where('texto_seccion', 'texto_correo_pie')->where('id_usuario', -1)->where('id_dominio', $text['id_dominio'])->get();
    	if($pdf){
	        try{
	        	\DB::table('texto_pdf')
	        	->where('texto_seccion', "texto_correo_pie")
	        	->where('id_usuario', -1)
	        	->where('id_dominio', $text['id_dominio'])
	            ->update(array('texto_pdf' => $text['textoPie']));
	            $r = 1;
			}catch(Exception $e){
	            $r = 0;
	        }
		}
		else{
			$r = 1;
			$pdf = new \Cotizaciontexto();
			$pdf->texto_pdf = $text['textoPie'];
			$pdf->texto_seccion = 'texto_correo_pie';
			$pdf->id_usuario = -1;
			$pdf->id_dominio = $text['id_dominio'];
			$pdf->save();
		}
        return $r;
    }

	public function consultaContenidoCorreo($idDominio){
		$textoEncabezado = "Aqui va el encabezado del correo";
		$textoCuerpo = "Aqui va el cuerpo del correo";
		$textoPie = "Aqui va el pie del correo";
		$encabezado = \DB::table('texto_pdf')->where('texto_seccion', 'texto_correo_encabezado')->where('id_dominio', $idDominio)->get();
		$cuerpo = \DB::table('texto_pdf')->where('texto_seccion', 'texto_correo_cuerpo')->where('id_dominio', $idDominio)->get();
		$pie = \DB::table('texto_pdf')->where('texto_seccion', 'texto_correo_pie')->where('id_usuario', -1)->where('id_dominio', $idDominio)->get();
		if($encabezado)
			$textoEncabezado = $encabezado[0]->texto_pdf;
		if($cuerpo)
			$textoCuerpo = $cuerpo[0]->texto_pdf;
		if($pie)
			$textoPie = $pie[0]->texto_pdf;
		$res = array("textoEncabezado" => utf8_encode($textoEncabezado), "textoCuerpo" => utf8_encode($textoCuerpo), "textoPie" => utf8_encode($textoPie));
		return json_encode($res);
	}
}
