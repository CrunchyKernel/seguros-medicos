
@section('contenido')
	<div class="row">
        <div class="col-md-12">
    	     <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">Generar mapa de sitio</h4>
                    <p>Dar clic en el boton Generar Sitemap.</p>
                </div><!-- panel-heading -->
                <div class="panel-footer">
                	<div class="row pull-right">
            			<button id="btnSitemap" class="btn btn-primary mr5" data-loading-text='Processando {{HTML::image('backend/images/loaders/loader31.gif')}}'>Generar Sitemap</button>
                	</div>
                </div><!-- panel-footer -->  
            </div><!-- panel -->
        </div><!-- col-md-6 -->
    </div><!-- row-->
@stop