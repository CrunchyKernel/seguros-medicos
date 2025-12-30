<?php

class SistemaFunciones {
	private static $dias_array = array('', 'Lunes', 'Martes', 'Mi&eacute;rcoles', 'Jueves', 'Viernes', 'S&aacute;bado', 'Domingo',);
    private static $meses_array = array('','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');

    public static function mesNombre($mes = -1){
        if($mes > 0 && $mes < 13){
            return SistemaFunciones::$meses_array[$mes];
        }
    }
    
    public static function existeCorreo($email){
        global $HTTP_HOST;
        $resultado = array();  
        //if (!eregi("^[_\.0-9a-z\-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,6}$",$email)) {  
        if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $email)) {
            $resultadoado[0]=false;  
            $resultado['code']="702";  
            return $resultado;  
        }  
        list ( $Username, $dominio ) = explode("@",$email);  
        if(getmxrr($dominio, $MXHost)){
            $conecta_dominio = $MXHost[0];
        }else{
            $conecta_dominio = $dominio;
        }
        $conectar = fsockopen($conecta_dominio, 25 );  
        if ($conectar){
            //if (ereg("^220", $ver = fgets($conectar, 1024))) {  
            if(!preg_match("/^220$/i", $ver = fgets($conectar, 1024))) {
                fputs($conectar, "HELO $HTTP_HOST\r\n");
                $ver = fgets( $conectar, 1024 );
                fputs($conectar, "MAIL FROM: <{$email}>\r\n");  
                $From = fgets( $conectar, 1024 );  
                fputs($conectar, "RCPT TO: <{$email}>\r\n");  
                $To = fgets($conectar, 1024);  
                fputs($conectar, "QUIT\r\n");  
                fclose($conectar);  
                //if (!ereg ("^250", $From) || !ereg ( "^250", $To )) {  
                //print_r($From);
                //print_r($To);
                //exit;
                if(!preg_match("/^250/", $From) || !preg_match("/^250/", $To) ) {
                    $resultado['existe']=false;  
                    $resultado['code']="700";  
                    return $resultado;  
                }  
            }else{  
                $resultado['existe'] = false;  
                $resultado['code'] = "Død";  
                return $resultado;  
            }
        }else{  
            $resultado['existe']=false;  
            $resultado['code']="701";  
            return $resultado;  
        }  
        $resultado['existe']=true;  
        $resultado['code']="200";  
        return $resultado;  
    }

    public static function aliasCategoria($cadena){
        $cadena = str_replace("…", "" ,$cadena);
        $cadena = str_replace("|", "" ,$cadena);
        $cadena = str_replace("ñ", "n" ,$cadena);
        $cadena = str_replace("Ñ", "N" ,$cadena);
        $no_permitidas= array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","À","Ã","Ì","Ò","Ù","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","ç","Ç","Ã¢","ê","Ã®","Ã´","Ã»","Ã‚","ÃŠ","ÃŽ","Ã”","Ã›","ü","Ã¶","Ã–","Ã¯","Ã¤","«","Ò","Ã","Ã„","Ã‹","&",":",";","@","ª","º","´","'");
        $permitidas=    array("a","e","i","o","u","A","E","I","O","U","n","A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","o","O","i","a","e","U","I","A","E");
        $cadena = str_replace($no_permitidas, $permitidas ,$cadena);
        $strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]","}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;","â€”", "â€“", ",", "<", ".", ">", "/", "?","-","ª","·","¿","¡","-","¨");
        $cadena = trim(str_replace($strip, "", strip_tags($cadena)));
        $cadena = preg_replace('/\s+/', "-", $cadena);
        return $cadena;
    }

    private static function getBlogCategoriasMenuHijosLista(&$menu, $blogCategoriasMenu = array(), $aliasActivo){
        if(count($blogCategoriasMenu) > 0){
            foreach($blogCategoriasMenu AS $blogCategoriaMenu){
                $categoriaPadre = Blogcategoria::find($blogCategoriaMenu['id_padre']);
                $aliasPadre = '';
                if($categoriaPadre->id_blog_categoria > 0){
                    $aliasPadre = '/'.$categoriaPadre->categoria_alias;
                }
                $menu .= '<li class="'.(($blogCategoriaMenu['categoria_alias'] == $aliasActivo) ? 'linkActivo' : '').'"><a href="'.URL::to($blogCategoriaMenu['categoria_alias']).'"><i class="fa fa-angle-right"></i> '.$blogCategoriaMenu['categoria'].'</a>
                            <ul>';
                if(count($blogCategoriaMenu['hijos']) > 0){
                    self::getBlogCategoriasMenuHijosLista($menu, $blogCategoriaMenu['hijos'], $aliasActivo);
                }
                $menu .= '</ul></li>';
            }
        }
    }

    public static function getBlogCategoriasMenuLista($blogCategoriasMenu = array(), $noMostrar = array(), $aliasActivo = ''){
        $menu = '';
        if(count($blogCategoriasMenu) > 0){
            foreach($blogCategoriasMenu AS $blogCategoriaMenu){
                if(!in_array($blogCategoriaMenu['categoria_alias'], $noMostrar)){
                    $menu .= '<li class="'.(($blogCategoriaMenu['categoria_alias'] == $aliasActivo) ? 'linkActivo' : '').'"><a href="'.URL::to('/'.$blogCategoriaMenu['categoria_alias']).'"><i class="fa fa-angle-right"></i> '.$blogCategoriaMenu['categoria'].'</a>
                            <ul>';
                    if(count($blogCategoriaMenu['hijos']) > 0){
                        self::getBlogCategoriasMenuHijosLista($menu, $blogCategoriaMenu['hijos'], $aliasActivo);
                    }
                    $menu .= '</ul></li>';
                }
            }
        }
        return $menu;
    }

    public static function getBlogCategoriasMenus(&$menu, $id_padre = -1){
        $blogCategorias = Blogcategoria::where('estatus', '=', 1)->where('id_blog_categoria', '>', 0)->where('id_padre', '=', $id_padre)->orderBy('orden')->get();
        if(count($blogCategorias) > 0){
            $categoriaPadre = blogCategoria::find($id_padre);
            $padreAlias = '';
            if($categoriaPadre->id_blog_categoria > 0){
                $padreAlias = '/'.$categoriaPadre->categoria_alias;
            }
            foreach($blogCategorias AS $blogCategoria){
                $menu[$blogCategoria->id_blog_categoria] = array('id_blog_categoria' => $blogCategoria->id_blog_categoria, 'id_padre' => $blogCategoria->id_padre, 'categoria_alias' => $blogCategoria->categoria_alias, 'categoria' => $blogCategoria->categoria);
                self::getBlogCategoriasMenus($menu[$blogCategoria->id_blog_categoria]['hijos'], $blogCategoria->id_blog_categoria);
            }
        }
    }

    public static function getBlogCategoriasMenu($id_padre = -1){
        $menu = array();
        self::getBlogCategoriasMenus($menu, $id_padre);
        
        return $menu;
    }
    
    private static function crearMenuNestableHijos(&$menuSitio, $id_padre){
        $menusHijos = Gmsitiomenu::where('id_padre', '=', $id_padre)->where('id_sitio_menu', '>', 0)->orderBy('orden', 'asc')->get();
        
        if(count($menusHijos) > 0){
            $menuSitio .= '<ul>';
            foreach($menusHijos AS $menuHijo){
                $menuSitio .= '<li><a href="'.URL::to($menuHijo->url_amigable).'">'.$menuHijo->titulo.((count(Gmsitiomenu::where('id_padre', '=', $menuHijo->id_sitio_menu)->where('id_sitio_menu', '>', 0)->get()) > 0) ? '<i class="fa fa-angle-right"></i>' : '').'</a>';
                SistemaFunciones::crearMenuNestableHijos($menuSitio, $menuHijo->id_sitio_menu);
                $menuSitio .= '</li>';
            }
            $menuSitio .= '</ul>';
        }
        return;
    }
    
    public static function crearMenuNestable(&$menuSitio, $id_padre, $sinHijos = 1){
        $menus = Gmsitiomenu::where('id_padre', '=', $id_padre)->where('id_sitio_menu', '>', 0)->orderBy('orden', 'asc')->get();
        if(count($menus) > 0){
            foreach($menus AS $menu){
                $menuSitio .= '<li><a href="'.URL::to($menu->url_amigable).'">'.(($sinHijos != 1) ? '<i class="fa fa-angle-right"></i> ' : '').$menu->titulo.((count(Gmsitiomenu::where('id_padre', '=', $menu->id_sitio_menu)->where('id_sitio_menu', '>', 0)->get()) > 0 && $sinHijos == 1) ? '<i class="fa fa-angle-down"></i>' : '').'</a>';
                if($sinHijos == 1){
                    SistemaFunciones::crearMenuNestableHijos($menuSitio, $menu->id_sitio_menu);
                }
                $menuSitio .= '</li>';
            }
        }
    }

    public static function formatMoney1($number, $fractional = false) { 
        if ($fractional) { 
            $number = sprintf('%.2f', $number); 
        }else{
            $number = sprintf('%.0f', $number); 
        }
        while (true) { 
            $replaced = preg_replace('/(-?\d+)(\d\d\d)/', '$1,$2', $number); 
            if ($replaced != $number) { 
                $number = $replaced; 
            } else { 
                break; 
            }
        } 
        //return '$ '.$number;
        return $number;

    }

    public static function fechaLetras($fecha = '0000-00-00', $corto = false){
        if($fecha != '0000-00-00' && date("Y", strtotime($fecha)) > 1965 && date("m", strtotime($fecha)) > 0 && date("d", strtotime($fecha)) > 0 ){
            if($corto == true){
                return substr(self::$dias_array[date('N', strtotime($fecha))], 0, 3).' '.date('d', strtotime($fecha)).' '.substr(self::$meses_array[date('n', strtotime($fecha))], 0, 3).' '.date('Y', strtotime($fecha));
            }
            return self::$dias_array[date('N', strtotime($fecha))].' '.date('d', strtotime($fecha)).' de '.self::$meses_array[date('n', strtotime($fecha))].' de '.date('Y', strtotime($fecha));
        }else{
            return "Fecha incorrecta";
        }
    }

    public static function breadCumbBackend($controlador_url = null){
        $breadCumb = '';
        if($controlador_url != null){
            $controladorDatos = App\Models\Backend\Modulo::where("controlador_url", "=", $controlador_url)->get();

            if(count($controladorDatos) === 1){
                $rutas = array();
                $controladorDatosTmp = App\Models\Backend\Modulo::find($controladorDatos[0]->id_modulo);
                $pos = 10;
                while($controladorDatosTmp->id_padre > 0){
                    $rutaControlador = '';
                    if($controladorDatosTmp->id_padre > 0){
                        $controladorDatosTmp = App\Models\Backend\Modulo::find($controladorDatosTmp->id_padre);
                        try {
                            $rutaControlador = route($controladorDatosTmp->controlador_url);
                        } catch (Exception $e) {}
                        $rutas[$pos] = array("texto" => $controladorDatosTmp->controlador, "icon" => $controladorDatosTmp->controlador_icono, "url" => $controladorDatosTmp->controlador_url);
                        $pos--;
                    }
                }
                sort($rutas);
                $breadCumb .= '<ul class="breadcrumb">
                                <li><a href="'.URL::to('/admingm').'"><i class="glyphicon glyphicon-home"></i></a></li>';
                if(count($rutas) > 0){
                    foreach($rutas AS $ruta){
                        $breadCumb .= '<li>
                                        <a href="'.URL::to('/admingm/'.$ruta["url"]).'">'.((isset($ruta["icon"]) && $ruta["icon"] != null) ? '<span class="'.$ruta["icon"].'"></span>' : '').'
                                        '.(($ruta["texto"]) ? $ruta["texto"] : '').'</a>
                                    </li>';
                    }
                }
                $controladorPadreDatos = App\Models\Backend\Modulo::find($controladorDatos[0]->id_padre);
                $breadCumb .= '<li>
                                    <a href="'.URL::to('/admingm/'.$controladorPadreDatos->controlador_url.'/'.$controladorDatos[0]->controlador_url).'"><span class="'.$controladorDatos[0]->controlador_icono.'"></span>
                                    '.$controladorDatos[0]->controlador.'</a>
                                </li>';

                $breadCumb .= '<h4><span class="'.$controladorDatos[0]->controlador_icono.'"></span> '.$controladorDatos[0]->controlador.'</h4>';
                $breadCumb .= '</ul>';
                
            }
        }
        return $breadCumb;
    }
    
    public static function archivoPeso($bytes){
        $clase = array(" Bytes", " KB", " MB", " GB", " TB"); 
        return round($bytes/pow(1024,($i = floor(log($bytes, 1024)))),2).$clase[$i];
    }
    
    public static function enviarAdministradorAcceso($administradorDatos, $contrasenia){
        $datosAcceso = array(
                        'nombre' => $administradorDatos->nombre.' '.$administradorDatos->apellido_paterno,
                        'usuario' => $administradorDatos->e_mail,
                        'contrasenia' => $contrasenia,
                    );
        try{
            //Mail::send('plantillas.correo.enviarAdministradorAccesoCorreo', $datosAcceso, function($message) use ($administradorDatos){
            //    $message->from('sistemas@segurodegastosmedicosmayores.mx', 'Sistemas - segurodegastosmedicosmayores.mx');
            //    $message->to($administradorDatos->e_mail, $administradorDatos->nombre.' '.$administradorDatos->apellido_paterno);
            //    $message->subject('Acceso de administrador: '.$administradorDatos->nombre.' '.$administradorDatos->apellido_paterno);
            //});
            \ALTMailer::mail('plantillas.correo.enviarAdministradorAccesoCorreo', 
            	$datosAcceso, 
            	null, 
            	'sistemas@segurodegastosmedicosmayores.mx', 
            	'Sistemas - segurodegastosmedicosmayores.mx',
            	[$administradorDatos->e_mail],
            	'Acceso de administrador: '.$administradorDatos->nombre.' '.$administradorDatos->apellido_paterno
            );
        }catch(Exception $e){
            echo $e->getMessage();
            return false;
        }
        return true;
    }

}