

@section('contenido')
    <div class="mb20"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">Seguimientos</h4>
                    <p>Listado de los seguimientos de las cotizaciones</p>
                    <div class="pull-right">
                        <a class="tooltips actualizarTabla" data-toggle="tooltip" href="#" data-original-title="Actualizar tabla"><i class="fa fa-refresh"></i></a>
                    </div>
                </div><!-- panel-heading -->
                <div class="panel-body" style="padding: 0px;">
                    <table id="listadoSeguimientos" class="table table-striped table-bordered table-hover responsive">
                        <thead class="">
                            <tr>
                                <th class="alignCenter">#</th>
                                <th class="alignCenter">Notas</th>
                                <th class="alignCenter">Fecha seguimiento</th>
                                <th class="alignCenter">Nombre</th>
                                <th class="alignCenter">Agente</th>
                                <th class="alignCenter">Estatus</th>
                                <th class="alignCenter"></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot class="">
                            <tr>
                                <th class="alignCenter">#</th>
                                <th class="alignCenter">Notas</th>
                                <th class="alignCenter">Fecha seguimiento</th>
                                <th class="alignCenter">Nombre</th>
                                <th class="alignCenter">Agente</th>
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
