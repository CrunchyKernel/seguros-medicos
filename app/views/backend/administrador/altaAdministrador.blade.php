
@section('contenido')
	<div class="row">
		<div class="col-md-12">
        	<form id="administradorForm" name="administradorForm">
				<div class="panel panel-default">
	                <div class="panel-heading">
	                    <h4 class="panel-title">Nuevo administrador</h4>
	                    <p>Complete el formulario.</p>
	                </div>
	                <div class="panel-body">
	                    <div class="row">
	                        <div class="col-sm-4">
	                            <div class="form-group">
	                                <label class="control-label">Nombre(s)</label>
	                                <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Nombre(s)">
	                            </div><!-- form-group -->
	                        </div><!-- col-sm-6 -->
	                        <div class="col-sm-4">
	                            <div class="form-group">
	                                <label class="control-label">Apellido paterno</label>
	                                <input type="text" id="apellido_paterno" name="apellido_paterno" class="form-control" placeholder="Apellido paterno">
	                            </div><!-- form-group -->
	                        </div><!-- col-sm-6 -->
	                        <div class="col-sm-4">
	                            <div class="form-group">
	                                <label class="control-label">Apellido materno</label>
	                                <input type="text" id="apellido_materno" name="apellido_materno" class="form-control" placeholder="Apellido materno">
	                            </div><!-- form-group -->
	                        </div><!-- col-sm-6 -->
	                    </div><!-- row -->
	                    <div class="row">
	                        <div class="col-sm-4">
	                            <div class="form-group">
	                                <label class="control-label">Correo electrónico</label>
	                                <input type="email" id="e_mail" name="e_mail" class="form-control" placeholder="Correo electrónico">
	                            </div><!-- form-group -->
	                        </div><!-- col-sm-6 -->
	                        <div class="col-sm-4">
	                            <div class="form-group">
	                                <label class="control-label">Teléfono celular</label>
	                                <input type="text" id="telefono_celular" name="telefono_celular" class="form-control" placeholder="Teléfono celular">
	                            </div><!-- form-group -->
	                        </div><!-- col-sm-6 -->
	                        <div class="col-sm-4">
	                            <div class="form-group">
	                                <label class="control-label">Teléfono particular</label>
	                                <input type="text" id="telefono_particular" name="telefono_particular" class="form-control" placeholder="Teléfono particular">
	                            </div><!-- form-group -->
	                        </div><!-- col-sm-6 -->
	                    </div><!-- row -->
	                    <div class="row">
	                        <div class="col-sm-4">
	                            <div class="form-group">
	                                <label class="control-label">Contraseña</label>
	                                <input type="text" id="contrasena" name="contrasena" class="form-control" placeholder="Contraseña">
	                            </div><!-- form-group -->
	                        </div><!-- col-sm-6 -->
	                        <div class="col-sm-4">
	                            <div class="form-group">
	                                <label class="control-label">&nbsp;</label>
	                                <div class="ckbox ckbox-success">
	                                    <input type="checkbox" id="activo" name="activo" checked="checked" />
	                                    <label for="activo">Cuenta activa</label>
	                                </div>
	                            </div><!-- form-group -->
	                        </div><!-- col-sm-6 -->
	                        <div class="col-sm-4">
	                            <div class="form-group">
	                                <label class="control-label">&nbsp;</label>
	                                <div class="ckbox ckbox-primary">
	                                    <input type="checkbox" id="enviarAcceso" name="enviarAcceso" />
	                                    <label for="enviarAcceso">Enviar acceso por e-mail</label>
	                                </div>
	                            </div><!-- form-group -->
	                        </div><!-- col-sm-6 -->
	                    </div>
	                </div><!-- panel-body -->
	                <div class="panel-footer">
	                    <button type="button" class="btn btn-primary registrarAdministrador" data-loading-text='Processando {{HTML::image('backend/images/loaders/loader31.gif')}}'>Registrar</button>
	                </div><!-- panel-footer -->
	            </div>
	        </form>
		</div>
    </div>
@stop