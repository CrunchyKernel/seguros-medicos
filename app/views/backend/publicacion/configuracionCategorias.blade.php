
@section('contenido')
	<div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">Categorías de blog</h4>
            <p>Ordene las categorías arrastrandolas.</p>
        </div><!-- panel-heading -->
        <div class="panel-body">
            <div class="dd" id="categoriasBlog">
		        <ol class="dd-list"></ol>
		    </div>
        </div><!-- panel-body -->
        <div class="panel-footer">
        	<div class="row pull-right">
    			<button class="btn btn-primary mr5 actualizarCategoriasOrden" data-loading-text='Processando {{HTML::image('backend/images/loaders/loader31.gif')}}'>Guardar</button>
        	</div>
        </div><!-- panel-footer -->  
    </div><!-- panel -->

@stop