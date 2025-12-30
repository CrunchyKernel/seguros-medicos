<?php

namespace Backend;

use App\Models\Backend\User;
use App\Models\Backend\Usuariosesion;
use App\Models\Backend\Modulo;
use App\Models\Backend\ModuloPermiso;

class AdministradorController extends \BaseController {
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

    public function actualizarPassword(){
        $respuesta = array(
                        "status" => "invalid",
                        "titulo" => "Actualizar contraseña",
                        "mensaje" => "Ocurrio un error al tratar de actualizar la contraseña",
                        "posicion" => "stack_bar_bottom",
                        "tipo" => "error",
                    );
        $datos = \Input::all();
        $datos['passwordActual'] = sha1($datos['passwordActual']);
        $datos['passwordNuevo'] = sha1($datos['passwordNuevo']);
        if(\Auth::user()->contrasena == $datos['passwordActual']){
            \Auth::user()->contrasena = $datos['passwordNuevo'];
            if(\Auth::user()->save()){
                $respuesta['status'] = 'success';
                $respuesta['tipo'] = 'success';
                $respuesta['mensaje'] = 'Contraseña actualizada correctamente';
            }
        }
        return json_encode($respuesta);
    }

    public function miPerfil(){
    	$this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/ckeditor/ckeditor");
    	$this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/ckeditor/adapters/jquery");
    	$this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/helpers/administrador/miPerfil_pie");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/jquery.validate.min");
        
        $pie = \DB::table('texto_pdf')
            ->where('texto_seccion', 'texto_correo_pie')
            ->where('id_usuario', \Auth::user()->id_usuario)
            ->where('id_dominio', 1)
            ->first(); 
        if($pie)
        	\View::share('pie', $pie->texto_pdf);
        else
        	\View::share('pie', "");
        \View::share('scripts', $this->scripts);

		$dominios = \Domain::select('id_dominio', 'nombre')->orderBy('id_dominio')->get();
        \View::share('dominios', $dominios);
		
        $this->layout->content = \View::make('backend.'.$this->ruta);
    }

    public function actualizarModuloPermiso(){
        $idModulo = \Input::get('idModulo');
        $idUsuario = \Input::get('idUsuario');
        $acceso = \Input::get('acceso');

        //$moduloPermiso = \DB::table("modulos_permisos")->where('id_modulo', '=', $idModulo)->where('id_usuario', '=', $idUsuario)->get();
        $moduloPermiso = ModuloPermiso::firstOrNew(array('id_modulo' => $idModulo, 'id_usuario' => $idUsuario));
        $moduloPermiso->acceso = $acceso;
        
        $respuesta = array(
                        "status" => "invalid",
                        "titulo" => "Agregar administrador",
                        "mensaje" => "Ocurrio un error al tratar de agregar al hijo",
                        "posicion" => "stack_bottom_left",
                        "tipo" => "error",
                    );
        if($moduloPermiso->save()){
            $respuesta["status"] = "success";
            $respuesta["tipo"] = "success";
            $respuesta["mensaje"] = "Permiso asignado";
        }
        return json_encode($respuesta);
    }

    public function permisosAdministrador($idAdministrador){
        $administradorDatos = User::find($idAdministrador);
        if($administradorDatos){
            \View::share('administradorDatos', $administradorDatos);

            $modulos = Modulo::where('id_padre', '=', -1)->orderBy('orden')->get();
            $modulosHtml = '<table class="table table-info" style="margin-bottom: 0px !important;">';
            if(count($modulos) > 0){
                foreach($modulos AS $modulo){
                    $modulosHijos = $modulo->moduloshijos()->orderBy('orden')->get();
                    if(count($modulosHijos) > 0){
                        $modulosHtml .= '<thead>
                                                <tr>
                                                    <th colspan="3" class="alignCenter"><i class="'.$modulo->controlador_icono.'"></i> '.strtoupper($modulo->controlador).'</th>
                                                </tr>
                                                <tr>
                                                    <th class="alignCenter" style="width: 80px;">ID</th>
                                                    <th class="alignCenter" style="vertical-align: middle;">Módulo</th>
                                                    <th class="alignCenter" style="width: 200px;">Permiso</th>
                                                </tr>
                                            </thead>
                                            <tbody>';
                        foreach($modulosHijos AS $moduloHijo){
                            $modulosHtml .= '<tr>
                                                <td class="alignCenter" style="vertical-align: middle;">'.$moduloHijo->id_modulo.'</td>
                                                <td style="vertical-align: middle;"><i class="'.$moduloHijo->controlador_icono.'"></i> '.$moduloHijo->controlador.'</td>
                                                <td class="alignCenter">'.User::comboPermisos($moduloHijo->id_modulo, $administradorDatos->id_usuario).'</td>
                                            </tr>';
                        }
                        $modulosHtml .= '</tbody>';
                    }
                }
            }
            $modulosHtml .= '</table>';
            \View::share('modulosHtml', $modulosHtml);

            $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/select2.min");
            $this->scripts[] = array("tipo" => "css", "archivo" => "backend/css/select2");
            \View::share('scripts', $this->scripts);
            
            $this->layout->content = \View::make('backend.'.$this->ruta);
        }else{
            return \Redirect::to('/admingm/administrador/consultaAdministradores');
        }
    }

    public function agregarAdministrador(){
        $administradorDatos = \Input::except('enviarAcceso');
        $enviarAcceso = \Input::get('enviarAcceso');
        $contrasenia = '';
        if(strlen($administradorDatos['contrasena']) > 0){
            $contrasenia = $administradorDatos['contrasena'];
        }else{
            $contrasenia = str_random(15);
        }
        $administrador = new User($administradorDatos);
        $administrador->activo = (($administrador->activo == 'on') ? 1 : 0);
        $administrador->contrasena = sha1($contrasenia);
        
        $respuesta = array(
                        "status" => "invalid",
                        "titulo" => "Agregar administrador",
                        "mensaje" => "Ocurrio un error al tratar de agregar al hijo",
                        "posicion" => "stack_bar_bottom",
                        "tipo" => "error",
                    );
        if($administrador->save()){
            $respuesta['mensaje'] = 'Administrador agregado correctamente';
            $respuesta['status'] = 'success';
            $respuesta['tipo'] = 'success';
            if($enviarAcceso == 'on'){
                if(\SistemaFunciones::enviarAdministradorAcceso($administrador, $contrasenia) == true){
                    $respuesta['mensaje'] .= '<br>Correo de acceso enviado correctamente.';
                }
            }
        }
        return json_encode($respuesta);
    }
	
    public function altaAdministrador()
    {
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/jquery.validate.min");
        \View::share('scripts', $this->scripts);
        
        $this->layout->content = \View::make('backend.'.$this->ruta);
    }

    public function enviarAccesoAdministrador(){
        $idAdministrador = \Input::get('idAdministrador');
        $administradorDatos = User::find($idAdministrador);

        $respuesta = array(
                        "status" => "invalid",
                        "titulo" => "Acceso administrador",
                        "mensaje" => "Ocurrio un error al tratar de enviar el acceso al administrador",
                        "posicion" => "stack_bar_bottom",
                        "tipo" => "error",
                    );
        if($administradorDatos){
            $contrasenia = str_random(15);
            if(\SistemaFunciones::enviarAdministradorAcceso($administradorDatos, $contrasenia) == true){
                $administradorDatos->contrasena = sha1($contrasenia);
                if($administradorDatos->save()){
                    $respuesta['mensaje'] = 'Correo de acceso enviado correctamente.';
                    $respuesta['status'] = 'success';
                    $respuesta['tipo'] = 'success';
                }
            }
        }
        return json_encode($respuesta);
    }

    public function eliminarAdministrador(){
        $idAdministrador = \Input::get('idAdministrador');
        $administradorDatos = User::find($idAdministrador);
        $respuesta = array(
                        "status" => "invalid",
                        "titulo" => "Eliminar administrador",
                        "mensaje" => "Ocurrio un error al tratar de eliminar al administrador",
                        "posicion" => "stack_bar_bottom",
                        "tipo" => "error",
                    );
        if($administradorDatos->delete()){
            $respuesta['mensaje'] = 'Administrador eliminado correctamente';
            $respuesta['status'] = 'success';
            $respuesta['tipo'] = 'success';
        }
        return json_encode($respuesta);
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

    public function getConsultaAdministradores(){
        $administradores = User::where('id_usuario', '>', 0)->get();
        return \Datatable::collection($administradores)
            ->showColumns('id_usuario')
            ->addColumn('nombre', function($administrador)
            {
                //return $administrador->nombre.' '.$administrador->apellido_paterno.' '.$administrador->apellido_materno;
                return '<a href="#" class="campo" data-campo="nombre" data-value="'.$administrador->nombre.'" data-type="text" data-original-title="Nombre" data-pk="'.$administrador->id_usuario.'"></a> <a href="#" class="campo" data-campo="apellido_paterno" data-value="'.$administrador->apellido_paterno.'" data-type="text" data-original-title="Apellido paterno" data-pk="'.$administrador->id_usuario.'"></a> <a href="#" class="campo" data-campo="apellido_materno" data-value="'.$administrador->apellido_materno.'" data-type="text" data-original-title="Apellido materno" data-pk="'.$administrador->id_usuario.'"></a>';
            })
            ->addColumn('telefono_celular', function($administrador)
            {
                return '<a href="#" class="campo" data-campo="telefono_celular" data-value="'.$administrador->telefono_celular.'" data-type="text" data-original-title="Teléfono celular" data-pk="'.$administrador->id_usuario.'"></a>';
            })
            ->addColumn('telefono_particular', function($administrador)
            {
                return '<a href="#" class="campo" data-campo="telefono_particular" data-value="'.$administrador->telefono_particular.'" data-type="text" data-original-title="Teléfono particular" data-pk="'.$administrador->id_usuario.'"></a>';
            })
            ->addColumn('e_mail', function($administrador)
            {
                return '<a href="#" class="campo" data-campo="e_mail" data-value="'.$administrador->e_mail.'" data-type="text" data-original-title="Correo electrónico" data-pk="'.$administrador->id_usuario.'"></a>';
            })
            ->addColumn('puesto', function($administrador)
            {
                return '<a href="#" class="campo" data-campo="puesto" data-value="'.$administrador->puesto.'" data-type="text" data-original-title="Puesto" data-pk="'.$administrador->id_usuario.'"></a>';
            })
            ->addColumn('activo', function($administrador)
            {
                return '<a href="#" class="campo" data-campo="activo" data-value="'.$administrador->activo.'" data-type="select" data-source=\'[ {value: 1, text: "Activo"}, {value: -1, text: "Inactivo"} ]\' data-original-title="Activo" data-pk="'.$administrador->id_usuario.'"></a>';
            })
            ->addColumn('opciones', function($administrador)
            {
                if($administrador->id_usuario != \Auth::user()->id_usuario){
                    return '<a href="#" data-toggle="tooltip" class="delete-row tooltips enviarAccesoAdministrador" data-idAdministrador="'.$administrador->id_usuario.'" data-original-title="Enviar acceso"><i class="fa fa-envelope-o"></i></a> 
                            <a href="'.\URL::to('/admingm/administrador/permisosAdministrador/'.$administrador->id_usuario).'" data-toggle="tooltip" class="delete-row tooltips"data-original-title="Permisos"><i class="fa fa-cogs"></i></a> 
                            <a href="#" data-toggle="tooltip" class="delete-row tooltips eliminarAdministrador" data-idAdministrador="'.$administrador->id_usuario.'" data-original-title="Eliminar"><i class="fa fa-trash-o"></i></a>';
                }
                return '';
            })
            ->searchColumns('id_usuario', 'nombre', 'telefono_celular', 'telefono_particular', 'e_mail', 'puesto', 'activo')
            ->orderColumns('id_usuario', 'nombre', 'telefono_celular', 'telefono_particular', 'e_mail', 'puesto', 'activo')
            ->make();
    }

	public function consultaAdministradores()
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

	public function guardaPie(){
		$r = 0;
        $text = \Input::all();
        try{
	        $pie = \DB::table('texto_pdf')
	            ->where('texto_seccion', 'texto_correo_pie')
	            ->where('id_usuario', \Auth::user()->id_usuario)
	            ->where('id_dominio', $text['id_dominio'])
	            ->first();
	        if($pie){
		        \DB::table('texto_pdf')
		        	->where('texto_seccion', "texto_correo_pie")
		        	->where('id_usuario', \Auth::user()->id_usuario)
		        	->where('id_dominio', $text['id_dominio'])
		        	->update(array('texto_pdf' => $text['textoPie']));
		        $r = 1;
			}
		    else{
		    	\DB::table('texto_pdf')
		    		->insert(array('texto_pdf' => $text['textoPie'], 
		    						'texto_seccion' => 'texto_correo_pie', 
		    						'id_usuario' => \Auth::user()->id_usuario, 
		    						'id_dominio' => $text['id_dominio']
		    						));
		    	$r = 1;
			}
		}catch(Exception $e){
            $r = 0;
        }
        return $r;
    }

	public function consultaPie($idDominio){
		$textoPie = "Aqui va el pie del correo";
		$pie = \DB::table('texto_pdf')->where('texto_seccion', 'texto_correo_pie')->where('id_usuario', \Auth::user()->id_usuario)->where('id_dominio', $idDominio)->get();
		if($pie)
			$textoPie = $pie[0]->texto_pdf;
		$res = array("textoPie" => utf8_encode($textoPie));
		return json_encode($res);
	}
}