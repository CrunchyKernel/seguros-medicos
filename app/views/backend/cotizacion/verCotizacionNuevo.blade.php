@section('contenido')
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-6">
                    <h5 class="lg-title mb10"><a href="#" class="campo" data-campo="nombre" data-value="{{$cotizacionDatos->nombre}}" data-pk="{{$cotizacionDatos->id_cotizacion}}" data-title="Nombre"></a></h5>
                    <address>
                        <strong><a href="#" class="campo" data-campo="e_mail" data-value="{{$cotizacionDatos->e_mail}}" data-pk="{{$cotizacionDatos->id_cotizacion}}" data-title="Correo electrónico"></a></strong><br>
                        <strong>Teléfono:</strong> <a href="#" class="campo" data-campo="telefono" data-value="{{$cotizacionDatos->telefono}}" data-pk="{{$cotizacionDatos->id_cotizacion}}" data-title="Teléfono"></a><br>
                        <a href="#" class="campo" data-campo="estado" data-value="{{$cotizacionDatos->estado}}" data-pk="{{$cotizacionDatos->id_cotizacion}}" data-title="Estado"></a>, <a href="#" class="campo" data-campo="ciudad" data-value="{{$cotizacionDatos->ciudad}}" data-pk="{{$cotizacionDatos->id_cotizacion}}" data-title="Ciudad"></a><br>
                        <strong>Cuenta con póliza:</strong> {{$cotizacionDatos->poliza_actual}}<br>
                        <strong>Integrantes:</strong> {{count($cotizacionDatos->integrantes)}}<br>
                        @if($asignarAgente[0]->acceso==1)
                        	<strong>Agente:</strong> <a href="#" class="campo" data-campo="id_agente" data-value="{{$cotizacionDatos->id_agente}}" data-type="select" data-source="/admingm/listasJson/agentesJson" data-original-title="Agente" data-pk="{{$cotizacionDatos->id_cotizacion}}">{{$cotizacionDatos->agente()->first()->nombre}} {{$cotizacionDatos->agente()->first()->apellido_paterno}}</a><br>
                        @else
                        	<strong>Agente:</strong> {{$cotizacionDatos->agente()->first()->nombre}} {{$cotizacionDatos->agente()->first()->apellido_paterno}}<br>
                        @endif
                      	<strong>Whatsapp:</strong> <a href="https://wa.me/521{{$cotizacionDatos->telefono}}?text=Hola {{$cotizacionDatos->nombre}}" target="_blank"><i class="fa fa-whatsapp"></i></a><br>
                      	@if(!is_null($cotizacionDatos->mapfre_numero))
                      		<strong>Mapfre:</strong> <a href="https://zonaliados.mapfre.com.mx/Zonaliados.Multiplataforma/AYESalud?Cotizacion={{$cotizacionDatos->mapfre_numero}}" target="_blank">{{$cotizacionDatos->mapfre_numero}}</a>
                      		<br>
	                    @endif
	                    <strong>Editar cotización:</strong> <a href="{{$cotizacionDatos->dominio()->first()->dominio . $cotizacionDatos->dominio()->first()->ver_cotizacion_nuevo}}/{{$cotizacionDatos->id_cotizacion}}/{{$cotizacionDatos->secret}}" target="_blank">Aquí</a>
	                    @if($testing==1)
	                    <button id="cmdWhatsapp" data-idCotizacion="{{$cotizacionDatos->id_cotizacion}}">Agregar a lista de distribucion</button>
	                    @endif
                    </address>
                </div>
                <div class="col-sm-6 text-right">
                    <h4 class="text-primary">Cotización: {{$cotizacionDatos->id_cotizacion}}</h4>
                    <h5 class="text-primary"><strong>Estatus:</strong> {{$cotizacionDatos->estatus()->first()->estatus}}</h5>
                    @if($cotizacionDatos->estatus==1)
                    	<h5 class="text-primary"><strong>Paquete:</strong> {{$cotizacionDatos->me_interesa}}</h5>
                    @endif
                    <h5 class="text-primary"><strong>Dominio:</strong> {{$cotizacionDatos->dominio()->first()->nombre}}</h5>
                    <h5 class="text-primary"><strong>Ruta:</strong> {{$cotizacionDatos->ruta}}</h5>
                    <p><strong>Fecha registro:</strong> {{SistemaFunciones::fechaLetras(date('Y-m-d', strtotime($cotizacionDatos->fecha_registro)))}} - {{date('H:i', strtotime($cotizacionDatos->fecha_registro))}} hrs.</p>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="ckbox ckbox-warning">
                    <input type="checkbox" id="prioridadCotizacion" data-idCotizacion="{{$cotizacionDatos->id_cotizacion}}" {{(($cotizacionDatos->prioridad()->where('cotizaciones_prioridad.id_agente', '=', \Auth::user()->id_usuario)->first()) ? 'checked=""' : '')}}>
                    <label for="prioridadCotizacion">Prioridad</label>
                </div>
            </div>
            <div class="table-responsive">
                @if(isset($paqueteDatos) && $paqueteDatos->id_paquete > 0)
                    <div class="alert alert-success">
                        <strong>Paquete seleccionado:</strong> {{$paqueteDatos->paquete}} <strong>Suma asegurada:</strong> {{strtoupper($cotizacionDatos->sa)}} <strong>Deducible:</strong> {{strtoupper($cotizacionDatos->ded)}}.
                    </div>
                @endif
                {{$tablaIntegrantes}}
                @if(strlen($cotizacionDatos->comentarios) > 0)
                    <br>
                    <div class="alert alert-info fade in nomargin">
                        <h5 style="margin: 0;">Comentarios</h5>
                        <p><?php echo str_replace("\n", "<br>", $cotizacionDatos->comentarios);?></p>
                    </div>
                @endif
                <br>
                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-info">
                    <li class="active" id="tab-cotizacion"><a href="#cotizacion_dasa" data-toggle="tab"><strong>Cotización</strong></a></li>
                    <!--li class=""><a href="#cotizacion_dbsa" data-toggle="tab"><strong>SB-DA</strong></a></li-->
                    <!--li class=""><a href="#cotizacion_dasb" data-toggle="tab"><strong>SA-DB</strong></a></li-->
                    <!--li class=""><a href="#cotizacion_dbsb" data-toggle="tab"><strong>SB-DB</strong></a></li-->
                    <li class="" id="tab-editar"><a href="#cotizacion_editar" data-toggle="tab"><strong>Editar</strong></a></li>
                </ul>
                <div class="tab-content tab-content-info mb30">
                    <div class="tab-pane active" id="cotizacion_dasa">
                        {{$tablaDatosDBSA}}
                        <hr>
                        <div class="text-right btn-invoice">
                            <button class="btn btn-success btn-lg enviarCotizacionEmail" data-sa='sa' data-ded='da' data-idCotizacion="{{$cotizacionDatos->id_cotizacion}}" data-secret="{{$cotizacionDatos->secret}}" data-email="{{$cotizacionDatos->e_mail}}" data-loading-text='Processando {{HTML::image('backend/images/loaders/loader31.gif')}}'><i class="fa fa-envelope-o mr5"></i> Enviar por e-mail [ SA - DA ]</button>
                            <button class="btn btn-info btn-lg verCotizacionPDF" data-sa='sa' data-ded='da' data-idCotizacion="{{$cotizacionDatos->id_cotizacion}}" data-secret="{{$cotizacionDatos->secret}}" data-loading-text='Processando {{HTML::image('backend/images/loaders/loader31.gif')}}'><i class="fa fa-file-pdf-o mr5"></i> Ver PDF [ SA - DA ]</button>
                            <button class="btn btn-danger btn-lg recotizarMapfre" id="btnMapfreSADA" data-sa='sa' data-ded='da' data-idCotizacion="{{$cotizacionDatos->id_cotizacion}}" data-loading-text='Procesando {{HTML::image('backend/images/loaders/loader31.gif')}}' data-text='<i class="fa fa-refresh mr5"></i> Recotizar Mapfre [ SA - DA ]'><i class="fa fa-refresh mr5"></i> Recotizar Mapfre [ SA - DA ]</button>
                        </div>
                    </div>
                    <!--div class="tab-pane" id="cotizacion_dbsa">
                        $tablaDatosDBSA
                        <hr>
                        <div class="text-right btn-invoice">
                            <button class="btn btn-success btn-lg enviarCotizacionEmail" data-sa='sb' data-ded='da' data-idCotizacion="{{$cotizacionDatos->id_cotizacion}}" data-secret="{{$cotizacionDatos->secret}}" data-email="{{$cotizacionDatos->e_mail}}"  data-loading-text='Processando {{HTML::image('backend/images/loaders/loader31.gif')}}'><i class="fa fa-envelope-o mr5"></i> Enviar por e-mail [ SB - DA ]</button>
                            <a href="{{URL::to('verCotizacionPDF/'.$cotizacionDatos->id_cotizacion.'/'.$cotizacionDatos->secret.'/sb/da')}}" target="_blank"><button class="btn btn-info btn-lg"><i class="fa fa-file-pdf-o mr5"></i> Ver PDF [ SB - DA ]</button></a>
                            <button class="btn btn-danger btn-lg recotizarMapfre" data-sa='sb' data-ded='da' data-idCotizacion="{{$cotizacionDatos->id_cotizacion}}" data-loading-text='Procesando {{HTML::image('backend/images/loaders/loader31.gif')}}' data-text='<i class="fa fa-refresh mr5"></i> Recotizar Mapfre [ SB - DA ]'><i class="fa fa-refresh mr5"></i> Recotizar Mapfre [ SB - DA ]</button>
                        </div>
                    </div-->
                    <!--div class="tab-pane" id="cotizacion_dasb">
                        $tablaDatosDASB
                        <hr>
                        <div class="text-right btn-invoice">
                            <button class="btn btn-success btn-lg enviarCotizacionEmail" data-sa='sa' data-ded='db' data-idCotizacion="{{$cotizacionDatos->id_cotizacion}}" data-secret="{{$cotizacionDatos->secret}}" data-email="{{$cotizacionDatos->e_mail}}"  data-loading-text='Processando {{HTML::image('backend/images/loaders/loader31.gif')}}'><i class="fa fa-envelope-o mr5"></i> Enviar por e-mail [ SA - DB ]</button>
                            <a href="{{URL::to('verCotizacionPDF/'.$cotizacionDatos->id_cotizacion.'/'.$cotizacionDatos->secret.'/sa/db')}}" target="_blank"><button class="btn btn-info btn-lg"><i class="fa fa-file-pdf-o mr5"></i> Ver PDF [ SA - DB ]</button></a>
                            <button class="btn btn-danger btn-lg recotizarMapfre" data-sa='sa' data-ded='db' data-idCotizacion="{{$cotizacionDatos->id_cotizacion}}" data-loading-text='Procesando {{HTML::image('backend/images/loaders/loader31.gif')}}' data-text='<i class="fa fa-refresh mr5"></i> Recotizar Mapfre [ SA - DB ]'><i class="fa fa-refresh mr5"></i> Recotizar Mapfre [ SA - DB ]</button>
                        </div>
                    </div-->
                    <!--div class="tab-pane" id="cotizacion_dbsb">
                        $tablaDatosDBSB
                        <hr>
                        <div class="text-right btn-invoice">
                            <button class="btn btn-success btn-lg enviarCotizacionEmail" data-sa='sb' data-ded='db' data-idCotizacion="{{$cotizacionDatos->id_cotizacion}}" data-secret="{{$cotizacionDatos->secret}}" data-email="{{$cotizacionDatos->e_mail}}"  data-loading-text='Processando {{HTML::image('backend/images/loaders/loader31.gif')}}'><i class="fa fa-envelope-o mr5"></i> Enviar por e-mail [ SB - DB ]</button>
                            <a href="{{URL::to('verCotizacionPDF/'.$cotizacionDatos->id_cotizacion.'/'.$cotizacionDatos->secret.'/sb/db')}}" target="_blank"><button class="btn btn-info btn-lg"><i class="fa fa-file-pdf-o mr5"></i> Ver PDF [ SB - DB ]</button></a>
                            <button class="btn btn-danger btn-lg recotizarMapfre" data-sa='sb' data-ded='db' data-idCotizacion="{{$cotizacionDatos->id_cotizacion}}" data-loading-text='Procesando {{HTML::image('backend/images/loaders/loader31.gif')}}' data-text='<i class="fa fa-refresh mr5"></i> Recotizar Mapfre [ SB - DB ]'><i class="fa fa-refresh mr5"></i> Recotizar Mapfre [ SB - DB ]</button>
                        </div>
                    </div-->
                    <div class="tab-pane" id="cotizacion_editar">
                        <form id="actualizarIntegrantesForm" name="actualizarIntegrantesForm">
                            <div class="row">
                                <div class="table-responsive">
                                    <div class="table-responsive">
                                        <table class="table table-info mb30">
                                            <thead>
                                                <tr>
                                                    <th class="alignCenter" style="width: 50px;"></th>
                                                    <th class="alignCenter" style="width: 50px;">Título</th>
                                                    <th class="alignCenter" style="width: 50px;">Incluir</th>
                                                    <th class="alignCenter">Nombre del integrante</th>
                                                    <th class="alignCenter" style="width: 130px;">Sexo</th>
                                                    <th class="alignCenter" style="width: 50px;">Edad</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {{$tablaIntegrantesEditar}}
                                            </tbody>
                                        </table>
                                    </div>
                                    <button type="button" class="btn btn-primary actualizarIntegrantes" data-loading-text='Processando {{HTML::image('backend/images/loaders/loader31.gif')}}'>Actualizar integrantes</button>
                                </div>
                            </div>
                            <input type="hidden" id="idCotizacion" name="idCotizacion" value="{{((isset($cotizacionDatos)) ? $cotizacionDatos->id_cotizacion : '-1')}}">
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6"> 
                        <form id="seguimientoForm">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">Seguimiento</h4>
                                    <p>Formulario de seguimiento</p>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label class="control-label">Notas</label>
                                                <textarea id="notas" name="notas" class="form-control" rows="5" style="height: 90px;"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="control-label">Fecha</label>
                                                <div class="input-group">
                                                    <input type="text" name="fecha" class="form-control" placeholder="yyyy/mm/dd" id="fechaProgramada" readonly="" style="cursor: pointer;" value="{{date('Y-m-d')}}">
                                                    <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="control-label">Hora</label>
                                                <div class="input-group">
                                                    <div class="bootstrap-timepicker"><input id="horaProgramada" name="hora" type="text" class="form-control" readonly="" style="cursor: pointer;"/></div>
                                                    <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    @if($cotizacionDatos->estatus < 9)
                                        <button class="btn btn-primary agregarSeguimiento" data-cotizacionEstatus="6" data-idCotizacion="{{$cotizacionDatos->id_cotizacion}}" type="button">Programar</button>
                                        @if($cotizacionDatos->estatus != 7)
                                            <button class="btn btn-success agregarSeguimiento" data-cotizacionEstatus="7" data-idCotizacion="{{$cotizacionDatos->id_cotizacion}}" type="button">Cerrar</button>
                                        @endif
                                        @if($cotizacionDatos->estatus != 8)
                                            <button class="btn btn-default agregarSeguimiento" data-cotizacionEstatus="10" data-idCotizacion="{{$cotizacionDatos->id_cotizacion}}" type="button">Teléfono falso</button>
                                        @endif
                                        @if($cotizacionDatos->estatus == 1 || $cotizacionDatos->estatus == 2 || $cotizacionDatos->estatus == 3)
                                            <button class="btn btn-info agregarSeguimiento" data-cotizacionEstatus="4" data-idCotizacion="{{$cotizacionDatos->id_cotizacion}}" type="button">No localizado</button>
                                        @endif
                                        @if($cotizacionDatos->estatus < 7)
                                            <button class="btn btn-default agregarSeguimiento" data-cotizacionEstatus="8" data-idCotizacion="{{$cotizacionDatos->id_cotizacion}}" type="button">Comprado</button>
                                        @endif
                                        @if($cotizacionDatos->estatus == 4)
                                            <button class="btn btn-warning agregarSeguimiento" data-cotizacionEstatus="5" data-idCotizacion="{{$cotizacionDatos->id_cotizacion}}" type="button">2do. Intento</button>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6"> 
                        <div class="panel panel-success-alt widget-todo">
                            <div class="panel-heading">
                                <h3 class="panel-title">Listado de Seguimientos</h3>
                            </div>
                            <ul class="list-group" style="min-height: 350px; height: 350px; overflow: auto;">
                                @if($cotizacionDatos->seguimientos()->get()->count() > 0)
                                    @foreach($cotizacionDatos->seguimientos()->orderBy('fecha_seguimiento','desc')->get() AS $seguimiento)
                                        <li class="list-group-item">
                                            <div class="ckbox ckbox-default">
                                                <input id="seguimiento_{{$seguimiento->id_seguimiento}}" type="checkbox" class="actualziarSeguimientoEstatus" {{(($seguimiento->realizado == 1) ? 'checked="checked"' : '')}} value="{{$seguimiento->id_seguimiento}}">
                                                <label for="seguimiento_{{$seguimiento->id_seguimiento}}" style="width: 95%;"><strong>Programado para: {{$seguimiento->fecha_seguimiento}}</strong> - {{$seguimiento->notas}}</label>
                                            </div>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <button class="btn btn-primary btn-block siguienteCotizacion" data-loading-text='Processando {{HTML::image('backend/images/loaders/loader31.gif')}}'><i class="fa fa-magic"></i> Siguiente cotización</button>
        </div>
    </div>

    <div id="enviarCotizacionEmailDiv" class="quick-compose-form">
        <form id="cotizacionEmailForm">
            <!--<input type="text" class="form-control tagsinput" id="para" name="para" placeholder="Para">-->
            <input type="hidden" id="idCotizacionEmail" name="idCotizacionEmail">
            <input type="hidden" id="sa" name="sa">
            <input type="hidden" id="ded" name="ded">
            <input type="text" id="para" name="para" class="form-control tm-input" placeholder="Escriba un e-mail y presione enter">
            <div class="tag-container tags"></div>
            <div class="row">
            	@foreach($paquetes AS $paquete)
            		<input type="checkbox" data-idpaquete="{{$paquete['id_paquete']}}" checked> {{$paquete['aseguradora']}} - {{$paquete['paquete']}} &nbsp;&nbsp;
            	@endforeach
            </div>
            @if(isset($textosRespuestaCorreo) && $textosRespuestaCorreo->count() > 0)
                <select id="textoRespuestaCorreo" class="form-control">
                    <option value="">Seleccione respuesta...</option>
                    @foreach($textosRespuestaCorreo AS $texto)
                        <option value="{{$texto->contenido}}">{{$texto->titulo}}</option>
                    @endforeach
                </select>
                <br>
            @endif
            <div id="summernote-quick" class="summernote-quick" contenteditable="true"></div>
            <!--<textarea class="summernote-quick" id="mensaje" name="mensaje" rows="18"></textarea>-->
        </form>
    </div>
    
    <div id="verPDFDiv" class="quick-compose-form">
    	<form id="cotizacionPDFForm">
    		<input type="hidden" id="pdfidCotizacion">
    		<input type="hidden" id="pdfsecret">
    		<input type="hidden" id="pdfsa">
    		<input type="hidden" id="pdfded">
    		<div class="row">
            	@foreach($paquetes AS $paquete)
            		<input type="checkbox" data-idpaquete="{{$paquete['id_paquete']}}" checked> {{$paquete['aseguradora']}} - {{$paquete['paquete']}} &nbsp;&nbsp;
            	@endforeach
            </div>
    	</form>
    </div>
@stop
