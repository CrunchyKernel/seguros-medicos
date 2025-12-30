
@section('contenido')
	<div class="row">
		<div class="col-md-12">
        	<form id="listaForm" name="listaForm" method="POST">
				<div class="panel panel-default">
	                <div class="panel-heading">
	                    <h4 class="panel-title">Nueva lista de distribución</h4>
	                    <p>Complete el formulario.</p>
	                </div>
	                <div class="panel-body">
	                    <div class="row">
	                        <div class="col-sm-12">
	                            <div class="form-group">
	                                <label class="control-label">Nombre</label>
	                                <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Nombre" value="{{((isset($listaDatos)) ? htmlentities($listaDatos->nombre) : '')}}">
	                            </div><!-- form-group -->
	                        </div><!-- col-sm-6 -->
	                    </div><!-- row -->
	                </div><!-- panel-body -->
	                <div class="panel-footer">
	                    <button type="button" class="btn btn-primary registrarLista" data-loading-text='Processando {{HTML::image('backend/images/loaders/loader31.gif')}}'>Registrar</button>
	                </div><!-- panel-footer -->
	            </div>
	            <input type="hidden" id="id_lista" name="id_lista" value="{{((isset($listaDatos)) ? $listaDatos->id_lista : '-1')}}">
	        </form>
		</div>
    </div>
    @if(isset($listaDatos))
	    <div class="panel panel-primary">
	    	<div class="panel-heading">
	    		<h4 class="panel-title">
	    			<a data-toggle="collapse" href="#tabPlantillas">Plantillas</a>
	    		</h4>
	    	</div>
	    	<div class="panel-collapse collapse in" role="tabpanel" id="tabPlantillas">
		    	<div class="panel-body">
		    		<form id="plantillaForm">
		    			<input type="hidden" name="id_lista" id="idLista2" value="{{((isset($listaDatos)) ? $listaDatos->id_lista : '-1')}}">
			    		<div class="row">
			    			<div class="col-sm-10">
			    				<div class="form-group">
	                                <label class="control-label">Plantilla</label>
	                                <input type="text" id="plantilla" name="plantilla" class="form-control" placeholder="Plantilla">
	                            </div>
			    			</div>
			    			<div class="col-sm-2">
			    				<div class="form-group">
	                                <label class="control-label">Orden</label>
	                                <input type="number" id="orden" name="orden" class="form-control" placeholder="Orden" step="1">
	                            </div>
			    			</div>
			    		</div>
			    		<div class="row">
			    			<div class="col-sm-2">
			    				<div class="form-group">
	                                <label class="control-label">Tipo de envio</label>
	                                <select class="form-control" id="tipo" name="tipo" required>
	                                	<option value="">Selecicona...</option>
	                                	<option value="1">Espaciado de días</option>
	                                	<option value="2">Día específico</option>
	                                </select>
	                            </div>
			    			</div>
			    			<div class="col-sm-2">
			    				<div class="form-group">
			    					<label class="control-label">Hora de envío</label>
			    					<select class="form-control" id="hora" name="hora" required>
			    						<option value="">Selecciona...</option>
			    						<option value="08:00">08:00</option>
			    						<option value="08:30">08:30</option>
			    						<option value="09:00">09:00</option>
			    						<option value="10:00">10:00</option>
			    						<option value="10:30">10:30</option>
			    						<option value="11:00">11:00</option>
			    						<option value="11:30">11:30</option>
			    						<option value="12:00">12:00</option>
			    						<option value="12:30">12:30</option>
			    						<option value="13:00">13:00</option>
			    						<option value="13:30">13:30</option>
			    						<option value="14:00">14:00</option>
			    						<option value="14:30">14:30</option>
			    						<option value="15:00">15:00</option>
			    						<option value="15:30">15:30</option>
			    						<option value="16:00">16:00</option>
			    						<option value="16:30">16:30</option>
			    						<option value="17:00">17:00</option>
			    						<option value="17:30">17:30</option>
			    						<option value="18:00">18:00</option>
			    						<option value="18:30">18:30</option>
			    						<option value="19:00">19:00</option>
			    						<option value="19:30">19:30</option>
			    						<option value="20:00">20:00</option>
			    						<option value="20:30">20:30</option>
			    					</select>
			    				</div>
			    			</div>
			    			<div class="col-sm-8">
			    				<div class="form-group">
			    					<label class="control-label">Ignorar</label>
			    					<div class="form-inline">
				    					<div class="checkbox">
											<label>
												<input type="checkbox" value="1" id="chkIDomingo" name="chkIDomingo"> Domingo
											</label>
										</div>
										<div class="checkbox">
											<label>
												<input type="checkbox" value="1" id="chkILunes" name="chkILunes"> Lunes
											</label>
										</div>
										<div class="checkbox">
											<label>
												<input type="checkbox" value="1" id="chkIMartes" name="chkIMartes"> Martes
											</label>
										</div>
										<div class="checkbox">
											<label>
												<input type="checkbox" value="1" id="chkIMiercoles" name="chkIMiercoles"> Miércoles
											</label>
										</div>
										<div class="checkbox">
											<label>
												<input type="checkbox" value="1" id="chkIJueves" name="chkIJueves"> Jueves
											</label>
										</div>
										<div class="checkbox">
											<label>
												<input type="checkbox" value="1" id="chkIViernes" name="chkIViernes"> Viernes
											</label>
										</div>
										<div class="checkbox">
											<label>
												<input type="checkbox" value="1" id="chkISabado" name="chkISabado"> Sábado
											</label>
										</div>
									</div>
			    				</div>
			    			</div>
			    		</div>
			    		<div class="row tipo-1 hidden">
			    			<div class="col-sm-2">
			    				<div class="form-group">
	                                <label class="control-label">Días</label>
	                                <input type="number" id="dias" name="dias" class="form-control" placeholder="Dias" step="1">
	                            </div>
			    			</div>
			    		</div>
			    		<div class="row tipo-2 hidden">
			    			<div class="col">
			    				<div class="form-group">
			    					<label class="control-label">Enviar cada</label>
			    					<div class="form-inline">
				    					<div class="checkbox">
											<label>
												<input type="checkbox" value="1" id="chkDomingo" name="chkDomingo"> Domingo
											</label>
										</div>
										<div class="checkbox">
											<label>
												<input type="checkbox" value="1" id="chkLunes" name="chkLunes"> Lunes
											</label>
										</div>
										<div class="checkbox">
											<label>
												<input type="checkbox" value="1" id="chkMartes" name="chkMartes"> Martes
											</label>
										</div>
										<div class="checkbox">
											<label>
												<input type="checkbox" value="1" id="chkMiercoles" name="chkMiercoles"> Miércoles
											</label>
										</div>
										<div class="checkbox">
											<label>
												<input type="checkbox" value="1" id="chkJueves" name="chkJueves"> Jueves
											</label>
										</div>
										<div class="checkbox">
											<label>
												<input type="checkbox" value="1" id="chkViernes" name="chkViernes"> Viernes
											</label>
										</div>
										<div class="checkbox">
											<label>
												<input type="checkbox" value="1" id="chkSabado" name="chkSabado"> Sábado
											</label>
										</div>
									</div>
			    				</div>
			    			</div>
			    		</div>
			    		<div class="row">
			    			<div class="col text-right">
			    				<button type="button" class="btn btn-primary registrarPlantilla" data-loading-text='Procesando {{HTML::image('backend/images/loaders/loader31.gif')}}'>Registrar</button>
			    			</div>
			    		</div>
		    		</form>
		    		<div class="row">
		    			<div class="col-sm-12">
		    				<table id="plantillasTable" class="table table-striped table-bordered table-hover responsive">
		                        <thead>
		                            <tr>
		                                <th class="alignCenter">Plantilla</th>
		                                <th class="alignCenter">Orden</th>
		                            </tr>
		                        </thead>
		                        <tbody></tbody>
		                        <tfoot>
		                            <tr>
		                                <th class="alignCenter">Plantilla</th>
		                                <th class="alignCenter">Orden</th>
		                            </tr>
		                        </tfoot>
		                    </table>
		    			</div>
		    		</div>
		    	</div>
	    	</div>
	    </div>
    @endif
@stop