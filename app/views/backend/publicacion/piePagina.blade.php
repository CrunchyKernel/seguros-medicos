@section('contenido')
	<div class="row">
        <div class="col-md-12">
        	<form id="piePaginaForm" method="POST" enctype="multipart/form-data">
	            <div class="panel panel-default">
	                <div class="panel-heading">
	                    <h4 class="panel-title">Pie de pagina</h4>
	                    <p>Complete el formulario.</p>
	                </div><!-- panel-heading -->
	                <div class="panel-body">
	                    <div class="col-sm-12">
	                        <div class="form-group">
	                            <label class="control-label">Contenido</label>
	                            <textarea id="contenido" name="contenido" placeholder="Enter text here..." class="form-control" rows="10">{{((isset($contenido)) ? $contenido['footer'] : '')}}</textarea>
	                        </div><!-- form-group -->
	                    </div><!-- row -->
	                </div>
	                <div class="panel-footer">
	                	<div class="row pull-right">
	            			<button class="btn btn-primary mr5 publicar" data-loading-text='Processando {{HTML::image('backend/images/loaders/loader31.gif')}}'>Publicar contenido</button>
	                	</div>
	                </div>
	            </div>
	        </form>
	    </div>
	</div>
@stop