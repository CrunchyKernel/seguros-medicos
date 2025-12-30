
@section('contenido')
	<div class="row">
        <div class="col-md-12">
        	<form id="cotizacionForm" name="cotizacionForm">
	            <div class="panel panel-default">
	                <div class="panel-heading">
	                    <h4 class="panel-title">Alta cotización</h4>
	                    <p>Complete el formulario.</p>
	                </div>
	                <div class="panel-body">
	                    <div class="row">
	                        <div class="col-sm-6">
	                            <div class="form-group">
	                                <label class="control-label">Nombre completo</label>
	                                <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Nombre completo" value="{{((isset($cotizacionDatos)) ? $cotizacionDatos->nombre : '')}}">
	                            </div>
	                        </div>
	                        <div class="col-sm-6">
	                            <div class="form-group">
	                                <label class="control-label">Correo electrónico</label>
	                                <input type="email" id="e_mail" name="e_mail" class="form-control" placeholder="Correo electrónico" value="{{((isset($cotizacionDatos)) ? $cotizacionDatos->e_mail : '')}}">
	                            </div>
	                        </div>
	                    </div>
	                    <div class="row">
	                        <div class="col-sm-4">
	                            <div class="form-group">
	                                <label class="control-label">Teléfono</label>
	                                <input type="text" id="telefono" name="telefono" class="form-control" placeholder="Teléfono" value="{{((isset($cotizacionDatos)) ? $cotizacionDatos->telefono : '')}}">
	                            </div>
	                        </div>
	                        <div class="col-sm-4">
	                            <div class="form-group">
	                                <label class="control-label">Estado</label>
	                                <input type="text" id="estado" name="estado" class="form-control" placeholder="Estado" value="Jalisco" readonly="" value="{{((isset($cotizacionDatos)) ? $cotizacionDatos->estado : '')}}">
	                            </div>
	                        </div>
	                        <div class="col-sm-4">
	                            <div class="form-group">
	                                <label class="control-label">Ciudad</label>
	                                <input type="text" id="ciudad" name="ciudad" class="form-control" placeholder="Ciudad" value="{{((isset($cotizacionDatos)) ? $cotizacionDatos->ciudad : '')}}">
	                            </div>
	                        </div>
	                    </div>
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
	                    					{{$tablaIntegrantes}}
	                                    </tbody>
	                                </table>
								</div>
							</div>
	                    </div>
	                </div>
	                <div class="panel-footer">
	                    <button type="button" class="btn btn-primary agregarCotizacion" data-loading-text='Processando {{HTML::image('backend/images/loaders/loader31.gif')}}'>Guardar cotización</button>
	                </div>
	            </div>
	            <input type="hidden" id="idCotizacion" name="idCotizacion" value="{{((isset($cotizacionDatos)) ? $cotizacionDatos->id_cotizacion : '-1')}}">
            </form>
        </div>
    </div>
@stop