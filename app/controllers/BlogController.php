<?php

class BlogController extends BaseController {
	protected $layout = 'layout.master';
	/*
	public function verContenido($alias){
		$contenido = Blog::where('alias', '=', $alias)->get();
		if(count($contenido) == 1){
			$contenido = $contenido[0];
			View::share('contenido', $contenido);
			View::share('metaTitulo', $contenido->titulo);
			View::share('metaDescripcion', str_limit(strip_tags(html_entity_decode($contenido->contenido)), 100, '...'));
			$this->layout->content = View::make('blog.verContenido');
		}else{
			return Redirect::to('/blog');
		}
		$blogCategorias = Blogcategoria::where('estatus', '=', 1)->orderBy('categoria')->get();
		View::share('blogCategorias', $blogCategorias);
		$contenidosReciente = Blog::select('titulo', 'alias')->where('estatus', '=', 1)->orderBy('fecha_registro', 'desc')->limit(10)->get();
		View::share('contenidosReciente', $contenidosReciente);
		Paginavisita::hit();
	}
	*/
	public function blog()
	{
		$categoria = Blogcategoria::where('categoria_alias', '=', 'blog')->get();
		View::share("categoria", $categoria);

		$categoriasHijos = Blogcategoria::select('id_blog_categoria')->where('id_padre', '=', $categoria[0]->id_blog_categoria)->get()->toArray();
		$contenidos = Blog::where('estatus', '=', 1)->whereIn('id_blog_categoria', $categoriasHijos)->orderBy('id_blog_categoria')->orderBy('orden')->paginate(10);
		$paginacion = $contenidos->links();
        View::share("paginacion", htmlentities($paginacion));
        View::share('contenidosArray', $contenidos->getItems() );

        $blogCategoriasMenu = SistemaFunciones::getBlogCategoriasMenu(-1);
        
        $categoriaPadre = Blogcategoria::find(-1);
        $rastroMigajas = array();
        $rastroMigajas[$categoriaPadre->id_blog_categoria] = $categoriaPadre;
        while($categoriaPadre->id_padre > 0){
            if($categoriaPadre->id_padre > 0){
                $categoriaPadre = Blogcategoria::find($categoriaPadre->id_padre);
            }
            $rastroMigajas[$categoriaPadre->id_blog_categoria] = $categoriaPadre;
        }
        $rastroMigajas = array_reverse($rastroMigajas);
        $rastroMigajasTexto = '';
        if(count($rastroMigajas) > 0){
            $n = 0;
            foreach($rastroMigajas AS $rastroMigaja){
                if(count($blogCategoriasMenu) == 0){
                    $blogCategoriasMenu = SistemaFunciones::getBlogCategoriasMenu($rastroMigaja->id_blog_categoria);
                }
                $rastroMigajasTexto .= '<a href="'.URL::to('/admingm/'.$rastroMigaja->categoria_alias).'" style="color: #fff !important;">'.$rastroMigaja->categoria.'</a>';
                if($n < (count($rastroMigajas) - 1)){
                    $rastroMigajasTexto .= ' <i>/</i> ';
                }
                $n++;
            }
        }
        View::share('rastroMigajasTexto', $rastroMigajasTexto);
        if(count($blogCategoriasMenu) == 0){
            $blogCategoriasMenu = SistemaFunciones::getBlogCategoriasMenu(-1);
        }
        $blogCategoriasMenu = SistemaFunciones::getBlogCategoriasMenuLista($blogCategoriasMenu, array('aseguradoras'), $categoriaPadre->categoria_alias);
        View::share("blogCategoriasMenu", $blogCategoriasMenu);

        return View::make('blog.blog');
		/*
		$contenidos = Blog::where('estatus', '=', 1)->orderBy('fecha_registro', 'desc')->paginate(10);
		$paginacion = $contenidos->links();
		View::share("paginacion", htmlentities($paginacion));
		View::share('contenidosArray', $contenidos->getItems() );
		$contenidosReciente = Blog::select('titulo', 'alias')->where('estatus', '=', 1)->orderBy('fecha_registro', 'desc')->limit(10)->get();
		View::share('contenidosReciente', $contenidosReciente);
		
		View::share('metaTitulo', 'Blog de Gastos médicos');
        View::share('metaDescripcion', 'Consulta nuestros artículos donde te ofrecemos información sobre seguros de gastos médicos y de salud');
		$this->layout->content = View::make('blog.blog');
		Paginavisita::hit();
		*/
	}
	
}
