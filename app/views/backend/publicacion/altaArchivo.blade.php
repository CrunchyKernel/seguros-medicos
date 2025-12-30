
@section('contenido')
	<div class="row">
		<div class="col-md-12">
        	<form id="archivoForm" name="redireccionForm" method="POST" enctype="multipart/form-data">
				<div class="panel panel-default">
	                <div class="panel-heading">
	                    <h4 class="panel-title">Nuevo archivo</h4>
	                    <p>Complete el formulario.</p>
	                </div>
	                <div class="panel-body">
	                	<div class="row">
	                		<div class="col-sm-12">
	                			<div class="form-group">
	                				<label class="control-label">Archivo</label>
	                				<input type="file" id="archivo" name="archivo" class="form-control">
	                			</div>
	                		</div>
	                	</div>
	                	<div class="row">
	                		<div class="col-sm-6">
	                			 <div class="form-group">
	                                <label class="control-label">Alias</label>
	                                <input type="text" id="alias" name="alias" class="form-control" placeholder="Alias">
	                            </div><!-- form-group -->
	                		</div>
	                		<div class="col-sm-6">
	                            <div class="form-group">
	                                <label class="control-label">Descargable</label>
	                                <input type="text" id="descarga" name="descarga" class="form-control" placeholder="Descargable">
	                            </div><!-- form-group -->
	                        </div><!-- col-sm-6 -->
	                	</div>
	                </div><!-- panel-body -->
	                <div class="panel-footer">
	                    <button type="button" class="btn btn-primary registrarArchivo" data-loading-text='Processando {{HTML::image('backend/images/loaders/loader31.gif')}}'>Registrar</button>
	                </div><!-- panel-footer -->
	            </div>
	        </form>
		</div>
    </div>
@stop