
@section('contenido')

<div class="row">
    <div class="col-md-12">
    	<form id="altaEmailWhiteForm" name="altaEmailWhiteForm">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">Lista blanca</h4>
                    <p>Nuevo correo electrónico</p>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="email" id="e_mail" name="e_mail" class="form-control" placeholder="Correo electrónico">
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <input type="text" id="cotizacionesTotales" name="cotizacionesTotales" class="form-control" placeholder="Nombre completo" value="20">
                            </div>
                        </div>
                        <div class="col-sm-2">
                        	 <button class="btn btn-primary agregarEmailWhite" type="submit" data-loading-text='Procesando {{HTML::image('assets/img/ajax-loader.gif')}}'><i class="fa fa-save"></i> Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="row">
	<div class="col-md-12">&nbsp;</div>
</div>
<div class="row">
	<div class="col-md-12">
		<table class="table table-striped table-hover" id="listadoCorreos" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th class="alignCenter">#</th>
                    <th class="alignCenter">Correo electrónico</th>
                    <th class="alignCenter">Cotizaciones</th>
                    <th class="alignCenter">Opciones</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
	</div>
</div>

@stop