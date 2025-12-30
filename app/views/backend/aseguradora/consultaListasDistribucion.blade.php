
@section('contenido')
	<div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">Listas de distribución</h4>
                    <p>Listado con las listas de distribución de whatsapp</p>
                    <a href="{{URL::to('admingm/aseguradora/altaListaDistribucion')}}"><i class="fa fa-plus-circle"></i> Nueva lista</a>
                    <div class="pull-right">
                        <a class="tooltips actualizarTabla" data-toggle="tooltip" href="#" data-original-title="Actualizar tabla"><i class="fa fa-refresh"></i></a>
                    </div>
                </div><!-- panel-heading -->
                <div class="panel-body" style="padding: 0px;">
                    <table id="basicTable" class="table table-striped table-bordered table-hover responsive">
                        <thead>
                            <tr>
                                <th class="alignCenter">#</th>
                                <th class="alignCenter">Nombre</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <th class="alignCenter">#</th>
                                <th class="alignCenter">Nombre</th>
                            </tr>
                        </tfoot>
                    </table>
                </div><!-- panel-body -->
            </div><!-- panel -->
        </div>
    </div>
    
@stop