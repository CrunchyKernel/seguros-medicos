
@section('contenido')
	<div class="row">
		<div class="col-md-12">
        	<form id="redireccionForm" name="redireccionForm" method="POST">
				<div class="panel panel-default">
	                <div class="panel-heading">
	                    <h4 class="panel-title">Nueva redireccion</h4>
	                    <p>Complete el formulario.</p>
	                </div>
	                <div class="panel-body">
	                	<div class="row">
	                		<div class="col-sm-6">
	                			 <div class="form-group">
	                                <label class="control-label">Alias</label>
	                                <input type="text" id="alias" name="alias" class="form-control" placeholder="Alias">
	                            </div><!-- form-group -->
	                		</div>
	                		<div class="col-sm-6">
	                            <div class="form-group">
	                                <label class="control-label">Redireccionar a</label>
	                                <input type="text" id="redirect_to" name="redirect_to" class="form-control" placeholder="Redireccionar a">
	                            </div><!-- form-group -->
	                        </div><!-- col-sm-6 -->
	                	</div>
	                    <div class="row">
	                        <div class="col-sm-6">
	                            <div class="form-group">
	                                <label class="control-label">Tipo</label>
	                                <input type="number" id="tipo" name="tipo" class="form-control">
	                            </div><!-- form-group -->
	                        </div><!-- col-sm-6 -->
	                    </div><!-- row -->
	                </div><!-- panel-body -->
	                <div class="panel-footer">
	                    <button type="button" class="btn btn-primary registrarRedireccion" data-loading-text='Processando {{HTML::image('backend/images/loaders/loader31.gif')}}'>Registrar</button>
	                </div><!-- panel-footer -->
	            </div>
	        </form>
		</div>
    </div>
@stop