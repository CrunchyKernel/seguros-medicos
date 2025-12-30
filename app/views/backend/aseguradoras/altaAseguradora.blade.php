
@section('contenido')
	<div class="row">
		<div class="col-md-12">
        	<form id="aseguradoraForm" name="aseguradoraForm" method="POST" enctype="multipart/form-data">
				<div class="panel panel-default">
	                <div class="panel-heading">
	                    <h4 class="panel-title">Nueva aseguradora</h4>
	                    <p>Complete el formulario.</p>
	                </div>
	                <div class="panel-body">
	                    <div class="row">
	                        <div class="col-sm-6">
	                            <div class="form-group">
	                                <label class="control-label">Nombre</label>
	                                <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Nombre" value="{{((isset($aseguradoraDatos)) ? htmlentities($aseguradoraDatos->nombre) : '')}}">
	                            </div><!-- form-group -->
	                        </div><!-- col-sm-6 -->
	                        <div class="col-sm-6">
	                            <div class="form-group">
	                                <label class="control-label">Clave</label>
	                                <input type="text" id="aseguradora" name="aseguradora" class="form-control" placeholder="Clave (sin espacios)" value="{{((isset($aseguradoraDatos)) ? htmlentities($aseguradoraDatos->aseguradora) : '')}}">
	                            </div><!-- form-group -->
	                        </div><!-- col-sm-6 -->
	                    </div><!-- row -->
	                    <div class="row">
	                    	<div class="col-sm-3">
	                            <div class="form-group">
	                                <label class="control-label">Logo</label>
	                                <input type="text" id="logo" name="logo" class="form-control" value="{{((isset($aseguradoraDatos)) ? htmlentities($aseguradoraDatos->logo) : '')}}">
	                            </div><!-- form-group -->
	                        </div><!-- col-sm-6 -->
	                        <div class="col-sm-3">
	                            <div class="form-group">
	                                <label class="control-label">Interes semestral</label>
	                                <input type="number" id="interes_semestral" name="interes_semestral" class="form-control" min="0" step="0.01" value="{{((isset($aseguradoraDatos)) ? htmlentities($aseguradoraDatos->interes_semestral) : '')}}">
	                            </div><!-- form-group -->
	                        </div><!-- col-sm-6 -->
	                        <div class="col-sm-3">
	                            <div class="form-group">
	                                <label class="control-label">Interes trimestral</label>
	                                <input type="number" id="interes_trimestral" name="interes_trimestral" class="form-control" min="0" step="0.01" value="{{((isset($aseguradoraDatos)) ? htmlentities($aseguradoraDatos->interes_trimestral) : '')}}">
	                            </div><!-- form-group -->
	                        </div><!-- col-sm-6 -->
	                        <div class="col-sm-3">
	                            <div class="form-group">
	                                <label class="control-label">Interes mensual</label>
	                                <input type="number" id="interes_mensual" name="interes_mensual" class="form-control" min="0" step="0.01" value="{{((isset($aseguradoraDatos)) ? htmlentities($aseguradoraDatos->interes_mensual) : '')}}">
	                            </div><!-- form-group -->
	                        </div><!-- col-sm-6 -->
	                        <div class="col-sm-3">
	                            <div class="form-group">
	                                <label class="control-label">Inflar precio (porcentaje)</label>
	                                <input type="number" id="inflar" name="inflar" class="form-control" min="0" step="0.1" value="{{((isset($aseguradoraDatos)) ? htmlentities($aseguradoraDatos->inflar) : '0')}}">
	                            </div><!-- form-group -->
	                        </div><!-- col-sm-6 -->
	                    </div><!-- row -->
	                    <div class="row">
	                    	<div class="col-sm-4">
	                            <div class="form-group">
	                                <label class="control-label">Orden</label>
	                                <input type="number" id="orden" name="orden" class="form-control" min="1" value="{{((isset($aseguradoraDatos)) ? htmlentities($aseguradoraDatos->orden) : '')}}">
	                            </div><!-- form-group -->
	                        </div><!-- col-sm-6 -->
	                        <div class="col-sm-4">
	                            <div class="form-group">
	                                <label class="control-label">&nbsp;</label>
	                                <div class="ckbox ckbox-success">
	                                    <input type="checkbox" id="activa" name="activa" {{((isset($aseguradoraDatos)) ? (($aseguradoraDatos->activa == 1) ? 'checked' : '') : '')}}/>
	                                    <label for="activo">Aseguradora activa</label>
	                                </div>
	                            </div><!-- form-group -->
	                        </div><!-- col-sm-6 -->
	                    </div>
	                    <div class="row">
							<div class="col-sm-6">
								<label class="control-label">Imagen (cotizador)</label>
								<div class="custom_file_upload">
									<div class="file_upload">
										<input type="file" id="imagen_cotizador" accept="image/jpeg, image/jpg">
									</div>
								</div>
								<label class="control-label">Vista previa</label><br>
								<img id="preview_cotizador" src="{{((isset($aseguradoraDatos) && strlen($aseguradoraDatos->imagen_cotizador) > 0) ? $aseguradoraDatos->imagen_cotizador : asset('backend/images/preview.png') )}}" style="max-width: 100%; height: auto;" />
							</div>
	                    </div>
	                    <div class="row">
							<div class="col-sm-6">
								<label class="control-label">Imagen (pdf)</label>
								<div class="custom_file_upload">
									<div class="file_upload">
										<input type="file" id="imagen_pdf" accept="image/jpeg, image/jpg">
									</div>
								</div>
								<label class="control-label">Vista previa</label><br>
								<img id="preview_pdf" src="{{((isset($aseguradoraDatos) && strlen($aseguradoraDatos->imagen_pdf) > 0) ? $aseguradoraDatos->imagen_pdf : asset('backend/images/preview.png') )}}" style="max-width: 100%; height: auto;" />
							</div>
	                    </div>
	                </div><!-- panel-body -->
	                <div class="panel-footer">
	                    <button type="button" class="btn btn-primary registrarAseguradora" data-loading-text='Processando {{HTML::image('backend/images/loaders/loader31.gif')}}'>Registrar</button>
	                </div><!-- panel-footer -->
	            </div>
	            <input type="hidden" id="id_aseguradora" name="id_aseguradora" value="{{((isset($aseguradoraDatos)) ? $aseguradoraDatos->id_aseguradora : '-1')}}">
	        </form>
		</div>
    </div>
    @if(isset($aseguradoraDatos))
	    <div class="panel panel-primary">
	    	<div class="panel-heading">
	    		<h4 class="panel-title">
	    			<a data-toggle="collapse" href="#tabPlanes" class="collapsed">Planes</a>
	    		</h4>
	    	</div>
	    	<div class="panel-collapse collapse" role="tabpanel" id="tabPlanes">
		    	<div class="panel-body">
		    		<form id="planForm">
			    		<div class="row">
			    			<div class="col-sm-6">
			    				<div class="form-group">
	                                <label class="control-label">Plan</label>
	                                <input type="text" id="paquete" name="paquete" class="form-control" placeholder="Plan">
	                            </div>
			    			</div>
			    			<div class="col-sm-6">
			    				<div class="form-group">
	                                <label class="control-label">Clave</label>
	                                <input type="text" id="paquete_campo" name="paquete_campo" class="form-control" placeholder="Clave">
	                            </div>
			    			</div>
			    		</div>
			    		<div class="row">
			    			<div class="col-sm-4">
			    				<div class="form-group">
	                                <label class="control-label">Derecho poliza</label>
	                                <input type="number" id="derecho_poliza" name="derecho_poliza" class="form-control" min="0" step="0.01">
	                            </div>
			    			</div>
			    			<div class="col-sm-4">
			    				<div class="form-group">
	                                <label class="control-label">Orden</label>
	                                <input type="number" id="orden2" name="orden" class="form-control" min="1" step="1">
	                            </div>
			    			</div>
			    			<div class="col-sm-4">
			    				<div class="form-group">
	                                <label class="control-label">&nbsp;</label>
	                                <div class="ckbox ckbox-success">
	                                    <input type="checkbox" id="activo" name="activo"/>
	                                    <label for="activo">Plan activo</label>
	                                </div>
	                            </div>
	                        </div>
			    		</div>
			    		<div class="row">
			    			<div class="col-sm-12">
			    				<button type="button" class="btn btn-primary registrarPlan" data-loading-text='Procesando {{HTML::image('backend/images/loaders/loader31.gif')}}'>Registrar</button>
			    			</div>
			    		</div>
			    		<input type="hidden" id="id_aseguradora2" name="id_aseguradora" value="{{((isset($aseguradoraDatos)) ? $aseguradoraDatos->id_aseguradora : '-1')}}">
		    		</form>
		    		<div class="row">
		    			<div class="col-sm-12">
		    				<table id="planesTable" class="table table-striped table-bordered table-hover responsive">
		                        <thead>
		                            <tr>
		                                <th class="alignCenter">#</th>
		                                <th class="alignCenter">Plan</th>
		                                <th class="alignCenter">Clave</th>
		                                <th class="alignCenter">Derecho poliza</th>
		                                <th class="alignCenter">Orden</th>
		                                <th class="alignCenter">Activo</th>
		                            </tr>
		                        </thead>
		                        <tbody></tbody>
		                        <tfoot>
		                            <tr>
		                                 <th class="alignCenter">#</th>
		                                <th class="alignCenter">Plan</th>
		                                <th class="alignCenter">Clave</th>
		                                <th class="alignCenter">Derecho poliza</th>
		                                <th class="alignCenter">Orden</th>
		                                <th class="alignCenter">Activo</th>
		                            </tr>
		                        </tfoot>
		                    </table>
		    			</div>
		    		</div>
		    	</div>
	    	</div>
	    </div>
	    <input type="hidden" name="idAseguradora" id="idAseguradora" value="{{((isset($aseguradoraDatos)) ? $aseguradoraDatos->id_aseguradora : '-1')}}">
	    <div class="panel panel-success-alt">
	        <div class="panel-heading">
	            <h4 class="panel-title">
	                <a data-toggle="collapse" href="#tabWeb" class="collapsed">Descripcion Web</a>
	            </h4>
	        </div>
	        <div id="tabWeb" class="panel-collapse collapse">
	            <div class="panel-body">
	                <form>
	                    <textarea id="textoWeb" name="textoWeb" rows="20" cols="10">
	                        {{((isset($aseguradoraDatos)) ? $aseguradoraDatos->descripcion_web : '')}}
	                    </textarea>
	                    <br>
	                </form>
	                <button class="btn btn-success" id="guardaWeb" >Guardar</button>
	            </div>
	        </div>
	    </div>
	    <div class="panel panel-success-alt">
	        <div class="panel-heading">
	            <h4 class="panel-title">
	                <a data-toggle="collapse" href="#tabMobile" class="collapsed">Descripcion Movil</a>
	            </h4>
	        </div>
	        <div id="tabMobile" class="panel-collapse collapse">
	            <div class="panel-body">
	                <form>
	                    <textarea id="textoMobile" name="textoMobile" rows="20" cols="10">
	                        {{((isset($aseguradoraDatos)) ? $aseguradoraDatos->descripcion_movil : '')}}
	                    </textarea>
	                    <br>
	                </form>
	                <button class="btn btn-success" id="guardaMobile" >Guardar</button>
	            </div>
	        </div>
	    </div>
	    <div class="panel panel-success-alt">
	        <div class="panel-heading">
	            <h4 class="panel-title">
	                <a data-toggle="collapse" href="#tabPromo" class="collapsed">Promociones pago de contado</a>
	            </h4>
	        </div>
	        <div id="tabPromo" class="panel-collapse collapse">
	            <div class="panel-body">
	                <form>
	                    <textarea id="textoPromo" name="textoPromo" rows="20" cols="10">
	                        {{((isset($aseguradoraDatos)) ? $aseguradoraDatos->descripcion_promo : '')}}
	                    </textarea>
	                    <br>
	                </form>
	                <button class="btn btn-success" id="guardaPromo" >Guardar</button>
	            </div>
	        </div>
	    </div>
    @endif
@stop