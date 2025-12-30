
@section('contenido')
    <div class="row">
        <div class="col-md-12">
            <form id="correoRespuestaForm" name="correoRespuestaForm">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">Nueva respuesta para correo</h4>
                    </div>
                    <div class="panel-body">
                    	<div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="control-label">Dominio</label>
                                    <select id="id_dominio" name="id_dominio" data-placeholder="Selecciona el dominio" style="width: 100%;">
					                    <option value="">Seleccionar...</option>
					                    @if(isset($dominios) && $dominios->count() > 0)
					                        @foreach($dominios AS $dominio)
					                            <option value="{{$dominio->id_dominio}}">{{$dominio->nombre}}</option>
					                        @endforeach
					                    @endif
					                </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="control-label">Título</label>
                                    <input type="text" id="titulo" name="titulo" class="form-control" placeholder="Nombre completo">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="control-label">Respuesta</label>
                                    <textarea class="form-control" rows="5" name="contenido"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <button type="button" class="btn btn-primary agregarCorreoRespuesta" data-loading-text='Processando {{HTML::image('backend/images/loaders/loader31.gif')}}'>Guardar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">Respuestas</h4>
                    <p>Listado de respuestas de correo</p>
                    <div class="pull-right">
                        <a class="tooltips actualizarTabla" data-toggle="tooltip" href="#" data-original-title="Actualizar tabla"><i class="fa fa-refresh"></i></a>
                    </div>
                </div><!-- panel-heading -->
                <div class="panel-body" style="padding: 0px;">
                    <table id="listadoCorreoRespuestas" class="table table-striped table-bordered table-hover responsive">
                        <thead class="">
                            <tr>
                                <th class="alignCenter">#</th>
                                <th class="alignCenter">Dominio</th>
                                <th class="alignCenter">Título</th>
                                <th class="alignCenter">Texto</th>
                                <th class="alignCenter"></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot class="">
                            <tr>
                                <th class="alignCenter">#</th>
                                <th class="alignCenter">Dominio</th>
                                <th class="alignCenter">Título</th>
                                <th class="alignCenter">Texto</th>
                                <th class="alignCenter"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div><!-- panel-body -->
            </div><!-- panel -->
        </div>
    </div>
    
@stop
