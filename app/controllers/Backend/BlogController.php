<?php

namespace Backend;

use App\Models\Backend\User;
use App\Models\Backend\Usuariosesion;

class BlogController extends \BaseController {
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

    public function actualizarPublicacionesOrden(){
        $menuEstructura = \Input::get('menu');
        if(is_array($menuEstructura)){
            $orden = 1;
            foreach($menuEstructura AS $menu){
                $blogDatos = \Blog::find($menu["id"]);
                $blogDatos->orden = $orden;
                $blogDatos->save();
                $orden++;
            }
        }
        return 1;
    }

    public function getPublicacionesCategorias(){
        $idBlogCategoria = \Input::get('idBlogCategoria');

        $publicaciones = \Blog::select('id_blog', 'titulo', 'id_blog_categoria', 'fecha_publicacion')
                                                ->where(function($query) use ($idBlogCategoria){
                                                    if($idBlogCategoria > 0){
                                                        $query->where('id_blog_categoria', '=', $idBlogCategoria);
                                                    }else{
                                                        $query->where('id_blog_categoria', '>', 0);
                                                    }
                                                })
                                                ->orderBy('id_blog_categoria')
                                                ->orderBy('orden')
                                                ->orderBy('fecha_publicacion', 'desc')
                                                ->get();
        $categoriasLista = '';
        if($publicaciones){
            foreach($publicaciones AS $publicacion){
                $categoriasLista .= '<li class="dd-item dd3-item" data-id="'.$publicacion->id_blog.'">
                                        <div class="dd-handle dd3-handle">Drag</div>
                                        <div class="dd3-content">
                                            '.$publicacion->titulo.' '.((isset($publicacion->categoria()->get()[0]->categoria)) ? '[ '.$publicacion->categoria()->get()[0]->categoria.' ]' : '').' [ '.$publicacion->fecha_publicacion.' ]
                                        </div>
                                    </li>';
            }
        }
        return $categoriasLista;
    }

    public function ordenarArticulos(){
        $categoriasBlog = \Blogcategoria::where('id_blog_categoria', '>', 0)->where('id_padre', '=', -1)->orderBy('categoria')->get();
        $categoriasOption = '';
        if(count($categoriasBlog) > 0){
            foreach($categoriasBlog AS $categoriaBlog){
                self::getCategoriaHijos($categoriaBlog->id_blog_categoria, $categoriasOption, 1, ((isset($publicacionDatos)) ? $publicacionDatos->id_blog_categoria : '') );
            }
        }
        \View::share('categoriasOption', $categoriasOption);

        $publicaciones = \Blog::select('id_blog', 'titulo', 'id_blog_categoria', 'fecha_publicacion')->orderBy('orden')->orderBy('fecha_publicacion', 'desc')->get();
        \View::share('publicaciones', $publicaciones);

        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/select2.min");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/css/select2");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/js/nestable/nestable");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/nestable/jquery.nestable");
        \View::share('scripts', $this->scripts);
        $this->layout->content = \View::make('backend.'.$this->ruta);
    }

    public function actualizarCategoria(){
        $categoriaDatos = \Input::all();
        $categoria = \Blogcategoria::find($categoriaDatos["pk"]);
        $categoria->$categoriaDatos["campo"] = $categoriaDatos["value"];

        try{
            if($categoria->save()){
                return 1;
            }
        }catch(Exception $e){
            
        }
        return 0;
    }

    public function eliminarCategoria(){
        $idBlogCategoria = \Input::get('idBlogCategoria');

        $categoriaDatos = \Blogcategoria::find($idBlogCategoria);
        $respuesta = array(
                        "status" => "invalid",
                        "titulo" => "Eliminar hijo",
                        "mensaje" => "Ocurrio un error al tratar de eliminar al hijo",
                        "posicion" => "stack_bar_bottom",
                        "tipo" => "error",
                    );
        \DB::beginTransaction();
        $categoriaDatos->categoriaHijos()->update(array('id_padre' => $categoriaDatos->id_padre));
        if($categoriaDatos->delete()){
            $respuesta['mensaje'] = 'Categoría eliminada correctamenta';
            $respuesta['status'] = 'success';
            $respuesta['tipo'] = 'success';
            \DB::commit();
        }else{
            \DB::rollback();
        }
        return json_encode($respuesta);
    }

    public function agregarCategoriaHijo(){
        $idPadre = \Input::get('idPadre');
        $nodoHijo = \Input::get('nodoHijo');

        $respuesta = array(
                        "status" => "invalid",
                        "titulo" => "Agregar hijo",
                        "mensaje" => "Ocurrio un error al tratar de agregar al hijo",
                        "posicion" => "stack_bar_bottom",
                        "tipo" => "error",
                    );
        $blogCategoriaPadre = \Blogcategoria::find($idPadre);
        $menuUltimoDatos = \Blogcategoria::where('id_padre', '=', $blogCategoriaPadre->id_blog)->orderBy('orden', 'desc')->first();
        $ultimo = 1;
        if($menuUltimoDatos){
            $ultimo = $menuUltimoDatos->orden++;
        }
        $blogCategoriaDatos = new \Blogcategoria;
        $blogCategoriaDatos->categoria = $nodoHijo;
        $blogCategoriaDatos->categoria_alias = \SistemaFunciones::aliasCategoria(strtolower($blogCategoriaPadre->categoria.' '.$nodoHijo));
        $blogCategoriaDatos->id_padre = $idPadre;
        $blogCategoriaDatos->orden = $ultimo;
        
        if($blogCategoriaDatos->save()){
            $respuesta['mensaje'] = 'Hijo agregado correctamente';
            $respuesta['status'] = 'success';
            $respuesta['tipo'] = 'success';
        }
        return json_encode($respuesta);
    }

    private function actualizarCategoriaOrdenHijos($id_padre, $hijos, $orden){
        if(is_array($hijos)){
            foreach($hijos AS $hijo){
                if(isset($hijo["children"]) && count($hijo["children"] > 0)){
                    $this->actualizarCategoriaOrdenHijos($hijo["id"], $hijo["children"], $orden);
                }
                $blogCategoriaDatos = \Blogcategoria::find($hijo["id"]);
                $blogCategoriaDatos->orden = $orden;
                $blogCategoriaDatos->id_padre = $id_padre;
                $blogCategoriaDatos->save();
                $orden++;
            }
        }
    }

    public function actualizarCategoriasOrden(){
        $menuEstructura = \Input::get('menu');
        if(is_array($menuEstructura)){
            $orden = 1;
            foreach($menuEstructura AS $menu){
                $blogCategoriaDatos = \Blogcategoria::find($menu["id"]);
                $blogCategoriaDatos->orden = $orden;
                $blogCategoriaDatos->id_padre = -1;
                $blogCategoriaDatos->save();
                if(isset($menu["children"]) && is_array($menu["children"])){
                    $this->actualizarCategoriaOrdenHijos($menu["id"], $menu["children"], $orden);
                }
                $orden++;
            }
        }
        return 1;
    }

    private function nestableHijos($id_padre = -1, &$nestableCategorias){
        if($id_padre > 0){
            $categoriasHijos = \Blogcategoria::where('id_padre', '=', $id_padre)->orderBy('orden')->get();
            if(count($categoriasHijos) > 0){
                $nestableCategorias .= '<ol class="dd-list">';
                foreach($categoriasHijos AS $categoriaHijo){
                    $nestableCategorias .= '<li class="dd-item dd3-item" data-id="'.$categoriaHijo->id_blog_categoria.'">
                                            <div class="dd-handle dd3-handle">Drag</div>
                                            <div class="dd3-content">
                                                <strong>Categoría:</strong> <a href="#" class="campo" data-campo="categoria" data-value="'.$categoriaHijo->categoria.'" data-type="text" data-pk="'.$categoriaHijo->id_blog_categoria.'" data-original-title="Categoría"></a> | <strong>Alias:</strong> <a href="#" class="campo" data-campo="categoria_alias" data-value="'.$categoriaHijo->categoria_alias.'" data-type="text" data-pk="'.$categoriaHijo->id_blog_categoria.'" data-original-title="Alias"></a> | <strong>Keywords:</strong> <a href="#" class="campo" data-campo="metakey" data-value="'.$categoriaHijo->metakey.'" data-type="text" data-pk="'.$categoriaHijo->id_blog_categoria.'" data-original-title="Keywords"></a> | <strong>Metadescripción:</strong> <a href="#" class="campo" data-campo="metadesc" data-value="'.$categoriaHijo->metadesc.'" data-type="text" data-pk="'.$categoriaHijo->id_blog_categoria.'" data-original-title="Meta descripción"></a>
                                                <div class="pull-right">
                                                    <a href="#" class="tooltips mr5 agregarHijo" data-nombre="'.$categoriaHijo->categoria.'" data-idBlogCategoria="'.$categoriaHijo->id_blog_categoria.'" data-toggle="tooltip" title="" data-original-title="Agregar hijo para: '.$categoriaHijo->categoria.'"><i class="fa fa-plus"></i></a>
                                                    <a href="#" id="addnewtodo" class="tooltips eliminarHijo" data-nombre="'.$categoriaHijo->categoria.'" data-idBlogCategoria="'.$categoriaHijo->id_blog_categoria.'" data-toggle="tooltip" title="" data-original-title="Eliminar: '.$categoriaHijo->categoria.'"><i class="fa fa-trash-o"></i></a>
                                                </div>
                                            </div>';
                    self::nestableHijos($categoriaHijo->id_blog_categoria, $nestableCategorias);
                    $nestableCategorias .= '</li>';
                }
                $nestableCategorias .= '</ol>';
            }
        }
    }

    public function getCategoriasNestableMenu(){
        $categorias = \Blogcategoria::where('id_blog_categoria', '>', 0)->where('id_padre', '=', -1)->orderBy('orden')->get();
        $nestableCategorias = '';
        if(count($categorias) > 0){
            foreach($categorias AS $categoria){
                $nestableCategorias .= '<li class="dd-item dd3-item" data-id="'.$categoria->id_blog_categoria.'">
                                            <div class="dd-handle dd3-handle">Drag</div>
                                            <div class="dd3-content">
                                                <strong>Categoría:</strong> <a href="#" class="campo" data-campo="categoria" data-value="'.$categoria->categoria.'" data-type="text" data-pk="'.$categoria->id_blog_categoria.'" data-original-title="Categoría"></a> | <strong>Alias:</strong> <a href="#" class="campo" data-campo="categoria_alias" data-value="'.$categoria->categoria_alias.'" data-type="text" data-pk="'.$categoria->id_blog_categoria.'" data-original-title="Alias"></a> | <strong>Keywords:</strong> <a href="#" class="campo" data-campo="metakey" data-value="'.$categoria->metakey.'" data-type="text" data-pk="'.$categoria->id_blog_categoria.'" data-original-title="Keywords"></a> | <strong>Metadescripción:</strong> <a href="#" class="campo" data-campo="metadesc" data-value="'.$categoria->metadesc.'" data-type="text" data-pk="'.$categoria->id_blog_categoria.'" data-original-title="Meta descripción"></a>
                                                <div class="pull-right">
                                                    <a href="#" class="tooltips mr5 agregarHijo" data-nombre="'.$categoria->categoria.'" data-idBlogCategoria="'.$categoria->id_blog_categoria.'" data-toggle="tooltip" title="" data-original-title="Agregar hijo para: '.$categoria->categoria.'"><i class="fa fa-plus"></i></a>
                                                    <a href="#" id="addnewtodo" class="tooltips eliminarHijo" data-nombre="'.$categoria->categoria.'" data-idBlogCategoria="'.$categoria->id_blog_categoria.'" data-toggle="tooltip" title="" data-original-title="Eliminar: '.$categoria->categoria.'"><i class="fa fa-trash-o"></i></a>
                                                </div>
                                            </div>';
                self::nestableHijos($categoria->id_blog_categoria, $nestableCategorias);
                $nestableCategorias .= '</li>';
            }
        }
        return $nestableCategorias;
    }

    public function configuracionCategorias(){
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/x-editable/dist/bootstrap3-editable/js/bootstrap-editable.min");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/js/x-editable/dist/bootstrap3-editable/css/bootstrap-editable");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/js/nestable/nestable");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/nestable/jquery.nestable");

        \View::share('scripts', $this->scripts);
        $this->layout->content = \View::make('backend.'.$this->ruta);
    }
    
    public function eliminarPublicacion(){
        $idBlog = \Input::get('idBlog');
        $publicacionDatos = \Blog::find($idBlog);
        $respuesta = array(
                        "status" => "invalid",
                        "titulo" => "Eliminar publicación",
                        "mensaje" => "Ocurrio un error al tratar de eliminar la publicación",
                        "posicion" => "stack_bar_bottom",
                        "tipo" => "error",
                    );
        if($publicacionDatos->delete()){
            $respuesta['mensaje'] = 'Publicación eliminada correctamente';
            $respuesta['status'] = 'success';
            $respuesta['tipo'] = 'success';
        }
        return json_encode($respuesta);
    }

    private static function getCategoriaHijos($id_blog_categoria, &$categoriasOption, $pos, $id_blog_categoria_select){
        $categorias = array();
        $categoriasHijos = \Blogcategoria::where('id_padre', '=', $id_blog_categoria)->orderBy('categoria')->get();
        $categoriaPadre = \Blogcategoria::find($id_blog_categoria);
        if(count($categoriasHijos) > 0){
            //$categoriasOption .= '<optgroup label="'.$categoriaPadre->categoria.'">';
            $categoriasOption .= '<option value="'.$categoriaPadre->id_blog_categoria.'" '.(($categoriaPadre->id_blog_categoria== $id_blog_categoria_select) ? 'selected' : '').'>'.str_pad("", $pos, "- ", STR_PAD_LEFT).'&nbsp; '.$categoriaPadre->categoria.'</option>';
            foreach($categoriasHijos AS $categoriasHijos){
                //$categorias[$categoriasHijos->id_blog_categoria] = $categoriasHijos;
                //$categoriasOption .= '<option>'.$categoriasHijos->categoria.'</option>';
                self::getCategoriaHijos($categoriasHijos->id_blog_categoria, $categoriasOption, ($pos+2), $id_blog_categoria_select);
                //$categorias[$categoriasHijos->id_blog_categoria]['hijos'] = self::getCategoriaHijos($categoriasHijos->id_blog_categoria);
            }
            //$categoriasOption .= '</optgroup>';
        }else{
            $categoriasOption .= '<option value="'.$categoriaPadre->id_blog_categoria.'" '.(($categoriaPadre->id_blog_categoria== $id_blog_categoria_select) ? 'selected' : '').'>'.str_pad("", $pos, "- ", STR_PAD_LEFT).'&nbsp; '.$categoriaPadre->categoria.'</option>';
            //$categoriasOption .= '<option>'.$pos.' '.$categoriaPadre->categoria.'</option>';
        }
        return $categoriasOption;
    }

    public function agregarPublicacion(){        
        $idBlog = \Input::get('id_blog');
        if($idBlog > 0){
            $publicacion = \Blog::find($idBlog);
            $publicacionDatos = \Input::except('imagen', 'id_blog', 'eliminarImagen', 'incluir_cotizador', 'raw');
            foreach($publicacionDatos AS $key=>$value){
                if($key == 'alias'){
                    if(strlen($value) == 0){
                        $value = strtolower(str_replace("-", " ", trim(\Input::get('titulo'))));
                    }else{
                        $value = strtolower(str_replace("-", " ", trim($value)));
                    }
                    $value = \SistemaFunciones::aliasCategoria(strtolower(trim($value)));
                }
                $publicacion->$key = $value;
            }
        }else{
            $publicacionDatos = \Input::except('imagen', 'id_blog');
            if(strlen($publicacionDatos["alias"]) == 0){
                $publicacionDatos["alias"] = \SistemaFunciones::aliasCategoria(strtolower(trim($publicacionDatos["titulo"])));
            }
            $publicacion = new \Blog($publicacionDatos);
            $publicacion->fecha_publicacion = date('Y-m-d');
        }
        $eliminarImagen = \Input::get('eliminarImagen');
        if($eliminarImagen == 'on'){
            $publicacion->imagen_large = '';
            $publicacion->imagen_medium = '';
            $publicacion->imagen_small = '';
        }
        $file = \Input::file('imagen');
        if($file){
            //$publicacion->imagen_large = \Image::make(file_get_contents($file->getRealPath()))->encode('data-url');
            //$publicacion->imagen_large = \Image::make(\Image::make(file_get_contents($file->getRealPath()))->encode('png', 90))->encode('data-url');
            $img_full = \Image::make(file_get_contents($file->getRealPath()));
            $img_full->resize(1024, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img_full->encode('png', 90);
            $publicacion->imagen_large = $img_full->encode('data-url');
            
            $img_medium = \Image::make(file_get_contents($file->getRealPath()));
            $img_medium->resize(360, 200, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img_medium->encode('png', 90);
            $publicacion->imagen_medium = $img_medium->encode('data-url');

            $img_small = \Image::make(file_get_contents($file->getRealPath()));
            $img_small->resize(50, 50, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img_small->encode('png', 90);
            $publicacion->imagen_small = $img_small->encode('data-url');
        }
        if(strlen($publicacion->contenido) > 0){
            $publicacion->introtext = str_limit(trim(strip_tags(html_entity_decode($publicacion->contenido))), 100, '...');
            $publicacion->introtext = str_replace("\n", " ", $publicacion->introtext);
            $publicacion->introtext = str_replace("<br>", " ", $publicacion->introtext);
        }
        $incluirCotizador = \Input::get('incluir_cotizador');
        if($incluirCotizador == 'on')
        	$publicacion->incluir_cotizador = 1;
        else
        	$publicacion->incluir_cotizador = 0;
        $incluirCotizadorNuevo = \Input::get('incluir_cotizador_nuevo');
        if($incluirCotizadorNuevo == 'on')
        	$publicacion->incluir_cotizador_nuevo = 1;
        else
        	$publicacion->incluir_cotizador_nuevo = 0;
        $raw = \Input::get('raw');
        if($raw == 'on')
        	$publicacion->raw = 1;
        else
        	$publicacion->raw = 0;
        $respuesta = array(
                        "status" => "invalid",
                        "titulo" => "Alta publicación",
                        "mensaje" => "Ocurrio un error al tratar de registrar la publicación",
                        "posicion" => "stack_bar_bottom",
                        "tipo" => "error",
                    );
        try{
           // dd($publicacion);
           $publicacion->alias_original = $publicacion->alias;
            if($publicacion->save()){
                if($idBlog > 0){
                    $respuesta['mensaje'] = 'Publicación actualizada correctamente';
                }else{
                    $respuesta['mensaje'] = 'Publicación agregada correctamente';
                }
                $respuesta['status'] = 'success';
                $respuesta['tipo'] = 'success';
            }
        }catch(Exception $e){
            $respuesta['mensaje'] = $e->getMessage();
        }
        return json_encode($respuesta);
    }
	
	public function altaPublicacion($idBlog = -1)
	{
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/bootstrap-timepicker.min");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/jquery.validate.min");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/select2.min");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/css/select2");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/css/jquery.tagsinput");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/jquery.tagsinput.min");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/ckeditor/ckeditor");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/ckeditor/adapters/jquery");
        
        if($idBlog > 0){
            $publicacionDatos = \Blog::find($idBlog);
            if($publicacionDatos){
                \View::share('publicacionDatos', $publicacionDatos);
            }
        }
        $categoriasBlog = \Blogcategoria::where('id_blog_categoria', '>', 0)->where('id_padre', '=', -1)->orderBy('categoria')->get();
        $categoriasOption = '';
        if(count($categoriasBlog) > 0){
            foreach($categoriasBlog AS $categoriaBlog){
                self::getCategoriaHijos($categoriaBlog->id_blog_categoria, $categoriasOption, 1, ((isset($publicacionDatos)) ? $publicacionDatos->id_blog_categoria : '') );
            }
        }
        \View::share('categoriasOption', $categoriasOption);
        \View::share('scripts', $this->scripts);
		$this->layout->content = \View::make('backend.'.$this->ruta);
	}

    public function actualizarPublicacion(){
        $publicacionDatos = \Input::all();
        $publicacion = \Blog::find($publicacionDatos["pk"]);
        $publicacion->$publicacionDatos["campo"] = $publicacionDatos["value"];

        try{
            if($publicacion->save()){
                return 1;
            }
        }catch(Exception $e){
            
        }
        return 0;
    }

    public function getConsultaPublicaciones(){
        $where = array();
        $idBlogCategoria = \Input::get('id_blog_categoria');
        $buscar = \Input::get('buscar');
        $tipo = \Input::get('tipo');
        $estatus = \Input::get('estatus');
        $publicaciones = new \Blog;
        if($idBlogCategoria > 0){
            $publicaciones = $publicaciones->where('blog.id_blog_categoria', '=', $idBlogCategoria);
            
        }
        if(strlen($buscar) > 0){
            $publicaciones = $publicaciones->where('blog.titulo', 'LIKE', '%'.$buscar.'%')
              ->orWhere('blog.alias', 'LIKE', '%' . $buscar . '%')
              ->orWhere('blog.id_blog', '=', $buscar);
           // $publicaciones = $publicaciones->orwhere('contenido', 'LIKE', '%'.$buscar.'%');
        }

        if($tipo > 0){
            $publicaciones = $publicaciones->where('blog.tipo', '=', $tipo);
        }
	
        if($estatus > 0){
            $publicaciones = $publicaciones->where('blog.estatus', '=', $estatus);
        }
        $publicaciones = $publicaciones->take(100)->orderBy('blog.fecha_publicacion', 'ASC')
                        ->join('blog_categorias', 'blog_categorias.id_blog_categoria', '=', 'blog.id_blog_categoria')
                        //->orWhere('blog_categorias.id_padre', '=', $idBlogCategoria)
                        ->select('blog.id_blog', 'blog.titulo', 'blog.alias', 'blog_categorias.categoria_alias', 'blog.fecha_publicacion', 'blog.tipo', 'blog.estatus', 'blog_categorias.categoria', 'blog_categorias.id_padre')
                        ->get();
        
        return \Datatable::collection($publicaciones)
            ->showColumns('id_blog')
            ->addColumn('titulo', function($publicacion)
            {
                //return '<a href="#" class="campo" data-campo="titulo" data-value="'.$publicacion->titulo.'" data-type="text" data-pk="'.$publicacion->id_blog.'" data-original-title="Título"></a>';
                return '<a href="'.\URL::to('admingm/publicacion/altaPublicacion/'.$publicacion->id_blog).'">'.$publicacion->titulo.'</a>';
            })
            ->addColumn('alias', function($publicacion)
            {
                return '<a href="'.\URL::to('admingm/publicacion/altaPublicacion/'.$publicacion->id_blog).'">'.$publicacion->alias.'</a>';
            })
            ->addColumn('categoria', function($publicacion){
                $retorno = $publicacion->categoria; 
                $padre = \DB::table('blog_categorias')->where('id_blog_categoria', $publicacion->id_padre)->first();
                
                if($padre->id_blog_categoria > 0 ){
                    $retorno .= " / ".$padre->categoria;
                }
                return $retorno;
            })
            /*
            ->addColumn('alias', function($publicacion)
            {
                return $publicacion->alias;
            })
            ->addColumn('id_blog_categoria', function($publicacion)
            {
                return '<a href="#" class="campo" data-campo="id_blog_categoria" data-value="'.$publicacion->id_blog_categoria.'" data-source="'.\URL::to('/admingm/listasJson/categoriasBlogJson/').'" data-type="select" data-original-title="Categoría" data-pk="'.$publicacion->id_blog.'"></a>';
            })
            */
            ->addColumn('fecha_publicacion', function($publicacion)
            {
                return $publicacion->fecha_publicacion;
            })
            ->addColumn('tipo', function($publicacion)
            {
                return '<a href="#" class="tipo" data-value="'.$publicacion->tipo.'" data-type="select" data-original-title="Tipo de página" data-pk="'.$publicacion->id_blog.'"></a>';
            })
            ->addColumn('estatus', function($publicacion)
            {
                return '<a href="#" class="estatus" data-value="'.$publicacion->estatus.'" data-type="select" data-original-title="Estatus" data-pk="'.$publicacion->id_blog.'"></a>';
            })
            ->addColumn('opciones', function($publicacion)
            {
                $opciones = '<a href="'.\URL::to('/admingm/pagina/editarPagina/'.$publicacion->id_blog).'" target="_blank" class="tooltips" data-toggle="tooltip" data-original-title="Editar página HTML"><i class="fa fa-html5"></i></a>';
                if($publicacion->alias == 'main'){
                    $opciones .= '<a href="'.\URL::to('/').'" target="_blank" class="tooltips" data-toggle="tooltip" data-original-title="Vista previa"><i class="fa fa-file-code-o"></i></a>';
                }else{
                    $opciones .= '<a href="'.\URL::to('/'.$publicacion->alias).'" target="_blank" class="tooltips" data-toggle="tooltip" data-original-title="Vista previa"><i class="fa fa-file-code-o"></i></a>
                                <a href="#" data-toggle="tooltip" class="delete-row tooltips eliminarPublicacion" data-idBlog="'.$publicacion->id_blog.'" data-original-title="Eliminar"><i class="fa fa-trash-o"></i></a>';
                }
                return $opciones;
            })
            ->searchColumns('id_blog', 'titulo', 'categoria', 'fecha_publicacion', 'tipo', 'estatus')
            ->orderColumns('id_blog', 'titulo', 'categoria', 'fecha_publicacion', 'tipo', 'estatus' )
            ->make();
    }
    
    public function consultaPublicaciones(){
        $categoriasBlog = \Blogcategoria::where('id_blog_categoria', '>', 0)->where('id_padre', '=', -1)->orderBy('categoria')->get();
        $categoriasOption = '';
        if(count($categoriasBlog) > 0){
            $categoriasOption = '<option value="-1">Todas las categorias</option>';
            foreach($categoriasBlog AS $categoriaBlog){
                self::getCategoriaHijos($categoriaBlog->id_blog_categoria, $categoriasOption, 1, ((isset($publicacionDatos)) ? $publicacionDatos->id_blog_categoria : '') );
            }
        }
        \View::share('categoriasOption', $categoriasOption);

        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/select2.min");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/css/select2");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/x-editable/dist/bootstrap3-editable/js/bootstrap-editable.min");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/js/x-editable/dist/bootstrap3-editable/css/bootstrap-editable");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/css/style.datatables");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/jquery.dataTables.min");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/dataTables/dataTables.bootstrap");
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/dataTables/dataTables.responsive");

        \View::share('scripts', $this->scripts);
        $this->layout->content = \View::make('backend.'.$this->ruta);
    }

}
