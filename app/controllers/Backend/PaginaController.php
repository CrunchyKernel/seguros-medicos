<?php

namespace Backend;

use App\Models\Backend\User;
use App\Models\Backend\Usuariosesion;
use App\Models\Backend\PaginaSeccion;

class PaginaController extends \BaseController {
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

    public function cargarPaginaImagen(){
        $uploads_dir = 'backend/builder/elements/images_post';
        $uploads_dir_front_end = 'images_post';
        $relative_path = 'images_post';
        $allowed_types = array("image/jpeg", "image/gif", "image/png", "image/svg", "application/pdf");
        /* DON'T CHANGE ANYTHING HERE!! */
        $return = array();
        if( !is_dir($uploads_dir) || !is_dir($uploads_dir_front_end) ) {
            $return['code'] = 0;
            $return['response'] = "The specified upload location does not exist. Please provide a correct folder in /iupload.php";
            die( json_encode( $return ) );
        }   
        //is the folder writable?
        if( !is_writable( $uploads_dir ) ) {
            $return['code'] = 0;
            $return['response'] = "The specified upload location is not writable. Please make sure the specified folder has the correct write permissions set for it.";
            die( json_encode( $return ) );
        }
        if ( !isset($_FILES['imageFileField']['error']) || is_array($_FILES['imageFileField']['error']) ) {
            $return['code'] = 0;
            $return['response'] = "Something went wrong with the file upload; please refresh the page and try again.";
            die( json_encode( $return ) );
        } 
        $name = $_FILES['imageFileField']['name'];
        $file_type = $_FILES['imageFileField']['type'];
        if(in_array($file_type, $allowed_types)) {
            if (move_uploaded_file( $_FILES['imageFileField']['tmp_name'], $uploads_dir."/".$name )) {
                copy($uploads_dir."/".$name, $uploads_dir_front_end."/".$name);
                //echo "yes";
            } else {
                $return['code'] = 0;
                $return['response'] = "The uploaded file couldn't be saved. Please make sure you have provided a correct upload folder and that the upload folder is writable.";
            }
            //print_r ($_FILES);
            $return['code'] = 1;
            $return['response'] = $relative_path."/".$name;
        } else {
            $return['code'] = 0;
            $return['response'] = "File type not allowed";
        }
        return json_encode( $return );
    }
    
    public function guardarPagina(){
        $paginaDatos = \Input::all();
        $pagina = \Blog::find($paginaDatos['idPagina']);
        if($pagina){
            $pagina->html = $paginaDatos['html'];
            $pagina->builder = $paginaDatos['builder'];
            if($pagina->save()){
                return array('realizado' => false);
            }
        }
        return array('realizado' => true);
    }
    
    public function getPaginaDatos(){
        $idPagina = \Input::get('idPagina');
        $paginaSecciones = \Blog::find($idPagina);
        return json_encode(array('html' => $paginaSecciones->html, 'secciones' => $paginaSecciones->builder));
    }

    public function editarPagina($idPagina){
        $paginaDatos = \Blog::find($idPagina);
        if($paginaDatos){
            $publicaciones = \Blog::where('id_blog', '=', $idPagina)->where('estatus', '=', 1)->orderBy('titulo')->get();
            if(count($publicaciones) > 0){
                $publicacionesLista = array();
                foreach($publicaciones AS $publicacion){
                    $publicacionesLista[] = array(
                                                'titulo' => $publicacion->titulo.' - '.$publicacion->categoria()->first()->categoria,
                                                'url' => $publicacion->categoria()->first()->categoria_alias.'/'.$publicacion->alias
                                            );
                }
                \View::share('publicacionesLista', $publicacionesLista);
            }
            \View::share('paginaDatos', $paginaDatos);
            return \View::make('backend.'.$this->ruta);
        }
    }

    public function altaPagina(){
        $paginaSecciones = PaginaSeccion::all();
        \View::share('paginaSecciones', $paginaSecciones);

        \View::share('scripts', $this->scripts);
        $this->layout->content = \View::make('backend.'.$this->ruta);
    }

    private function actualizarMenuHijosOrden($id_padre, $hijos, $orden){
        if(is_array($hijos)){
            foreach($hijos AS $hijo){
                if(isset($hijo["children"]) && count($hijo["children"] > 0)){
                    $this->actualizarMenuHijosOrden($hijo["id"], $hijo["children"], $orden);
                }
                $menuDatos = \Gmsitiomenu::find($hijo["id"]);
                $menuDatos->orden = $orden;
                $menuDatos->id_padre = $id_padre;
                $menuDatos->save();
                $orden++;
            }
        }
    }

    public function actualizarMenusOrden(){
        $menuEstructura = \Input::get('menu');
        if(is_array($menuEstructura)){
            $orden = 1;
            foreach($menuEstructura AS $menu){
                $menuDatos = \Gmsitiomenu::find($menu["id"]);
                $menuDatos->orden = $orden;
                $menuDatos->id_padre = -1;
                $menuDatos->save();
                if(isset($menu["children"]) && is_array($menu["children"])){
                    $this->actualizarMenuHijosOrden($menu["id"], $menu["children"], $orden);
                }
                $orden++;
            }
        }
        return 1;
    }

    public function eliminarMenu(){
        $idSitioMenu = \Input::get('idSitioMenu');

        $menuDatos = \Gmsitiomenu::find($idSitioMenu);
        $respuesta = array(
                        "status" => "invalid",
                        "titulo" => "Eliminar hijo",
                        "mensaje" => "Ocurrio un error al tratar de eliminar al hijo",
                        "posicion" => "stack_bar_bottom",
                        "tipo" => "error",
                    );
        \DB::beginTransaction();
        $menuDatos->menuHijos()->update(array('id_padre' => $menuDatos->id_padre));
        if($menuDatos->delete()){
            $respuesta['mensaje'] = 'Menú eliminado correctamenta';
            $respuesta['status'] = 'success';
            $respuesta['tipo'] = 'success';
            \DB::commit();
        }else{
            \DB::rollback();
        }
        return json_encode($respuesta);
    }

    public function agregarMenuHijo(){
        $idPadre = \Input::get('idPadre');
        $nodoHijo = \Input::get('nodoHijo');

        $respuesta = array(
                        "status" => "invalid",
                        "titulo" => "Agregar hijo",
                        "mensaje" => "Ocurrio un error al tratar de agregar al hijo",
                        "posicion" => "stack_bar_bottom",
                        "tipo" => "error",
                    );
        $menuPadre = \Gmsitiomenu::find($idPadre);
        $menuUltimoDatos = \Gmsitiomenu::where('id_padre', '=', $menuPadre->id_sitio_menu)->orderBy('orden', 'desc')->first();
        $ultimo = 1;
        if($menuUltimoDatos){
            $ultimo = $menuUltimoDatos->orden++;
        }
        $menuDatos = new \Gmsitiomenu;
        $menuDatos->nombre = $nodoHijo;
        $menuDatos->titulo = $nodoHijo;
        $menuDatos->url_amigable = \SistemaFunciones::aliasCategoria(strtolower($menuPadre->titulo.' '.$nodoHijo));
        $menuDatos->id_padre = $idPadre;
        $menuDatos->orden = $ultimo;
        
        if($menuDatos->save()){
            $respuesta['mensaje'] = 'Hijo agregado correctamente';
            $respuesta['status'] = 'success';
            $respuesta['tipo'] = 'success';
        }
        return json_encode($respuesta);
    }

    private function nestableHijos($id_padre = -1, &$nestableCategorias){
        if($id_padre > 0){
            $categoriasHijos = \Gmsitiomenu::where('id_padre', '=', $id_padre)->orderBy('orden')->get();
            if(count($categoriasHijos) > 0){
                $nestableCategorias .= '<ol class="dd-list">';
                foreach($categoriasHijos AS $categoriaHijo){
                    $nestableCategorias .= '<li class="dd-item dd3-item" data-id="'.$categoriaHijo->id_sitio_menu.'">
                                            <div class="dd-handle dd3-handle">Drag</div>
                                            <div class="dd3-content">
                                                <strong>Menú:</strong> <a href="#" class="campo" data-campo="titulo" data-value="'.$categoriaHijo->titulo.'" data-type="text" data-pk="'.$categoriaHijo->id_sitio_menu.'" data-original-title="Categoría"></a> | <strong>URL amigable:</strong> <a href="#" class="campo" data-campo="url_amigable" data-value="'.$categoriaHijo->url_amigable.'" data-type="text" data-pk="'.$categoriaHijo->id_sitio_menu.'" data-original-title="Alias"></a>
                                                <div class="pull-right">
                                                    <a href="#" class="tooltips mr5 agregarHijo" data-nombre="'.$categoriaHijo->titulo.'" data-idSitioMenu="'.$categoriaHijo->id_sitio_menu.'" data-toggle="tooltip" title="" data-original-title="Agregar hijo para: '.$categoriaHijo->titulo.'"><i class="fa fa-plus"></i></a>
                                                    <a href="#" id="addnewtodo" class="tooltips eliminarHijo" data-nombre="'.$categoriaHijo->titulo.'" data-idSitioMenu="'.$categoriaHijo->id_sitio_menu.'" data-toggle="tooltip" title="" data-original-title="Eliminar: '.$categoriaHijo->titulo.'"><i class="fa fa-trash-o"></i></a>
                                                </div>
                                            </div>';
                    self::nestableHijos($categoriaHijo->id_sitio_menu, $nestableCategorias);
                    $nestableCategorias .= '</li>';
                }
                $nestableCategorias .= '</ol>';
            }
        }
    }

    public function actualizarMenu(){
        $menuDatos = \Input::all();
        $menu = \Gmsitiomenu::find($menuDatos["pk"]);
        $menu->$menuDatos["campo"] = $menuDatos["value"];

        try{
            if($menu->save()){
                return 1;
            }
        }catch(Exception $e){
            
        }
        return 0;
    }

    public function getMenuNestableMenu(){
        $menus = \Gmsitiomenu::where('id_sitio_menu', '>', 0)->where('id_padre', '=', -1)->orderBy('orden')->get();
        $nestableCategorias = '';
        if(count($menus) > 0){
            foreach($menus AS $menu){
                $nestableCategorias .= '<li class="dd-item dd3-item" data-id="'.$menu->id_sitio_menu.'">
                                            <div class="dd-handle dd3-handle">Drag</div>
                                            <div class="dd3-content">
                                                <strong>Menú:</strong> <a href="#" class="campo" data-campo="titulo" data-value="'.$menu->titulo.'" data-type="text" data-pk="'.$menu->id_sitio_menu.'" data-original-title="Categoría"></a> | <strong>URL amigable:</strong> <a href="#" class="campo" data-campo="url_amigable" data-value="'.$menu->url_amigable.'" data-type="text" data-pk="'.$menu->id_sitio_menu.'" data-original-title="Alias"></a>
                                                <div class="pull-right">
                                                    <a href="#" class="tooltips mr5 agregarHijo" data-nombre="'.$menu->titulo.'" data-idSitioMenu="'.$menu->id_sitio_menu.'" data-toggle="tooltip" title="" data-original-title="Agregar hijo para: '.$menu->titulo.'"><i class="fa fa-plus"></i></a>
                                                    <a href="#" id="addnewtodo" class="tooltips eliminarHijo" data-nombre="'.$menu->titulo.'" data-idSitioMenu="'.$menu->id_sitio_menu.'" data-toggle="tooltip" title="" data-original-title="Eliminar: '.$menu->titulo.'"><i class="fa fa-trash-o"></i></a>
                                                </div>
                                            </div>';
                self::nestableHijos($menu->id_sitio_menu, $nestableCategorias);
                $nestableCategorias .= '</li>';
            }
        }
        return $nestableCategorias;
    }
	
	public function menu()
	{
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/x-editable/dist/bootstrap3-editable/js/bootstrap-editable.min");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/js/x-editable/dist/bootstrap3-editable/css/bootstrap-editable");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/js/nestable/nestable");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/nestable/jquery.nestable");
        \View::share('scripts', $this->scripts);
        $this->layout->content = \View::make('backend.'.$this->ruta);
	}

	public function consultaRedirecciones()
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
	
	public function getConsultaRedirecciones(){
        $redirecciones = \Redireccion::where('id', '>', 0)->get();
        return \Datatable::collection($redirecciones)
            ->showColumns('id')
             ->addColumn('alias', function($redireccion)
            {
                 //return $paquete->paquete;
                 return '<a href="#" class="campo" data-campo="alias" data-value="'.$redireccion->alias.'" data-type="text" data-original-title="Alias" data-pk="'.$redireccion->id.'"></a>';
            })
            ->addColumn('redirect_to', function($redireccion)
            {
                 //return $paquete->paquete;
                 return '<a href="#" class="campo" data-campo="redirect_to" data-value="'.$redireccion->redirect_to.'" data-type="text" data-original-title="Redireccionar a" data-pk="'.$redireccion->id.'"></a>';
            })
            ->addColumn('tipo', function($redireccion)
            {
                //return $paquete->paquete_campo;
                return '<a href="#" class="campo" data-campo="tipo" data-value="'.$redireccion->tipo.'" data-type="number" data-original-title="Tipo" data-pk="'.$redireccion->id.'"></a>';
            })
            ->searchColumns('id', 'alias', 'redirect_to', 'tipo')
            ->orderColumns('id')
            ->make();
    }
	
	public function actualizarRedireccion(){
        $redireccionDatos = \Input::all();
        $redirect = \Redireccion::find($redireccionDatos["pk"]);
        $redirect->$redireccionDatos["campo"] = $redireccionDatos["value"];

        try{
            if($redirect->save()){
                return 1;
            }
        }catch(Exception $e){
            return 0;
        }
        return 0;
    } 
	
	public function altaRedireccion()
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
	
	public function agregarRedireccion(){
    	$redireccionDatos = \Input::all();
		$redirect = new \Redireccion($redireccionDatos);
		
		$respuesta = array(
                        "status" => "invalid",
                        "titulo" => "Alta redireccion",
                        "mensaje" => "Ocurrio un error al tratar de registrar la aseguradora",
                        "posicion" => "stack_bar_bottom",
                        "tipo" => "error",
                    );
        try{
			if($redirect->save()){
			 	 $respuesta['mensaje'] = 'Redireccion agregada correctamente';
                $respuesta['status'] = 'success';
                $respuesta['tipo'] = 'success';
            }
		}catch(Exception $e){
            $respuesta['mensaje'] = $e->getMessage();
        }
        return json_encode($respuesta);
    } 
	
	public function sitemap()
    {
        \View::share('scripts', $this->scripts);
        $this->layout->content = \View::make('backend.'.$this->ruta);
    }

	public function doSitemap(){
		$url = 'https://www.segurodegastosmedicosmayores.mx/';
		$lastmod = date("Y-m-d\TH:i:s-06:00");
		$map = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
		$map .= '<url><loc>' . $url . '</loc><lastmod>' . $lastmod . '</lastmod><priority>1.00</priority></url>';
		$map .= '<url><loc>' . $url . 'cotizador</loc><lastmod>' . $lastmod . '</lastmod><priority>0.90</priority></url>';
		$map .= '<url><loc>' . $url . 'nosotros</loc><lastmod>' . $lastmod . '</lastmod><priority>0.90</priority></url>';
		$map .= '<url><loc>' . $url . 'paquetes</loc><lastmod>' . $lastmod . '</lastmod><priority>0.90</priority></url>';
		$map .= '<url><loc>' . $url . 'contacto</loc><lastmod>' . $lastmod . '</lastmod><priority>0.90</priority></url>';
		$map .= '<url><loc>' . $url . 'blog</loc><lastmod>' . $lastmod . '</lastmod><priority>0.90</priority></url>';
		$pages = \Blog::where("estatus", 1)->get();
		foreach($pages as $page){
			$map .= '<url><loc>' . $url . $page->alias . '</loc><lastmod>' . $lastmod . '</lastmod><priority>0.80</priority></url>';
		}
		$sections = \DB::select(\DB::raw("select id_blog_categoria, categoria_alias, ifnull((select count(*) from gm_blog where estatus = 1 and id_blog_categoria = a.id_blog_categoria), 0) paginas from gm_blog_categorias a where a.estatus = 1 and a.mostrar = 1 and ifnull((select count(*) from gm_blog where estatus = 1 and id_blog_categoria = a.id_blog_categoria), 0) > 0 order by 3 desc"));
		foreach($sections as $section){
			$map .= '<url><loc>' . $url . $section->categoria_alias . '</loc><lastmod>' . $lastmod . '</lastmod><priority>0.70</priority></url>';
			if($section->paginas>10){
				for($x=2;$x<=ceil($section->paginas/10);$x++){
					$map .= '<url><loc>' . $url . $section->categoria_alias . '?page=' . $x . '</loc><lastmod>' . $lastmod . '</lastmod><priority>0.70</priority></url>';
				}
			}
		}
		$map .= '</urlset>';
		file_put_contents(public_path() . "/sitemap.xml", $map);
		$res = array("status" => 200);
		return json_encode($res);
	}

	public function consultaArchivos()
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
	
	public function getConsultaArchivos(){
        $archivos = \Archivo::where('id', '>', 0)->get();
        return \Datatable::collection($archivos)
            ->showColumns('id')
            ->addColumn('alias', function($archivo)
            {
                 //return $paquete->paquete;
                 return '<a href="#" class="campo" data-campo="alias" data-value="'.$archivo->alias.'" data-type="text" data-original-title="Alias" data-pk="'.$archivo->id.'"></a>';
            })
            ->addColumn('descarga', function($archivo)
            {
                 //return $paquete->paquete;
                 return '<a href="#" class="campo" data-campo="descarga" data-value="'.$archivo->descarga.'" data-type="text" data-original-title="Descargable" data-pk="'.$archivo->id.'"></a>';
            })
            ->addColumn('tipo', function($archivo)
            {
                //return $paquete->paquete_campo;
                return '<a href="#" class="campo" data-campo="tipo" data-value="'.$archivo->tipo.'" data-type="text" data-original-title="Tipo" data-pk="'.$archivo->id.'"></a>';
            })
            ->addColumn('eliminar', function($archivo){
            	return '<a href="#" class="delete" data-id="' . $archivo->id . '"><i class="fa fa-times"></i></a>';
            })
            ->searchColumns('id', 'alias', 'descarga', 'tipo')
            ->orderColumns('id')
            ->make();
    }
    
    public function actualizarArchivo(){
        $archivoDatos = \Input::all();
        $archivo = \Archivo::find($archivoDatos["pk"]);
        $archivo->$archivoDatos["campo"] = $archivoDatos["value"];

        try{
            if($archivo->save()){
                return 1;
            }
        }catch(Exception $e){
            return 0;
        }
        return 0;
    } 
    
    public function altaArchivo()
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
    
    public function agregarArchivo(){
    	$archivoDatos = \Input::except('archivo');
		$archivo = new \Archivo($archivoDatos);
		
		$respuesta = array(
                        "status" => "invalid",
                        "titulo" => "Alta archivo",
                        "mensaje" => "Ocurrio un error al tratar de registrar la aseguradora",
                        "posicion" => "stack_bar_bottom",
                        "tipo" => "error",
                    );
        try{
        	$archivo->archivo = '';
        	$archivo->tipo = '';
			if($archivo->save()){
				$name = "";
				$tipo = "";
				if(isset($_FILES["archivo"])){
					if($_FILES["archivo"]["size"]>0){
						$ext = pathinfo($_FILES["archivo"]["name"], PATHINFO_EXTENSION);
						$name = "archivo-descargable-" . $archivo->id . "." . $ext;
						$tipo = $_FILES["archivo"]["type"];
						move_uploaded_file($_FILES["archivo"]["tmp_name"], public_path() .  "/descargas/" . $name);
						$archivo->archivo = $name;
						$archivo->tipo = $tipo;
						$archivo->save();
					}
				}
				
			 	$respuesta['mensaje'] = 'Archivo agregado correctamente';
                $respuesta['status'] = 'success';
                $respuesta['tipo'] = 'success';
            }
		}catch(Exception $e){
            $respuesta['mensaje'] = $e->getMessage();
        }
        return json_encode($respuesta);
    } 

	public function eliminarArchivo($idArchivo){
		$res = array("status" => 200);
		$archivo = \Archivo::where("id", $idArchivo)->first();
		if($archivo){
			if($archivo->archivo!="")
				unlink(public_path() . "/descargas/" . $archivo->archivo);
			$archivo->delete();
		}
		return json_encode($res);
	}

	public function piePagina(){
		$this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/bootstrap-timepicker.min");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/jquery.validate.min");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/select2.min");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/css/select2");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/css/jquery.tagsinput");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/jquery.tagsinput.min");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/ckeditor/ckeditor");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/ckeditor/adapters/jquery");
		
		$contenido["footer"] = file_get_contents(app_path() . "/views/layout/portoFooter.blade.php");
		\View::share('contenido', $contenido);
		
		\View::share('scripts', $this->scripts);
        $this->layout->content = \View::make('backend.'.$this->ruta);
	}

	public function doPiePagina(){
		$contenido = \Input::get('contenido');
		file_put_contents(app_path() . "/views/layout/portoFooter.blade.php", $contenido);
		
		$respuesta['status'] = 'success';
		$respuesta['mensaje'] = 'Pie de pagina actualizado correctamente';
		$respuesta['posicion'] = 'stack_bar_bottom';
		$respuesta['tipo'] = 'success';
		return json_encode($respuesta);
	}
}