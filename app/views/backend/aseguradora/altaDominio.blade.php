
@section('contenido')
	<div class="row">
		<div class="col-md-12">
        	<form id="dominioForm" name="dominioForm" method="POST">
				<div class="panel panel-default">
	                <div class="panel-heading">
	                    <h4 class="panel-title">Nuevo dominio</h4>
	                    <p>Complete el formulario.</p>
	                </div>
	                <div class="panel-body">
	                	<div class="row">
	                		<div class="col-sm-6">
	                			<div class="form-group">
	                				<label class="control-label">Nombre</label>
	                				<input type="text" name="nombre" id="nombre" class="form-control" placeholder="Nombre">
	                			</div>
	                		</div>
	                		<div class="col-sm-6">
	                            <div class="form-group">
	                                <label class="control-label">Dominio</label>
	                                <input type="text" id="dominio" name="dominio" class="form-control" placeholder="Dominio">
	                            </div><!-- form-group -->
	                        </div><!-- col-sm-6 -->
	                	</div>
	                    <div class="row">
	                        <div class="col-sm-6">
	                            <div class="form-group">
	                                <label class="control-label">Email</label>
	                                <input type="email" id="email" name="email" class="form-control" placeholder="Email">
	                            </div><!-- form-group -->
	                        </div><!-- col-sm-6 -->
	                        <div class="col-sm-6">
	                            <div class="form-group">
	                                <label class="control-label">Remitente</label>
	                                <input type="text" id="sender" name="sender" class="form-control" placeholder="Remitente">
	                            </div><!-- form-group -->
	                        </div><!-- col-sm-6 -->
	                    </div><!-- row -->
	                    <div class="row">
	                        <div class="col-sm-6">
	                            <div class="form-group">
	                                <label class="control-label">Logo</label>
	                                <input type="text" id="logo" name="logo" class="form-control" placeholder="Logo">
	                            </div><!-- form-group -->
	                        </div><!-- col-sm-6 -->
	                        <div class="col-sm-6">
	                            <div class="form-group">
	                                <label class="control-label">Ver cotizacion</label>
	                                <input type="text" id="ver_cotizacion" name="ver_cotizacion" class="form-control" placeholder="Ver cotizacion">
	                            </div><!-- form-group -->
	                        </div><!-- col-sm-6 -->
	                    </div><!-- row -->
	                    <div class="row">
	                    	<div class="col-sm-6">
	                            <div class="form-group">
	                                <label class="control-label">&nbsp;</label>
	                                <div class="ckbox ckbox-success">
	                                    <input type="checkbox" id="activo" name="activo"/>
	                                    <label for="activo">Dominio activo</label>
	                                </div>
	                            </div><!-- form-group -->
	                        </div><!-- col-sm-6 -->
	                        <div class="col-sm-6">
	                            <div class="form-group">
	                                <label class="control-label">Ver cotizacion nuevo</label>
	                                <input type="text" id="ver_cotizacion_nuevo" name="ver_cotizacion_nuevo" class="form-control" placeholder="Ver cotizacion nuevo">
	                            </div><!-- form-group -->
	                        </div><!-- col-sm-6 -->
	                    </div>
	                </div><!-- panel-body -->
	                <div class="panel-footer">
	                    <button type="button" class="btn btn-primary registrarDominio" data-loading-text='Processando {{HTML::image('backend/images/loaders/loader31.gif')}}'>Registrar</button>
	                </div><!-- panel-footer -->
	            </div>
	        </form>
		</div>
    </div>
@stop