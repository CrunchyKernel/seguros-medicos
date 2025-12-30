
@section('contenido')
    <div class="mb20"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">Administradores</h4>
                    <p>Listado con los administradores del sistema</p>
                    <div class="pull-right">
                        <a class="tooltips actualizarTabla" data-toggle="tooltip" href="#" data-original-title="Actualizar tabla"><i class="fa fa-refresh"></i></a>
                    </div>
                </div><!-- panel-heading -->
                <div class="panel-body" style="padding: 0px;">
                    <table id="basicTable" class="table table-striped table-bordered table-hover responsive">
                        <thead class="">
                            <tr>
                                <th class="alignCenter">#</th>
                                <th class="alignCenter">Nombre</th>
                                <th class="alignCenter">Celular</th>
                                <th class="alignCenter">Particular</th>
                                <th class="alignCenter">E-mail</th>
                                <th class="alignCenter">Puesto</th>
                                <th class="alignCenter">Estatus</th>
                                <th class="alignCenter"></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot class="">
                            <tr>
                                <th class="alignCenter">#</th>
                                <th class="alignCenter">Nombre</th>
                                <th class="alignCenter">Celular</th>
                                <th class="alignCenter">Particular</th>
                                <th class="alignCenter">E-mail</th>
                                <th class="alignCenter">Puesto</th>
                                <th class="alignCenter">Estatus</th>
                                <th class="alignCenter"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div><!-- panel-body -->
            </div><!-- panel -->
        </div>
    </div>
    
@stop