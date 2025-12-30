<?php
/**
 * Created by PhpStorm.
 * User: desarrollo-protecto
 * Date: 12/07/16
 * Time: 09:37 AM
 */

Class CotizacionController extends \BaseController {
    public $ruta = '';
    public $scripts = array();
    protected $layout = 'layout.masterPDF';

    
    public function cotizacion2016($idCotizacion = -1, $secret = ''){
        $cotizacionDatos = Cotizacion::find($idCotizacion);
        if($cotizacionDatos){

            if($cotizacionDatos->visto == -1){
                $cotizacionDatos->visto = 1;
                $cotizacionDatos->save();
            }
            $cotizacionDatos->integrantes = json_decode($cotizacionDatos->integrantes);
            $sumaAsegurada = 'sb';
            $dedubicle = 'db';
            foreach($cotizacionDatos->integrantes AS $integrante){
                if($integrante->edad > 54){
                    $sumaAsegurada = 'sa';
                    $dedubicle = 'da';
                }
            }

            View::share('cotizacionDatos', $cotizacionDatos);
            $cotizacion = new Cotizador($cotizacionDatos, $sumaAsegurada, $dedubicle);
            $cotizacion::cotizar();
            $tablaDatos = $cotizacion::tablaDatos2016();
            View::share('tablaDatos', $tablaDatos);

            $tablaClienteDatos = $cotizacion::tablaClienteDatos2016();
            View::share('tablaClienteDatos', $tablaClienteDatos);

            $tablaIntegrantes = $cotizacion::tablaIntegrantes2016();
            View::share('tablaIntegrantes', $tablaIntegrantes);

            $textoProtecto = \DB::table('texto_pdf')->where('id_texto_pdf', 1)->get()[0];
            View::share('textoProtecto', $textoProtecto->texto_pdf);
            View::share('fechaRegistro', $cotizacionDatos->fecha_registro);
            //Traemos los textos descriptivos de cada plan para agregarlos al pdf.
            //Se seleciconan sólo los de las aseguradoras activas y plan activo
            $textos_aseguradora_activa = \DB::table('aseguradoras')
                ->join('paquetes', 'aseguradoras.id_aseguradora', '=','paquetes.id_aseguradora')
                ->where('aseguradoras.activa', 1) //1 = activo
                ->where('paquetes.activo', 1)
                ->select('paquetes.descripcion_backend', 'paquetes.paquete', 'aseguradoras.nombre')
                ->orderBy('paquetes.id_paquete', 'DESC')
                ->get();

            View::share('textos_plan_activo', $textos_aseguradora_activa);

            View::share('PDF', 1);
            $this->layout->content = View::make('cotizacion.cotizacionPDF');
        }else{
            return Redirect::to('/cotizador');
        }
    }

    /*Función que reciba los parametros para llamar a la vista de la tabla correspondiente, los pase y generé el pdf.
    dicho pdf debe ser mostrado en pantalla*/
    public function muestraPDF($idCotizacion, $mostrar = true, $sa = 'sb', $ded = 'db'){
        $cotizacionDatos = Cotizacion::find($idCotizacion);

        return PDF::loadFile('http://segurodegastosmedicosmayores.mx/cotizacion/verCotizacionPDF/'.$idCotizacion.'/'.$cotizacionDatos->secret.'/'.$sa.'/'.$ded)
            ->stream($cotizacionDatos->nombre.''.$idCotizacion.'.pdf');
    }

    public function  generarCotizacionPDF($idCotizacion, $mostrar = true, $sa = 'sb', $ded = 'db'){
        $cotizacionDatos = Cotizacion::find($idCotizacion);
        $cotizacionDatos->integrantes = json_decode($cotizacionDatos->integrantes);
        $cotizacion = new Cotizador($cotizacionDatos, $sa, $ded);
        $cotizacion::cotizar();
        View::share('cotizacionDatos', $cotizacionDatos);
        View::share('tablaDatos', $cotizacion::tablaDatos2016());
        View::share('tablaIntegrantes', $cotizacion::tablaIntegrantes2016());
        View::share('tablaClienteDatos', $cotizacion::tablaClienteDatos2016());

        $textoProtecto = \DB::table('texto_pdf')->where('id_texto_pdf', 1)->get()[0];
        View::share('textoProtecto', $textoProtecto->texto_pdf);
        View::share('fechaRegistro', $cotizacionDatos->fecha_registro);
        //Traemos los textos descriptivos de cada plan para agregarlos al pdf.
        //Se seleciconan sólo los de las aseguradoras activas y plan activo
        $textos_aseguradora_activa = \DB::table('aseguradoras')
            ->join('paquetes', 'aseguradoras.id_aseguradora', '=','paquetes.id_aseguradora')
            ->where('aseguradoras.activa', 1) //1 = activo
            ->where('paquetes.activo', 1)
            ->select('paquetes.descripcion_backend', 'paquetes.paquete', 'aseguradoras.nombre')
            ->orderBy('paquetes.id_paquete', 'DESC')
            ->get();

        View::share('textos_plan_activo', $textos_aseguradora_activa);
        $this->layout->content = View::make('cotizacion.cotizacionPDF');
        
        //$ruta = 'tmp/cotizacion_'.$cotizacionDatos->id_cotizacion.'_'.$sa.'_'.$ded.'.pdf';
        //return $ruta;
    }

    public function previsualizar($idDominio){
    	$textoEncabezado = "Aqui va el encabezado del correo";
		$textoCuerpo = "Aqui va el cuerpo del correo";
		$textoPie = "Aqui va el pie del correo";
        $cotizacionDatos = \Cotizacion::first();
        $encabezado = \DB::table('texto_pdf')
            ->where('texto_seccion', 'texto_correo_encabezado')
            ->where('id_dominio', $idDominio)
            ->first();
        if($encabezado)
        	$textoEncabezado = $encabezado->texto_pdf;
        $cuerpo = \DB::table('texto_pdf')
            ->where('texto_seccion', 'texto_correo_cuerpo')
            ->where('id_dominio', $idDominio)
            ->first();
        if($cuerpo)
        	$textoCuerpo = $cuerpo->texto_pdf;
        $pie = \DB::table('texto_pdf')
            ->where('texto_seccion', 'texto_correo_pie')
            ->where('id_dominio', $idDominio)
            ->where('id_usuario', -1)
            ->first();
        if($pie)
        	$textoPie = $pie->texto_pdf;
        \View::share('id_cotizacion', 1);
        \View::share('nombre', $cotizacionDatos->nombre);
        \View::share('secret', $cotizacionDatos->secret);
        \View::share('cuerpo', $textoCuerpo);
        \View::share('pie', $textoPie);
        \View::share('encabezado', $textoEncabezado);
        \View::share('scripts', $this->scripts);
        $this->layout->content = \View::make('backend.correo.previsualizar');
    }
    
}
