
@section('contenido')
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-6">
                    <h5 class="lg-title mb10">{{$cotizacionDatos->nombre}}</h5>
                    <address>
                        <strong>{{$cotizacionDatos->e_mail}}</strong><br>
                        <strong>Teléfono:</strong> {{$cotizacionDatos->telefono}}<br>
                        {{$cotizacionDatos->estado}}, {{$cotizacionDatos->ciudad}}<br>
                        <strong>Cuenta con póliza:</strong> {{$cotizacionDatos->poliza_actual}}<br>
                        <strong>Integrantes:</strong> {{count($cotizacionDatos->integrantes)}}<br>
                    </address>
                </div>
                <div class="col-sm-6 text-right">
                    <h4 class="text-primary">Cotización: {{$cotizacionDatos->id_cotizacion}}</h4>
                    <h5 class="text-primary"><strong>Estatus:</strong> {{$cotizacionDatos->estatus()->first()->estatus}}</h5>
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
                    <p><strong>Comentarios:</strong> {{$cotizacionDatos->comentarios}}</p>
                @endif
                <br>
                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-info">
                    <li class="active"><a href="#cotizacion_dasa" data-toggle="tab"><strong>DA-SA</strong></a></li>
                    <li class=""><a href="#cotizacion_dbsa" data-toggle="tab"><strong>DB-SA</strong></a></li>
                    <li class=""><a href="#cotizacion_dasb" data-toggle="tab"><strong>DA-SB</strong></a></li>
                    <li class=""><a href="#cotizacion_dbsb" data-toggle="tab"><strong>DB-SB</strong></a></li>
                </ul>
                <div class="tab-content tab-content-info mb30">
                    <div class="tab-pane active" id="cotizacion_dasa">
                        {{$tablaDatosDASA}}
                        <hr>
                        <div class="text-right btn-invoice">
                            <button class="btn btn-success btn-lg enviarCotizacionEmail" data-sa='sa' data-ded='da' data-idCotizacion="{{$cotizacionDatos->id_cotizacion}}" data-secret="{{$cotizacionDatos->secret}}" data-email="{{$cotizacionDatos->e_mail}}" data-loading-text='Processando {{HTML::image('backend/images/loaders/loader31.gif')}}'><i class="fa fa-envelope-o mr5"></i> Enviar por e-mail [ SA - DA ]</button>
                            <a href="{{URL::to('verCotizacionPDF/'.$cotizacionDatos->id_cotizacion.'/'.$cotizacionDatos->secret.'/sa/da')}}" target="_blank"><button class="btn btn-info btn-lg"><i class="fa fa-file-pdf-o mr5"></i> Ver PDF [ SA - DA ]</button></a>
                        </div>
                    </div>
                    <div class="tab-pane" id="cotizacion_dbsa">
                        {{$tablaDatosDBSA}}
                        <hr>
                        <div class="text-right btn-invoice">
                            <button class="btn btn-success btn-lg enviarCotizacionEmail" data-sa='sb' data-ded='da' data-idCotizacion="{{$cotizacionDatos->id_cotizacion}}" data-secret="{{$cotizacionDatos->secret}}" data-email="{{$cotizacionDatos->e_mail}}"  data-loading-text='Processando {{HTML::image('backend/images/loaders/loader31.gif')}}'><i class="fa fa-envelope-o mr5"></i> Enviar por e-mail [ SB - DA ]</button>
                            <a href="{{URL::to('verCotizacionPDF/'.$cotizacionDatos->id_cotizacion.'/'.$cotizacionDatos->secret.'/sb/da')}}" target="_blank"><button class="btn btn-info btn-lg"><i class="fa fa-file-pdf-o mr5"></i> Ver PDF [ SB - DA ]</button></a>
                        </div>
                    </div>
                    <div class="tab-pane" id="cotizacion_dasb">
                        {{$tablaDatosDASB}}
                        <hr>
                        <div class="text-right btn-invoice">
                            <button class="btn btn-success btn-lg enviarCotizacionEmail" data-sa='sa' data-ded='db' data-idCotizacion="{{$cotizacionDatos->id_cotizacion}}" data-secret="{{$cotizacionDatos->secret}}" data-email="{{$cotizacionDatos->e_mail}}"  data-loading-text='Processando {{HTML::image('backend/images/loaders/loader31.gif')}}'><i class="fa fa-envelope-o mr5"></i> Enviar por e-mail [ SA - DB ]</button>
                            <a href="{{URL::to('verCotizacionPDF/'.$cotizacionDatos->id_cotizacion.'/'.$cotizacionDatos->secret.'/sa/db')}}" target="_blank"><button class="btn btn-info btn-lg"><i class="fa fa-file-pdf-o mr5"></i> Ver PDF [ SA - DB ]</button></a>
                        </div>
                    </div>
                    <div class="tab-pane" id="cotizacion_dbsb">
                        {{$tablaDatosDBSB}}
                        <hr>
                        <div class="text-right btn-invoice">
                            <button class="btn btn-success btn-lg enviarCotizacionEmail" data-sa='sb' data-ded='db' data-idCotizacion="{{$cotizacionDatos->id_cotizacion}}" data-secret="{{$cotizacionDatos->secret}}" data-email="{{$cotizacionDatos->e_mail}}"  data-loading-text='Processando {{HTML::image('backend/images/loaders/loader31.gif')}}'><i class="fa fa-envelope-o mr5"></i> Enviar por e-mail [ SB - DB ]</button>
                            <a href="{{URL::to('verCotizacionPDF/'.$cotizacionDatos->id_cotizacion.'/'.$cotizacionDatos->secret.'/sb/db')}}" target="_blank"><button class="btn btn-info btn-lg"><i class="fa fa-file-pdf-o mr5"></i> Ver PDF [ SB - DB ]</button></a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12"> 
                        <div class="panel panel-success-alt widget-todo">
                            <div class="panel-heading">
                                <h3 class="panel-title">Listado de Seguimientos</h3>
                            </div>
                            <ul class="list-group" style="min-height: 350px; height: 350px; overflow: auto;">
                                @if($cotizacionDatos->seguimientos()->get()->count() > 0)
                                    @foreach($cotizacionDatos->seguimientos()->orderBy('fecha_seguimiento','desc')->get() AS $seguimiento)
                                        <li class="list-group-item">
                                            <label for="seguimiento_{{$seguimiento->id_seguimiento}}" style="width: 95%;"><strong>Programado para: {{$seguimiento->fecha_seguimiento}}</strong> - {{$seguimiento->notas}}</label>
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
@stop
