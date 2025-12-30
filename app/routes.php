<?php

ini_set('memory_limit', '1024M');

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
/*
Route::group(['prefix', 'admingm', 'namespace' => 'Backend', 'before' => 'auth'], function()
{
    Route::get('/', array('before' => 'auth'), function(){
        return Redirect::to('main');
    });
});
*/

function getModuloHijos($id_padre){
    $tempHtml = '';
    $moduloPadre = App\Models\Backend\Modulo::find($id_padre);
    if($moduloPadre->activo == 1 && $moduloPadre->mostrar == 1){
        $modulosHijos = App\Models\Backend\Modulo::find($id_padre)->modulosHijos()->orderBy("orden")->get();
        if(count($modulosHijos) > 0){
            
            $agregarDrop = false;
            $tempHtml = '<ul class="children">';
            
            foreach($modulosHijos AS $moduloHijo){
                if($moduloHijo->activo == 1 && $moduloHijo->mostrar == 1){
                    //$permiso = App\Models\Backend\User::find(Auth::user()->id_usuario)->modulos()->where('id_modulo', '=', $moduloHijo->id_modulo)->where('activo', '=', '1')->orderBy("orden")->get();
                    $permiso = App\Models\Backend\User::find(Auth::user()->id_usuario)->modulos()->whereRaw('gm_modulos.id_modulo='.$moduloHijo->id_modulo)->whereRaw('gm_modulos.activo=1')->whereRaw('gm_modulos.mostrar=1')->orderBy("orden")->get();

                    if(count($permiso) == 1){
                        if($moduloPadre->id_padre > 0){
                            $agregarDrop = true;
                        }
                        if($moduloHijo->id_modulo == 16){
                            $tempHtml .= '<li class="'.((Request::segment(3) == $moduloHijo->controlador_url) ? 'active' : '').'"><a href="'.URL::to('/admingm/pagina/'.$moduloHijo->controlador_url).'"><i class="'.$moduloHijo->controlador_icono.'"></i> '.$moduloHijo->controlador.'</a></li>';    
                        }else{
                            $tempHtml .= '<li class="'.((Request::segment(3) == $moduloHijo->controlador_url) ? 'active' : '').'"><a href="'.URL::to('/admingm/'.$moduloPadre->controlador_url.'/'.$moduloHijo->controlador_url).'"><i class="'.$moduloHijo->controlador_icono.'"></i> '.$moduloHijo->controlador.'</a></li>';    
                        }

                    }
                    $tempHtml .= getModuloHijos($moduloHijo->id_modulo);
                }
            }
            $tempHtml .= '</ul>';
            return $tempHtml;
        }
    }
    return $tempHtml;
}

Route::get('/admigm', array('before' => 'auth'), function(){
    return Redirect::to('main');
});
/*Nuevo pdf*/
Route::post('/cotizacion/enviarCotizacinEmail', 'CotizadorController@enviarCotizacionEmail');
Route::get('/verCotizacionPDF/{idCotizacion}/{secret}/{sa}/{db}', 'CotizadorController@verCotizacionPdf');
Route::get('/verCotizacionPDF/{idCotizacion}/{secret}/{sa}/{db}/{paquetes}', 'CotizadorController@verCotizacionPdfPaquetes');
Route::get('/cotizacion/verCotizacionPDF/{idCotizacion}/{secret}/{sa}/{db}', 'CotizacionController@generarCotizacionPDF');
Route::get('/Correo/previsualizar/{idDominio}', 'CotizacionController@previsualizar');
//
Route::group(['namespace' => 'Backend', 'prefix' => 'admingm'], function(){
    /*
    $contenidos = Blog::all();
    foreach($contenidos AS $contenido){
        $contenido->introtext = str_limit(trim(strip_tags(html_entity_decode($contenido->contenido))), 150);
        $contenido->save();
    }
    exit;
    App::before(function($request){
        
    });
    */
    if (Auth::check()){
        $permisos_lista = array();
        $permisos = App\Models\Backend\User::find(Auth::user()->id_usuario)->modulos()->get();
        
        $modulos = App\Models\Backend\Modulo::where('id_modulo', '>', '0')->where('id_padre', '=', '-1')->where('activo', '=', '1')->where('mostrar', '=', '1')->orderBy("orden", "asc")->get();
        $menuHtml = '';
        foreach($modulos AS $modulo){
            $tmp = getModuloHijos($modulo->id_modulo, $menuHtml);
            if(strlen($tmp) > 0){
                $menuHtml .= '<li class="parent '.((Request::segment(2) == $modulo->controlador_url) ? 'active' : '').'"><a href="#"> <i class="'.$modulo->controlador_icono.'"></i> <span> '.$modulo->controlador.'</span> </a>';
                $menuHtml .= $tmp;
                $menuHtml .= '</li>';
            }
        }
        View::share('menuHtml', $menuHtml);

        $cotizacionesNuevas = \Cotizacion::select('id_cotizacion')->whereIn('estatus', array(1,2))->count();
        \View::share('cotizacionesNuevas', $cotizacionesNuevas);

        $cotizacionesProceso = \Cotizacion::select('id_cotizacion','nombre','secret','fecha_registro')->where('id_agente',\Auth::user()->id_usuario)->where('estatus', 3)->count();
        \View::share('cotizacionesProceso', $cotizacionesProceso);

        $cotizacionesIntentos = \Cotizacion::select('id_cotizacion')->where('id_agente', '=', -1)->whereIn('estatus', array(4,5))->count();
        \View::share('cotizacionesIntentos', $cotizacionesIntentos);
        
        $cotizacionSiguienteProgramada = \Cotizacion::select('id_cotizacion')->where('id_agente', '=', \Auth::user()->id_usuario)->where('estatus', '=', 6)->groupBy('id_cotizacion')->get()->toArray();
        $cotizacionSeguimientoProgramado = \Cotizacionseguimiento::whereIn('id_cotizacion', $cotizacionSiguienteProgramada)->where('realizado', '=', -1)->where('fecha_seguimiento', '<=', date('Y-m-d 20:00'))->groupBy('id_cotizacion')->orderBy('fecha_seguimiento', 'desc')->get()->count();
        \View::share('pendientesHoy', $cotizacionSeguimientoProgramado);
        $cotizacionSiguienteProgramadaPriodidad = \Cotizacion::select('id_cotizacion')->where('id_agente', '=', \Auth::user()->id_usuario)->where('estatus', '=', 6)->has('prioridad')->groupBy('id_cotizacion')->get()->toArray();
        $cotizacionSeguimientoHoyProgramadoPrioridad = \Cotizacionseguimiento::whereIn('id_cotizacion', $cotizacionSiguienteProgramadaPriodidad)->where('realizado', '=', -1)->where('fecha_seguimiento', '<=', date('Y-m-d 20:00'))->groupBy('id_cotizacion')->orderBy('fecha_seguimiento', 'desc')->get();
        \View::share('cotizacionSeguimientoHoyProgramadoPrioridad', $cotizacionSeguimientoHoyProgramadoPrioridad);
        
        $cotizacionSiguienteProgramada = \Cotizacion::select('id_cotizacion')->where('id_agente', '=', \Auth::user()->id_usuario)->where('estatus', '=', 6)->groupBy('id_cotizacion')->get()->toArray();
        $cotizacionSeguimientoProgramado = \Cotizacionseguimiento::whereIn('id_cotizacion', $cotizacionSiguienteProgramada)->where('realizado', '=', -1)->where('fecha_seguimiento', '<', date('Y-m-d H:i:00'))->groupBy('id_cotizacion')->orderBy('fecha_seguimiento', 'desc')->get();
        \View::share('pendientesAnteriores', $cotizacionSeguimientoProgramado);
        $cotizacionSiguienteProgramada = \Cotizacion::select('id_cotizacion')->where('id_agente', '=', \Auth::user()->id_usuario)->where('estatus', '=', 6)->has('prioridad')->groupBy('id_cotizacion')->get()->toArray();
        $cotizacionSeguimientoProgramadoPrioridad = \Cotizacionseguimiento::whereIn('id_cotizacion', $cotizacionSiguienteProgramada)->where('realizado', '=', -1)->where('fecha_seguimiento', '<', date('Y-m-d H:i:00'))->groupBy('id_cotizacion')->orderBy('fecha_seguimiento', 'desc')->get();
        \View::share('cotizacionSeguimientoProgramadoPrioridad', $cotizacionSeguimientoProgramadoPrioridad);
    }
    Route::get('/publicacion', function(){
        return Redirect::to('/admingm/publicacion/consultaPublicaciones');
    });
    Route::get('/administrador', function(){
        return Redirect::to('/admingm/administrador/consultaAdministradores');
    });
    Route::post('/administrador/actualizarPassword', 'AdministradorController@actualizarPassword');
    Route::get('/administrador/miPerfil', 'AdministradorController@miPerfil');
    Route::post('/administrador/miPerfil/guardaPie', 'AdministradorController@guardaPie');
    Route::post('/administrador/actualizarModuloPermiso', 'AdministradorController@actualizarModuloPermiso');
    Route::get('/administrador/permisosAdministrador/{idAdministrador}', 'AdministradorController@permisosAdministrador');
    Route::post('/administrador/agregarAdministrador', 'AdministradorController@agregarAdministrador');
    Route::get('/administrador/altaAdministrador', 'AdministradorController@altaAdministrador');
    Route::post('/administrador/enviarAccesoAdministrador', 'AdministradorController@enviarAccesoAdministrador');
    Route::post('/administrador/eliminarAdministrador', 'AdministradorController@eliminarAdministrador');
    Route::post('/administrador/actualizarAdministrador', 'AdministradorController@actualizarAdministrador');
    Route::post('/administrador/getConsultaAdministradores', 'AdministradorController@getConsultaAdministradores');
    Route::get('/administrador/consultaAdministradores', 'AdministradorController@consultaAdministradores');
    Route::get('/administrador/consultaPie/{idDominio}', 'AdministradorController@consultaPie');

    Route::post('/aseguradora/guardarPaqueteDeducibles', 'AseguradoraController@guardarPaqueteDeducibles');
    Route::post('/aseguradora/cargarPaqueteDeducibles', 'AseguradoraController@cargarPaqueteDeducibles');
    Route::post('/aseguradora/cargarPaquetesAseguradora', 'AseguradoraController@cargarPaquetesAseguradora');
    Route::get('/aseguradora/deducibles', 'AseguradoraController@deducibles');
    Route::get('/aseguradora/sumasAseguradas', 'AseguradoraController@sumasAseguradas');
    Route::get('/aseguradora/sumasAseguradas2023', 'AseguradoraController@sumasAseguradas2023');
    Route::post('/aseguradora/actualizarSumaAsegurada', 'AseguradoraController@actualizarSumaAsegurada');
    Route::post('/aseguradora/actualizarSumaAsegurada2023', 'AseguradoraController@actualizarSumaAsegurada2023');
    Route::get('/aseguradora/notasPlanes', 'AseguradoraController@notasPlanes');
    Route::get('/aseguradora/recargos', 'RecargosController@recargos');
    Route::post('/aseguradora/guardarNota', 'AseguradoraController@guardarNota');
    Route::post('/aseguradora/on-off-aseguradora', 'AseguradoraController@onOffAseguradora');
    Route::post('/aseguradora/on-off-plan', 'AseguradoraController@onOffPlan');
    Route::get('/aseguradora/imprimibles', 'AseguradoraController@imprimibles');
    Route::post('/aseguradora/imprimibles/guardarTextoProtecto', 'AseguradoraController@guardaTextoProtecto');
    Route::post('/aseguradora/imprimibles/guardarTextoSaludo', 'AseguradoraController@guardaTextoSaludo');
    Route::post('/aseguradora/imprimibles/guardarCotizacionEncabezado', 'AseguradoraController@guardarCotizacionEncabezado');
    Route::post('/aseguradora/imprimibles/guardarCotizacionAbajode', 'AseguradoraController@guardarCotizacionAbajode');
    Route::post('/aseguradora/imprimibles/guardarCotizacionPie', 'AseguradoraController@guardarCotizacionPie');
    Route::post('/aseguradora/actualizarPaquete', 'AseguradoraController@actualizarPaquete');
    Route::post('/aseguradora/actualizarPaquete2023', 'AseguradoraController@actualizarPaquete2023');
    Route::post('/aseguradora/recargos/actualizarInteres', 'RecargosController@actualizarInteres');
    Route::get('/aseguradora/consultaImprimibles/{idDominio}', 'AseguradoraController@consultaImprimibles');
    
    Route::get('/aseguradora/baseMapfre', 'AseguradoraController@baseMapfre');
    Route::post('/aseguradora/baseMapfre', 'AseguradoraController@baseMapfreUpdate');
    Route::get('/aseguradora/estadisticas', 'AseguradoraController@estadisticas');
    Route::post('/aseguradora/estadisticas', 'AseguradoraController@estadisticasReporte');
    Route::get('/aseguradora/conceptos2023', 'AseguradoraController@conceptos2023');
    Route::get('/aseguradora/getConceptos2023', 'AseguradoraController@getConceptos2023');
    Route::post('/aseguradora/actualizaConceptos2023', 'AseguradoraController@actualizaConceptos2023');
    Route::get('/aseguradora/meInteresa', 'AseguradoraController@meInteresa');
    Route::post('/aseguradora/guardarMeInteresa', 'AseguradoraController@guardarMeInteresa');
    Route::get('/aseguradora/consultaListasDistribucion', 'AseguradoraController@listasDistribucion');
    Route::post('/aseguradora/getConsultaListasDistribucion', 'AseguradoraController@getConsultaListasDistribucion');
    Route::get('/aseguradora/altaListaDistribucion', 'AseguradoraController@altaListaDistribucion');
    Route::get('/aseguradora/altaListaDistribucion/{idLista}', 'AseguradoraController@altaListaDistribucion');
    Route::post('/aseguradora/guardarListasDistribucion', 'AseguradoraController@guardarListasDistribucion');
    Route::post('/aseguradora/getConsultaListaDistribucionesPlantillas/{idLista}', 'AseguradoraController@getConsultaListaDistribucionesPlantillas');
    Route::post('/aseguradora/agregarPlantilla', 'AseguradoraController@agregarPlantilla');
    Route::get('/cotizacion/cotizacionesListaDistribucion', 'CotizacionController@cotizacionesListaDistribucion');
    Route::post('/cotizacion/getCotizacionesListaDistribucion', 'CotizacionController@getCotizacionesListaDistribucion');
    Route::get('/cotizacion/cotizacionListaDistribucion/{idCotizacion}', 'CotizacionController@cotizacionListaDistribucion');
    Route::post('/cotizacion/whatsappMessage', 'CotizacionController@whatsappMessage');

    Route::post('/Correo/respuestasCorreo', 'CorreoRespuestaController@store');
    Route::delete('/Correo/respuestasCorreo/{id}', 'CorreoRespuestaController@destroy');
    Route::put('/Correo/actualizarCorreoRespuestaCampos', 'CorreoRespuestaController@update');
    Route::post('/Correo/getConsultaCorreoRespuesta', 'CorreoRespuestaController@show');
    Route::get('/Correo/respuestasCorreo', 'CorreoRespuestaController@index');

    Route::get('/Correo/contenido', 'ContenidoCorreoController@editarContenido');
    Route::post('/Correo/contenido/guardaEncabezado', 'ContenidoCorreoController@guardaEncabezado');
    Route::post('/Correo/contenido/guardaCuerpo', 'ContenidoCorreoController@guardaCuerpo');
    Route::post('/Correo/contenido/guardaPie', 'ContenidoCorreoController@guardaPie');
    Route::get('/Correo/consultaContenidoCorreo/{idDominio}', 'ContenidoCorreoController@consultaContenidoCorreo');

    Route::post('/seguimiento/actualizarSeguimientosCalendario', 'CalendarioController@actualizarSeguimientosCalendario');
    Route::any('/seguimiento/getSeguimientosCalendario', 'CalendarioController@show');
    Route::get('/seguimiento/calendario', 'CalendarioController@index');

    Route::any('/seguimiento/getConsultaSeguimientos', 'SeguimientoController@getConsultaSeguimientos');
    Route::get('/seguimiento/consultaSeguimientos', 'SeguimientoController@consultaSeguimientos');

    Route::post('/cotizacion/getConsultaCotizacionesPrioridad', 'CotizacionController@getConsultaCotizacionesPrioridad');
    Route::get('/cotizacion/misPrioridades', 'CotizacionController@misPrioridades');
    Route::post('/cotizacion/prioridadCotizacion', 'CotizacionController@prioridadCotizacion');
    Route::post('/cotizacion/actualizarIntegrantes', 'CotizacionController@actualizarIntegrantes');
    Route::post('/cotizacion/actualizarIntegrantes2023', 'CotizacionController@actualizarIntegrantes2023');
    Route::post('/cotizacion/seguimientoRealizado', 'CotizacionController@seguimientoRealizado');
    Route::post('/cotizacion/siguienteCotizacion', 'CotizacionController@siguienteCotizacion');
    Route::post('/cotizacion/agregarSeguimiento', 'CotizacionController@agregarSeguimiento');
    Route::get('/cotizacion', function(){
        return Redirect::to('/admingm/cotizacion/consultaCotizaciones');
    });
    Route::post('/cotizacion/actualizarCotizacionCampos', 'CotizacionController@actualizarCotizacionCampos');
    Route::post('/cotizacion/eliminarCotizacion', 'CotizacionController@eliminarCotizacion');
    Route::post('/cotizacion/enviarCotizacinEmail', 'CotizacionController@enviarCotizacinEmail');
    Route::post('/cotizacion/enviarCotizacinEmailNuevo', 'CotizacionController@enviarCotizacinEmailNuevo');
    Route::get('/cotizacion/verCotizacionRapida/{idCotizacion}', 'CotizacionController@verCotizacionRapida');
    Route::get('/cotizacion/verCotizacion/{idCotizacion}/{secret}', 'CotizacionController@verCotizacion');
    Route::get('/cotizacion/verCotizacion/{idCotizacion}', 'CotizacionController@verCotizacion');
    Route::get('/cotizacion/verCotizacionNuevo/{idCotizacion}/{secret}', 'CotizacionController@verCotizacionNuevo');
    Route::post('/cotizacion/agregarCotizacion', 'CotizacionController@agregarCotizacion');
    Route::get('/cotizacion/altaCotizacion', 'CotizacionController@altaCotizacion');
    Route::get('/cotizacion/altaCotizacion/{idCotizacion}', 'CotizacionController@altaCotizacion');
    Route::post('/cotizacion/getConsultaCotizaciones', 'CotizacionController@getConsultaCotizaciones');
    Route::get('/cotizacion/consultaCotizaciones', 'CotizacionController@consultaCotizaciones');
    Route::post('/cotizacion/getConsultaCotizacionesTodas', 'CotizacionController@getConsultaCotizacionesTodas');
    Route::get('/cotizacion/getConsultaCotizacionesTodas2', 'CotizacionController@getConsultaCotizacionesTodas2');
    Route::get('/cotizacion/getConsultaCotizacionesTodas2/{idAgente}/{idEstatus}', 'CotizacionController@getConsultaCotizacionesTodas2');
    Route::get('/cotizacion/getConsultaCotizacionesTodas2/{idAgente}/{idEstatus}/{valor}', 'CotizacionController@getConsultaCotizacionesTodas2');
    Route::get('/cotizacion/cotizacionesTodas', 'CotizacionController@cotizacionesTodas');
    Route::post('/cotizacion/guardarEmailBlackList', 'CotizacionController@guardarEmailBlackList');
    Route::post('/cotizacion/eliminarEmailBlackList', 'CotizacionController@eliminarEmailBlackList');
    Route::post('/cotizacion/actualizarEmailBlackListCampo', 'CotizacionController@actualizarEmailBlackListCampo');
    Route::post('/cotizacion/getEmailBlackList', 'CotizacionController@getEmailBlackList');
    Route::get('/cotizacion/emailBlackList', 'CotizacionController@emailBlackList');
    Route::post('/cotizacion/guardarEmailWhiteList', 'CotizacionController@guardarEmailWhiteList');
    Route::post('/cotizacion/eliminarEmailWhiteList', 'CotizacionController@eliminarEmailWhiteList');
    Route::post('/cotizacion/actualizarEmailWhiteListCampo', 'CotizacionController@actualizarEmailWhiteListCampo');
    Route::post('/cotizacion/getEmailWhiteList', 'CotizacionController@getEmailWhiteList');
    Route::get('/cotizacion/emailWhiteList', 'CotizacionController@emailWhiteList');
    Route::post('/cotizacion/recotizar/{idCotizacion}/{suma}/{deducible}', 'CotizacionController@recotizarMapfre');
    Route::post('/cotizacion/recotizar/{idCotizacion}/{suma}/{deducible}/{primera}', 'CotizacionController@recotizarMapfre');
    Route::post('/cotizacion/recotizar2023/{idCotizacion}/{hospitales}', 'CotizacionController@recotizarMapfre2023');
    Route::post('/cotizacion/cotizacionToListaDistribucion', 'CotizacionController@cotizacionToListaDistribucion');

    Route::get('/publicacion', function(){
        return Redirect::to('/admingm/publicacion/consultaPublicaciones');
    });
    Route::post('/publicacion/getPublicacionesCategorias', 'BlogController@getPublicacionesCategorias');
    Route::post('/publicacion/actualizarPublicacionesOrden', 'BlogController@actualizarPublicacionesOrden');
    Route::get('/publicacion/ordenarArticulos', 'BlogController@ordenarArticulos');
    Route::post('/publicacion/actualizarCategoria', 'BlogController@actualizarCategoria');
    Route::post('/publicacion/eliminarCategoria', 'BlogController@eliminarCategoria');
    Route::post('/publicacion/agregarCategoriaHijo', 'BlogController@agregarCategoriaHijo');
    Route::post('/publicacion/getCategoriasNestableMenu', 'BlogController@getCategoriasNestableMenu');
    Route::post('/publicacion/actualizarCategoriasOrden', 'BlogController@actualizarCategoriasOrden');
    Route::get('/publicacion/configuracionCategorias', 'BlogController@configuracionCategorias');
    Route::post('/publicacion/actualizarPublicacion', 'BlogController@actualizarPublicacion');
    Route::post('/publicacion/eliminarPublicacion', 'BlogController@eliminarPublicacion');
    Route::post('/publicacion/getConsultaPublicaciones', 'BlogController@getConsultaPublicaciones');
    Route::post('/publicacion/agregarPublicacion', 'BlogController@agregarPublicacion');
    Route::get('/publicacion/altaPublicacion', 'BlogController@altaPublicacion');
    Route::get('/publicacion/altaPublicacion/{idBlog}', 'BlogController@altaPublicacion');
    Route::get('/publicacion/consultaPublicaciones', 'BlogController@consultaPublicaciones');

    Route::get('/pagina', function(){
        return Redirect::to('/admingm/pagina/menu');
    });
    Route::post('/pagina/cargarPaginaImagen', 'PaginaController@cargarPaginaImagen');
    Route::post('/pagina/getPaginaDatos', 'PaginaController@getPaginaDatos');
    Route::post('/pagina/guardarPagina', 'PaginaController@guardarPagina');
    Route::get('/pagina/editarPagina/{idPagina}', 'PaginaController@editarPagina');
    Route::get('/pagina/altaPagina', 'PaginaController@altaPagina');
    Route::post('/pagina/eliminarMenu', 'PaginaController@eliminarMenu');
    Route::post('/pagina/actualizarMenusOrden', 'PaginaController@actualizarMenusOrden');
    Route::post('/pagina/actualizarMenu', 'PaginaController@actualizarMenu');
    Route::post('/pagina/agregarMenuHijo', 'PaginaController@agregarMenuHijo');
    Route::post('/pagina/getMenuNestableMenu', 'PaginaController@getMenuNestableMenu');
    Route::get('/pagina/menu', 'PaginaController@menu');

    Route::get('/listasJson/cotizacionEstatusJson', 'ListasJsonController@cotizacionEstatusJson');
    Route::get('/listasJson/agentesJson', 'ListasJsonController@agentesJson');
    Route::get('/listasJson/categoriasBlogJson', 'ListasJsonController@categoriasBlogJson');
    Route::get('/listasJson/dominiosJson', 'ListasJsonController@dominiosJson');

    Route::get('/main', 'MainController@main');
    
    Route::post('/login/iniciarSesion', 'LoginController@iniciarSesion');
    Route::get('/login/cerrarSesion', 'LoginController@cerrarSesion');
    Route::get('/login', function(){
        return View::make('backend.login.iniciarSesion');
    });
    //Agregados por Mario
    //Route::resource('/dominios', 'DominiosController');
    //Route::post('/dominios/cambiaDB', 'DominiosController@cambiaDominioDB');

	Route::get('/aseguradoras', function(){
        return Redirect::to('/admingm/aseguradoras/consultaAseguradoras');
    });
    Route::get('/aseguradora/consultaAseguradoras', 'AseguradorasController@consultaAseguradoras');
    Route::post('/aseguradoras/getConsultaAseguradoras', 'AseguradorasController@getConsultaAseguradoras');
    Route::post('/aseguradoras/getConsultaAseguradoraPlanes/{idAseguradora}', 'AseguradorasController@getConsultaAseguradoraPlanes');
    Route::get('/aseguradoras/altaAseguradora', 'AseguradorasController@altaAseguradora');
    Route::get('/aseguradoras/altaAseguradora/{idAseguradora}', 'AseguradorasController@altaAseguradora');
    Route::post('/aseguradoras/agregarAseguradora', 'AseguradorasController@agregarAseguradora');
    Route::post('/aseguradoras/agregarPlan', 'AseguradorasController@agregarPlan');
    Route::post('/aseguradoras/actualizarAseguradora', 'AseguradorasController@actualizarAseguradora');
    Route::post('/aseguradoras/guardarWeb', 'AseguradorasController@guardarWeb');
    Route::post('/aseguradoras/guardarMobile', 'AseguradorasController@guardarMobile');
    Route::post('/aseguradoras/guardarPromo', 'AseguradorasController@guardarPromo');
    
    Route::get('/aseguradora/consultaDominios', 'DomainsController@consultaDominios');
    Route::post('/aseguradora/getConsultaDominios', 'DomainsController@getConsultaDominios');
    Route::post('/aseguradora/actualizarDominio', 'DomainsController@actualizarDominio');
    Route::get('/aseguradora/altaDominio', 'DomainsController@altaDominio');
    Route::post('/aseguradora/agregarDominio', 'DomainsController@agregarDominio');
	
	Route::get('/publicacion/consultaRedirecciones', 'PaginaController@consultaRedirecciones');
	Route::post('/publicacion/getConsultaRedirecciones', 'PaginaController@getConsultaRedirecciones');
	Route::post('/publicacion/actualizarRedireccion', 'PaginaController@actualizarRedireccion');
	Route::get('/publicacion/altaRedireccion', 'PaginaController@altaRedireccion');
    Route::post('/publicacion/agregarRedireccion', 'PaginaController@agregarRedireccion');
    
    Route::get('/publicacion/consultaArchivos', 'PaginaController@consultaArchivos');
	Route::post('/publicacion/getConsultaArchivos', 'PaginaController@getConsultaArchivos');
	Route::post('/publicacion/actualizarArchivo', 'PaginaController@actualizarArchivo');
	Route::get('/publicacion/altaArchivo', 'PaginaController@altaArchivo');
    Route::post('/publicacion/agregarArchivo', 'PaginaController@agregarArchivo');
    Route::post('/publicacion/eliminarArchivo/{idArchivo}', 'PaginaController@eliminarArchivo');
	
	Route::get('/publicacion/sitemap', 'PaginaController@sitemap');
	Route::post('/publicacion/doSitemap', 'PaginaController@doSitemap');
	
	Route::get('/publicacion/piePagina', 'PaginaController@piePagina');
	Route::post('/publicacion/doPiePagina', 'PaginaController@doPiePagina');
	
    Route::get('/', function(){
        return Redirect::to('/admingm/main');
    });
});

App::before(function($request)
{
    if($_SERVER['REQUEST_METHOD'] === 'OPTIONS' ){
        header('Access-Control-Allow-Origin', '*');
        header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers', 'Origin, Content-Type, Accept, Authorization, X-Request-With');
        header('Access-Control-Allow-Credentials', 'true');
    }
    
    $menuSitio = '';
    SistemaFunciones::crearMenuNestable($menuSitio, -1);
    View::share("menuSitio", $menuSitio);
    
    $contenidosReciente = Blog::select('id_blog_categoria', 'titulo', 'alias', 'imagen_large', 'imagen_medium', 'imagen_small', 'fecha_publicacion')->where('estatus', '=', 1)->orderBy('id_blog_categoria')->orderBy('orden')->orderBy('fecha_publicacion', 'desc')->limit(5)->get();
    View::share('contenidosReciente', $contenidosReciente);
    
    $menuSitioFooter = '';
    
    SistemaFunciones::crearMenuNestable($menuSitioFooter, -1, -1);
    View::share("menuSitioFooter", $menuSitioFooter);
});

App::missing(function($exception)
{
	if(Request::path()!=""){
		$contenido = Blog::where("alias", "=", Request::path())->where("estatus", "=", 1)->get();
		if(count($contenido)==1){
			$contenido = $contenido[0];
            //$contenido->contenido = str_replace("http://nuevo.gastos-medicos.com.mx/admin_gm/application/", asset(''), $contenido->contenido);
            
            $blogCategoriasMenu = SistemaFunciones::getBlogCategoriasMenu($contenido->id_blog_categoria);
            $categoriaPadre = array();
            if($contenido->categoria->id_padre > 0){
                $categoriaPadre = Blogcategoria::find($contenido->categoria->id_padre);
            }else{
                $categoriaPadre = Blogcategoria::find($contenido->id_blog_categoria);
            }
            $rastroMigajas = array();
            $rastroMigajas[$categoriaPadre->id_blog_categoria] = $categoriaPadre;
            
            while($categoriaPadre->id_padre > 0){
                if($categoriaPadre->id_padre > 0){
                    $categoriaPadre = Blogcategoria::find($categoriaPadre->id_padre);
                }
                $rastroMigajas[$categoriaPadre->id_blog_categoria] = $categoriaPadre;
            }
            $rastroMigajasTexto = '';
            if(count($rastroMigajas) > 0){
                $n = 0;
                foreach($rastroMigajas AS $rastroMigaja){
                    if(count($blogCategoriasMenu) == 0){
                        $blogCategoriasMenu = SistemaFunciones::getBlogCategoriasMenu($rastroMigaja->id_blog_categoria);
                    }
                    // $rastroMigajasTexto .= '<a href="'.URL::to('/admingm/'.$rastroMigaja->categoria_alias).'" style="color: #fff !important;">'.$rastroMigaja->categoria.'</a>';
                    $rastroMigajasTexto .= '<a href="'.URL::to($rastroMigaja->categoria_alias).'" style="color: #fff !important;">'.$rastroMigaja->categoria.'</a>';
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
            $blogCategoriasMenu = SistemaFunciones::getBlogCategoriasMenuLista($blogCategoriasMenu, array('aseguradoras'), $contenido->categoria->categoria_alias);
            View::share("blogCategoriasMenu", $blogCategoriasMenu);
            $contenido->html = json_decode($contenido->html);
            View::share('contenido', $contenido);
            View::share('metaTitulo', $contenido->titulo);
            View::share('metaKeys', $contenido->metakey);
            View::share('metaDescripcion', str_limit(trim(strip_tags(html_entity_decode($contenido->metadesc))), 155, ''));
            
            $contenidosRelacionados = Blog::select('id_blog_categoria', 'titulo', 'alias', 'fecha_publicacion', 'imagen_small')
                ->where('estatus', '=', 1)->where('id_blog_categoria', '=', $contenido->id_blog_categoria)
                ->where('id_blog', '<>', $contenido->id_blog)
                ->orderBy('id_blog_categoria')->orderBy('orden')->orderBy('fecha_publicacion', 'desc')->limit(5)->get();
            View::share('contenidosRelacionados', $contenidosRelacionados);
            $contenidosPopulares = Blog::select('id_blog_categoria', 'titulo', 'alias', 'imagen_large', 'imagen_medium', 'imagen_small', 'fecha_publicacion')
                ->whereRaw("MATCH(`metakey`) AGAINST('".$contenido->metakey."' IN BOOLEAN MODE)")
                ->orderBy('id_blog_categoria')->orderBy('orden')->orderBy('fecha_publicacion', 'ASC')
                ->where('estatus', '=', 1)->where('id_blog', '<>', $contenido->id_blog)
                ->paginate(5)->getItems();
            View::share('contenidosPopulares', $contenidosPopulares);
            //Paginavisita::hit();
            $contenido::hitVisita($contenido->id_blog);
            if($contenido->tipo == 2){
            	// Para plantilla original	
            	/*if($contenido->alias == 'main'){
            	    View::share('contenido', $contenido->html);
                    return View::make('inicio');
                }
                if(!Input::get('porto'))
                	return View::make('blog.verContenidoHtml');
                else{
					if(Input::get('porto')!='1')
						return View::make('blog.verContenidoHtml');
					else
						return View::make('blog.verPortoHtml');
				}*/
				return View::make('blog.verPortoHtml');
            }
            if($contenido->id_blog!=225 && $contenido->id_blog!=226 && $contenido->id_blog!=235){
				// Para plantilla original
				/*if(!Input::get('porto'))
					return View::make('blog.verContenido');
				else{
					if(Input::get('porto')!='1')
						return View::make('blog.verContenido');
					else
						return View::make('blog.verPortoBlog');
				}*/
				if($contenido->id_blog==248 || $contenido->incluir_cotizador==1 || $contenido->incluir_cotizador_nuevo==1){
					$cotizadores = new \Cotizadores();
					$cotizadores->alias = $contenido->alias;
					$cotizadores->movil = ((isset($_COOKIE["isMobile"])) ? (($_COOKIE["isMobile"]=="true") ? 1 : 0) : 0);
					$cotizadores->save();
				}
				return View::make('blog.verPortoBlog');
			}
            else
            	return View::make('blog.verPortoContenido');
		}
	}
	if(Request::path()!=""){
		$redireccion = Redireccion::where("alias", "=", Request::path())->first();
		if($redireccion){
			return Redirect::to('/' . $redireccion->redirect_to, $redireccion->tipo);
		}
	}
	
    if(strlen(Request::segment(1)) > 0 || (strlen(Request::segment(1)) > 0 && strlen(Request::segment(2)) > 0)){
        $contenido = Blog::where('alias', '=', Request::segment(1))->where('estatus', '=', 1)->get();
        if(count($contenido) == 0){
            $contenido = Blog::where('alias', '=', Request::segment(2))->where('estatus', '=', 1)->get();
        }
        if(count($contenido) == 1){
        	$contenido = $contenido[0];
            //$contenido->contenido = str_replace("http://nuevo.gastos-medicos.com.mx/admin_gm/application/", asset(''), $contenido->contenido);
            
            $blogCategoriasMenu = SistemaFunciones::getBlogCategoriasMenu($contenido->id_blog_categoria);
            $categoriaPadre = array();
            if($contenido->categoria->id_padre > 0){
                $categoriaPadre = Blogcategoria::find($contenido->categoria->id_padre);
            }else{
                $categoriaPadre = Blogcategoria::find($contenido->id_blog_categoria);
            }
            $rastroMigajas = array();
            $rastroMigajas[$categoriaPadre->id_blog_categoria] = $categoriaPadre;
            
            while($categoriaPadre->id_padre > 0){
                if($categoriaPadre->id_padre > 0){
                    $categoriaPadre = Blogcategoria::find($categoriaPadre->id_padre);
                }
                $rastroMigajas[$categoriaPadre->id_blog_categoria] = $categoriaPadre;
            }
            $rastroMigajasTexto = '';
            if(count($rastroMigajas) > 0){
                $n = 0;
                foreach($rastroMigajas AS $rastroMigaja){
                    if(count($blogCategoriasMenu) == 0){
                        $blogCategoriasMenu = SistemaFunciones::getBlogCategoriasMenu($rastroMigaja->id_blog_categoria);
                    }
                    // $rastroMigajasTexto .= '<a href="'.URL::to('/admingm/'.$rastroMigaja->categoria_alias).'" style="color: #fff !important;">'.$rastroMigaja->categoria.'</a>';
                    $rastroMigajasTexto .= '<a href="'.URL::to($rastroMigaja->categoria_alias).'" style="color: #fff !important;">'.$rastroMigaja->categoria.'</a>';
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
            $blogCategoriasMenu = SistemaFunciones::getBlogCategoriasMenuLista($blogCategoriasMenu, array('aseguradoras'), $contenido->categoria->categoria_alias);
            View::share("blogCategoriasMenu", $blogCategoriasMenu);
            $contenido->html = json_decode($contenido->html);
            View::share('contenido', $contenido);
            View::share('metaTitulo', $contenido->titulo);
            View::share('metaKeys', $contenido->metakey);
            View::share('metaDescripcion', str_limit(trim(strip_tags(html_entity_decode($contenido->metadesc))), 155, ''));
            
            $contenidosRelacionados = Blog::select('id_blog_categoria', 'titulo', 'alias', 'fecha_publicacion', 'imagen_small')
                ->where('estatus', '=', 1)->where('id_blog_categoria', '=', $contenido->id_blog_categoria)
                ->where('id_blog', '<>', $contenido->id_blog)
                ->orderBy('id_blog_categoria')->orderBy('orden')->orderBy('fecha_publicacion', 'desc')->limit(5)->get();
            View::share('contenidosRelacionados', $contenidosRelacionados);
            $contenidosPopulares = Blog::select('id_blog_categoria', 'titulo', 'alias', 'imagen_large', 'imagen_medium', 'imagen_small', 'fecha_publicacion')
                ->whereRaw("MATCH(`metakey`) AGAINST('".$contenido->metakey."' IN BOOLEAN MODE)")
                ->orderBy('id_blog_categoria')->orderBy('orden')->orderBy('fecha_publicacion', 'ASC')
                ->where('estatus', '=', 1)->where('id_blog', '<>', $contenido->id_blog)
                ->paginate(5)->getItems();
            View::share('contenidosPopulares', $contenidosPopulares);
            //Paginavisita::hit();
            $contenido::hitVisita($contenido->id_blog);
            if($contenido->tipo == 2){
               // Para plantilla original 
               /*if($contenido->alias == 'main'){
                    View::share('contenido', $contenido->html);
                    return View::make('inicio');
                }
                if(!Input::get('porto'))
                	return View::make('blog.verContenidoHtml');
                else{
					if(Input::get('porto')!='1')
						return View::make('blog.verContenidoHtml');
					else
						return View::make('blog.verPortoHtml');
				}*/
				return View::make('blog.verPortoHtml');
            }
            if($contenido->id_blog==248){
				if(null!==Request::segment(3)){
					if(is_numeric(Request::segment(2))){
						$cotizacionDatos = Cotizacion::find(Request::segment(2));
						if($cotizacionDatos){
							if($cotizacionDatos->secret==Request::segment(3)){
								$cotizacionDatos->integrantes = json_decode($cotizacionDatos->integrantes);
								View::share('cotizacionDatos', $cotizacionDatos);
							}
						}
					}
				}
				else{
					if(null!==Request::segment(2)){
						$cotizacionDatos = Cotizacion::where('link_cotizacion', '=', Request::segment(2))->first();
						if($cotizacionDatos){
							$cotizacionDatos->integrantes = json_decode($cotizacionDatos->integrantes);
							View::share('cotizacionDatos', $cotizacionDatos);
						}
					}
				}
				$cotizadores = new \Cotizadores();
				$cotizadores->alias = $contenido->alias;
				$cotizadores->movil = ((isset($_COOKIE["isMobile"])) ? (($_COOKIE["isMobile"]=="true") ? 1 : 0) : 0);
				$cotizadores->save();
				return View::make('blog.verPortoBlog');
			}
			else{
		       if($contenido->id_blog!=225 && $contenido->id_blog!=226 && $contenido->id_blog!=235){
		       		// Para plantilla original 
		        	/*if(!Input::get('porto'))
						return View::make('blog.verContenido');
					else{
						if(Input::get('porto')!='1')
							return View::make('blog.verContenido');
						else
							return View::make('blog.verPortoBlog');
					}*/
					return View::make('blog.verPortoBlog');
				}
		        else
		        	return View::make('blog.verPortoContenido');
			}
        }
    }
    $cotizadorNuevo = Blog::where('alias', '=', Request::segment(1))->where('estatus', '=', 3)->get();
    if(count($cotizadorNuevo)>0){
    	$contenido = $cotizadorNuevo[0];
    	$cotizadores = new \Cotizadores();
		$cotizadores->alias = $contenido->alias;
		$cotizadores->movil = ((isset($_COOKIE["isMobile"])) ? (($_COOKIE["isMobile"]=="true") ? 1 : 0) : 0);
		$cotizadores->save();
    	View::share('contenido', $contenido);
    	
    	return View::make('blog.verPortoBlog');
    }
    $categoria = Blogcategoria::where('categoria_alias', '=', Request::segment(1))->get();
    if(count($categoria) == 1){
    	$categoria = $categoria[0];
        
        $contenidos = Blog::where('id_blog_categoria', '=', $categoria->id_blog_categoria)->where('estatus', '=', 1)->orderBy('id_blog_categoria')->orderBy('orden')->orderBy('fecha_publicacion', 'desc')->paginate(100);
        $paginacion = $contenidos->links();
        View::share("paginacion", htmlentities($paginacion));
        View::share('contenidosArray', $contenidos->getItems() );

        $blogCategoriasMenu = SistemaFunciones::getBlogCategoriasMenu($categoria->id_padre);
        $rastroMigajas = array();
        $categoriaPadre = Blogcategoria::find($categoria->id_blog_categoria);
        $rastroMigajas[0] = $categoriaPadre;
        $n = 1;
        while($categoriaPadre->id_padre > 0){
            if($categoriaPadre->id_padre > 0){
                $categoriaPadre = Blogcategoria::find($categoriaPadre->id_padre);
            }
            $rastroMigajas[$n] = $categoriaPadre;
            $n++;
        }
        //ksort($rastroMigajas);
        $rastroMigajas = array_reverse($rastroMigajas);
        $rastroMigajasTexto = '';
        if(count($rastroMigajas) > 0){
            $n = 0;
            foreach($rastroMigajas AS $rastroMigaja){
                /*
                if(count($blogCategoriasMenu) == 0){
                    $blogCategoriasMenu = SistemaFunciones::getBlogCategoriasMenu($rastroMigaja->id_blog_categoria);
                }
                */
                $rastroMigajasTexto .= '<a href="'.URL::to('/'.$rastroMigaja->categoria_alias).'" style="color: #fff !important;">'.$rastroMigaja->categoria.'</a>';
                if($n < (count($rastroMigajas) - 1)){
                    $rastroMigajasTexto .= ' <i>/</i> ';
                }
                $n++;
            }
        }
        View::share('metaKeys', $categoria->metakey);
        View::share('metaTitulo', $categoria->categoria);
        View::share('metaDescripcion', str_limit(trim(strip_tags(html_entity_decode($categoria->metadesc))), 155, ''));
        View::share('rastroMigajasTexto', $rastroMigajasTexto);
        if($categoria->categoria_alias == 'aseguradoras' || $categoriaPadre->categoria_alias == 'aseguradoras'){
            $blogCategoriasMenu = SistemaFunciones::getBlogCategoriasMenu($categoria->id_blog_categoria);
            if(count($blogCategoriasMenu) == 0){
                $blogCategoriasMenu = SistemaFunciones::getBlogCategoriasMenu($categoria->id_padre);
            }
            $blogCategoriasMenu = SistemaFunciones::getBlogCategoriasMenuLista($blogCategoriasMenu, array(), $categoria->categoria_alias);
            View::share("blogCategoriasMenu", $blogCategoriasMenu);
        }else{
            $blogCategoriasMenu = SistemaFunciones::getBlogCategoriasMenu($categoriaPadre->id_blog_categoria);
            if(count($blogCategoriasMenu) == 0){
                $blogCategoriasMenu = SistemaFunciones::getBlogCategoriasMenu($categoriaPadre->id_padre);
            }
            $blogCategoriasMenu = SistemaFunciones::getBlogCategoriasMenuLista($blogCategoriasMenu, array('aseguradoras'), $categoria->categoria_alias);
            View::share("blogCategoriasMenu", $blogCategoriasMenu);
        }
        View::share('categoria', $categoria);
        //Paginavisita::hit();
        
        // Para plantilla original
        /*if(!Input::get('porto'))
        	return View::make('blog.categoriasContenido');
        else{
			if(Input::get('porto')!='1')
				return View::make('blog.categoriasContenido');
			else
				return View::make('blog.categoriasPorto');
		}*/
		return View::make('blog.categoriasPorto');
    }else{
    	$contenido = Blog::where('alias', '=', '404')->where('estatus', '=', 1)->get();
		if(count($contenido) == 1){
			$contenido = $contenido[0];
            //$contenido->contenido = str_replace("http://nuevo.gastos-medicos.com.mx/admin_gm/application/", asset(''), $contenido->contenido);
            
            $blogCategoriasMenu = SistemaFunciones::getBlogCategoriasMenu($contenido->id_blog_categoria);
            $categoriaPadre = array();
            if($contenido->categoria->id_padre > 0){
                $categoriaPadre = Blogcategoria::find($contenido->categoria->id_padre);
            }else{
                $categoriaPadre = Blogcategoria::find($contenido->id_blog_categoria);
            }
            $rastroMigajas = array();
            $rastroMigajas[$categoriaPadre->id_blog_categoria] = $categoriaPadre;
            
            while($categoriaPadre->id_padre > 0){
                if($categoriaPadre->id_padre > 0){
                    $categoriaPadre = Blogcategoria::find($categoriaPadre->id_padre);
                }
                $rastroMigajas[$categoriaPadre->id_blog_categoria] = $categoriaPadre;
            }
            $rastroMigajasTexto = '';
            if(count($rastroMigajas) > 0){
                $n = 0;
                foreach($rastroMigajas AS $rastroMigaja){
                    if(count($blogCategoriasMenu) == 0){
                        $blogCategoriasMenu = SistemaFunciones::getBlogCategoriasMenu($rastroMigaja->id_blog_categoria);
                    }
                    // $rastroMigajasTexto .= '<a href="'.URL::to('/admingm/'.$rastroMigaja->categoria_alias).'" style="color: #fff !important;">'.$rastroMigaja->categoria.'</a>';
                    $rastroMigajasTexto .= '<a href="'.URL::to($rastroMigaja->categoria_alias).'" style="color: #fff !important;">'.$rastroMigaja->categoria.'</a>';
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
            $blogCategoriasMenu = SistemaFunciones::getBlogCategoriasMenuLista($blogCategoriasMenu, array('aseguradoras'), $contenido->categoria->categoria_alias);
            View::share("blogCategoriasMenu", $blogCategoriasMenu);
            $contenido->html = json_decode($contenido->html);
            View::share('contenido', $contenido);
            View::share('metaTitulo', $contenido->titulo);
            View::share('metaKeys', $contenido->metakey);
            View::share('metaDescripcion', str_limit(trim(strip_tags(html_entity_decode($contenido->metadesc))), 155, ''));
            
            $contenidosRelacionados = Blog::select('id_blog_categoria', 'titulo', 'alias', 'fecha_publicacion', 'imagen_small')
                ->where('estatus', '=', 1)->where('id_blog_categoria', '=', $contenido->id_blog_categoria)
                ->where('id_blog', '<>', $contenido->id_blog)
                ->orderBy('id_blog_categoria')->orderBy('orden')->orderBy('fecha_publicacion', 'desc')->limit(5)->get();
            View::share('contenidosRelacionados', $contenidosRelacionados);
            $contenidosPopulares = Blog::select('id_blog_categoria', 'titulo', 'alias', 'imagen_large', 'imagen_medium', 'imagen_small', 'fecha_publicacion')
                ->whereRaw("MATCH(`metakey`) AGAINST('".$contenido->metakey."' IN BOOLEAN MODE)")
                ->orderBy('id_blog_categoria')->orderBy('orden')->orderBy('fecha_publicacion', 'ASC')
                ->where('estatus', '=', 1)->where('id_blog', '<>', $contenido->id_blog)
                ->paginate(5)->getItems();
            View::share('contenidosPopulares', $contenidosPopulares);
            //Paginavisita::hit();
            $contenido::hitVisita($contenido->id_blog);
            
            // Para plantilla original
            if($contenido->tipo == 2){
                /*if($contenido->alias == 'main'){
                    View::share('contenido', $contenido->html);
                    return View::make('inicio');
                }
                if(!Input::get('porto'))
                	return View::make('blog.verContenidoHtml');
                else{
					if(Input::get('porto')!='1')
						return View::make('blog.verContenidoHtml');
					else
						return View::make('blog.verPortoHtml');
				}*/
				return View::make('blog.verPortoHtml');
            }
            //return View::make('blog.verContenido');
            
            //** Modificado por Marcelo Aguilera 10/03/2025
            //** Se reemplaza la plantilla blog.verPortoContenido por
            return View::make('blog.verPortoBlog');
        }
		else{
	        $q = null;
	        if(Input::has('buscar')){
	            $q = Input::get('buscar');
	        }else{
	            $q = htmlentities(urldecode(Request::segment(1)));
	        }
	        $contenido = Blog::where('alias', '=', $q)->where('estatus', '=', 1)->get();
	        
	        if(count($contenido) == 1){
	            $contenido = $contenido[0];
	            //$contenido->contenido = str_replace("http://nuevo.gastos-medicos.com.mx/admin_gm/application/", asset(''), $contenido->contenido);

	            $blogCategoriasMenu = SistemaFunciones::getBlogCategoriasMenu($contenido->id_blog_categoria);
	            
	            $categoriaPadre = Blogcategoria::find($contenido->id_blog_categoria);
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
	            $contenido->html = json_decode($contenido->html);
	            
	            View::share('contenido', $contenido);
	            View::share('metaTitulo', $contenido->titulo);
	            View::share('metaKeys', $contenido->metakey);
	            View::share('metaDescripcion', str_limit(trim(strip_tags(html_entity_decode($contenido->metadesc))), 155, ''));
	            
	            $contenidosRelacionados = Blog::select('id_blog_categoria', 'titulo', 'alias', 'fecha_publicacion', 'imagen_small')->where('estatus', '=', 1)->where('id_blog_categoria', '=', $contenido->id_blog_categoria)->orderBy('id_blog_categoria')->orderBy('orden')->orderBy('fecha_publicacion', 'desc')->limit(5)->get();
	            View::share('contenidosRelacionados', $contenidosRelacionados);
	            /*
	            $contenidosPopulares = DB::table('blog_visitas')
	                                         ->select('blog.id_blog_categoria', 'blog.titulo', 'blog.alias', 'blog.imagen_small', 'blog.fecha_publicacion', DB::raw('count(*) AS visitas'))
	                                         ->where('blog.estatus', '=', 1)
	                                         ->whereRaw("MATCH(`metakey`) AGAINST('".str_replace(",", " ", $contenido->metakey)."' IN BOOLEAN MODE)")
	                                         ->join('blog', 'blog_visitas.id_blog', '=', 'blog.id_blog')
	                                         ->groupBy('blog_visitas.id_blog')
	                                         ->orderBy('visitas', 'desc')
	                                         ->distinct('ip')
	                                         ->limit(5)
	                                         ->get();
	            */
	            $contenidosPopulares = Blog::select('id_blog_categoria', 'titulo', 'alias', 'imagen_large', 'imagen_medium', 'imagen_small', 'fecha_publicacion')->whereRaw("MATCH(`metakey`) AGAINST('".$contenido->metakey."' IN BOOLEAN MODE)")->orderBy('id_blog_categoria')->orderBy('orden')->orderBy('fecha_publicacion', 'desc')->where('estatus', '=', 1)->paginate(5)->getItems();
	            View::share('contenidosPopulares', $contenidosPopulares);
	            
	            //Paginavisita::hit();
	            $contenido::hitVisita($contenido->id_blog);
	            // Para plantilla original
	            if($contenido->tipo == 2){
	                /*if(!Input::get('porto'))
	                	return View::make('blog.verContenidoHtml');
	                else{
						if(Input::get('porto')!='1')
							return View::make('blog.verContenidoHtml');
						else
							return View::make('blog.verPortoHtml');
					}*/
					return View::make('blog.verPortoHtml');
	            }
	            //return View::make('blog.verContenido');
	            return View::make('blog.verPortoContenido');
	        }
	        View::share('categoria', $q);
	        $contenidos = null;
	        try{
	            $contenidos = Blog::whereRaw("MATCH(`titulo`, `contenido`, `metakey`) AGAINST('$q' IN BOOLEAN MODE)")->orderBy('id_blog_categoria')->orderBy('orden')->orderBy('fecha_publicacion', 'desc')->where('estatus', '=', 1)->paginate(10);
	        }catch(Exception $e){
	            return Redirect::to('/blog');
	        }
	        $paginacion = $contenidos->links();
	        View::share("paginacion", htmlentities($paginacion));
	        View::share('contenidosArray', $contenidos->getItems() );
	        
	        $rastroMigajasTexto = '';
	        $blogCategoriasMenu = '';
	        $categoriaPadre = Blogcategoria::find(1);
	        if($categoriaPadre){
		        $rastroMigajas = array();
		        $rastroMigajas[$categoriaPadre->id_blog_categoria] = $categoriaPadre;
		        while($categoriaPadre->id_padre > 0){
		            if($categoriaPadre->id_padre > 0){
		                $categoriaPadre = Blogcategoria::find($categoriaPadre->id_padre);
		            }
		            $rastroMigajas[$categoriaPadre->id_blog_categoria] = $categoriaPadre;
		        }
		        $blogCategoriasMenu = SistemaFunciones::getBlogCategoriasMenu(-1);
		        $rastroMigajas = array_reverse($rastroMigajas);
		        $rastroMigajasTexto = '';
			

		        if(count($rastroMigajas) > 0){
		            $n = 0;
		            foreach($rastroMigajas AS $rastroMigaja){
		                if(count($blogCategoriasMenu) == 0){
		                    $blogCategoriasMenu = SistemaFunciones::getBlogCategoriasMenu($rastroMigaja->id_blog_categoria);
		                }
		                $rastroMigajasTexto .= '<a href="'.URL::to('/'.$rastroMigaja->categoria_alias).'" style="color: #fff !important;">'.$rastroMigaja->categoria.'</a>';
		                if($n < (count($rastroMigajas) - 1)){
		                    $rastroMigajasTexto .= ' <i>/</i> ';
		                }
		                $n++;
		            }
		        }
			
				$blogCategoriasMenu = SistemaFunciones::getBlogCategoriasMenuLista($blogCategoriasMenu, array('aseguradoras'));
			}
	        View::share('rastroMigajasTexto', $rastroMigajasTexto);
	        View::share("blogCategoriasMenu", $blogCategoriasMenu);
			/*
	        $blobCategorias = Blogcategoria::where('estatus', '=', 1)->where('mostrar', '=', 1)->where('id_blog_categoria', '>', 0)->orderBy('categoria')->get();
	        View::share('blobCategorias', $blobCategorias);
	        */
	        //Paginavisita::hit();
	        
	        return View::make('blog.busquedaContenido');
		}
    }
    return Redirect::to('/');
});
Route::get('/gracias', 'CotizadorController@gracias');
Route::post('/cotizacionContratar', 'CotizadorController@cotizacionContratar');
Route::post('/enviarCotizacionEmail', 'CotizadorController@enviarCotizacionEmail');
//Route::get('/verCotizacionPdf/{idCotizacion}/{secret}/{sa}/{db}', 'CotizadorController@verCotizacionPdf');
//Route::get('/verCotizacionPdf/{idCotizacion}/{secret}', 'CotizadorController@verCotizacionPdf');
Route::get('/verCotizacion/{idCotizacion}/{secret}', 'CotizadorController@verCotizacion');
Route::post('/nuevaCotizacion', 'CotizadorController@nuevaCotizacion');
Route::post('/mini/nuevaCotizacion', 'CotizadorController@miniNuevaCotizacion');
Route::post('/mini/nuevaCotizacionOrigen', 'CotizadorController@miniNuevaCotizacionOrigen');
Route::get('/mini/verCotizacion/{idCotizacion}/{secret}', 'CotizadorController@miniVerCotizacion');
//Route::get('/cotizador', 'CotizadorController@cotizador');
Route::get('/contratarPaquete/{idCotizacion}/{secret}/{paquete}/{sa}/{db}', 'CotizadorController@contratarPaquete');

Route::get('/', 'MainController@bienvenido');
Route::get('/nosotros', 'MainController@nosotros');
Route::get('/paquetes', 'MainController@paquetes');
Route::get('/faq', 'MainController@faq');

Route::post('/enviarContacto', 'MainController@enviarContacto');
Route::get('/contacto', 'MainController@contacto');
Route::post('/postContacto', 'MainController@postContacto');

Route::get('/blog', 'BlogController@blog');

Route::get('/descargas/{alias}', 'DescargasController@download');
Route::get('/cotizacion/{uuid}', 'CotizadorController@cotizacionUUID');
Route::post('/cotizacion/cuestionario', 'CotizadorController@cuestionario');
Route::post('/nuevaCotizacionWS', 'CotizadorController@nuevaCotizacionWS');
Route::post('/cotizarWS/{idCotizacion}/{secret}', 'CotizadorController@cotizarWS');
Route::post('/recotizarWS/{idCotizacion}/{secret}', 'CotizadorController@recotizarWS');
Route::get('/cotizacion/{idCotizacion}/{secret}', 'CotizadorController@cotizacionWS');
Route::post('nuevaCotizacionWS2023', 'CotizadorController@nuevaCotizacionWS2023');
Route::post('testCotizacionWS2023', 'CotizadorController@testCotizacionWS2023');
Route::post('actualizaCotizacionWS2023/{idCotizacion}/{secret}', 'CotizadorController@actualizaCotizacionWS2023');
Route::get('/cotizacion2023/{idCotizacion}/{secret}', 'CotizadorController@cotizacionWS2023');
Route::get('/cotizacion2023/{idCotizacion}', 'CotizadorController@cotizacionWS2023');
Route::post('/cotizarWS2023/{idCotizacion}/{secret}', 'CotizadorController@cotizarWS2023');
Route::post('/recotizarWS2023/{idCotizacion}/{secret}', 'CotizadorController@recotizarWS2023');
Route::get('/poblaciones/{idEstado}', 'CotizadorController@poblacionesEstado');
Route::get('/cotizacion-nuevo/{idCotizacion}/{secret}', 'CotizadorController@cotizacionWS2023Local');
Route::get('/cotizacion-nuevo/{idCotizacion}', 'CotizadorController@cotizacionWS2023Local');
Route::get('/me-interesa/{idCotizacion}/{secret}/{paquete}', 'CotizadorController@meInteresa');
Route::post('/me-interesa/contactanos', 'CotizadorController@meInteresaContactanos');
//Route::get('/correo-plantilla/{idCotizacion}', 'CotizadorController@correoPlantilla');
Route::get('/cron/cotizaciones/mailer', 'CotizadorController@cronCotizacionesMailer');
Route::get('/ip-location', 'CotizadorController@ipLocation');
//Route::get('test', 'CotizadorController@testPDF');
Route::post('/whatsapp/received/{phone}', 'CotizadorController@whatsappReceived');
Route::get('/desbloquear-usuario/{secret}', 'Backend\LoginController@desbloquearUsuario');