<?php

namespace Backend;

use App\Models\Backend\Usuario;
use App\Models\Backend\Usuariosesion;

class AseguradorasController extends \BaseController {
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

    public function agregarAseguradora(){
        $idAseguradora = \Input::get('id_aseguradora');
        if($idAseguradora>0){
			$aseguradora = \Aseguradora::find($idAseguradora);
			$aseguradora->nombre = \Input::get('nombre');
			$aseguradora->aseguradora = \Input::get('aseguradora');
			$aseguradora->orden = \Input::get('orden');
		}
		else{
			$aseguradoraDatos = \Input::except('logo', 'interes_semestral', 'interes_trimestral', 'interes_mensual', 'inflar', 'activa', 'imagen_cotizador', 'imagen_pdf');
			$aseguradora = new \Aseguradora($aseguradoraDatos);
		}
		
		$configuracion = '';
		$logo = \Input::get('logo');
		$interes_semestral = \Input::get('interes_semestral');
		$interes_trimestral = \Input::get('interes_trimestral');
		$interes_mensual = \Input::get('interes_mensual');
		$inflar = \Input::get('inflar');
		$configuracion = '{"logo":"' . $logo . '", "interes_semestral":' . $interes_semestral . ', "interes_trimestral":' . $interes_trimestral . ', "interes_mensual":' . $interes_mensual . ', "inflar":' . $inflar . '}';
		$aseguradora->configuracion = $configuracion;
		
		$activa = \Input::get('activa');
        if($activa == 'on')
        	$aseguradora->activa = 1;
        else
        	$aseguradora->activa = 0;
        
        $respuesta = array(
                        "status" => "invalid",
                        "titulo" => "Alta aseguradora",
                        "mensaje" => "Ocurrio un error al tratar de registrar la aseguradora",
                        "posicion" => "stack_bar_bottom",
                        "tipo" => "error",
                        "idAseguradora" => -1,
                    );
        try{
			if($aseguradora->save()){
				if($idAseguradora > 0){
                    $respuesta['mensaje'] = 'Aseguradora actualizada correctamente';
                }else{
                	$idAseguradora = $aseguradora->id_aseguradora;
                    $respuesta['mensaje'] = 'Aseguradora agregada correctamente';
                }
                $respuesta['status'] = 'success';
                $respuesta['tipo'] = 'success';
                $respuesta['idAseguradora'] = $idAseguradora;
                
                $cotizador = \Input::file('imagen_cotizador');
        		if($cotizador){
					$cotizador->move(public_path() . '/assets/images/aseguradoras', $idAseguradora . ".jpg");
				}
				
				$pdf = \Input::file('imagen_pdf');
        		if($pdf){
					$pdf->move(public_path() . '/images_post/images', $idAseguradora . ".jpg");
				}
			}
		}catch(Exception $e){
            $respuesta['mensaje'] = $e->getMessage();
        }
        return json_encode($respuesta);
    }
	
	public function agregarPlan(){
		$planDatos = \Input::except('activo');
		$paquete = new \Paquete($planDatos);
		
		$activo = \Input::get('activo');
        if($activo == 'on')
        	$paquete->activo = 1;
        else
        	$paquete->activo = 0;
         
        $paquete->descripcion_backend = "";
        
        $respuesta = array(
                        "status" => "invalid",
                        "titulo" => "Agregar plan",
                        "mensaje" => "Ocurrio un error al tratar de agregar el plan",
                        "posicion" => "stack_bar_bottom",
                        "tipo" => "error",
                    );
        try{
			if($paquete->save()){
				$respuesta['status'] = 'success';
				$respuesta['mensaje'] = 'Se agrego correctamente el plan';
            	$respuesta['tipo'] = 'success';
			}
		}catch(Exception $e){
            $respuesta['mensaje'] = $e->getMessage();
        }
        return json_encode($respuesta);
	}
	
    public function altaAseguradora($idAseguradora = -1)
    {
    	if($idAseguradora > 0){
            $aseguradoraDatos = \Aseguradora::find($idAseguradora);
            if($aseguradoraDatos){
            	$interes = json_decode($aseguradoraDatos["configuracion"], true);
            	$aseguradoraDatos["logo"] = $interes["logo"];
            	$aseguradoraDatos["interes_semestral"] = $interes["interes_semestral"];
            	$aseguradoraDatos["interes_trimestral"] = $interes["interes_trimestral"];
            	$aseguradoraDatos["interes_mensual"] = $interes["interes_mensual"];
            	$aseguradoraDatos["inflar"] = $interes["inflar"];
            	if(file_exists(public_path() . '/assets/images/aseguradoras/' . $idAseguradora . '.jpg'))
            		$aseguradoraDatos["imagen_cotizador"] = \URL::to('assets/images/aseguradoras/' . $idAseguradora . '.jpg');
            	if(file_exists(public_path() . '/images_post/images/' . $idAseguradora . '.jpg'))
            		$aseguradoraDatos["imagen_pdf"] = \URL::to('images_post/images/' . $idAseguradora . '.jpg');
                \View::share('aseguradoraDatos', $aseguradoraDatos);
            }
        }
        
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/ckeditor/ckeditor");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/ckeditor/adapters/jquery");
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

    public function actualizarAdministrador(){
        $administradorDatos = \Input::all();
        $administrador = User::find($administradorDatos["pk"]);
        $administrador->$administradorDatos["campo"] = $administradorDatos["value"];

        try{
            if($administrador->save()){
                return 1;
            }
        }catch(Exception $e){
            return 0;
        }
        return 0;
    }

    public function getConsultaAseguradoras(){
        $aseguradoras = \Aseguradora::where('id_aseguradora', '>', 0)->get();
        return \Datatable::collection($aseguradoras)
            ->showColumns('id_aseguradora')
            ->addColumn('nombre', function($aseguradora)
            {
                //return $administrador->nombre.' '.$administrador->apellido_paterno.' '.$administrador->apellido_materno;
                return '<a href="'.\URL::to('admingm/aseguradoras/altaAseguradora/'.$aseguradora->id_aseguradora).'">'.$aseguradora->nombre.'</a>';
            })
            ->addColumn('aseguradora', function($aseguradora)
            {
                return $aseguradora->aseguradora;
            })
            ->addColumn('configuracion', function($aseguradora)
            {
                return $aseguradora->configuracion;
            })
            ->addColumn('orden', function($aseguradora)
            {
                return $aseguradora->orden;
            })
            ->addColumn('estatus', function($aseguradora)
            {
                return (($aseguradora->activa==1) ? 'Activa' : 'Inactiva');
            })
            ->searchColumns('id_aseguradora', 'nombre', 'aseguradora', 'configuracion', 'orden', 'estatus')
            ->orderColumns('orden')
            ->make();
    }

	public function getConsultaAseguradoraPlanes($idAseguradora){
        $paquetes = \Paquete::where('id_aseguradora', '=', $idAseguradora)->get();
        return \Datatable::collection($paquetes)
            ->showColumns('id_paquete')
            ->addColumn('paquete', function($paquete)
            {
                 //return $paquete->paquete;
                 return '<a href="#" class="campo" data-campo="paquete" data-value="'.$paquete->paquete.'" data-type="text" data-original-title="Plan" data-pk="'.$paquete->id_paquete.'"></a>';
            })
            ->addColumn('clave', function($paquete)
            {
                //return $paquete->paquete_campo;
                return '<a href="#" class="campo" data-campo="paquete_campo" data-value="'.$paquete->paquete_campo.'" data-type="text" data-original-title="Clave" data-pk="'.$paquete->id_paquete.'"></a>';
            })
            ->addColumn('derecho_poliza', function($paquete)
            {
                //return $paquete->derecho_poliza;
                return '<a href="#" class="campo" data-campo="derecho_poliza" data-value="'.$paquete->derecho_poliza.'" data-type="number" data-original-title="Derecho de poliza" data-pk="'.$paquete->id_paquete.'"></a>';
            })
            ->addColumn('orden', function($paquete)
            {
                //return $paquete->orden;
                return '<a href="#" class="campo" data-campo="orden" data-value="'.$paquete->orden.'" data-type="number" data-original-title="Orden" data-pk="'.$paquete->id_paquete.'"></a>';
            })
            ->addColumn('estatus', function($paquete)
            {
                //return (($paquete->activo==1) ? 'Activo' : 'Inactivo');
                return '<a href="#" class="estatus" data-campo="activo" data-value="'.$paquete->activo.'" data-type="select" data-source=\'[ {value: 1, text: "Activo"}, {value: -1, text: "Inactivo"} ]\' data-original-title="Estatus" data-pk="'.$paquete->id_paquete.'"></a>';
            })
            ->searchColumns('id_paquete', 'paquete', 'clave', 'derecho_poliza', 'orden', 'estatus')
            ->orderColumns('orden')
            ->make();
    }
	
	public function consultaAseguradoras()
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

	public function actualizarAseguradora(){
        $paqueteDatos = \Input::all();
        $paquete = \Paquete::find($paqueteDatos["pk"]);
        $paquete->$paqueteDatos["campo"] = $paqueteDatos["value"];

        try{
            if($paquete->save()){
                return 1;
            }
        }catch(Exception $e){
            return 0;
        }
        return 0;
    }

	public function guardarWeb(){
        $textoPT = \Input::all();
        \DB::table('aseguradoras')->where('id_aseguradora', $textoPT['id'])->update(array('descripcion_web' => $textoPT['textoPT']));
        return 1;
    }
    
    public function guardarMobile(){
        $textoPT = \Input::all();
        \DB::table('aseguradoras')->where('id_aseguradora', $textoPT['id'])->update(array('descripcion_movil' => $textoPT['textoPT']));
        return 1;
    }
    
    public function guardarPromo(){
        $textoPT = \Input::all();
        \DB::table('aseguradoras')->where('id_aseguradora', $textoPT['id'])->update(array('descripcion_promo' => $textoPT['textoPT']));
        return 1;
    }
}